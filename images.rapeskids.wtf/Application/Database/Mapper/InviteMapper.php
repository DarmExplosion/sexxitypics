<?php

namespace Application\Database\Mapper;

use Application\Database\Connection;

require_once dirname(__DIR__) . "/../Vendor/autoload.php";

/**
 *
 */
class InviteMapper extends Connection
{
    /**
     *
     */
    public function __construct()
    {
        parent::__construct('invite');
    }

    /**
     * @param array $filter
     * @return string|object|null
     */
    protected function get(array $filter): null|string|object
    {
        return $this->collection->findOne(
            $filter
        );
    }

    /**
     * @param array $filter
     * @return int
     */
    protected function count(array $filter): int
    {
        return $this->collection->countDocuments(
            $filter
        );
    }

    /**
     * @param array $filter
     * @param array $data
     * @return object
     */
    protected function update(array $filter, array $data): object
    {
        return $this->collection->updateOne(
            $filter,
            ['$set' => $data]
        );
    }
}