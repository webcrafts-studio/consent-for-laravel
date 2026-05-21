<?php

use Illuminate\Support\Facades\Blade;

it('renders consent gated content when the category is accepted', function () {
    $this->app['request']->cookies->set('consent_preferences', json_encode([
        'marketing' => true,
    ]));

    expect(Blade::render("@consent('marketing')pixel@endconsent"))->toBe('pixel');
});

it('renders unless consent content when the category is missing', function () {
    expect(Blade::render("@unlessconsent('marketing')fallback@endunlessconsent"))->toBe('fallback');
});
