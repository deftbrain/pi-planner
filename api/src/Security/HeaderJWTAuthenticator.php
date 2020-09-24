<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class HeaderJWTAuthenticator extends AbstractJWTAuthenticator
{
    protected function getAuthenticationScheme(): string
    {
        return 'Bearer';
    }

    protected function getJwt(Request $request): ?string
    {
        $authSchemeAndJwt = $request->headers->get('Authorization');
        if (!$authSchemeAndJwt) {
            return null;
        }

        $authSchemeAndJwt = explode(' ', $authSchemeAndJwt);
        if (
            count($authSchemeAndJwt) === 2
            || $authSchemeAndJwt[1]
            || $this->getAuthenticationScheme() === $authSchemeAndJwt[0]
        ) {
            return $authSchemeAndJwt[1];
        }

        return null;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey)
    {
        $credentials = $this->getCredentials($request);
        $expiresAt = $credentials['decodedJwt']->claims->get('exp');
        $response = new JsonResponse([
            'username' => $token->getUser()->getUsername(),
            'expiresAt' => $expiresAt,
        ]);
        $this->authCookieProvider->setCookieValueTo($response, $credentials['jwt'], $expiresAt);

        return $response;
    }
}
