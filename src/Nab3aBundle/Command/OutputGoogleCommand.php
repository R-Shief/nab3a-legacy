<?php

namespace Nab3aBundle\Command;

use Clue\JsonStream\StreamingJsonParser;
use GuzzleHttp\Psr7\Stream;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class OutputGoogleCommand extends Command
{
    use LoggerAwareTrait;
    use ContainerAwareTrait;

    protected function configure()
    {
        $this
            ->setName('output:google')
            ->addArgument('document', InputArgument::REQUIRED)
            ->addArgument('sheet', InputArgument::REQUIRED)
            ->addOption('batch', 'b', InputOption::VALUE_REQUIRED, 'number of rows per batch', 100)
            ->addOption('headers', null, InputOption::VALUE_REQUIRED, 'headers as JSON array')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $stream = new Stream(STDIN);
        $parser = new StreamingJsonParser();
        $data = [];

        $documentId = $input->getArgument('document');
        $sheetId = $input->getArgument('sheet');
        if ($input->getOption('headers')) {
            $headers = \GuzzleHttp\json_decode($input->getOption('headers'), true);
        }

        while (!$stream->eof()) {
            $chunk = $stream->read(4096);
            $objects = $parser->push($chunk);

            if (!empty($objects) && !isset($headers)) {
                $headers = array_keys($objects[0]);
            }

            $rows = array_map('array_values', $objects);
            $data = array_merge($data, $rows);

            if (count($data) >= $input->getOption('batch')) {
                array_unshift($data, $headers);
                $this->runBatch($documentId, $sheetId, $data);
                $data = [];
            }
        }
    }

    private function runBatch($documentId, $sheetId, $data)
    {
        $scriptId = $this->container->get('nab3a.standalone.parameters')->get('nab3a.google.script');
        $client = $this->container->get('nab3a.google.client');

        $request = new \Google_Service_Script_ExecutionRequest();
        $request->setFunction('addRows');
        $request->setParameters([$documentId, $sheetId, $data]);
        $request->setDevMode(true);

        $service = new \Google_Service_Script($client);

        $response = $service->scripts->run($scriptId, $request);

        if ($response->getError()) {
            /** @var \Google_Service_Script_Status $error */
            $error = $response->getError();
            throw new \RuntimeException($error->getDetails()[0]['errorMessage'], $error->getCode());
        }

        $response = $response->getResponse();
        $this->logger->info('added rows', $response);
    }
}
