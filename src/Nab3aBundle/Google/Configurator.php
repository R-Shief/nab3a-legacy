<?php

namespace Nab3aBundle\Google;

class Configurator
{
    private $credentialsPath;

    public function __construct($credentialsPath)
    {
        $this->credentialsPath = $credentialsPath;
    }

    public function __invoke(\Google_Client $client)
    {
        if (file_exists($this->credentialsPath)) {
            $accessToken = file_get_contents($this->credentialsPath);
            $client->setAccessToken($accessToken);
        }

        // Refresh the token if it's expired.
        if ($client->isAccessTokenExpired()) {
            $refreshToken = $client->getRefreshToken();
            $client->fetchAccessTokenWithRefreshToken($refreshToken);
            $accessToken = $client->getAccessToken();
            if (!isset($accessToken['refresh_token'])) {
                $accessToken['refresh_token'] = $refreshToken;
            }
            file_put_contents($this->credentialsPath, \GuzzleHttp\json_encode($accessToken));
        }

        $http = $client->authorize();
        $client->setHttpClient($http);
    }
}
