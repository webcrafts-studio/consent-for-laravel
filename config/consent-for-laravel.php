<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Consent Cookie
    |--------------------------------------------------------------------------
    |
    | Consent decisions are stored in a first-party cookie as JSON. The MVP is
    | intentionally database-free so the package stays easy to install.
    |
    */
    'cookie' => [
        'name' => env('CONSENT_FOR_LARAVEL_COOKIE', 'consent_preferences'),
        'lifetime_minutes' => 60 * 24 * 365,
        'same_site' => 'Lax',
        'secure' => null,
    ],

    /*
    |--------------------------------------------------------------------------
    | Categories
    |--------------------------------------------------------------------------
    |
    | Required categories are always treated as accepted. Optional categories
    | can be checked in Blade using @consent('marketing').
    |
    */
    'categories' => [
        'necessary' => [
            'label' => 'Necessary',
            'description' => 'Required for the website to work correctly.',
            'required' => true,
        ],

        'preferences' => [
            'label' => 'Preferences',
            'description' => 'Remember choices that improve the experience.',
            'required' => false,
        ],

        'analytics' => [
            'label' => 'Analytics',
            'description' => 'Help understand how visitors use the website.',
            'required' => false,
        ],

        'marketing' => [
            'label' => 'Marketing',
            'description' => 'Allow advertising, pixels, and remarketing tools.',
            'required' => false,
        ],
    ],
];
