<?php

namespace Nab3aBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AccessTokenCommand extends AbstractCommand
{
    protected function configure()
    {
        parent::configure();
        $this->setName('google');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $credentialsPath = $this->container->get('nab3a.standalone.parameters')->get('nab3a.google.credentials_path');

        $client = $this->container->get('nab3a.google.client.unauth');
        $url = $client->createAuthUrl();

        $io = new SymfonyStyle($input, $output);
        $io->text([
          'Open the following link in your browser:',
          '',
          $url,
        ]);
        $accessToken = $io->ask('Enter verification code', null, function ($code) use ($client) {
            return $client->fetchAccessTokenWithAuthCode($code);
        });

        // Store the credentials to disk.
        if (!file_exists(dirname($credentialsPath))) {
            mkdir(dirname($credentialsPath), 0700, true);
        }
        file_put_contents($credentialsPath, json_encode($accessToken));
        $io->text(sprintf('Credentials saved to %s', $credentialsPath));
    }
}
