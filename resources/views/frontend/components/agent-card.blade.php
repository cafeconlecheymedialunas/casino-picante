@once
@push('styles')
<style>
    .agent-card { display:flex; align-items:center; gap:12px; border:1px solid rgba(255,255,255,.09); border-radius:10px; background:rgba(255,255,255,.04); padding:12px 14px; transition:border-color .18s,background .18s; }
    .agent-card:hover { border-color:rgba(255,106,26,.32); background:rgba(255,106,26,.06); }
    .agent-avatar { flex-shrink:0; width:42px; height:42px; border-radius:10px; background:linear-gradient(135deg,var(--orange),var(--amber)); display:flex; align-items:center; justify-content:center; overflow:hidden; color:#190702; font-family:var(--font-display); font-size:17px; font-weight:900; }
    .agent-avatar img { width:100%; height:100%; object-fit:cover; }
    .agent-info { min-width:0; }
    .agent-info strong { display:block; color:#fff; font-size:13px; font-weight:900; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .agent-info span { display:block; color:rgba(255,255,255,.46); font-size:11px; margin-top:2px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
</style>
@endpush
@endonce

@php
    $rawAvatar = $agent->avatar ?? null;
    $avatarUrl = $rawAvatar && \Illuminate\Support\Str::startsWith($rawAvatar, ['http://', 'https://'])
        ? $rawAvatar
        : 'https://ui-avatars.com/api/?name='.urlencode($agent->name ?? 'A').'&background=ff6a1a&color=190702&bold=true&size=128';
@endphp

<article class="agent-card">
    <div class="agent-avatar">
        <img src="{{ $avatarUrl }}" alt="{{ $agent->name }}">
    </div>
    <div class="agent-info">
        <strong>{{ $agent->name }}</strong>
        <span>{{ $agent->cargo ?? $agent->username ?? 'Agente' }}</span>
    </div>
</article>
