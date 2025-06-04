<?php

namespace Sb\LaravelWerkos\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Sb\LaravelWerkos\LaravelWerkOs;
use WorkOS\Exception\UnexpectedValueException;

class WorkOsLogoutRequest extends FormRequest
{
    /**
     * @throws UnexpectedValueException
     */
    public function logout(): RedirectResponse
    {
        $workOsAccessToken = $this->session()->get('workos_access_token');

        $workOsSessionId = $workOsAccessToken ? LaravelWerkOs::retrieveWorkOsSessionId($workOsAccessToken) : false;

        Auth::guard('web')->logout();

        $this->session()->invalidate();
        $this->session()->regenerateToken();

        if (! $workOsSessionId) {
            return redirect('/');
        }

        $workOsLogoutUrl = LaravelWerkOs::userManager()->getLogoutUrl(
            $workOsSessionId,
        );

        return redirect($workOsLogoutUrl);
    }
}
