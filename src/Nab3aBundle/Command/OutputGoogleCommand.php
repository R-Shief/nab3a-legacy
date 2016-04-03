<?php

namespace Nab3aBundle\Command;

use GuzzleHttp\Psr7\Stream;
use Nab3aBundle\Google\ScriptService;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
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
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $stream = new Stream(STDIN);
        $buffer = '';

        while (!$stream->eof()) {
            $buffer .= $stream->read(4096);
        }

        $scriptId = $this->container->get('nab3a.standalone.parameters')->get('nab3a.google.script');
        /** @var \Google_Client $client */
        $client = $this->container->get('nab3a.google.client');
        $documentId = $input->getArgument('document');
        $sheetId = $input->getArgument('sheet');

        $data = json_decode($buffer, true);

        $s = new ScriptService($scriptId, $client);

        $request = $s->makeRequest('addRows', [$documentId, $sheetId, $data]);
        $service = $s->makeService();

        $response = $service->scripts->run($scriptId, $request);

        if ($response->getError()) {
            /** @var \Google_Service_Script_Status $error */
            $error = $response->getError();
            throw new \RuntimeException($error->getMessage(), $error->getCode());
        }

        $response = $response->getResponse();
        $this->logger->info('added rows', $response);
    }
}
