<?php


namespace Application\Database;

use MongoDB\Client;
use MongoDB\Collection;

require_once dirname(__DIR__) . "/Vendor/autoload.php";

/**
 *
 */
class Connection
{
    /**
     * @var Collection
     */
    protected Collection $collection;

    /**
     * @var array|string[]
     */
    private array $connectionArray = [
        'username' => 'exitydb',
        'password' => 'FK5ETP3vfTxzdxXg@',
        'host' => '127.0.0.1/'
    ];

    /**
     * @return string
     */
    private function setConnectionString(): string
    {
        return 'mongodb://' . $this->connectionArray['username'] . ':' . $this->connectionArray['password'] . $this->connectionArray['host'];
    }

    /**
     * @return Client
     */
    private function setClient(): Client
    {
        return new Client(
            $this->setConnectionString()
        );
    }

    /**
     * @param string $collectionArgument
     */
    public function __construct(string $collectionArgument)
    {
        $this->collection = $this->setClient()->host->$collectionArgument;
    }

}