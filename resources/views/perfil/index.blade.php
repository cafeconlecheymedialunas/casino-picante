@extends('layouts.dashboard')

@section('content')
<div class="page-container">
    <style>
        .profile-page { display:flex; flex-direction:column; gap:18px; }
        .profile-header { display:flex; justify-content:space-between; align-items:flex-start; gap:16px; flex-wrap:wrap; margin-bottom:2px; }
        .profile-kicker { font-size:11px; color:var(--muted); letter-spacing:.12em; font-weight:800; text-transform:uppercase; }
        .profile-title { font-family:var(--font-display); font-size:34px; margin-top:2px; }
        .profile-card { border:1px solid var(--line); border-radius:8px; background:linear-gradient(180deg,#170b0b,#0f0707); overflow:hidden; }
        .profile-tabs { display:flex; gap:8px; padding:12px; border-bottom:1px solid var(--line); background:rgba(255,255,255,.02); }
        .profile-tabs label { height:34px; padding:0 14px; border:1px solid var(--line); border-radius:7px; display:inline-flex; align-items:center; justify-content:center; color:var(--muted); font-size:12px; font-weight:800; cursor:pointer; }
        #tab-profile:checked ~ .profile-card .tabs-profile,
        #tab-security:checked ~ .profile-card .tabs-security,
        #tab-chats:checked ~ .profile-card .tabs-chats { color:var(--white); border-color:var(--orange); background:rgba(255,106,26,.14); }
        .tab-panel { display:none; padding:20px; }
        #tab-profile:checked ~ .profile-card .panel-profile,
        #tab-security:checked ~ .profile-card .panel-security,
        #tab-chats:checked ~ .profile-card .panel-chats { display:block; }
        .form-grid { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
        .form-group { margin-bottom:14px; }
        .form-label { display:block; font-size:11px; font-weight:800; color:var(--muted); letter-spacing:.08em; text-transform:uppercase; margin-bottom:6px; }
        .form-input { width:100%; background:rgba(255,255,255,.05); border:1px solid var(--line-2); border-radius:8px; padding:10px 12px; color:#fff; font-size:13px; font-family:var(--font-body); outline:none; }
        .form-input:focus { border-color:var(--orange); box-shadow:0 0 0 3px rgba(255,106,26,.12); }
        .avatar-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(92px, 1fr)); gap:12px; margin-top:8px; }
        .avatar-option { position:relative; cursor:pointer; border:1px solid var(--line); border-radius:8px; padding:10px; background:rgba(255,255,255,.03); transition:.15s; }
        .avatar-option input { position:absolute; opacity:0; pointer-events:none; }
        .avatar-option img { width:100%; aspect-ratio:1; display:block; border-radius:8px; background:rgba(255,255,255,.05); }
        .avatar-option span { display:block; margin-top:7px; color:var(--muted); font-size:11px; font-weight:800; text-align:center; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        .avatar-option:has(input:checked) { border-color:var(--orange); background:rgba(255,106,26,.12); box-shadow:0 0 0 3px rgba(255,106,26,.1); }
        .avatar-option:has(input:checked) span { color:var(--white); }
        .section-title { color:var(--muted); font-size:11px; font-weight:800; letter-spacing:.1em; text-transform:uppercase; margin:0 0 14px; }
        .flash-message { background:rgba(37,196,107,.12); border:1px solid var(--good); border-radius:8px; padding:12px 16px; color:var(--good); font-size:13px; font-weight:700; }
        @media (max-width:860px) { .form-grid { grid-template-columns:1fr; } .profile-tabs { overflow-x:auto; } }
    </style>

    <div class="profile-page">
        <div class="profile-header">
            <div>
                <div class="profile-kicker">PERFIL</div>
                <div class="profile-title">Mi Perfil</div>
            </div>
        </div>

        @if(session('message'))
            <div class="flash-message">{{ session('message') }}</div>
        @endif

        @if(!$user)
            <div class="profile-card"><div class="tab-panel" style="display:block;text-align:center;color:var(--muted);padding:40px;">No hay usuario autenticado.</div></div>
        @else
            <input type="radio" id="tab-profile" name="profile_tabs" checked hidden>
            <input type="radio" id="tab-security" name="profile_tabs" hidden>
            <input type="radio" id="tab-chats" name="profile_tabs" hidden>

            <div class="profile-card">
                <div class="profile-tabs">
                    <label for="tab-profile" class="tabs-profile">Perfil</label>
                    <label for="tab-security" class="tabs-security">Seguridad</label>
                    <label for="tab-chats" class="tabs-chats">Chats</label>
                </div>

                <div class="tab-panel panel-profile">
                    <p class="section-title">Datos personales</p>
                    <form method="POST" action="{{ route('perfil.update') }}">
                        @csrf
                        @method('PUT')
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label" for="name">Nombre</label>
                                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" class="form-input" required>
                            </div>
                            @if(!$isAgent)
                                <div class="form-group">
                                    <label class="form-label" for="apellido">Apellido</label>
                                    <input type="text" id="apellido" name="apellido" value="{{ old('apellido', $user->apellido ?? '') }}" class="form-input">
                                </div>
                            @else
                                <div class="form-group">
                                    <label class="form-label">Email</label>
                                    <input type="email" value="{{ $user->email }}" class="form-input" readonly>
                                </div>
                            @endif
                        </div>

                        @php
                            $avatarLibrary = [
                                ['value' => 'avatar_nova', 'label' => 'Nova', 'seed' => 'Nova'],
                                ['value' => 'avatar_blaze', 'label' => 'Blaze', 'seed' => 'Blaze'],
                                ['value' => 'avatar_onyx', 'label' => 'Onyx', 'seed' => 'Onyx'],
                                ['value' => 'avatar_vega', 'label' => 'Vega', 'seed' => 'Vega'],
                                ['value' => 'avatar_luna', 'label' => 'Luna', 'seed' => 'Luna'],
                                ['value' => 'avatar_orion', 'label' => 'Orion', 'seed' => 'Orion'],
                                ['value' => 'avatar_pixel', 'label' => 'Pixel', 'seed' => 'Pixel'],
                                ['value' => 'avatar_arcade', 'label' => 'Arcade', 'seed' => 'Arcade'],
                                ['value' => 'avatar_neon', 'label' => 'Neon', 'seed' => 'Neon'],
                                ['value' => 'avatar_terra', 'label' => 'Terra', 'seed' => 'Terra'],
                                ['value' => 'avatar_sol', 'label' => 'Sol', 'seed' => 'Sol'],
                                ['value' => 'avatar_zen', 'label' => 'Zen', 'seed' => 'Zen'],
                                ['value' => 'avatar_ember', 'label' => 'Ember', 'seed' => 'Ember'],
                                ['value' => 'avatar_cobalt', 'label' => 'Cobalt', 'seed' => 'Cobalt'],
                                ['value' => 'avatar_iris', 'label' => 'Iris', 'seed' => 'Iris'],
                                ['value' => 'avatar_mint', 'label' => 'Mint', 'seed' => 'Mint'],
                            ];
                            $selectedAvatar = old('avatar', $user->avatar ?? 'avatar_nova');
                        @endphp
                        <div class="form-group">
                            <label class="form-label">Libreria de avatars</label>
                            <div class="avatar-grid">
                                @foreach($avatarLibrary as $avatar)
                                    <label class="avatar-option">
                                        <input type="radio" name="avatar" value="{{ $avatar['value'] }}" {{ $selectedAvatar === $avatar['value'] ? 'checked' : '' }}>
                                        <img src="https://api.dicebear.com/9.x/adventurer/svg?seed={{ urlencode($avatar['seed']) }}&backgroundColor=ffdfbf,ffd5dc,d1d4f9,c0aede,b6e3f4" alt="{{ $avatar['label'] }}">
                                        <span>{{ $avatar['label'] }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <button type="submit" class="btn-primary" style="padding:10px 25px;font-size:13px;">Guardar cambios</button>
                    </form>
                </div>

                <div class="tab-panel panel-security">
                    <p class="section-title">Cambiar contrasena</p>
                    <form method="POST" action="{{ route('perfil.password') }}">
                        @csrf
                        @method('PUT')
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label" for="current_password">Contrasena actual</label>
                                <input type="password" id="current_password" name="current_password" class="form-input" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="password">Nueva contrasena</label>
                                <input type="password" id="password" name="password" class="form-input" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="password_confirmation">Confirmar contrasena</label>
                                <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" required>
                            </div>
                        </div>
                        <button type="submit" class="btn-primary" style="padding:10px 25px;font-size:13px;">Cambiar contrasena</button>
                    </form>
                </div>

                <div class="tab-panel panel-chats">
                    <livewire:components.message-chat
                        :user-id="$isAgent ? null : $user->id"
                        :agent-id="$isAgent ? $user->id : null"
                        :is-agent="$isAgent"
                        :all-chats="$isAgent"
                        :key="'profile-message-chat-'.($isAgent ? 'agent-' : 'user-').$user->id"
                    />
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
