<div class="page-container">
@section('header')
    <x-livewire.components.page-header title="CHATS" subtitle="Conversaciones internas reunidas desde clientes, agentes y perfil" />
@endsection

    <livewire:components.message-chat :is-agent="true" :all-chats="true" key="dashboard-all-chats" />
</div>
