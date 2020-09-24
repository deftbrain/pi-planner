<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticationCookieProvider
{
    private const COOKIE_NAME = 'AUTH_JWT';

    /**
     * @var string
     */
    private $commonHost;

    public function __construct(string $commonHost)
    {
        $this->commonHost = $commonHost;
    }

    public function getCookieValueFrom(Request $request): ?string
    {
        return $request->cookies->get(self::COOKIE_NAME);
    }

    public function setCookieValueTo(Response $response, string $value = null, int $expiresAt = 0): void
    {
        $response->headers->setCookie(
            new Cookie(
                self::COOKIE_NAME,
                $value,
                $expiresAt,
                null,
                $this->commonHost,
                true,
                true,
                false,
                Cookie::SAMESITE_STRICT
            )
        );
    }
}
