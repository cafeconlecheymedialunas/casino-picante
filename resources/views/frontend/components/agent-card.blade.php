<article class="agent-card">
    <div class="agent-card-head">
        <h3>{{ $agent->name }}</h3>
        @if(filled($agent->username))<small>{{ $agent->username }}</small>@endif
    </div>
    <div class="agent-card-body">
        @if(filled($agent->email))<div>{{ $agent->email }}</div>@endif
        @if(filled($agent->phone))<div>Tel: {{ $agent->phone }}</div>@endif
    </div>
</article>
