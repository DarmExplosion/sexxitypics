<?php

namespace Application\Image;

use Application\Image\Exception\UploadSuccessException;
use Exception;
use iamdual\Uploader;
use Application\Image\Exception\UploadFailureException;
use RuntimeException;
use Symfony\Component\Finder\Finder;

require_once dirname(__DIR__) . "/Vendor/autoload.php";

/**
 *
 */
class ImageService extends ImageModel
{
    /**
     * @var Uploader
     */
    public Uploader $upload;

    /**
     * @var string
     */
    public string $fileName;

    /**
     * @var Finder
     */
    public Finder $files;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct();
        $this->fileName = substr(str_shuffle(str_repeat('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', random_int(1,6))), 1, 6);
    }

    /**
     * @param bool $validate
     * @param array $files
     * @param array $settings
     * @param array $post
     * @throws UploadFailureException
     * @throws UploadSuccessException
     */
    public function processUpload(bool $validate, array $files, array $settings, array $post = []): void
    {
        if(!$validate)
        {
            $this->upload = new Uploader($files);

            $this->upload->max_size($settings['size'])
                ->path('data/' . $settings['folder'])
                ->name($settings['name'])
                ->allowed_extensions($settings['enabled_extensions'])
                ->disallowed_extensions($settings['disabled_extensions'])
                ->max_dimensions($settings['max_size'][0], $settings['max_size'][1])
                ->min_dimensions($settings['min_size'][0], $settings['min_size'][1])
                ->must_be_image()
                ->override();

            if(!$this->upload->check())
            {
                throw new UploadFailureException('Cannot be uploaded.');
            }

            if($settings['deleteOld'])
            {
                if(file_exists($_SERVER['DOCUMENT_ROOT'] . '/data/' . $settings['folder'] . '/' . $settings['name'] . '.jpg') ||
                    file_exists($_SERVER['DOCUMENT_ROOT'] . '/data/' . $settings['folder'] . '/' . $settings['name'] . '.png') ||
                    file_exists($_SERVER['DOCUMENT_ROOT'] . '/data/' . $settings['folder'] . '/' . $settings['name'] . '.gif'))
                {
                    unlink($_SERVER['DOCUMENT_ROOT'] . '/data/' . $settings['folder'] . '/' . $settings['name'] . '.jpg');
                    unlink($_SERVER['DOCUMENT_ROOT'] . '/data/' . $settings['folder'] . '/' . $settings['name'] . '.png');
                    unlink($_SERVER['DOCUMENT_ROOT'] . '/data/' . $settings['folder'] . '/' . $settings['name'] . '.gif');
                }
            }

            if(!$this->upload->upload())
            {
                throw new UploadFailureException('Cannot be uploaded.');
            }

            throw new UploadSuccessException('Successfully uploaded.');
        }
        else
        {
            $this->upload = new Uploader($files['exity']);

            $this->upload->max_size(25)
                ->path('data/' . $this->getByToken($post['token'])->uuid)
                ->name($this->fileName)
                ->must_be_image();

            if(!$this->upload->upload())
            {
                throw new UploadFailureException('Cannot be uploaded.');
            }

            throw new UploadSuccessException('https://' . $this->getByToken($post['token'])->domain . '/image/' . $this->fileName);
        }
    }

    /**
     * @param string $image
     * @return array
     */
    public function findImage(string $image): array
    {
        $this->files = (new Finder())->files()->in($_SERVER['DOCUMENT_ROOT'] . '/data')->name($image . '.*')->depth(1);

        foreach($this->files->getIterator() as $file)
        {
            return [
                'url' => 'https://exity.pics/data/' . $file->getRelativePath() . '/' . $file->getFilename(),
                'path' => $file->getRelativePath(),
                'time' => $file->getATime(),
                'name' => $image,
                'rawname' => $file->getRelativePath() . '/' . $file->getFilename()
            ];
        }

        throw new RuntimeException();
    }

    public function getUsersBioImages(int $userid, string $directory): string
    {
        if(file_exists($_SERVER['DOCUMENT_ROOT'] . '/data/' . $directory . '/' . $userid . '.jpg'))
        {
            return '/data/' . $directory . '/' . $userid . '.jpg';
        }
        elseif(file_exists($_SERVER['DOCUMENT_ROOT'] . '/data/' . $directory . '/' . $userid . '.png'))
        {
            return '/data/' . $directory . '/' . $userid . '.png';
        }
        elseif(file_exists($_SERVER['DOCUMENT_ROOT'] . '/data/' . $directory . '/' . $userid . '.gif'))
        {
            return '/data/' . $directory . '/' . $userid . '.gif';
        }
        else
        {
            return '/data/' . $directory . '/' . 'default.png';
        }
    }

    /**
     * @param string $uuid
     * @return array
     */
    public function countImages(string $uuid): array
    {
        try
        {
            return [
                'total' => (new Finder())->files()->in($_SERVER['DOCUMENT_ROOT'] . '/data/' . $uuid)->count(),
                'twodays' => (new Finder())->files()->in($_SERVER['DOCUMENT_ROOT'] . '/data/' . $uuid)->date('> 2 days ago')->count()
            ];
        }
        catch(Exception)
        {
            return [
                'total' => 0,
                'twodays' => 0
            ];
        }
    }
}