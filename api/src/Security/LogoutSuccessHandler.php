<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;

class LogoutSuccessHandler implements LogoutSuccessHandlerInterface
{
    /**
     * @var AuthenticationCookieProvider
     */
    private $authCookieProvider;

    public function __construct(AuthenticationCookieProvider $authCookieProvider)
    {
        $this->authCookieProvider = $authCookieProvider;
    }

    public function onLogoutSuccess(Request $request)
    {
        $response = new JsonResponse;
        $this->authCookieProvider->setCookieValueTo($response);
        return $response;
    }
}
