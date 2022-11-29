<?php

namespace Application\Domain;

require_once dirname(__DIR__) . "/Vendor/autoload.php";

/**
 *
 */
class DomainController extends DomainModel
{
    /**
     * @param array $post
     * @throws Exception\DomainValidationException
     */
    public function callDomainChange(array $post): void
    {
        if($this->validateDomainFromArray($post))
        {
            (new DomainService())->changeDomain($post);
        }
    }
}