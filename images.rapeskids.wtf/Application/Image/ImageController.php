<?php

namespace Application\Image;

use RuntimeException;

require_once dirname(__DIR__) . "/Vendor/autoload.php";

/**
 *
 */
class ImageController extends ImageService
{
    public function callUpload(bool $validate, array $files, array $settings = [], array $post = []): void
    {
        if ($validate)
        {
            if($this->validateFields($files, $post) && $this->validateToken($post))
            {
                $this->processUpload(true, $files, [], $post);
            }
        }

        $this->processUpload(false, $files, $settings, $post);
    }

    /**
     * @param string $image
     * @return array|RuntimeException
     */
    public function callImageReturn(string $image): array|RuntimeException
    {
        return $this->findImage($image);
    }

    /**
     * @param int $userid
     * @param string $directory
     * @return string
     */
    public function callPortfolioImageReturn(int $userid, string $directory = 'avatar'): string
    {
        return $this->getUsersBioImages($userid, $directory);
    }
}