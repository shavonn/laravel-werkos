<?php

namespace Sb\LaravelWerkos;

use Exception;
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use WorkOS\Exception\UnexpectedValueException;

class LaravelWerkos
{
    public static function userManager(): UserManagement
    {
        return app(UserManagement::class);
    }

    public static function retrieveWorkOsSessionId(string $workOsAccessToken): ?string
    {
        $decodedSession = self::getDecodedWorkOsSession($workOsAccessToken);
        if (! $decodedSession) {
            return null;
        }

        return $decodedSession['sid'];
    }

    public static function getDecodedWorkOsSession(string $workOsAccessToken): ?array
    {
        try {
            $jwks = static::getJwks();

            return (array) JWT::decode($workOsAccessToken, JWK::parseKeySet($jwks));
        } catch (Exception) {
        }

        return null;
    }

    /**
     * @throws ConnectionException
     * @throws UnexpectedValueException
     */
    public static function getJwks()
    {
        return Http::get(
            self::userManager()->getJwksUrl()
        )->json();
    }
}
