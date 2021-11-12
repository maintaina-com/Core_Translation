<?php
/**
 * Copyright 2016-2021 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category Horde
 * @license  http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package  Core
 */
namespace Horde\Core\Translation\Test\Middleware;

use Horde\Test\TestCase;
use Horde\Core\Translation\Middleware\Api\GetTranslationBase;

use \Horde_Session;
use \Horde_Exception;


class GetTranslationTest extends Testcase
{
    use SetUpTrait;

    protected function getMiddleware()
    {
        return new class(
            $this->responseFactory,
            $this->streamFactory,
            $this->registry
        ) extends GetTranslationBase
        {
            protected function getData(): array
            {
                return ["a"=>"b"];
            }
        };
    }

    public function testResponseHasJsonHeader()
    {
        $this->registry->method('preferredLang')->willReturn('de_DE');
        $this->registry->method('setLanguage')->willReturn('de_DE');
        $middleware = $this->getMiddleware();
        $request = $this->requestFactory->createServerRequest('GET', '/passwd/en_US/passwd/ns');
        $request = $request->withAttribute('route', [
            'languageCode' => 'de_DE',
            'domain' => 'passwd',
            'namespace' => 'translation',
        ]);
        $response = $middleware->process($request, $this->handler);
        $this->assertEquals('application/json', $response->getHeaderLine('Content-Type'));
    }
}
