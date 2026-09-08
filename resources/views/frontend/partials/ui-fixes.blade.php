{{-- UI fixes: included after the compiled theme CSS so these rules win.
     Kept inline (not a file under /public, which is git-ignored) so the
     styles actually ship with the repo and need no build step. --}}
<style>
/* ==========================================================================
   UI fixes — loaded after the compiled theme CSS so it can override it.
   Kept as a plain stylesheet (no build step) so deploying it is just a file
   copy and no asset hashes change.
   ========================================================================== */

/* --------------------------------------------------------------------------
   1. Select2 dropdown arrow
   The theme hides Select2's own triangle (`.select2-selection__arrow b`) and
   draws the arrow with an icon font instead (`content:"\f102"; font-family:
   flaticon`). When that font fails to load the browser renders a tofu box
   (□) and the control looks broken. Drawing the arrow as an inline SVG
   removes the font dependency entirely.
   -------------------------------------------------------------------------- */
.select2-container--default .select2-selection--single .select2-selection__arrow::after {
    content: "" !important;
    font-family: inherit !important;
    display: block !important;
    position: absolute;
    top: 50% !important;
    right: 14px;
    transform: translateY(-50%);
    width: 12px;
    height: 12px;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='none'%3E%3Cpath d='M4 6l4 4 4-4' stroke='%23767F8C' stroke-width='1.75' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: center;
    background-size: 12px 12px;
}

/* The theme opts some controls out of the arrow altogether — keep that. */
.custom-select-padding .select2-container--default .select2-selection--single .select2-selection__arrow::after {
    display: none !important;
}

/* Mirrored for RTL (Urdu / Sindhi), where the theme moves the arrow left. */
body[dir="rtl"] .select2-container--default .select2-selection--single .select2-selection__arrow::after {
    right: auto;
    left: 14px;
}

/* --------------------------------------------------------------------------
   2. Filter action buttons (Search / Clear)
   The theme's `.btn` sets `width:fit-content; overflow:hidden;
   text-overflow:ellipsis` with 24px side padding, so two buttons sharing a
   narrow grid column get their labels chopped ("Sear…", "Cle…"). Inside a
   `.filter-actions` group let them size to their own text instead.
   -------------------------------------------------------------------------- */
.filter-actions {
    flex-wrap: wrap;
}

.filter-actions .btn {
    width: auto;
    overflow: visible;
    text-overflow: clip;
    padding-left: 14px;
    padding-right: 14px;
    text-align: center;
}

/* Stacked full-width buttons once the row collapses on small screens. */
@media (max-width: 767.98px) {
    .filter-actions .btn {
        flex: 1 1 auto;
    }
}

/* --------------------------------------------------------------------------
   3. Full-width page banners (course listing, career counseling)
   `.banner-img` had no rule of its own, so the banner rendered at its natural
   size and got clipped instead of scaling down with the page.
   -------------------------------------------------------------------------- */
.main-banner {
    width: 100%;
}

.main-banner .banner-img {
    display: block;
    width: 100%;
    height: auto;
    max-width: 100%;
}

/* --------------------------------------------------------------------------
   4. Salary range slider tooltips (job / category filter sidebar)
   noUiSlider centres each tooltip over its handle. At the ends of the track
   the tooltip runs past the filter panel and gets clipped, so the max value
   read "1,200,000 (P". Insetting the track leaves room for both tooltips,
   and a smaller tooltip keeps long amounts on one line.
   -------------------------------------------------------------------------- */
.price-range-slider {
    padding: 0 46px;
    margin-top: 6px;
}

.price-range-slider .noUi-tooltip {
    font-size: 12px;
    line-height: 1.2;
    padding: 3px 6px;
    white-space: nowrap;
}

/* Nothing between the track and the panel may clip the tooltips. */
.price-range-slider,
.list-sidebar__accordion-body,
#priceCollapse {
    overflow: visible;
}

/* --------------------------------------------------------------------------
   5. Busy state for one-shot actions (see the script below)
   -------------------------------------------------------------------------- */
[data-busy="1"] {
    opacity: .6;
    pointer-events: none;
    cursor: progress;
}
</style>

<script>
    // Requests that used to give no feedback at all — the bookmark toggle and
    // the "Add a CV/Resume" upload — left people unsure whether anything had
    // happened, so they clicked again and hit errors. Mark such controls busy
    // on the first activation and ignore the rest.
    //
    // Delegated from `document` so it works no matter when this runs.
    (function () {
        function markBusy(el, label) {
            el.setAttribute('data-busy', '1');
            if (!label) return;

            // <input type="submit"> carries its label in `value`; a <button>
            // carries it as text. (Every button also *has* a `value`
            // property, so the tag is what decides.)
            if (el.tagName === 'INPUT') {
                el.value = label;
            } else {
                el.textContent = label;
            }
        }

        document.addEventListener('click', function (e) {
            var link = e.target.closest ? e.target.closest('a[data-busy-click]') : null;
            if (!link) return;

            if (link.getAttribute('data-busy') === '1') {
                e.preventDefault();

                return;
            }

            markBusy(link);
        });

        document.addEventListener('submit', function (e) {
            var form = e.target;
            if (!form.matches || !form.matches('form[data-busy-submit]')) return;

            // Something else already stopped this submit (a client-side check,
            // for instance) — leave the button alone so it can be retried.
            if (e.defaultPrevented) return;

            if (form.getAttribute('data-busy') === '1') {
                e.preventDefault();

                return;
            }

            form.setAttribute('data-busy', '1');

            var button = form.querySelector('[type="submit"]');
            if (button) {
                markBusy(button, '{{ __('loading') }}...');
            }
        });
    })();
</script>
