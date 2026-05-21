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

        const currentDecisions = readConsentCookie();

        if (! currentDecisions) {
            banner.hidden = false;
        }

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

        syncPreferenceInputs(currentDecisions);

        const writeConsentCookie = (decisions) => {
            const maxAge = Math.max(0, Number(config.lifetimeMinutes) || 0) * 60;
            const sameSite = config.sameSite ? `; SameSite=${config.sameSite}` : '';
            const secure = config.secure === true ? '; Secure' : '';

            document.cookie = `${encodeURIComponent(config.cookieName)}=${encodeURIComponent(JSON.stringify(decisions))}; Path=/; Max-Age=${maxAge}${sameSite}${secure}`;
            window.dispatchEvent(new CustomEvent('consent:updated', { detail: { decisions } }));
            window.location.reload();
        };

        const decisionsForAll = (accepted) => Object.fromEntries(
            config.categories.map((category) => [
                category,
                accepted || config.requiredCategories.includes(category),
            ]),
        );

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

        const openPreferences = () => {
            if (! preferences) {
                return;
            }

            syncPreferenceInputs(readConsentCookie());

            banner.hidden = true;
            preferences.hidden = false;
        };

        const closePreferences = () => {
            if (! preferences) {
                return;
            }

            preferences.hidden = true;
            banner.hidden = Boolean(readConsentCookie());
        };

        window.ConsentForLaravel = {
            ...(window.ConsentForLaravel || {}),
            openPreferences,
            closePreferences,
            decisions: () => readConsentCookie() || decisionsForAll(false),
        };

        document.querySelectorAll('[data-consent-open-preferences]').forEach((trigger) => {
            trigger.addEventListener('click', (event) => {
                event.preventDefault();
                openPreferences();
            });
        });

        banner.querySelector('[data-consent-accept]')?.addEventListener('click', () => {
            writeConsentCookie(decisionsForAll(true));
        });

        banner.querySelector('[data-consent-reject]')?.addEventListener('click', () => {
            writeConsentCookie(decisionsForAll(false));
        });

        banner.querySelector('[data-consent-customize]')?.addEventListener('click', () => {
            openPreferences();
        });

        preferences?.querySelector('[data-consent-cancel]')?.addEventListener('click', () => {
            closePreferences();
        });

        preferences?.querySelector('[data-consent-save]')?.addEventListener('click', () => {
            writeConsentCookie(decisionsFromPreferences());
        });
    })();
</script>
