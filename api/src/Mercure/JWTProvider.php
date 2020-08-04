<?php

namespace App\Mercure;

use Jose\Component\KeyManagement\JWKFactory;
use Jose\Component\Signature\Algorithm\HS256;
use Jose\Easy\Build;
use Symfony\Component\Mercure\Update;

class JWTProvider
{
    public const JWT_TTL = 3600;
    private const PURPOSE_PUBLISH = 'publish';
    private const PURPOSE_SUBSCRIBE = 'subscribe';

    /**
     * @var string
     */
    private $secretKey;

    public function __construct(string $secretKey)
    {
        $this->secretKey = $secretKey;
    }

    public function __invoke(Update $update = null)
    {
        $operation = $update ? self::PURPOSE_PUBLISH : self::PURPOSE_SUBSCRIBE;
        return Build::jws()
            ->payload(['mercure' => [$operation => ['*']]])
            ->nbf()
            ->exp((new \DateTime(sprintf('+ %d seconds', self::JWT_TTL)))->getTimestamp())
            ->alg(new HS256)
            ->sign(JWKFactory::createFromSecret($this->secretKey));
    }
}
