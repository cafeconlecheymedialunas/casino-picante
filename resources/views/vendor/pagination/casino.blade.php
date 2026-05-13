@php
$pageName = $paginator->getPageName();
$onEachSide = $paginator->onEachSide ?? 2;
@endphp

<div class="pg-wrap">
    <div class="pg-info">
        Mostrando
        @if ($paginator->firstItem())
            <span class="pg-highlight">{{ $paginator->firstItem() }}</span>
            -
            <span class="pg-highlight">{{ $paginator->lastItem() }}</span>
        @else
            <span class="pg-highlight">0</span>
        @endif
        de <span class="pg-highlight">{{ $paginator->total() }}</span>
    </div>

    @if ($paginator->hasPages())
        <div class="pg-nav">
            {{-- Previous --}}
            @if ($paginator->onFirstPage())
                <span class="pg-btn disabled">
                    <svg class="pg-icon" viewBox="0 0 24 24"><path d="m15 18-6-6 6-6"/></svg>
                </span>
            @else
                <button type="button" wire:click="previousPage('{{ $pageName }}')" class="pg-btn">
                    <svg class="pg-icon" viewBox="0 0 24 24"><path d="m15 18-6-6 6-6"/></svg>
                </button>
            @endif

            {{-- Pages --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="pg-dots">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        <span wire:key="paginator-{{ $pageName }}-page{{ $page }}">
                            @if ($page == $paginator->currentPage())
                                <span class="pg-btn active">{{ $page }}</span>
                            @else
                                <button type="button" wire:click="gotoPage({{ $page }}, '{{ $pageName }}')" class="pg-btn">{{ $page }}</button>
                            @endif
                        </span>
                    @endforeach
                @endif
            @endforeach

            {{-- Next --}}
            @if ($paginator->hasMorePages())
                <button type="button" wire:click="nextPage('{{ $pageName }}')" class="pg-btn">
                    <svg class="pg-icon" viewBox="0 0 24 24"><path d="m9 18 6-6-6-6"/></svg>
                </button>
            @else
                <span class="pg-btn disabled">
                    <svg class="pg-icon" viewBox="0 0 24 24"><path d="m9 18 6-6-6-6"/></svg>
                </span>
            @endif
        </div>
    @endif
</div>
