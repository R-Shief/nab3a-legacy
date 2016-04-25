<?php

namespace Nab3aBundle\Google;

use Nab3aBundle\Console\AbstractCommand;
use React\Http;
use React\Socket;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessUtils;

/**
 * Class AccessTokenCommand.
 */
class AccessTokenCommand extends AbstractCommand
{
    /**
     *
     */
    protected function configure()
    {
        parent::configure();
        $this->setName('google:access-token');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     *
     * @throws Socket\ConnectionException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $credentialsPath = $this->container
          ->get('nab3a.standalone.parameters')
          ->get('nab3a.google.credentials_path');

        $port = random_int(pow(2, 15) + pow(2, 14), pow(2, 16) - 1);

        $client = $this->container->get('nab3a.google.client.unauth');
        $client->setRedirectUri('http://localhost:'.$port);
        $url = $client->createAuthUrl();
        $url = ProcessUtils::escapeArgument($url);
        $cmd = self::openBrowser($url);
        if ($cmd) {
            $proc = new Process($cmd.' '.$url);
            $proc->run();
        } else {
            $output->writeln(
              'no suitable browser opening command found, open yourself: '.$url
            );
        }

        $loop = $this->container->get('nab3a.event_loop');

        $socket = new Socket\Server($loop);
        $socket->listen($port);

        $http = new Http\Server($socket);
        $http->once('request', [$this, 'serverListener']);

        $loop->run();

        $output->writeln(sprintf('Credentials saved to %s', $credentialsPath));
    }

    /**
     * @param Http\Request  $request
     * @param Http\Response $response
     *
     * @throws \Exception
     */
    public function serverListener(Http\Request $request, Http\Response $response)
    {
        $client = $this->container->get('nab3a.google.client.unauth');
        $query = $request->getQuery();
        $code = $query['code'];
        $accessToken = $client->fetchAccessTokenWithAuthCode($code);
        $this->saveCredentials($accessToken);

        $response->writeHead(200, array('Content-Type' => 'text/html'));

        $response->write('<!DOCTYPE "html">');
        $response->write('<html>');
        $response->write('<head>');
        $response->write('<title>Successfully Authenticated</title>');
        $response->write('</head>');
        $response->write('<body>');
        $response->write('You\'ve been authenticated with Google Drive! You may close this page.');
        $response->write('<script>open(location, \'_self\').close();</script>');
        $response->write('</body>');
        $response->write('</html>');
        $response->end();
        $response->on('close', function () {
            $this->container->get('nab3a.event_loop')->stop();
        });
    }

    /**
     * @param $accessToken
     */
    private function saveCredentials($accessToken)
    {
        $credentialsPath = $this->container
          ->get('nab3a.standalone.parameters')
          ->get('nab3a.google.credentials_path');

        if (!file_exists(dirname($credentialsPath))) {
            mkdir(dirname($credentialsPath), 0700, true);
        }
        file_put_contents($credentialsPath, \GuzzleHttp\json_encode($accessToken));
    }

    /**
     * @param $url
     *
     * @return string
     */
    private static function openBrowser($url)
    {
        $finder = new ExecutableFinder();
        if (defined('PHP_WINDOWS_VERSION_BUILD')) {
            return passthru('start "web" explorer "'.$url.'"');
        }
        $cmd = $finder->find('xdg-open') ?: $finder->find('open');

        return $cmd;
    }
}
