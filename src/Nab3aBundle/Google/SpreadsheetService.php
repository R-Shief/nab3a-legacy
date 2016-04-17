<?php

namespace Nab3aBundle\Google;

use Google_Service_Script;
use Google_Service_Script_ExecutionRequest;
use Google_Service_Script_Operation;
use RuntimeException;

/**
 * Class SpreadsheetService.
 *
 * @method mixed addRows(string $documentId, string $sheetId, array $data) add rows to spreadsheet
 */
class SpreadsheetService
{
    /**
     * @var Google_Service_Script
     */
    private $service;

    /**
     * @var string
     */
    private $scriptId;

    public function __construct(Google_Service_Script $service, $scriptId)
    {
        $this->service = $service;
        $this->scriptId = $scriptId;
    }

    /**
     * @param $name
     * @param $arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        $request = new Google_Service_Script_ExecutionRequest();
        $request->setFunction($name);
        $request->setParameters($arguments);
        $request->setDevMode(true);

        /** @var Google_Service_Script_Operation $response */
        $response = $this->service->scripts->run($this->scriptId, $request);

        if ($response->getError()) {
            $error = $response->getError();
            throw new RuntimeException($error->getDetails()[0]['errorMessage'], $error->getCode());
        }

        return $response->getResponse();
    }
}
