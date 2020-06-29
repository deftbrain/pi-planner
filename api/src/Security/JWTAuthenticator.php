<?php

namespace App\Security;

use Jose\Component\Core\JWKSet;
use Jose\Easy\Load;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class JWTAuthenticator extends AbstractGuardAuthenticator
{
    private const TOKEN_PREFIX = 'Bearer ';
    private const HEADER_AUTHORIZATION = 'Authorization';

    /**
     * @var ParameterBagInterface
     */
    private $params;

    /**
     * @var JWKSet
     */
    private $jwkSet;

    public function __construct(ParameterBagInterface $params, JWKSet $jwkSet)
    {
        $this->params = $params;
        $this->jwkSet = $jwkSet;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new JsonResponse(
            ['message' => $authException ? $authException->getMessage() : 'Authorization via JWT is required'],
            Response::HTTP_UNAUTHORIZED
        );
    }

    public function supports(Request $request)
    {
        return (bool) $this->getJWT($request);
    }

    public function getCredentials(Request $request)
    {
        return $this->getJWT($request);
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        if (!$credentials) {
            return null;
        }

        $jwt = Load::jws($credentials)
            ->alg('RS256')
            ->nbf()
            ->exp()
            ->aud($this->params->get('microsoft.oauth.client_id'))
            ->claim('tid', $this->params->get('microsoft.oauth.tenant_id'))
            ->keyset($this->jwkSet)
            ->run();

        return (new User)->setEmail($jwt->claims->get('email'));
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new JsonResponse(['message' => $exception->getMessage()], Response::HTTP_UNAUTHORIZED);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey)
    {
        return null;
    }

    public function supportsRememberMe()
    {
        return false;
    }

    private function getJWT(Request $request): ?string
    {
        return substr($request->headers->get(self::HEADER_AUTHORIZATION), strlen(self::TOKEN_PREFIX)) ?: null;
    }
}
