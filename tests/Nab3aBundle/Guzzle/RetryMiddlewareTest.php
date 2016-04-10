<?php

namespace Nab3aBundle\Tests\Guzzle;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Nab3aBundle\Guzzle\RetryMiddleware;

class RetryMiddlewareTest extends \PHPUnit_Framework_TestCase
{
    public function testCanRetryExceptions()
    {
        $calls = [];
        $mh = function ($handler) use (&$calls) {
            return function ($request, $options) use ($handler, &$calls) {
                $calls[] = func_get_args();

                return $handler($request, $options);
            };
        };
        $m = RetryMiddleware::retry();
        $h = new MockHandler([new ConnectException('error', new Request('', '')), new Response(201)]);
        $c = new Client(['handler' => $m($mh($h))]);
        $p = $c->sendAsync(new Request('GET', 'http://test.com'), []);
        $this->assertEquals(201, $p->wait()->getStatusCode());
        $this->assertCount(2, $calls);
//        $this->assertEquals(0, $calls[0][0]);
//        $this->assertNull($calls[0][2]);
//        $this->assertInstanceOf('Exception', $calls[0][3]);
//        $this->assertEquals(1, $calls[1][0]);
//        $this->assertInstanceOf(Response::class, $calls[1][2]);
//        $this->assertNull($calls[1][3]);
    }

    public function testBackoffCalculateDelay()
    {
        $f = RetryMiddleware::exponentialDelay(1);
        $this->assertEquals(0, $f(0));
        $this->assertEquals(1, $f(1));
        $this->assertEquals(2, $f(2));
        $this->assertEquals(4, $f(3));
        $this->assertEquals(8, $f(4));
    }
}
