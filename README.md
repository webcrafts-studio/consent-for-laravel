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
    <p>You can enable marketing cookies in the cookie preferences panel.</p>
@endunlessconsent
```

For scripts that should become active immediately after consent is saved, render them as inert scripts:

```blade
<script type="text/plain" data-consent="marketing">
    window.marketingPixelLoaded = true;
</script>
```

External scripts can use `data-src` so the browser does not request them before consent:

```blade
<script
    type="text/plain"
    data-consent="analytics"
    data-src="https://www.googletagmanager.com/gtag/js?id=G-XXXX"
    async
></script>
```

Check consent in PHP:

```php
use WebcraftsStudio\ConsentForLaravel\Facades\Consent;

Consent::has('marketing'); // true or false
Consent::decisions(); // ['necessary' => true, 'analytics' => false, ...]
```

Open the preferences panel again from any page element:

```blade
<button type="button" data-consent-open-preferences>
    Cookie settings
</button>
```

Or call the browser API directly:

```html
<script>
    window.ConsentForLaravel?.openPreferences();
</script>
```

The browser runtime dispatches events after it starts and whenever consent changes:

```js
window.addEventListener('consent:ready', (event) => {
    console.log(event.detail.decisions);
});

window.addEventListener('consent:updated', (event) => {
    console.log(event.detail.decisions);
});

window.addEventListener('consent:revoked', (event) => {
    console.log(event.detail.categories);
});
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
- publishable config, banner view, and preferences panel
- public JS API for opening the preferences panel
- client-side activation for inert consent scripts
- `@consent` and `@unlessconsent` Blade directives
- `Consent` facade and `ConsentManager`
- no database dependency

Google Consent Mode v2 and audit tooling are planned next.

## Testing

```bash
composer test
```

## Credits

- [Webcrafts Studio](https://github.com/webcrafts-studio)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
