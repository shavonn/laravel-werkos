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

### Provided WorkOS Creds

Add service in `services.php`. Add env var keys to your `.env` file.

```php
    'workos' => [
        'client_id' => env('WORKOS_CLIENT_ID'),
        'secret' => env('WORKOS_API_KEY'),
        'redirect_url' => env('WORKOS_REDIRECT_URL'),
        'logout_redirect_url' => env('WORKOS_LOGOUT_REDIRECT_URL'),
    ],
```

### Migrate (if needed)

The migration is for cases where WorkOS columns are not already added to the `users` table.

The migration:

- Adds the `workos_id` and `avatar` columns to the `users` table
- Drops the `password` column from the `users` table

```bash
    php artisan vendor:publish --tag="werkos-migrations"
    php artisan migrate
```

For existing applications not previously using WorkOS, you can alter the password column instead until you complete
migrating your users. Modify the migration.

Up:

```php
$table->string('password')->nullable()->change();
```

Down:

```php
$table->string('password')->nullable(false)->change();
````

### WerkOs Config

You can publish the config file with:

```bash
    php artisan vendor:publish --tag="werkos-config"
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

An abstract for WorkOS authentication request that calls `authenticate` and `handleMissingLocalUser` methods defined on
the Auth form requests that extend it, so it can be used to create and customize handling.

To use the extending auth request, call the `attempt` method.  
When successful, `attempt` will return a user. Else, it will throw an error.

In controller:

```php
public function authMe(WorkOsPasswordAuthRequest $request) {
    $user $request->attempt();
    // handle the success or failure however you like
    return // whatever your return...
}
```

Current list of existing requests and what they handle:

#### WorkOsPasswordAuthRequest

- Validates email and password with basic validation.
- Uses Laravel's normal login rate limiting logic (untested in this context).
- Authenticates with `email`, `password`, `ipAddress`, and `userAgent` using WorkOS `authenticateWithPassword`.
  args.

#### WorkOsProviderAuthUrlRequest

- Generates a random string as `state`
- Gets authorization url with `provider` and generated `state` using WorkOS `getAuthorizationUrl`.
- The form request expects `provider` as a url param. Ex `auth/{provider}/gimme-url`.
    - `provider`: provider ID from WorkOS (authkit, GoogleOAuth, etc)
- Stores the `state` value in the Laravel session for later verification.

#### WorkOsAuthCallbackRequest

- Handles the callback from interacting with an authorization url.
- Retrieves `code` and `state` from the url as query values, as provided by WorkOS.
- Authenticates with `code`, `ipAddress`, and `userAgent` using `authenticateWithCode`.
- Can create new user when Laravel user not found.
    - Configuration in werkos config. Default is `true`. `workos.provider_auth.create_missing_user`
    - If enabled, a user will be created, Registered event fired, and user authenticated.

### Utils

#### LaravelWerkOs::userManager

#### LaravelWerkOs::retrieveWorkOsSessionId

#### LaravelWerkOs::getDecodedWorkOsSession

#### LaravelWerkOs::getJwks

## TODOs

- Add other auth methods
    - SSO
    - Support organizations
    - Add caching where useful
    - Create auth request stubs for publishing
    - Custom informative errors

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
