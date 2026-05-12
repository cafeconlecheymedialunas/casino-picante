@extends('layouts.dashboard')

@section('header')
    <x-livewire.components.page-header title="PERFIL" subtitle="Configura tu cuenta" />
@endsection

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
                            <div class="form-group">
                                <label class="form-label" for="apellido">Apellido</label>
                                <input type="text" id="apellido" name="apellido" value="{{ old('apellido', $user->apellido ?? '') }}" class="form-input">
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="email">Email</label>
                                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" class="form-input">
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="username">Usuario</label>
                                <input type="text" id="username" name="username" value="{{ old('username', $user->username ?? '') }}" class="form-input">
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="phone">Teléfono</label>
                                <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone ?? '') }}" class="form-input">
                            </div>
                            @if($isAgent)
                            <div class="form-group" style="grid-column:1/-1;">
                                <label class="form-label">Líneas asignadas</label>
                                <div style="display:flex;flex-wrap:wrap;gap:6px;margin-top:4px;">
                                    @foreach($user->activeLines as $line)
                                        @php $rol = $line->pivot->role ?? 'miembro'; @endphp
                                        <span style="display:inline-flex;align-items:center;gap:6px;padding:4px 12px;border-radius:999px;font-size:11px;font-weight:700;background:rgba(255,106,26,0.1);border:1px solid rgba(255,106,26,0.25);color:var(--orange);">
                                            {{ $line->icon ?? '●' }} {{ $line->name }}
                                            <span style="padding:1px 7px;border-radius:999px;font-size:9px;font-weight:800;text-transform:uppercase;background:{{ $rol === 'encargado' ? 'rgba(255,106,26,0.2)' : 'rgba(255,255,255,0.06)' }};color:{{ $rol === 'encargado' ? 'var(--orange)' : 'var(--muted-2)' }};">
                                                {{ $rol === 'encargado' ? 'ADMIN' : 'MIEMBRO' }}
                                            </span>
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                            @else
                            <div class="form-group">
                                <label class="form-label" for="contact">Contacto</label>
                                <input type="text" id="contact" name="contact" value="{{ old('contact', $user->contact ?? '') }}" class="form-input">
                            </div>
                            @endif
                        </div>

                        <div class="form-group">
                            <x-avatar-library name="avatar" :selected="old('avatar', $user->avatar ?? \App\Support\AvatarLibrary::default())" />
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
