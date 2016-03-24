<?php

namespace Nab3aBundle\Loader;

use Doctrine\DBAL\Connection;
use Symfony\Component\Config\Loader\Loader;

class DbalLoader extends Loader
{
    /**
     * Loads a resource.
     *
     * @param string|null $type The resource type or null if unknown
     *
     * @throws \Exception If something went wrong
     *
     * @return array
     */
    public function load(Connection $conn, $type = null)
    {
        $params = [];

        $qb = $conn->createQueryBuilder();
        $query = $qb->select('phrase')->from('twitter_streaming__track')->where('is_active = 1')->orderBy('id');
        $stmt = $conn->query($query);

        $rows = $stmt->fetchAll(\PDO::FETCH_COLUMN);
        $params['track'] = implode(',', $rows);

        $qb = $conn->createQueryBuilder();
        $query = $qb->select('user_id')->from('twitter_streaming__follow')->where('is_active = 1')->orderBy('id');
        $stmt = $conn->query($query);

        $rows = $stmt->fetchAll(\PDO::FETCH_COLUMN);
        $params['follow'] = implode(',', $rows);

        $qb = $conn->createQueryBuilder();
        $query = $qb->select('south', 'west', 'north', 'east')->from('twitter_streaming__location')->where('is_active = 1')->orderBy('id');
        $stmt = $conn->query($query);

        $rows = $stmt->fetchAll(\PDO::FETCH_NUM);
        $rows = array_map(function ($row) {
            return implode(',', $row);
        }, $rows);
        $params['location'] = implode(',', $rows);

        return $params;
    }

    /**
     * Returns whether this class supports the given resource.
     *
     * @param mixed       $resource A resource
     * @param string|null $type     The resource type or null if unknown
     *
     * @return bool True if this class supports the given resource, false otherwise
     */
    public function supports($resource, $type = null)
    {
        return $resource instanceof Connection;
    }
}
