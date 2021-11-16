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
class GetLanguages implements MiddlewareInterface
{
    protected ResponseFactoryInterface $responseFactory;
    protected StreamFactoryInterface $streamFactory;
    protected Horde_Registry $registry;
    protected $config;

    public function __construct(
        ResponseFactoryInterface $responseFactory,
        StreamFactoryInterface $streamFactory,
        Horde_Registry $registry
    ) {
        $this->responseFactory = $responseFactory;
        $this->streamFactory = $streamFactory;
        $this->registry = $registry;
        // $this->config = $registry->loadConfigFile('nls.php', 'horde_nls_config', 'horde')->config['horde_nls_config'];
        $this->languages = $registry->nlsconfig->languages;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $json = json_encode($this->languages);
        $body = $this->streamFactory->createStream($json);
        return $this->responseFactory
            ->createResponse(200)
            ->withBody($body)
            ->withHeader('Content-Type', 'application/json');
    }
}
