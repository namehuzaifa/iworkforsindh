@if ($paginator->hasPages())
    @once
        <style>
            .rt-pager {
                display: flex;
                align-items: center;
                justify-content: center;
                flex-wrap: wrap;
                gap: 12px;
                margin: 24px 0;
                width: 100%;
            }

            .rt-pager__btn {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                min-height: 44px;
                padding: 10px 18px;
                border: 1px solid #E4E5E8;
                border-radius: 6px;
                background: #FFFFFF;
                color: #18191C;
                font-size: 14px;
                font-weight: 500;
                line-height: 1;
                text-decoration: none;
                white-space: nowrap;
                transition: background-color .15s ease, border-color .15s ease, color .15s ease;
            }

            .rt-pager__btn:hover,
            .rt-pager__btn:focus-visible {
                background: #0A65CC;
                border-color: #0A65CC;
                color: #FFFFFF;
                text-decoration: none;
            }

            .rt-pager__btn--disabled {
                opacity: .45;
                cursor: not-allowed;
                pointer-events: none;
            }

            .rt-pager__icon {
                width: 16px;
                height: 16px;
                flex: 0 0 auto;
            }

            /* Arrows follow reading direction in RTL (Urdu / Sindhi) */
            [dir="rtl"] .rt-pager__icon {
                transform: scaleX(-1);
            }

            @media (max-width: 400px) {
                .rt-pager {
                    gap: 8px;
                }

                .rt-pager__btn {
                    padding: 10px 14px;
                }
            }
        </style>
    @endonce

    <div class="rt-pager" role="navigation" aria-label="{{ __('pagination') }}">
        @if ($paginator->onFirstPage())
            <span class="rt-pager__btn rt-pager__btn--disabled" aria-disabled="true">
                <svg class="rt-pager__icon" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                    <path d="M10 13L5 8l5-5" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
                <span class="rt-pager__label">{{ __('previous') }}</span>
            </span>
        @else
            <a class="rt-pager__btn" href="{{ $paginator->previousPageUrl() }}" rel="prev">
                <svg class="rt-pager__icon" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                    <path d="M10 13L5 8l5-5" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
                <span class="rt-pager__label">{{ __('previous') }}</span>
            </a>
        @endif

        @if ($paginator->hasMorePages())
            <a class="rt-pager__btn" href="{{ $paginator->nextPageUrl() }}" rel="next">
                <span class="rt-pager__label">{{ __('next') }}</span>
                <svg class="rt-pager__icon" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                    <path d="M6 3l5 5-5 5" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
            </a>
        @else
            <span class="rt-pager__btn rt-pager__btn--disabled" aria-disabled="true">
                <span class="rt-pager__label">{{ __('next') }}</span>
                <svg class="rt-pager__icon" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                    <path d="M6 3l5 5-5 5" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
            </span>
        @endif
    </div>
@endif
