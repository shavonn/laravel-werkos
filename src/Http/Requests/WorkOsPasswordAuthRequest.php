<?php

namespace Sb\LaravelWerkos\Http\Requests;

use Exception;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Sb\LaravelWerkos\LaravelWerkos;
use WorkOS\Exception\WorkOSException;
use WorkOS\Resource\AuthenticationResponse;
use WorkOS\Resource\User as WorkOsUser;

class WorkOsPasswordAuthRequest extends WorkOsAuthFormRequest
{
    /**
     * @throws WorkOSException
     */
    protected function authenticate(): AuthenticationResponse
    {
        $this->ensureIsNotRateLimited();

        try {
            $response = LaravelWerkOs::userManager()->userManager()->authenticateWithPassword(
                email: $this->string('email'),
                password: $this->string('password'),
                ipAddress: $this->ip(),
                userAgent: $this->userAgent(),
            );

            RateLimiter::clear($this->throttleKey());

            return $response;

        } catch (Exception) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }
    }

    protected function handleMissingLocalUser(WorkOsUser $workOsUser): null
    {
        return null;
    }

    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
