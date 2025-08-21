<?php

namespace Sb\LaravelWerkos\Http\Requests;

use Illuminate\Foundation\Auth\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use WorkOS\Resource\AuthenticationResponse;
use WorkOS\Resource\User as WorkOsUser;

abstract class WorkOsAuthFormRequest extends FormRequest
{
    public function attempt(): array
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

        return ['user' => $user,  'access_token' => $workOsAccessToken, 'refresh_token' => $workOsRefreshToken];
    }

    abstract protected function authenticate(): AuthenticationResponse;

    abstract protected function handleMissingLocalUser(WorkOsUser $workOsUser);
}
