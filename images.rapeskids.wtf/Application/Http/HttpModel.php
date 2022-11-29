<?php

namespace Application\Http;

use Application\Database\Repository\UserRepository;
use Application\Session\SessionService;
use RuntimeException;

require_once dirname(__DIR__) . "/Vendor/autoload.php";

/**
 *
 */
class HttpModel extends UserRepository
{
    /**
     * @var SessionService
     */
    protected SessionService $sessionService;

    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->sessionService = new SessionService;
    }
}