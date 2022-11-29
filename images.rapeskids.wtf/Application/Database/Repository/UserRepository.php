<?php

namespace Application\Database\Repository;

use Application\Database\Mapper\UserMapper;
use MongoDB\BSON\Regex;

require_once dirname(__DIR__) . "/../Vendor/autoload.php";

/**
 *
 */
class UserRepository extends UserMapper
{
    /**
     * @param int $userId
     * @return string|object|null
     */
    public function getByUserID(int $userId): null|string|object
    {
        return $this->get(
            ['id' => $userId]
        );
    }

    /**
     * @param string $userName
     * @return string|object|null
     */
    public function getByUserName(string $userName): null|string|object
    {
        return $this->get(
            ['name' => new Regex($userName, "i")]
        );
    }

    /**
     * @param string $uuid
     * @return string|object|null
     */
    public function getByUUID(string $uuid): null|string|object
    {
        return $this->get(
            ['uuid' => $uuid]
        );
    }

    /**
     * @param string $token
     * @return string|object|null
     */
    public function getByToken(string $token): null|string|object
    {
        return $this->get(
            ['token' => $token]
        );
    }

    /**
     * @param string $token
     * @return int
     */
    public function countByToken(string $token): int
    {
        return $this->count(
            ['token' => $token]
        );
    }

    /**
     * @param string $username
     * @return int
     */
    public function countByUsername(string $username): int
    {
        return $this->count(
            ['name' => new Regex($username, "i")]
        );
    }

    /**
     * @param string $uuid
     * @param array $data
     * @return object
     */
    public function updateByUUID(string $uuid, array $data): object
    {
        return $this->update(
            ['uuid' => $uuid],
            $data
        );
    }
}