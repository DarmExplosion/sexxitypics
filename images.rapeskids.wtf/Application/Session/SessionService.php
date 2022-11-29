<?php


namespace Application\Session;

use Delight\Cookie\Session;
use JetBrains\PhpStorm\Pure;

require_once dirname(__DIR__) . "/Vendor/autoload.php";

/**
 * Class SessionService
 * @package Application\Session
 */
class SessionService
{

    /**
     * @param string|null $uuid
     * @param int $time
     * @return $this
     */
    public function set(?string $uuid = null, int $time = 600): self
    {
        ob_start();
        session_name('exity');
        session_set_cookie_params(
            null,
            '/',
            null,
            true,
            true
        );

        Session::start('Strict');

        if (!is_null($uuid))
        {
            Session::set('timestamp', time());
            Session::set('time', $time);
            Session::set('uuid', $uuid);
        }

        if (isset($_SESSION['uuid']) && time() - $_SESSION['timestamp'] > $_SESSION['time'])
        {
            $this->destroy();
        }

        return $this;
    }

    /**
     * @param string $key
     * @param string $value
     */
    public function change(string $key, string $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * @param string $value
     * @return string|null
     */
    #[Pure] public function get(string $value = 'uuid'): ?string
    {
        return Session::get(
            $value
        );
    }

    /**
     * @return bool
     */
    public function destroy(): bool
    {
        return session_destroy();
    }
}