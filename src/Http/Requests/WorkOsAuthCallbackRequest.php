<?php

namespace Sb\LaravelWerkos\Http\Requests;

use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Log;
use JsonException;
use Sb\LaravelWerkos\LaravelWerkos;
use WorkOS\Exception\WorkOSException;
use WorkOS\Resource\AuthenticationResponse;
use WorkOS\Resource\User as WorkOsUser;

class WorkOsAuthCallbackRequest extends WorkOsAuthFormRequest
{
    /**
     * @throws WorkOSException
     * @throws JsonException
     */
    protected function authenticate(): AuthenticationResponse
    {
        $this->ensureStateIsValid();

        return LaravelWerkOs::userManager()->authenticateWithCode(
            $this->query('code'),
            $this->ip(),
            $this->userAgent()
        );
    }

    protected function handleMissingLocalUser(WorkOsUser $workOsUser): ?Authenticatable
    {
        if (config('werkos.provider_auth.create_missing_user')) {
            $userClass = config('werkos.user_class');
            $newUser = app($userClass)->create([
                'name' => $workOsUser->firstName.' '.$workOsUser->lastName,
                'email' => $workOsUser->email,
                'email_verified_at' => now(),
                'workos_id' => $workOsUser->id,
                'avatar' => $workOsUser->avatar ?? '',
            ]);

            event(new Registered($newUser));

            return $newUser;
        }

        return null;
    }

    /**
     * @throws JsonException
     */
    protected function ensureStateIsValid(): void
    {
        $decodedJSON = json_decode($this->string('state'), true, 512, JSON_THROW_ON_ERROR);
        $state = $decodedJSON['state'] ?? false;

        if ($state !== $this->session()->get('state')) {
            Log::info('WorkOsAuthCallbackRequest: state is invalid', ['state' => $state, 'session_state' => $this->session()->get('state')]);
            abort(403);
        }

        $this->session()->forget('state');
    }
}
