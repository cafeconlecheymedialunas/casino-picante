<?php

namespace App\Livewire\Agents;

use App\Models\Agent;
use App\Models\Line;
use App\Models\LineAgent;
use App\Models\LineAgentPermission;
use App\Models\Notification;
use App\Models\Role;
use App\Models\User;
use App\Support\AvatarLibrary;
use App\Support\LineRoles;
use App\Support\Roles;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Component;

class AgentRegister extends Component
{
    public string $name = '';

    public string $apellido = '';

    public string $email = '';

    public string $username = '';

    public string $password = '';

    public string $password_confirmation = '';

    public string $phone = '';

    public string $avatar = '';

    public array $selectedLines = [];

    public bool $registered = false;

    protected function rules(): array
    {
        return [
            'name' => 'required|min:2|max:100',
            'apellido' => 'nullable|max:100',
            'email' => 'required|email',
            'username' => 'nullable|min:3|max:60|alpha_dash',
            'password' => 'required|min:6|confirmed',
            'phone' => 'nullable|max:30',
            'selectedLines' => 'required|array|min:1',
            'selectedLines.*' => 'integer|exists:lines,id',
        ];
    }

    protected $messages = [
        'name.required' => 'El nombre es obligatorio.',
        'name.min' => 'El nombre debe tener al menos 2 caracteres.',
        'email.required' => 'El email es obligatorio.',
        'email.email' => 'Ingresá un email válido.',
        'username.alpha_dash' => 'Solo letras, números, guiones y guion bajo.',
        'username.min' => 'El username debe tener al menos 3 caracteres.',
        'password.required' => 'La contraseña es obligatoria.',
        'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
        'password.confirmed' => 'Las contraseñas no coinciden.',
        'selectedLines.required' => 'Tenés que seleccionar al menos una línea.',
    ];

    public function mount()
    {
        $this->avatar = AvatarLibrary::default();
    }

    public function register(): void
    {
        $email = trim(strtolower($this->email));

        if (Agent::where('email', $email)->exists()) {
            $this->addError('email', 'Este email ya está registrado.');

            return;
        }
        if (User::where('email', $email)->exists()) {
            $this->addError('email', 'Este email ya está registrado en una cuenta.');

            return;
        }

        $this->validate();

        $username = $this->resolveUsername();
        $name = trim($this->name);
        $apellido = trim($this->apellido) ?: null;
        $roleId = Role::where('name', Roles::AGENTE)->value('id');

        $agent = Agent::create([
            'name' => $name,
            'apellido' => $apellido,
            'email' => $email,
            'username' => $username,
            'password' => Hash::make($this->password),
            'phone' => trim($this->phone) ?: null,
            'avatar' => $this->avatar,
            'status' => 'active',
            'cargo' => 'agente',
        ]);

        $user = User::create([
            'role_id' => $roleId,
            'name' => $name,
            'apellido' => $apellido,
            'email' => $email,
            'username' => $username,
            'password' => Hash::make($this->password),
            'avatar' => $this->avatar,
            'status' => 'active',
        ]);

        $agent->update(['user_id' => $user->id]);

        $lineIds = collect($this->selectedLines)->map(fn ($id) => (int) $id)->unique();

        foreach ($lineIds as $lineId) {
            $line = Line::find($lineId);
            $linePermissions = $line->permissions ?? [];

            $lineAgent = LineAgent::create([
                'line_id' => $lineId,
                'agent_id' => $agent->id,
                'role' => LineRoles::MIEMBRO,
                'is_active' => true,
            ]);

            foreach ($linePermissions as $perm) {
                LineAgentPermission::create([
                    'line_id' => $lineId,
                    'agent_id' => $agent->id,
                    'permission' => $perm,
                ]);
            }

            $this->notifyEncargadosLinea($line, $agent);
        }

        $this->registered = true;
    }

    private function notifyEncargadosLinea(Line $line, Agent $agent): void
    {
        $encargados = $line->lineAgents()
            ->where('role', LineRoles::ENCARGADO)
            ->where('is_active', true)
            ->where('agent_id', '!=', $agent->id)
            ->get();

        $displayName = trim($agent->name.' '.($agent->apellido ?? ''));
        $msg = "Nuevo agente registrado en \"{$line->name}\": {$displayName}";

        foreach ($encargados as $la) {
            $this->notifyAgent(
                (int) $la->agent_id,
                'Nuevo agente registrado',
                $msg,
                'agents',
                '/agentes',
                'success'
            );
        }
    }

    private function notifyAgent(int $agentId, string $title, string $message, string $icon, string $url, string $type = 'info'): void
    {
        Notification::create([
            'agent_id' => $agentId,
            'title' => $title,
            'message' => $message,
            'icon' => $icon,
            'url' => $url,
            'type' => $type,
        ]);
    }

    private function resolveUsername(): string
    {
        $username = trim($this->username);
        if ($username) {
            if (Agent::where('username', $username)->exists() || User::where('username', $username)->exists()) {
                $this->addError('username', 'Este username ya está en uso.');
            }

            return $username;
        }

        $base = Str::slug($this->name, '_') ?: Str::before($this->email, '@') ?: 'agente';
        $base = Str::limit($base, 50, '');
        $username = $base;
        $suffix = 1;

        while (Agent::where('username', $username)->exists() || User::where('username', $username)->exists()) {
            $username = Str::limit($base, 46, '').'_'.$suffix++;
        }

        return $username;
    }

    public function render()
    {
        $lines = Line::orderBy('name')->get(['id', 'name']);

        return view('livewire.agents.agent-register', compact('lines'))->layout('layouts.auth');
    }
}
