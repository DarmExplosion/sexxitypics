<?php


namespace Application\Authentication;

use Application\Database\Repository\InviteRepository;
use Application\Session\SessionService;
use Ramsey\Uuid\Uuid;

require_once dirname(__DIR__) . "/Vendor/autoload.php";

/**
 * Class AuthenticationService
 * @package Application\Authentication
 */
class AuthenticationService extends AuthenticationModel
{
    /**
     * @var SessionService
     */
    public SessionService $sessionService;

    /**
     * @var InviteRepository
     */
    public InviteRepository $inviteRepository;

    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->sessionService = new SessionService;
        $this->inviteRepository = new InviteRepository;
    }

    /**
     * @param array $postArguments
     */
    public function login(array $postArguments): void
    {
        if($postArguments['rememberMe'])
        {
            $this->sessionService->set($this->getByUserName($postArguments['loginUsername'])->uuid, 86400);
        }
        else
        {
            $this->sessionService->set($this->getByUserName($postArguments['loginUsername'])->uuid);
        }
    }

    /**
     * @param $postArguments
     */
    public function register($postArguments): void
    {
        $this->insert([
            'id' => $this->count([]) + 1,
            'name' => $postArguments['registerUsername'],
            'uuid' => Uuid::uuid4()->toString(),
            'password' => password_hash($postArguments['registerPassword'], PASSWORD_DEFAULT),
            'registered' => time(),
            'token' => Uuid::uuid4()->toString(),
            'domain' => 'exity.pics',
            'banned' => false,
            'premium' => false,
            'staff' => false,
            'embed' => [
                'enabled' => true,
                'color' => '#0092FE',
                'title' => 'exity.pics',
                'description' => 'i used exity.pics to upload this image.'
            ],
            'portfolio' => [
                'status' => '',
                'description' => ''
            ]
        ]) === false ?: $this->inviteRepository->updateByCode($postArguments['registerInviteCode'], ['valid' => false]);
    }

    /**
     *
     */
    public function logout(): void
    {
        $this->sessionService->destroy();
    }
}