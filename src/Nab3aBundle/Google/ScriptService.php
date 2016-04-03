<?php

namespace Nab3aBundle\Google;

class ScriptService
{
    private $scriptId;
    /**
     * @var \Google_Client
     */
    private $client;

    public function __construct($scriptId, \Google_Client $client)
    {
        $this->scriptId = $scriptId;
        $this->client = $client;
    }

    /**
     * @return \Google_Service_Script
     */
    public function makeService()
    {
        $service = new \Google_Service_Script($this->client);

        return $service;
    }

    /**
     * @param $function
     * @param $parameters
     *
     * @return \Google_Service_Script_ExecutionRequest
     */
    public function makeRequest($function, $parameters)
    {
        $request = new \Google_Service_Script_ExecutionRequest();
        $request->setFunction($function);
        $request->setParameters($parameters);
        $request->setDevMode(true);

        return $request;
    }
}
