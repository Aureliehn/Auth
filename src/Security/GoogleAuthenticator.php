<?php

namespace App\Security;

use App\Entity\User; // your user entity
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\HttpFoundation\RedirectResponse;
use KnpU\OAuth2ClientBundle\Client\Provider\GoogleUser;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use League\OAuth2\Client\Token\AccessToken;

class GoogleAuthenticator extends SocialAuthenticator
{
    private $clientRegistry;
    private $em;
    private $router;

    public function __construct(ClientRegistry $clientRegistry, EntityManagerInterface $em, RouterInterface $router, )
    {
        $this->clientRegistry = $clientRegistry;
        $this->em = $em;
	    $this->router = $router;
        // $this->userRepository = $usersRepository;
    }

    public function supports(Request $request)
    {
        return $request->getPathInfo() === '/connect/google/check' && $request->isMethod('GET'); 
    }

    public function getCredentials(Request $request)
    {
        return $this->fetchAccessToken($this->getGoogleClient());
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        // dd($credentials)
        /** @var GoogleUser $googleUser*/
        $googleUser = $this->getGoogleClient()->fetchUserFromToken($credentials);
            // dd($googleUser);
        $email = $googleUser->getEmail();
        $user = $this->em->getRepository('App:User')
            ->findOneBy((['email' => $email]));
        if (!$user){
            $user = new User();
            $user->setEmail($googleUser->getEmail());
            //
            $user->setFullname($googleUser->getName());
            $user->setImage($googleUser->getAvatar());
            //
            $this->em->persist($user);
            $this->em->flush();
        }
        return $user;

    }

    /**
     * @return \KnpU\OAuth2ClientBundle\Client\OAuth2Client
     */
    private function getGoogleClient()
    {
        return $this->clientRegistry
            ->getClient('google');
	}

    /**
     * @param Request $request
     * @param \Symfony\Component\Security\Core\Exception\AuthenticationException $authException
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse('login');
    }

        /**
     * @param Request $request
     * @param \Symfony\Component\Security\Core\Exception\AuthenticationException $authException
     * 
     * @return \Symfony\Component\HttpFoundation\Response|null
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {

    }

    
        /**
     * @param Request $request
     * @param \Symfony\Component\Security\Core\Authentification\Token\TokenInterface $token
     * @param string $providerKey
     * @return void
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {

    }






}