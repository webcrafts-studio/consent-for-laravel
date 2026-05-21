<?php

use WebcraftsStudio\ConsentForLaravel\ConsentManager;

it('always grants required categories', function () {
    expect(app(ConsentManager::class)->has('necessary'))->toBeTrue();
});

it('reads accepted optional categories from the consent cookie', function () {
    $this->app['request']->cookies->set('consent_preferences', json_encode([
        'analytics' => false,
        'marketing' => true,
    ]));

    $consent = app(ConsentManager::class);

    expect($consent->has('marketing'))->toBeTrue()
        ->and($consent->has('analytics'))->toBeFalse();
});
