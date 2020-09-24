<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;

class CookieJWTAuthenticator extends AbstractJWTAuthenticator
{
    protected function getAuthenticationScheme(): string
    {
        // https://tools.ietf.org/id/draft-broyer-http-cookie-auth-00.html
        return 'Cookie';
    }

    protected function getJwt(Request $request): ?string
    {
        return $this->authCookieProvider->getCookieValueFrom($request);
    }
}
