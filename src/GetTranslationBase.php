<?php

declare(strict_types=1);

namespace Horde\Core\Translation;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Horde_Registry;

/**
 * Base Class for returning (nested) array of translatable strings.
 */
abstract class GetTranslationBase implements MiddlewareInterface
{
    protected ResponseFactoryInterface $responseFactory;
    protected StreamFactoryInterface $streamFactory;
    protected Horde_Registry $registry;

    public function __construct(
        ResponseFactoryInterface $responseFactory,
        StreamFactoryInterface $streamFactory,
        Horde_Registry $registry
    ) {
        $this->responseFactory = $responseFactory;
        $this->streamFactory = $streamFactory;
        $this->registry = $registry;
    }

    abstract protected function getData(): array;

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $route = $request->getAttribute('route');

        $lang = $route['languageCode'];
        // uniform language code format. Frontend uses dash as separator
        $lang = str_replace('-', '_', $lang);
        $lang = $this->registry->nlsconfig->aliases[$lang] ?? $lang;
        $namespace = $route['namespace'] ?? null;
        $currentLang = $this->registry->preferredLang();
        $this->registry->setLanguage($lang);
        // TODO: setTextDomain
        // $this->registry->setTextDomain($context, 'locale');
        $data = $this->getData();
        if ($namespace) {
            $data = $data[$namespace] ?? $data;
        }
        $json = json_encode($data);
        $this->registry->setLanguage($currentLang);
        // TODO: revert setTextDomain
        // $this->registry->setTextDomain($context, 'locale');

        $body = $this->streamFactory->createStream($json);
        return $this->responseFactory
            ->createResponse(200)
            ->withBody($body)
            ->withHeader('Content-Type', 'application/json');
    }
}
