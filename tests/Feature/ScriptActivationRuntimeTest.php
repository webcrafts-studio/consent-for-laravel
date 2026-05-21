<?php

it('renders runtime support for inert consent scripts', function () {
    $html = view('consent-for-laravel::banner')->render();

    expect($html)
        ->toContain('activateScripts')
        ->toContain('script[type="text/plain"][data-consent]')
        ->toContain('data-consent-activated')
        ->toContain('data-src');
});

it('renders consent lifecycle browser events', function () {
    $html = view('consent-for-laravel::banner')->render();

    expect($html)
        ->toContain('consent:ready')
        ->toContain('consent:updated')
        ->toContain('consent:revoked');
});
