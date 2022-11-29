<?php

namespace Application\Authentication;

use Application\Database\Repository\InviteRepository;
use Application\Database\Repository\UserRepository;
use Aura\Filter;
use ReCaptcha\ReCaptcha;
use Application\Authentication\Exception\AuthenticationErrorException;

require_once dirname(__DIR__) . "/Vendor/autoload.php";

/**
 *
 */
class AuthenticationModel extends UserRepository
{
    /**
     * @var Filter\ValueFilter
     */
    private Filter\ValueFilter $filter;

    /**
     * @var ReCaptcha
     */
    public ReCaptcha $recaptcha;

    /**
     * @var InviteRepository
     */
    private InviteRepository $inviteRepository;

    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->filter = (new Filter\FilterFactory())->newValueFilter();
        $this->recaptcha = new ReCaptcha('6LfRiaAbAAAAAIVOmMsZbFMZT_9CL0eCBIFk58dr');
        $this->inviteRepository = new InviteRepository;
    }

    /**
     * @param string $response
     * @return bool
     * @throws AuthenticationErrorException
     */
    protected function checkCaptcha(string $response): bool
    {
        if ($this->recaptcha->verify($response)->isSuccess())
        {
            return true;
        }

        throw new AuthenticationErrorException('Captcha verification failed.');
    }

    /**
     * @param array $post
     * @return bool
     * @throws AuthenticationErrorException
     */
    protected function validateLogin(array $post): bool
    {
        if(empty(trim($post['loginUsername'])) && empty(trim($post['loginPassword'])))
        {
            throw new AuthenticationErrorException('Fields are empty.');
        }
        elseif(!$this->filter->validate($post['loginUsername'], 'alnum'))
        {
            throw new AuthenticationErrorException('Username must be alphanumeric.');
        }
        elseif(!$this->filter->validate($post['loginUsername'], 'strlenBetween', 3, 16))
        {
            throw new AuthenticationErrorException('Username is too short or too long.');
        }
        elseif(!$this->filter->validate($post['loginPassword'], 'strlenBetween', 6, 32))
        {
            throw new AuthenticationErrorException('Password is too short or too long.');
        }
        elseif($this->countByUsername($post['loginUsername']) === 0)
        {
            throw new AuthenticationErrorException('Username does not exist.');
        }
        elseif(!password_verify($post['loginPassword'], $this->getByUserName($post['loginUsername'])->password))
        {
            throw new AuthenticationErrorException('Password does not match.');
        }
        else
        {
            return true;
        }
    }

    /**
     * @param array $post
     * @return bool
     * @throws AuthenticationErrorException
     */
    protected function validateRegister(array $post): bool
    {
        if(empty(trim($post['registerUsername'])) && empty(trim($post['registerPassword'])) && empty(trim($post['registerInviteCode'])))
        {
            throw new AuthenticationErrorException('Fields are empty.');
        }
        elseif(!$this->filter->validate($post['registerUsername'], 'alnum'))
        {
            throw new AuthenticationErrorException('Username must be alphanumeric.');
        }
        elseif(!$this->filter->validate($post['registerUsername'], 'strlenBetween', 3, 16))
        {
            throw new AuthenticationErrorException('Username is too short or too long.');
        }
        elseif(!$this->filter->validate($post['registerPassword'], 'strlenBetween', 6, 32))
        {
            throw new AuthenticationErrorException('Password is too short or too long.');
        }
        elseif($this->countByUsername($post['registerUsername']) === 1)
        {
            throw new AuthenticationErrorException('Username already exists.');
        }
        elseif($this->inviteRepository->countByCode($post['registerInviteCode']) === 0)
        {
            throw new AuthenticationErrorException('Invite code does not exist');
        }
        elseif($this->inviteRepository->getByCode($post['registerInviteCode'])->valid === false)
        {
            throw new AuthenticationErrorException('Invite code is taken.');
        }
        else
        {
            return true;
        }
    }
}