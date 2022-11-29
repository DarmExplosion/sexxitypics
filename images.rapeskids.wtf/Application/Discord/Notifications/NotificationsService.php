<?php


namespace Application\Discord\Notifications;

use Application\Config\Configuration;
use Application\Database\DatabaseDataProvider;
use Woeler\DiscordPhp\Message\DiscordEmbedMessage;
use Woeler\DiscordPhp\Webhook\DiscordWebhook;

require_once dirname(__DIR__) . "/../Vendor/autoload.php";

/**
 * Class NotificationsService
 * @package Application\Discord\Notifications
 */
class NotificationsService extends DatabaseDataProvider
{

    public function sendWebhook(string $uuid): void
    {
        $webhook = new DiscordWebhook($this->get(['uuid' => $uuid])->webhook->url);
        $embed = new DiscordEmbedMessage;

        $embed
            ->setAuthorIcon('https://logos-world.net/wp-content/uploads/2020/12/Discord-Logo.png')
            ->setAuthorName($this->get(['uuid' => $uuid])->name . ' just uploaded a new file')
            ->setColorWithHexValue('#4DE827')
            ->setDescription($this->get(['uuid' => $uuid])->webhook->description)
            ->setFooterText('TIP: You can customize or disable this feature in the dashboard.');

        $webhook->send($embed);
    }
}