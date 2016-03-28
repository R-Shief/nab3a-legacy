<?php

namespace Nab3aBundle\Google;

use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

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
     * @param $function
     * @param $parameters
     *
     * @return RequestInterface
     */
    public function makeRequest($function, $parameters)
    {
        $request = new \Google_Service_Script_ExecutionRequest();
        $request->setFunction($function);
        $request->setParameters($parameters);
        $request->setDevMode(true);

        $service = new \Google_Service_Script($this->client);

        return $service->scripts->run($this->scriptId, $request);
    }

    public function run(RequestInterface $request)
    {
        $promise = $this
          ->client
          ->getHttpClient()
          ->sendAsync($request);

        return $promise->then(
          function (ResponseInterface $response) {
              return \GuzzleHttp\json_decode($response->getBody(), true);
          },
          function (RequestException $e) {
              $response = \GuzzleHttp\json_decode($e->getResponse()->getBody(), true);
              // The API executed, but the script returned an error.

              // Extract the first (and only) set of error details. The values of this
              // object are the script's 'errorMessage' and 'errorType', and an array of
              // stack trace elements.
              $error = $response['error']['details'][0];
              printf("Script error message: %s\n", $error['errorMessage']);

              if (array_key_exists('scriptStackTraceElements', $error)) {
                  // There may not be a stacktrace if the script didn't start executing.
                  echo "Script error stacktrace:\n";
                  foreach ($error['scriptStackTraceElements'] as $trace) {
                      printf("\t%s: %d\n", $trace['function'], $trace['lineNumber']);
                  }
              }
          });
    }
}
