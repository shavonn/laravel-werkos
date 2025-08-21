<?php

namespace Sb\LaravelWerkos\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Sb\LaravelWerkos\LaravelWerkos;
use WorkOS\Exception\ConfigurationException;
use WorkOS\Exception\UnexpectedValueException;

class WorkOsProviderAuthUrlRequest extends FormRequest
{
    /**
     * @throws ConfigurationException
     * @throws UnexpectedValueException
     */
    public function getAuthorizationUrl(string $provider): string
    {
        $state = Str::random(20);
        $workOsAuthorizationUrl = LaravelWerkOs::userManager()->getAuthorizationUrl(
            state: ['state' => $state],
            provider: $provider,
        );

        $this->session()->put('state', $state);

        return $workOsAuthorizationUrl;
    }
}
