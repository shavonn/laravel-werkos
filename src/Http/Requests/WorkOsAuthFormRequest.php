<?php

namespace Sb\LaravelWerkos\Http\Requests;

use Illuminate\Foundation\Auth\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Sb\LaravelWerkOs\DTO\WorkOsUser;

abstract class WorkOsAuthFormRequest extends FormRequest
{
    public function attempt()
    {
        $authResponse = $this->authenticate();

        [$workOsUser, $workOsAccessToken, $workOsRefreshToken] = [
            $authResponse->user,
            $authResponse->accessToken,
            $authResponse->refreshToken,
        ];

        $user = app(User::class)::where('workos_id', $workOsUser->id)->first();

        if (! $user) {
            $user = $this->handleMissingLocalUser($workOsUser);
        }

        Auth::guard('web')->login($user);

        $this->session()->put('workos_access_token', $workOsAccessToken);
        $this->session()->put('workos_refresh_token', $workOsRefreshToken);

        $this->session()->regenerate();

        return $user;
    }

    abstract public function authenticate();

    abstract protected function handleMissingLocalUser(WorkOsUser $workOsUser);
}
