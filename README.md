# Consent for Laravel

A Laravel-first cookie consent manager with Blade gates and category-based script activation.

Consent for Laravel is being built around one small idea: consenting to a category should feel natural in Blade.

```blade
@consent('marketing')
    <script src="https://example.com/pixel.js"></script>
@endconsent
```

## Installation

```bash
composer require webcrafts-studio/consent-for-laravel
```

Publish the config and starter views:

```bash
php artisan consent:install
```

Or publish them separately:

```bash
php artisan vendor:publish --tag="consent-for-laravel-config"
php artisan vendor:publish --tag="consent-for-laravel-views"
```

## Usage

Include the starter banner in your layout:

```blade
@include('consent-for-laravel::banner')
```

Gate scripts or markup by consent category:

```blade
@consent('analytics')
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-XXXX"></script>
@endconsent

@unlessconsent('marketing')
    <p>You can enable marketing cookies in cookie preferences.</p>
@endunlessconsent
```

Check consent in PHP:

```php
use WebcraftsStudio\ConsentForLaravel\Facades\Consent;

Consent::has('marketing'); // true or false
Consent::decisions(); // ['necessary' => true, 'analytics' => false, ...]
```

## Configuration

The default categories are:

- `necessary`, always enabled
- `preferences`
- `analytics`
- `marketing`

You can customize labels, descriptions, and categories in `config/consent-for-laravel.php`.

## Current Scope

This first version is intentionally small:

- first-party consent cookie
- publishable config and banner view
- `@consent` and `@unlessconsent` Blade directives
- `Consent` facade and `ConsentManager`
- no database dependency

Instant client-side activation without reload, preferences modal, Google Consent Mode v2, and audit tooling are planned next. See [ROADMAP.md](ROADMAP.md).

## Testing

```bash
composer test
```

## Credits

- [Webcrafts Studio](https://github.com/webcrafts-studio)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
