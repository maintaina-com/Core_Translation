<?php

declare(strict_types=1);

namespace Horde\Core\Translation\Middleware\Api;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Horde_Registry;

/**
 * Returns locale json file for a specific language and namespace.
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
        $domain = $route['domain'];
        $namespace = $route['namespace'];
        $currentLang = $this->registry->preferredLang();
        $this->registry->setLanguage($lang);
        // TODO: setTextDomain
        // $this->registry->setTextDomain($context, 'locale');
        $json = json_encode($this->getData());
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
