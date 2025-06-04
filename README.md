# A WorkOS integration package for Laravel, developed for my convenience and maybe yours, too.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/shavonn/laravel-werkos.svg?style=flat-square)](https://packagist.org/packages/shavonn/laravel-werkos)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/shavonn/laravel-werkos/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/shavonn/laravel-werkos/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/shavonn/laravel-werkos/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/shavonn/laravel-werkos/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/shavonn/laravel-werkos.svg?style=flat-square)](https://packagist.org/packages/shavonn/laravel-werkos)

Why the name? Fun? 🎊 Yes, but primarily to avoid namespace collision which happens when attempting to use Laravel's
WorkOS package and WorkOS's PHP SDK.

At current this package only covers what I am using so far which **will** likely expand quickly, but happy to accept
PRs. :)
No SSO yet because the current project isn't multi-tenant but the next is.

## Installation

You can install the package via composer:

```bash
  composer require shavonn/laravel-werkos
```

WorkOS creds in `services.php`.

```php
    'workos' => [
        'client_id' => env('WORKOS_CLIENT_ID'),
        'secret' => env('WORKOS_API_KEY'),
    ],
```

If you are just adding WorkOS, you can publish and run this migration:

```bash
    php artisan vendor:publish --tag="laravel-werkos-migrations"
    php artisan migrate
```

It will add the `workos_id` and `avatar` columns to the `users` table and set the `password` column to nullable. You can
also
drop the `password` column if you don't need to migrate users.

You can publish the config file with:

```bash
    php artisan vendor:publish --tag="laravel-werkos-config"
```

## Usage

### Wrapped for convenience

These methods are wrapped to include reference to config values. You'll still want to
use [WorkOS API Reference](https://workos.com/docs/reference) for direction on use.

#### Sb\LaravelWerkOs\UserManagement

```php
use Sb\LaravelWerkos\LaravelWerkos\UserManagement;
(new UserManagement)->getAuthorizationUrl(...args);
// or
use Sb\LaravelWerkos\LaravelWerkos;
LaravelWerkos::userManager()->getAuthorizationUrl();
```

Currently covered methods:

- getAuthorizationUrl
- authenticateWithCode
- authenticateWithPassword
- getLogoutUrl
- getJwksUrl

### Auth Form Requests

I created these Laravel Form Requests to handle auth types and manage the local Laravel auth session.

#### WorkOsAuthFormRequest

`WorkOsAuthFormRequest` is an abstract with an `attempt` method which calls two other methods on form requests that
extend
it-- `authenticate` and `handleMissingLocalUser`, so you can use it create and customize your own auth methods and
session management. When successful, `attempt` will return a user. Else, it will throw an error.

In a controller:

```php
public function authMe(WorkOsPasswordAuthRequest $request) {
    $request->attempt();
    // handle the success or failure however you like
}
```

Current list of existing requests and what they handle:

#### WorkOsPasswordAuthRequest

- Contains basic email and password validation.
- Wrapped in the Laravel login rate limiting logic (untested).
- Authenticates email and password with WorkOS `authenticateWithPassword` and passes in `ipAddress`, and `userAgent`
  args.

#### WorkOsProviderAuthUrlRequest

- Gets an authorization Url for a provider with `getAuthorizationUrl`, expects `provider` (provider ID from WorkOS docs)
  as a url param. `auth/{provider}/gimme-url` or
  however you like.
- Creates a state val, sends it in url request, and stores it in the Laravel session for later verification.

#### WorkOsAuthUrlCallbackRequest

- This request handles the callback from interacting with an authorization url.
- It will retrieve the `code` and `state` value from the url query string as provided by WorkOS.
- It will authenticate with `authenticateWithCode`, using `code`, `ipAddress`, and `userAgent` args.
- Can create new user if local Laravel user not found. Configuration for this in this package's config file.
    - If enabled, a user will be created, Registered event fired, and user authenticated.

### Utils

#### LaravelWerkOs::userManager

#### LaravelWerkOs::retrieveWorkOsSessionId

#### LaravelWerkOs::getDecodedWorkOsSession

#### LaravelWerkOs::getJwks

## TODOs

- Add other auth methods
    - SSO
    - support organizations
    - add caching where useful
    - create auth request stubs for publishing

## Testing

None right now.

```bash
  composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Shavonn Brown](https://github.com/shavonn)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
