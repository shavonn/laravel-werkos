<?php

namespace Sb\LaravelWerkos;

use WorkOS\Exception\ConfigurationException;
use WorkOS\Exception\UnexpectedValueException;
use WorkOS\Exception\WorkOSException;
use WorkOS\Resource\AuthenticationResponse;
use WorkOS\UserManagement as WorkOSUserManagement;

class UserManagement
{
    /**
     * @throws ConfigurationException
     * @throws UnexpectedValueException
     */
    public function getAuthorizationUrl($state = null, $provider = null, $connectionId = null, $organizationId = null, $domainHint = null, $loginHint = null, $screenHint = null): string
    {
        return app(WorkOSUserManagement::class)->getAuthorizationUrl(
            redirectUri: config('services.workos.redirect_url'),
            state: $state,
            provider: $provider,
            connectionId: $connectionId,
            organizationId: $organizationId,
            domainHint: $domainHint,
            loginHint: $loginHint,
            screenHint: $screenHint
        );
    }

    /**
     * @throws WorkOSException
     */
    public function authenticateWithCode($code, $ipAddress = null, $userAgent = null): AuthenticationResponse
    {
        return app(WorkOSUserManagement::class)->authenticateWithCode(
            clientId: config('services.workos.client_id'),
            code: $code,
            ipAddress: $ipAddress,
            userAgent: $userAgent
        );
    }

    /**
     * @throws WorkOSException
     */
    public function authenticateWithPassword(string $email, string $password, ?string $ipAddress = null, ?string $userAgent = null): AuthenticationResponse
    {
        return app(WorkOSUserManagement::class)->authenticateWithPassword(
            clientId: config('services.workos.client_id'),
            email: $email,
            password: $password,
            ipAddress: $ipAddress,
            userAgent: $userAgent
        );
    }

    /**
     * @throws UnexpectedValueException
     */
    public function getLogoutUrl(string $sessionId): string
    {
        return app(WorkOSUserManagement::class)->getLogoutUrl(
            sessionId: $sessionId,
            return_to: config('services.workos.logout_redirect_url')
        );
    }

    /**
     * @throws UnexpectedValueException
     */
    public function getJwksUrl(): string
    {
        return app(WorkOSUserManagement::class)->getJwksUrl(config('services.workos.client_id'));
    }
}
