<?php

namespace Application\Twig;

use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader;
use Twig\Environment;

require_once dirname(__DIR__) . "/Vendor/autoload.php";

/**
 *
 */
class TwigController
{
    /**
     * @var Loader\FilesystemLoader
     */
    private Loader\FilesystemLoader $storage;

    /**
     * @var Environment
     */
    public Environment $twig;

    /**
     * @param string $path
     */
    public function __construct(string $path)
    {
        $this->storage = new Loader\FilesystemLoader($path);
        $this->twig = new Environment($this->storage, ['debug' => true]);
    }


    /**
     * @param string $file
     * @param array $variables
     * @return self
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function render(string $file, array $variables = []): self
    {
        if(empty($variables))
        {
            echo $this->twig->render($file . '.html.twig', []);
            return $this;
        }

        echo $this->twig->render($file . '.html.twig', $variables);
        return $this;
    }

    /**
     * @param string $file
     * @param array $variables
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function load(string $file, array $variables = []): string
    {
        if(empty($variables))
        {
            return $this->twig->render($file . '.html.twig', []);
        }

        return $this->twig->render($file . '.html.twig', $variables);
    }
}