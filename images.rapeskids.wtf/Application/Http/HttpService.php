<?php

namespace Application\Http;

use Apfelbox\FileDownload\FileDownload;
use Application\Http\Exception\HttpSuccessException;
use Application\Http\Exception\HttpValidationException;
use voku\helper\AntiXSS;
use Aura\Filter;
use Ramsey\Uuid\Uuid;

require_once dirname(__DIR__) . "/Vendor/autoload.php";

/**
 *
 */
class HttpService extends HttpModel
{
    /**
     * @var FileDownload
     */
    private FileDownload $file;

    /**
     * @var AntiXSS
     */
    private AntiXSS $filter;

    /**
     * @var bool
     */
    protected bool $isClicked = false;

    /**
     * @var bool|null
     */
    protected bool|null $isToggled = null;

    /**
     *
     */
    public function __construct()
    {
        $this->filter = new AntiXSS;
        parent::__construct();

    }

    /**
     * @param callable $callback
     * @return callable|bool|null
     */
    public function callback(callable $callback): callable|bool|null
    {
        if($this->isClicked === true || $this->isToggled === true || $this->isToggled === false)
        {
            return $callback();
        }

        return null;
    }

    /**
     * @param string $postValue
     * @return callable|bool|null
     * @throws HttpSuccessException
     */
    public function regenerateToken(string $postValue): callable|bool|null
    {
        return $this->click($postValue)->callback(function () {
            $this->updateByUUID(
                $this->sessionService->get(),
                ['token' => Uuid::uuid4()->toString()]
            );

            throw new HttpSuccessException('Successfully changed.');
        });

    }

    /**
     * @param string $postName
     * @return $this
     */
    public function toggle(string $postName): self
    {
        if($_POST[$postName] === "false")
        {
            $this->isToggled = false;
        }
        elseif($_POST[$postName] === "true")
        {
            $this->isToggled = true;
        }

        return $this;
    }

    /**
     * @param string $postValue
     * @return callable|bool|null
     */
    public function downloadConfig(string $postValue): callable|bool|null
    {
        $this->file = FileDownload::createFromString(
            '{"Version": "13.5.0","Name": "exity.pics - ","DestinationType": "ImageUploader","RequestMethod": "POST","RequestURL": "https://exity.pics/upload","Body": "MultipartFormData","Arguments": {"token": "' . $this->getByUUID($this->sessionService->get())->token . '"},"FileFormName": "exity"}'
        );

        return $this->click($postValue)->callback(function () {
            $this->file->sendDownload(Uuid::uuid4() . '.sxcu');
        });
    }

    /**
     * @param string $postHidden
     * @throws HttpSuccessException
     */
    public function toggleEmbed(string $postHidden): void
    {
        $this->toggle($postHidden);

        if ($this->isToggled === true) {
            $this->updateByUUID(
                $this->sessionService->get(),
                ['embed.enabled' => true]
            );

            throw new HttpSuccessException('Successfully updated.');
        } elseif ($this->isToggled === false) {
            $this->updateByUUID(
                $this->sessionService->get(),
                ['embed.enabled' => false]
            );

            throw new HttpSuccessException('Successfully updated.');
        }
    }

    /**
     * @param string $post
     * @param string $column
     * @param array $settings
     * @throws HttpSuccessException
     * @throws HttpValidationException
     */
    public function updateData(string $post, string $column, array $settings): void
    {
        if(strlen(trim($_POST[$post])) > $settings['maxLength'] || strlen(trim($_POST[$post])) < $settings['minLength'])
        {
            throw new HttpValidationException('Field is too short or too long.');
        }
        elseif($settings['filter'])
        {
            $this->filter->xss_clean($_POST[$post]);

            if($this->filter->isXssFound())
            {
                throw new HttpValidationException('Field contains illegal characters.');
            }
        }

        $this->updateByUUID(
            $this->sessionService->get(),
            [$column => $_POST[$post]]
        );

        throw new HttpSuccessException('Changed successfully.');

    }

    /**
     * @param string $postName
     * @return $this
     */
    public function click(string $postName): self
    {
        if (isset($_POST[$postName]))
        {
            $this->isClicked = true;
        }

        return $this;
    }
}