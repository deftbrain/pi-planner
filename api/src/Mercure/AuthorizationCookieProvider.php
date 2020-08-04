<?php

namespace App\Mercure;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\WebLink\GenericLinkProvider;

class AuthorizationCookieProvider implements EventSubscriberInterface
{
    private const COOKIE_NAME = 'mercureAuthorization';

    /**
     * @var JWTProvider
     */
    private $jwtProvider;
    /**
     * @var string
     */
    private $commonHost;

    public function __construct(JWTProvider $jwtProvider, string $commonHost)
    {
        $this->jwtProvider = $jwtProvider;
        $this->commonHost = $commonHost;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => ['setAuthCookie', -1],
        ];
    }

    public function setAuthCookie(ResponseEvent $event): void
    {
        /** @var GenericLinkProvider $linkProvider */
        $request = $event->getRequest();
        $linkProvider = $request->attributes->get('_links');
        if (!$linkProvider || !$linkProvider->getLinksByRel('mercure')) {
            return;
        }

        $mercureLinks = $linkProvider->getLinksByRel('mercure');
        $mercureHubUrl = reset($mercureLinks)->getHref();
        $event->getResponse()->headers->setCookie(
            new Cookie(
                self::COOKIE_NAME,
                ($this->jwtProvider)(),
                new \DateTime(sprintf('+ %d seconds', $this->jwtProvider::JWT_TTL)),
                parse_url($mercureHubUrl, PHP_URL_PATH),
                $this->commonHost,
                true,
                true,
                false,
                Cookie::SAMESITE_STRICT
            )
        );
    }
}
