<?php

namespace Nab3aBundle\Stream;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Promise\PromiseInterface;
use Nab3aBundle\Loader\LoaderHelper;

class RequestFactory
{
    const FILTER_METHOD = 'POST';
    const FILTER_URL = 'statuses/filter.json';

    const SAMPLE_METHOD = 'GET';
    const SAMPLE_URL = 'statuses/sample.json';

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var array
     */
    private $options;

    public function __construct(ClientInterface $client, array $options = array())
    {
        $this->client = $client;
        $this->options = $options;
    }

    /**
     * @param $params
     *
     * @return PromiseInterface
     */
    public function filter($params)
    {
        // @todo At least one predicate parameter (follow, locations, or track)
        //   must be specified. The default access level allows up to 400 track
        //   keywords, 5,000 follow userids and 25 0.1-360 degree location boxes.
        return $this->request(self::FILTER_METHOD, self::FILTER_URL, [
          'form_params' => array_filter($params) + $this->options,
        ]);
    }

    /**
     * @return PromiseInterface
     */
    public function sample($params)
    {
        $promise = $this->request(self::SAMPLE_METHOD, self::SAMPLE_URL, [
            'query' => array_filter($params) + $this->options,
        ]);

        return $promise;
    }

    /**
     * @param $method
     * @param $uri
     * @param array $options
     *
     * @return PromiseInterface
     */
    public function request($method, $uri, array $options = [])
    {
        return $this->client->requestAsync($method, $uri, $options);
    }

    public function fromStream($config)
    {
        switch ($config['type']) {
            case 'filter':
                $params = LoaderHelper::makeQueryParams($config['parameters']['track'], $config['parameters']['follow'], $config['parameters']['locations']);

                return $this->filter($params);
            case 'sample':
                $params = $config['parameters'];

                return $this->sample($params);
        }
    }
}
