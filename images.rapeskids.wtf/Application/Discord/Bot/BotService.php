<?php

namespace Application\Discord\Bot;

use Discord\Discord;
use Discord\Exceptions\IntentException;
use Discord\Parts\Channel\Message;
use Discord\Parts\User\Activity;
use Application\Database\Repository\UserRepository;

require_once dirname(__DIR__) . "/../Vendor/autoload.php";

/**
 *
 */
class BotService extends UserRepository
{
    /**
     * @var Discord
     */
    private Discord $discord;

    /**
     * @var array
     */
    private array $data;

    /**
     * @throws IntentException
     */
    public function __construct()
    {
        parent::__construct();
        $this->discord = new Discord(
            ['token' => 'ODc3MjA5NTkzNzQxMjEzNzE3.YRvTGA.mkONSZdHlWhlMETdsGaAJxVXBZM']
        );

        $this->discord->on('ready', function(Discord $discord)
        {
            $this->data['activity'] = $discord->factory(Activity::class);

            $this->data['activity']->type = Activity::TYPE_WATCHING;
            $this->data['activity']->name = "porn";
            $discord->updatePresence($this->data['activity'], false, "online", false);

            $this->discord->on('message', function(Message $message)
            {
                if($message->content === '-amihalal')
                {
                    $this->data['halalMeter'] = ['no, you are haram !!!', 'yes, you very halal :)', 'yes, you are halal ||but also a bit haram||'];
                    $message->channel->sendMessage($this->data['halalMeter'][random_int(0,2)]);
                }
            });

            $this->discord->on('message', function(Message $message)
            {
                if($message->content === '-info')
                {
                    /*$message->channel->sendMessage('', false,
                        [
                            'color' => random_int(1, 16777214),
                            'author' => [
                                'name' => 'exity bot',
                                'icon_url' => 'https://logos-world.net/wp-content/uploads/2020/12/Discord-Logo.png'
                            ],
                            'fields' => [
                                [
                                    'name' => '🍣 *Public information*',
                                    'value' => "**Username** -> {$this->}"
                                ],
                                [
                                    'name' => '📷 *Image host*',
                                    'value' => "**-info <username|uid>** -> Shows info about the user"
                                ]
                            ]
                        ]
                    );*/
                }
            });

            $this->discord->on('message', function(Message $message)
            {
                if($message->content === '-help')
                {
                    $message->channel->sendMessage('', false,
                        [
                            'color' => random_int(1, 16777214),
                            'author' => [
                                'name' => 'exity bot',
                                'icon_url' => 'https://logos-world.net/wp-content/uploads/2020/12/Discord-Logo.png'
                            ],
                            'fields' => [
                                [
                                    'name' => '🍕 *Basic*',
                                    'value' => "**-help** -> This embed\n**-avatar \<id\>** -> Shows avatar"
                                ],
                                [
                                    'name' => '📷 *Image host*',
                                    'value' => "**-info <username|uid>** -> Shows info about the user"
                                ],
                                [
                                    'name' => '🎉 *Fun*',
                                    'value' => "**-amihalal** -> Says truth"
                                ]
                            ]
                        ]);
                }
            });
        });

        $this->discord->run();
    }
}
new BotService();