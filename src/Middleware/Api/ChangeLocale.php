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
 * Changes the selected locale for the current session.
 */
class ChangeLocale implements MiddlewareInterface
{
    protected ResponseFactoryInterface $responseFactory;
    protected StreamFactoryInterface $streamFactory;
    protected Horde_Registry $registry;
    protected $config;
    protected array $languages;


    public function __construct(
        ResponseFactoryInterface $responseFactory,
        StreamFactoryInterface $streamFactory,
        Horde_Registry $registry
    ) {
        $this->responseFactory = $responseFactory;
        $this->streamFactory = $streamFactory;
        $this->registry = $registry;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $languages = $this->registry->nlsconfig->languages;
        $route = $request->getAttribute('route');

        $lang = $route['languageCode'];
        if (array_key_exists($lang, $languages)) {
            $this->registry->setLanguageEnvironment($lang);
        } else {
            return $this->responseFactory
            ->createResponse(400, 'Invalid language code');
        }

        $json = json_encode(['success' => true]);
        $body = $this->streamFactory->createStream($json);
        return $this->responseFactory
            ->createResponse(200)
            ->withBody($body)
            ->withHeader('Content-Type', 'application/json');
    }
}
