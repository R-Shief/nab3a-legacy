<?php

namespace Nab3aBundle\Tests\Stream;

use GuzzleHttp\Client;
use GuzzleHttp\Promise\PromiseInterface;
use Nab3aBundle\Stream\RequestFactory;

class RequestFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RequestFactory
     */
    private $rf;

    public function setUp()
    {
        $client = new Client();
        $this->rf = new RequestFactory($client);
    }

    public function testFilter()
    {
        $request = $this->rf->filter([]);
        $this->assertInstanceOf(PromiseInterface::class, $request);
    }

    public function testSample()
    {
        $request = $this->rf->sample([]);
        $this->assertInstanceOf(PromiseInterface::class, $request);
    }

    public function testFromStreamConfig()
    {
        $request = $this->rf->fromStreamConfig([
            'type' => 'sample',
            'parameters' => [],
        ]);
        $this->assertInstanceOf(PromiseInterface::class, $request);

        $request = $this->rf->fromStreamConfig([
          'type' => 'filter',
          'parameters' => [
              'track' => [],
              'follow' => [],
              'locations' => [],
          ],
        ]);
        $this->assertInstanceOf(PromiseInterface::class, $request);
    }
}
