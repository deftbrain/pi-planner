<?php

namespace App\Security;

use Jose\Component\Checker\InvalidClaimException;
use Jose\Component\Core\JWKSet;
use Jose\Easy\JWT;
use Jose\Easy\Load;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

abstract class AbstractJWTAuthenticator extends AbstractGuardAuthenticator
{
    /**
     * @var AuthenticationCookieProvider
     */
    protected $authCookieProvider;

    /**
     * @var string
     */
    protected $commonHost;

    /**
     * @var ParameterBagInterface
     */
    private $params;

    /**
     * @var JWKSet
     */
    private $jwkSet;

    public function __construct(
        AuthenticationCookieProvider $authCookieProvider,
        ParameterBagInterface $params,
        JWKSet $jwkSet
    ) {
        $this->authCookieProvider = $authCookieProvider;
        $this->params = $params;
        $this->jwkSet = $jwkSet;
    }

    abstract protected function getAuthenticationScheme(): string;

    abstract protected function getJwt(Request $request): ?string;

    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new JsonResponse(
            ['message' => $authException ? $authException->getMessage() : 'Authorization using a JWT is required'],
            Response::HTTP_UNAUTHORIZED
        );
    }

    public function supports(Request $request)
    {
        return (bool) $this->getJwt($request);
    }

    /**
     * @return array [
     *     'jwt' => string,
     *     'decodedJwt' => JWT
     * ]
     */
    public function getCredentials(Request $request)
    {
        try {
            $jwt = $this->getJwt($request);
            $decodedJwt = Load::jws($jwt)
                ->alg('RS256')
                ->nbf()
                ->exp()
                ->aud($this->params->get('microsoft.oauth.client_id'))
                ->claim('tid', $this->params->get('microsoft.oauth.tenant_id'))
                ->keyset($this->jwkSet)
                ->run();
            return ['jwt' => $jwt, 'decodedJwt' => $decodedJwt];
        } catch (InvalidClaimException $exception) {
            throw new UnauthorizedHttpException($this->getAuthenticationScheme(), $exception->getMessage());
        }
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        /** @var JWT $jwt */
        $jwt = $credentials['decodedJwt'];
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
}
