<?php

namespace Application\Http;

use Application\Authentication\Exception\AuthenticationErrorException;
use Application\Authentication\Exception\AuthenticationSuccessException;
use Application\Domain\Exception\DomainSuccessException;
use Application\Domain\Exception\DomainValidationException;
use Application\Json\JsonController;
use Application\Http\Exception\HttpSuccessException;
use Application\Http\Exception\HttpValidationException;
use Application\Image\Exception\UploadFailureException;
use Application\Image\Exception\UploadSuccessException;
use Application\Image\Exception\ValidationException;
use Application\Twig\TwigController;
use Application\Authentication\AuthenticationController;
use Application\Domain\DomainController;
use Application\Image\ImageController;
use Exception;
use RuntimeException;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

require_once dirname(__DIR__) . "/Vendor/autoload.php";

/**
 *
 */
class HttpController extends HttpService
{
    /**
     * @var TwigController
     */
    private TwigController $twig;

    /**
     * @var AuthenticationController
     */
    private AuthenticationController $authenticationController;

    private ImageController $imageController;

    private string $url = 'https://exity.pics';

    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->twig = new TwigController($_SERVER['DOCUMENT_ROOT'] . '/Application/Twig/Templates');
        $this->authenticationController = new AuthenticationController;
        $this->imageController = new ImageController;
    }

    /**
     * @param string $directory
     */
    public function authorize(string $directory): void
    {
        if ($directory === '/dashboard')
        {
            if (empty($_SESSION['uuid']))
            {
                header('Location: https://exity.pics/');
            }
        }
        elseif ($directory === '/')
        {
            if (isset($_SESSION['uuid']))
            {
                header('Location: https://exity.pics/dashboard');
            }
        }
    }

    /**
     * @return TwigController
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function handleMainpage(): TwigController
    {
        $this->sessionService->set();

        $this->authorize('/');

        try
        {
            $this->click('registerHidden')->callback(function() {
                $this->authenticationController->callRegister($_POST);
                throw new AuthenticationSuccessException('Successfully registered.');
            });

            $this->click('g-recaptcha-response')->callback(function() {
                $this->authenticationController->callLogin($_POST);
                throw new AuthenticationSuccessException('Successfully logged in.');
            });
        }
        catch(AuthenticationErrorException $response)
        {
            return $this->twig->render('mainpage', [
                'type' => 'error',
                'response' => $response->getMessage()
            ]);
        }
        catch(AuthenticationSuccessException $response)
        {
            return $this->twig->render('mainpage', [
                'type' => 'success',
                'response' => $response->getMessage()
            ]);
        }

        return $this->twig->render('mainpage', [
            'type' => 'none',
            'url' => $this->url
        ]);
    }

    /**
     * @param string $username
     * @return TwigController
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function handleBioPage(string $username): TwigController
    {
        try
        {
            $this->countByUsername($username) === 1 ?: throw new RuntimeException();

            return $this->twig->render('bio', [
                'user' => $this->getByUserName($username),
                'avatar' => $this->imageController->callPortfolioImageReturn($this->getByUserName($username)->id),
                'background' => $this->imageController->callPortfolioImageReturn($this->getByUserName($username)->id, 'background')
            ]);
        }
        catch (RuntimeException)
        {
            header('HTTP/1.1 404');
            return $this->showError();
        }

    }

    public function handleDashboard(): TwigController
    {
        $this->sessionService->set();

        $this->authorize('/dashboard');

        try
        {
            $this->click('logout')->callback(function() {
                $this->sessionService->destroy();
                throw new HttpSuccessException('Successfully logged out.');
            });

            $this->click('changeDomain')->callback(function() {
                (new DomainController)->callDomainChange($_POST);
                throw new DomainSuccessException('Successfully updated.');
            });
            
            $this->regenerateToken('changeToken');
            
            $this->downloadConfig('downloadConfig');

            $this->toggleEmbed('embedHidden');

            $this->click('usernameChange')->callback(function()
            {
                $this->countByUsername(trim($_POST['usernameChangeField'])) === 0 ?: throw new HttpValidationException('Username is taken.');

                password_verify(trim($_POST['passwordCurrentChangeField']), $this->getByUUID($this->sessionService->get())->password) ?: throw new HttpValidationException('Current password is incorrect.');

                $this->updateData('usernameChangeField', 'name', [
                    'minLength' => 3,
                    'maxLength' => 16,
                    'filter' => true
                ]);
            });


            $this->click('titleChange')->callback(function()
            {
                $this->updateData('titleField', 'embed.title', [
                    'minLength' => 0,
                    'maxLength' => 128,
                    'filter' => true
                ]);
            });

            $this->click('colorChange')->callback(function()
            {
                $this->updateData('colorField', 'embed.color', [
                    'minLength' => 7,
                    'maxLength' => 7,
                    'filter' => true
                ]);
            });

            $this->click('descriptionChange')->callback(function()
            {
                $this->updateData('descriptionField', 'embed.description', [
                    'minLength' => 0,
                    'maxLength' => 128,
                    'filter' => true
                ]);
            });

            $this->click('authorChange')->callback(function()
            {
                $this->updateData('authorField', 'embed.author', [
                    'minLength' => 0,
                    'maxLength' => 64,
                    'filter' => true
                ]);
            });

            $this->click('portfolioChange')->callback(function()
            {
                $this->updateData('portfolioChangeField', 'portfolio.description', [
                    'minLength' => 0,
                    'maxLength' => 512,
                    'filter' => true
                ]);
            });

            $this->click('statusChange')->callback(function()
            {
                $this->updateData('statusField', 'portfolio.status', [
                    'minLength' => 0,
                    'maxLength' => 32,
                    'filter' => true
                ]);
            });

            $this->click('passwordChange')->callback(function()
            {

                trim($_POST['passwordChangeField']) === trim($_POST['passwordVerifyChangeField']) ?: throw new HttpValidationException('New password does not match.');

                password_verify(trim($_POST['passwordCurrentChangeField']), $this->getByUUID($this->sessionService->get())->password) ?: throw new HttpValidationException('Current password is incorrect.');

                $this->updateData(password_hash('passwordChangeField', PASSWORD_DEFAULT), 'password', [
                    'minLength' => 6,
                    'maxLength' => 64,
                    'filter' => true
                ]);

                $this->sessionService->destroy();
            });

            $this->click('avatarChange')->callback(function()
            {

                $this->imageController->callUpload(false, $_FILES["avatarForm"], [
                    'size' => 5,
                    'name' => $this->getByUUID($this->sessionService->get())->id,
                    'min_size' => [200, 200],
                    'max_size' => [2000, 2000],
                    'folder' => 'avatar',
                    'enabled_extensions' => ['png', 'jpg', 'gif'],
                    'disabled_extensions' => ['tif', 'tiff', 'eps', 'raw', 'jpeg'],
                    'deleteOld' => true
                ]);
            });

            $this->click('backgroundChange')->callback(function()
            {

                $this->imageController->callUpload(false, $_FILES["backgroundForm"], [
                    'size' => 15,
                    'name' => $this->getByUUID($this->sessionService->get())->id,
                    'min_size' => [500, 500],
                    'max_size' => [4000, 4000],
                    'folder' => 'background',
                    'enabled_extensions' => ['png', 'jpg', 'gif'],
                    'disabled_extensions' => ['tif', 'tiff', 'eps', 'raw', 'jpeg'],
                    'deleteOld' => true
                ]);
            });
        }
        catch (HttpSuccessException|DomainSuccessException|UploadSuccessException $response)
        {
            return $this->twig->render('dashboard', [
                'user' => $this->getByUUID($this->sessionService->get()),
                'images' => $this->imageController->countImages($this->sessionService->get()),
                'type' => 'success',
                'domains' => (new DomainController())->domainList,
                'response' => $response->getMessage(),
                'avatar' => $this->imageController->callPortfolioImageReturn($this->getByUUID($this->sessionService->get())->id),
                'background' => $this->imageController->callPortfolioImageReturn($this->getByUUID($this->sessionService->get())->id, 'background')
            ]);
        }
        catch (Exception $response)
        {
            return $this->twig->render('dashboard', [
                'user' => $this->getByUUID($this->sessionService->get()),
                'images' => $this->imageController->countImages($this->sessionService->get()),
                'type' => 'error',
                'domains' => (new DomainController())->domainList,
                'response' => $response->getMessage(),
                'avatar' => $this->imageController->callPortfolioImageReturn($this->getByUUID($this->sessionService->get())->id),
                'background' => $this->imageController->callPortfolioImageReturn($this->getByUUID($this->sessionService->get())->id, 'background')
            ]);
        }

        return $this->twig->render('dashboard', [
            'user' => $this->getByUUID($this->sessionService->get()),
            'images' => $this->imageController->countImages($this->sessionService->get()),
            'domains' => (new DomainController())->domainList,
            'type' => 'none',
            'avatar' => $this->imageController->callPortfolioImageReturn($this->getByUUID($this->sessionService->get())->id),
            'background' => $this->imageController->callPortfolioImageReturn($this->getByUUID($this->sessionService->get())->id, 'background')
        ]);
    }

    public function upload(array $files, array $post): string
    {
        try
        {
            $this->imageController->callUpload(true, $files, [], $post);
        }
        catch(UploadFailureException|ValidationException $response)
        {
            header("HTTP/1.1 400 " . $response->getMessage());
        }
        catch(UploadSuccessException $response)
        {
            return $response->getMessage();
        }

        return 'Unknown error.';

    }

    public function showImage(string $image): string|TwigController
    {
        try
        {
            $imageArray = $this->imageController->callImageReturn($image);

            return $this->twig->render('image', [
                'avatar' => $this->imageController->callPortfolioImageReturn($this->getByUUID($imageArray['path'])->id),
                'parameter' => $image,
                'image' => $imageArray['url'],
                'rawname' => $imageArray['rawname'],
                'user' => $this->getByUUID($imageArray['path']),
                'time' => date('F j, Y, g:i', $imageArray['time']) . ' GMT'
            ]);
        }
        catch (RuntimeException)
        {
            header('HTTP/1.1 404');
            return $this->showError();
        }
    }

    public function returnJson(int $userid): string
    {
        header('Content-Type: application/json');
        return (new JsonController)->callJsonGet($userid);
    }

    /**
     * @param int $code
     * @return string|TwigController
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function showError(int $code = 404): string|TwigController
    {
        return $this->twig->render('error', ['code' => $code]);
    }
}