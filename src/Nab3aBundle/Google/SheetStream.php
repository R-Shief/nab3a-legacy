<?php

namespace Nab3aBundle\Google;

use GuzzleHttp\Client;
use GuzzleHttp\Promise\PromiseInterface;
use React\EventLoop\LoopInterface;
use React\EventLoop\Timer\TimerInterface;
use React\Stream\WritableStream;

class SheetStream extends WritableStream
{
    /**
     * @var
     */
    private $scriptId;

    /**
     * @var
     */
    private $documentId;

    /**
     * @var ScriptService
     */
    private $s;

    private $client;
    /**
     * @var \React\EventLoop\LoopInterface
     */
    private $loop;

    public function __construct($scriptId, $documentId, \Google_Client $client, LoopInterface $loop)
    {
        $this->scriptId = $scriptId;
        $this->documentId = $documentId;
        $this->client = $client;
        $this->s = new ScriptService($this->scriptId, $this->client);
        $this->loop = $loop;
    }

    public function write($data)
    {
        $request = $this->s->makeRequest('addRows', [$this->documentId, 'Sheet1', $data]);

        $promise = $this->s->run($request);

        $results = $promise->then(function ($response) {
            return $response['response']['result'];
        });

        $timer = $this->loop->addPeriodicTimer(1, function (TimerInterface $timer) use (&$promise) {
            if ($promise->getState() !== PromiseInterface::PENDING) {
                $timer->cancel();
            } else {
                $promise->wait();
            }
        });
    }
}
