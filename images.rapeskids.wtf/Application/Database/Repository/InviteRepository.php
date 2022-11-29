<?php

namespace Application\Database\Repository;

use Application\Database\Mapper\InviteMapper;

require_once dirname(__DIR__) . "/../Vendor/autoload.php";

/**
 *
 */
class InviteRepository extends InviteMapper
{
    /**
     * @param string $code
     * @return string|object|null
     */
    public function getByCode(string $code): null|string|object
    {
        return $this->get(
            ['code' => $code]
        );
    }

    /**
     * @param string $code
     * @return int
     */
    public function countByCode(string $code): int
    {
        return $this->count(
            ['code' => $code]
        );
    }

    /**
     * @param string $code
     * @param array $data
     * @return object
     */
    public function updateByCode(string $code, array $data): object
    {
        return $this->update(
            ['code' => $code],
            $data
        );
    }
}