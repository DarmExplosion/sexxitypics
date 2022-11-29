<?php

namespace Application\Domain;

use Application\Database\Repository\UserRepository;
use Application\Session\SessionService;

require_once dirname(__DIR__) . "/Vendor/autoload.php";

/**
 *
 */
class DomainService extends UserRepository
{
    /**
     * @param array $post
     */
    public function changeDomain(array $post): void
    {
        $this->updateByUUID(
            (new SessionService)->get(),
            ['domain' => $post['domain']]
        );
    }
}