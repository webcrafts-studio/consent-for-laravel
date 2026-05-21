<?php

it('exposes a browser API for reopening the preferences panel', function () {
    $html = view('consent-for-laravel::banner')->render();

    expect($html)
        ->toContain('window.ConsentForLaravel')
        ->toContain('openPreferences')
        ->toContain('closePreferences')
        ->toContain('data-consent-open-preferences');
});
