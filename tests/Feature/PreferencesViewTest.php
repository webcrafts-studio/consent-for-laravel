<?php

it('renders configured categories in the preferences panel', function () {
    $html = view('consent-for-laravel::preferences')->render();

    expect($html)
        ->toContain('Necessary')
        ->toContain('Analytics')
        ->toContain('Marketing')
        ->toContain('data-consent-category')
        ->toContain('Save preferences');
});

it('marks required categories as disabled', function () {
    $html = view('consent-for-laravel::preferences')->render();

    expect($html)
        ->toContain('value="necessary"')
        ->toContain('disabled');
});
