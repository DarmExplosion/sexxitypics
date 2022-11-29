<?php

namespace Application\Json;

use Application\Database\Repository\UserRepository;

require_once dirname(__DIR__) . "/Vendor/autoload.php";

class JsonService extends UserRepository
{
    /**
     * @param int $userid
     * @return string
     */
    public function getJson(int $userid): string
    {
        return "{\"author_name\":\"{$this->getByUserID($userid)->embed->author}\"}";
    }
}