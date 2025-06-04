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
    public function getAuthorizationUrl(): string
    {
        $workOsAuthorizationUrl = LaravelWerkOs::userManager()->getAuthorizationUrl(
            state: ['state' => $state = Str::random(20)],
            provider: $this->string('provider'),
        );

        $this->session()->put('state', $state);

        return $workOsAuthorizationUrl;
    }
}
