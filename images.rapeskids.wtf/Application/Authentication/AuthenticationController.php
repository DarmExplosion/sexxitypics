<?php

namespace Application\Authentication;

require_once dirname(__DIR__) . "/Vendor/autoload.php";

/**
 *
 */
class AuthenticationController extends AuthenticationService
{
    /**
     * @param array $post
     * @throws Exception\AuthenticationErrorException
     */
    public function callLogin(array $post): void
    {
        if($this->validateLogin($post))
        {
            $this->login($post);
        }
    }

    /**
     * @param array $post
     * @throws Exception\AuthenticationErrorException
     */
    public function callRegister(array $post): void
    {
        if($this->validateRegister($post))
        {
            $this->register($post);
        }
    }
}