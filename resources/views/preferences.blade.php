@php
    /** @var \WebcraftsStudio\ConsentForLaravel\ConsentManager $consent */
    $consent ??= app(\WebcraftsStudio\ConsentForLaravel\ConsentManager::class);
    $categories ??= $consent->categories();
@endphp

<div
    data-consent-preferences
    hidden
    role="dialog"
    aria-modal="true"
    aria-labelledby="consent-preferences-title"
    style="position: fixed; inset: 0; z-index: 60; display: grid; place-items: center; padding: 1rem; background: rgba(24, 24, 27, .45); font-family: ui-sans-serif, system-ui, sans-serif;"
>
    <div style="width: min(100%, 36rem); max-height: calc(100vh - 2rem); overflow: auto; padding: 1rem; border: 1px solid #d4d4d8; border-radius: .5rem; background: #fff; color: #18181b; box-shadow: 0 18px 50px rgba(24, 24, 27, .18);">
        <h2 id="consent-preferences-title" style="margin: 0 0 .35rem; font-size: 1.15rem; line-height: 1.3;">
            Cookie preferences
        </h2>

        <p style="margin: 0 0 1rem; color: #52525b; line-height: 1.45;">
            Choose which optional cookies this website can use.
        </p>

        <div style="display: grid; gap: .75rem;">
            @foreach ($categories as $key => $category)
                @php
                    $required = ($category['required'] ?? false) === true;
                @endphp

                <label style="display: grid; grid-template-columns: 1fr auto; gap: .75rem; align-items: start; padding: .85rem; border: 1px solid #e4e4e7; border-radius: .5rem;">
                    <span>
                        <span style="display: block; font-weight: 700;">
                            {{ $category['label'] ?? ucfirst($key) }}
                        </span>

                        <span style="display: block; margin-top: .2rem; color: #52525b; line-height: 1.4;">
                            {{ $category['description'] ?? '' }}
                        </span>
                    </span>

                    <input
                        type="checkbox"
                        value="{{ $key }}"
                        data-consent-category
                        @checked($required || $consent->has($key))
                        @disabled($required)
                        style="margin-top: .25rem;"
                    >
                </label>
            @endforeach
        </div>

        <div style="display: flex; flex-wrap: wrap; justify-content: flex-end; gap: .5rem; margin-top: 1rem;">
            <button type="button" data-consent-cancel style="padding: .55rem .8rem; border: 1px solid #d4d4d8; border-radius: .375rem; background: #fff; color: #18181b; cursor: pointer;">
                Back
            </button>

            <button type="button" data-consent-save style="padding: .55rem .8rem; border: 1px solid #18181b; border-radius: .375rem; background: #18181b; color: #fff; cursor: pointer;">
                Save preferences
            </button>
        </div>
    </div>
</div>
