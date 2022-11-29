<?php

namespace Application\Json;

use Application\Json\JsonService;

require_once dirname(__DIR__) . "/Vendor/autoload.php";

class JsonController extends JsonService
{
    /**
     * @param int $userid
     * @return string
     */
    public function callJsonGet(int $userid): string
    {
        return $this->getJson($userid);
    }
}