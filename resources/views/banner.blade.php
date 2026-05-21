@php
    /** @var \WebcraftsStudio\ConsentForLaravel\ConsentManager $consent */
    $consent = app(\WebcraftsStudio\ConsentForLaravel\ConsentManager::class);
    $categories = $consent->categories();
    $requiredCategories = array_keys(array_filter($categories, fn (array $category): bool => ($category['required'] ?? false) === true));
@endphp

<div
    data-consent-banner
    hidden
    style="position: fixed; right: 1rem; bottom: 1rem; z-index: 50; max-width: 28rem; padding: 1rem; border: 1px solid #d4d4d8; border-radius: .5rem; background: #fff; color: #18181b; box-shadow: 0 18px 50px rgba(24, 24, 27, .16); font-family: ui-sans-serif, system-ui, sans-serif;"
>
    <strong style="display: block; margin-bottom: .35rem;">Cookie preferences</strong>

    <p style="margin: 0 0 .85rem; line-height: 1.45;">
        We use optional cookies only when you allow them.
    </p>

    <div style="display: flex; flex-wrap: wrap; gap: .5rem;">
        <button type="button" data-consent-reject style="padding: .55rem .8rem; border: 1px solid #d4d4d8; border-radius: .375rem; background: #fff; color: #18181b; cursor: pointer;">
            Reject optional
        </button>

        <button type="button" data-consent-customize style="padding: .55rem .8rem; border: 1px solid #d4d4d8; border-radius: .375rem; background: #fff; color: #18181b; cursor: pointer;">
            Customize
        </button>

        <button type="button" data-consent-accept style="padding: .55rem .8rem; border: 1px solid #18181b; border-radius: .375rem; background: #18181b; color: #fff; cursor: pointer;">
            Accept all
        </button>
    </div>
</div>

@include('consent-for-laravel::preferences', [
    'consent' => $consent,
    'categories' => $categories,
])

<script>
    (() => {
        const banner = document.querySelector('[data-consent-banner]');
        const preferences = document.querySelector('[data-consent-preferences]');
        let currentDecisions = null;

        if (! banner) {
            return;
        }

        const config = {
            cookieName: @json($consent->cookieName()),
            lifetimeMinutes: @json($consent->cookieLifetime()),
            categories: @json(array_keys($categories)),
            requiredCategories: @json($requiredCategories),
            sameSite: @json(config('consent-for-laravel.cookie.same_site', 'Lax')),
            secure: @json(config('consent-for-laravel.cookie.secure')),
        };

        const decisionsForAll = (accepted) => Object.fromEntries(
            config.categories.map((category) => [
                category,
                accepted || config.requiredCategories.includes(category),
            ]),
        );

        const readConsentCookie = () => {
            const prefix = `${encodeURIComponent(config.cookieName)}=`;
            const cookie = document.cookie
                .split('; ')
                .find((item) => item.startsWith(prefix));

            if (! cookie) {
                return null;
            }

            try {
                return JSON.parse(decodeURIComponent(cookie.slice(prefix.length)));
            } catch {
                return null;
            }
        };

        const hasConsent = (category, decisions) => (
            config.requiredCategories.includes(category) || decisions?.[category] === true
        );

        const activeDecisions = () => currentDecisions || decisionsForAll(false);

        const dispatchConsentEvent = (name, detail) => {
            window.dispatchEvent(new CustomEvent(name, { detail }));
        };

        const revokedCategories = (previousDecisions, decisions) => {
            if (! previousDecisions) {
                return [];
            }

            return config.categories.filter((category) => (
                previousDecisions[category] === true && decisions[category] !== true
            ));
        };

        const activateScripts = (decisions = activeDecisions()) => {
            document.querySelectorAll('script[type="text/plain"][data-consent]').forEach((script) => {
                const category = script.dataset.consent;

                if (! category || ! hasConsent(category, decisions)) {
                    return;
                }

                const activatedScript = document.createElement('script');

                Array.from(script.attributes).forEach((attribute) => {
                    if (['type', 'data-consent', 'data-src', 'data-consent-activated'].includes(attribute.name)) {
                        return;
                    }

                    activatedScript.setAttribute(attribute.name, attribute.value);
                });

                if (script.dataset.src) {
                    activatedScript.src = script.dataset.src;
                }

                activatedScript.dataset.consentActivated = category;
                activatedScript.textContent = script.textContent;

                script.replaceWith(activatedScript);
            });
        };

        const writeConsentCookie = (decisions) => {
            const maxAge = Math.max(0, Number(config.lifetimeMinutes) || 0) * 60;
            const sameSite = config.sameSite ? `; SameSite=${config.sameSite}` : '';
            const secure = config.secure === true ? '; Secure' : '';

            document.cookie = `${encodeURIComponent(config.cookieName)}=${encodeURIComponent(JSON.stringify(decisions))}; Path=/; Max-Age=${maxAge}${sameSite}${secure}`;
        };

        const syncPreferenceInputs = (decisions) => {
            if (! preferences || ! decisions) {
                return;
            }

            preferences.querySelectorAll('[data-consent-category]').forEach((input) => {
                if (input.disabled) {
                    input.checked = true;

                    return;
                }

                input.checked = decisions[input.value] === true;
            });
        };

        const decisionsFromPreferences = () => {
            if (! preferences) {
                return decisionsForAll(false);
            }

            return Object.fromEntries(
                config.categories.map((category) => {
                    const input = preferences.querySelector(`[data-consent-category][value="${category}"]`);

                    return [
                        category,
                        config.requiredCategories.includes(category) || input?.checked === true,
                    ];
                }),
            );
        };

        const persistConsent = (decisions) => {
            const previousDecisions = currentDecisions;
            const revoked = revokedCategories(previousDecisions, decisions);

            writeConsentCookie(decisions);

            currentDecisions = decisions;
            syncPreferenceInputs(decisions);
            activateScripts(decisions);

            banner.hidden = true;

            if (preferences) {
                preferences.hidden = true;
            }

            dispatchConsentEvent('consent:updated', { decisions });

            if (revoked.length > 0) {
                dispatchConsentEvent('consent:revoked', {
                    categories: revoked,
                    decisions,
                });
            }
        };

        const openPreferences = () => {
            if (! preferences) {
                return;
            }

            syncPreferenceInputs(activeDecisions());

            banner.hidden = true;
            preferences.hidden = false;
        };

        const closePreferences = () => {
            if (! preferences) {
                return;
            }

            preferences.hidden = true;
            banner.hidden = Boolean(currentDecisions);
        };

        currentDecisions = readConsentCookie();

        if (! currentDecisions) {
            banner.hidden = false;
        }

        syncPreferenceInputs(activeDecisions());
        activateScripts(activeDecisions());

        window.ConsentForLaravel = {
            ...(window.ConsentForLaravel || {}),
            openPreferences,
            closePreferences,
            activateScripts,
            decisions: activeDecisions,
            has: (category) => hasConsent(category, activeDecisions()),
        };

        document.querySelectorAll('[data-consent-open-preferences]').forEach((trigger) => {
            trigger.addEventListener('click', (event) => {
                event.preventDefault();
                openPreferences();
            });
        });

        banner.querySelector('[data-consent-accept]')?.addEventListener('click', () => {
            persistConsent(decisionsForAll(true));
        });

        banner.querySelector('[data-consent-reject]')?.addEventListener('click', () => {
            persistConsent(decisionsForAll(false));
        });

        banner.querySelector('[data-consent-customize]')?.addEventListener('click', () => {
            openPreferences();
        });

        preferences?.querySelector('[data-consent-cancel]')?.addEventListener('click', () => {
            closePreferences();
        });

        preferences?.querySelector('[data-consent-save]')?.addEventListener('click', () => {
            persistConsent(decisionsFromPreferences());
        });

        dispatchConsentEvent('consent:ready', {
            decisions: activeDecisions(),
        });
    })();
</script>
