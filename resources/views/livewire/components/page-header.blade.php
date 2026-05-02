<div class="page-header">
    <div class="header-content">
        <h1 class="page-title">{{ $title }}</h1>
        <p class="page-subtitle">{{ $subtitle }}</p>
    </div>
    @if($showButton && $buttonText)
        <button wire:click="{{ $buttonAction }}" class="btn-primary">
            <span>+</span> {{ $buttonText }}
        </button>
    @endif
</div>

<style>
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 24px 28px 16px;
        margin: -24px -28px 24px -28px;
        position: sticky;
        top: 0;
        z-index: 100;
        background: var(--black);
        border-bottom: 1px solid var(--line);
    }
    .page-title {
        font-family: var(--font-display);
        font-size: 36px;
        color: var(--white);
        margin: 0;
        letter-spacing: 0.02em;
    }
    .page-subtitle {
        font-size: 12px;
        color: var(--muted);
        margin-top: 2px;
    }
    @media (max-width: 640px) {
        .page-header { 
            flex-direction: column; 
            gap: 16px;
            margin: -24px -16px 24px -16px;
            padding: 16px;
        }
        .page-title { font-size: 28px; }
    }
</style>