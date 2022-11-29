<?php

namespace Application\Image;

use Ramsey\Uuid\Uuid;
use Application\Image\Exception\ValidationException;
use Application\Database\Repository\UserRepository;

require_once dirname(__DIR__) . "/Vendor/autoload.php";

/**
 *
 */
class ImageModel extends UserRepository
{
    /**
     * @param array $files
     * @param array $post
     * @return bool
     * @throws ValidationException
     */
    public function validateFields(array $files = [], array $post = []): bool
    {
        if(empty($post))
        {
            throw new ValidationException('Arrays are empty.');
        }
        elseif(!array_key_exists('exity', $files))
        {
            throw new ValidationException('Invalid form name.');
        }
        elseif(!array_key_exists('token', $post))
        {
            throw new ValidationException('Invalid post name.');
        }

        return true;
    }

    /**
     * @param array $post
     * @return bool
     * @throws ValidationException
     */
    public function validateToken(array $post = []): bool
    {
        if(empty($post['token']))
        {
            throw new ValidationException('Token is empty.');
        }
        elseif(!Uuid::isValid($post['token']))
        {
            throw new ValidationException('Token is not in the correct format.');
        }
        elseif($this->countByToken($post['token']) === 0)
        {
            throw new ValidationException('Token does not exist.');
        }

        return true;
    }
}