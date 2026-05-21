<?php

namespace WebcraftsStudio\ConsentForLaravel;

use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Http\Request;

class ConsentManager
{
    public function __construct(
        protected Config $config,
        protected Request $request,
    ) {}

    public function has(string $category): bool
    {
        if ($this->isRequired($category)) {
            return true;
        }

        return $this->decisions()[$category] ?? false;
    }

    public function missing(string $category): bool
    {
        return ! $this->has($category);
    }

    public function decisions(): array
    {
        $decisions = [];

        foreach ($this->categories() as $key => $category) {
            $decisions[$key] = (bool) ($this->readCookie()[$key] ?? false);

            if (($category['required'] ?? false) === true) {
                $decisions[$key] = true;
            }
        }

        return $decisions;
    }

    public function categories(): array
    {
        return $this->config->get('consent-for-laravel.categories', []);
    }

    public function cookieName(): string
    {
        return $this->config->get('consent-for-laravel.cookie.name', 'consent_preferences');
    }

    public function cookieLifetime(): int
    {
        return (int) $this->config->get('consent-for-laravel.cookie.lifetime_minutes', 525600);
    }

    public function isRequired(string $category): bool
    {
        return (bool) ($this->categories()[$category]['required'] ?? false);
    }

    protected function readCookie(): array
    {
        $value = $this->request->cookies->get($this->cookieName());

        if (! is_string($value) || $value === '') {
            return [];
        }

        $decoded = json_decode($value, true);

        if (! is_array($decoded)) {
            return [];
        }

        return array_map(static fn ($decision): bool => $decision === true, $decoded);
    }
}
