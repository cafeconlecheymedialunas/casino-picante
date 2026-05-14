<?php

namespace App\Livewire\Frontend;

use App\Models\BonusAssignment;
use App\Models\Line;
use App\Models\LineRating;
use App\Models\Post;
use App\Models\Raffle;
use App\Models\RaffleNumber;
use App\Models\Ticket;
use App\Models\TicketMessage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

class ClientAccount extends Component
{
    public string $activeTab = 'perfil';

    public string $name = '';

    public string $apellido = '';

    public string $username = '';

    public string $email = '';

    public string $phone = '';

    public string $contact = '';

    public string $avatar = '';

    public int $preferred_line_id = 0;

    public string $current_password = '';

    public string $password = '';

    public string $password_confirmation = '';

    public string $ticket_subject = '';

    public string $ticket_category = '';

    public int $ticket_line_id = 0;

    public string $ticket_description = '';

    protected $rules = [
        'name' => ['required', 'string', 'min:2', 'max:255'],
        'username' => ['required', 'string', 'min:3', 'max:40', 'alpha_dash'],
        'email' => ['required', 'email', 'max:255'],
        'phone' => ['nullable', 'string', 'max:50'],
        'contact' => ['nullable', 'string', 'max:500'],
        'avatar' => ['nullable', 'string'],
        'preferred_line_id' => ['nullable', 'integer', 'exists:lines,id'],
    ];

    protected $messages = [
        'name.required' => 'El nombre es obligatorio.',
        'username.required' => 'El nombre de cliente es obligatorio.',
        'username.alpha_dash' => 'Solo letras, números, guiones y guion bajo.',
        'email.required' => 'El email es obligatorio.',
        'email.unique' => 'Ese email ya está registrado.',
        'phone.max' => 'Máximo 50 caracteres.',
        'contact.max' => 'Máximo 500 caracteres.',
    ];

    public function mount(): void
    {
        $user = auth()->user();
        if (! $user) {
            $this->redirectRoute('login', navigate: true);

            return;
        }
        if (! $user->hasRole('cliente')) {
            $this->redirectRoute('dashboard', navigate: true);

            return;
        }

        $this->name = $user->name ?? '';
        $this->apellido = $user->apellido ?? '';
        $this->username = $user->username ?? '';
        $this->email = $user->email ?? '';
        $this->phone = $user->phone ?? '';
        $this->contact = $user->contact ?? '';
        $this->avatar = $user->avatar ?? '';
        $this->preferred_line_id = $user->line_id ?? 0;
    }

    public function saveProfile(): void
    {
        $user = auth()->user();
        if (! $user) {
            return;
        }

        $validated = $this->validate([
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'username' => ['required', 'string', 'min:3', 'max:40', 'alpha_dash', "unique:users,username,{$user->id}"],
            'email' => ['required', 'email', 'max:255', "unique:users,email,{$user->id}"],
            'phone' => ['nullable', 'string', 'max:50'],
            'contact' => ['nullable', 'string', 'max:500'],
            'avatar' => ['nullable', 'string'],
            'preferred_line_id' => ['nullable', 'integer', 'exists:lines,id'],
        ], [], [
            'name' => 'Nombre',
            'username' => 'Nombre de Cliente',
            'email' => 'Email',
            'phone' => 'Celular',
            'contact' => 'Contacto extra',
            'avatar' => 'Avatar',
            'preferred_line_id' => 'Línea preferida',
        ]);

        $user->update($validated);
        session()->flash('client_message', 'Tus datos personales fueron actualizados.');
    }

    public function savePassword(): void
    {
        $user = auth()->user();
        if (! $user) {
            return;
        }

        $validated = $this->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'password_confirmation' => ['required'],
        ], [
            'current_password.required' => 'La contraseña actual es obligatoria.',
            'password.required' => 'La nueva contraseña es obligatoria.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password_confirmation.required' => 'Confirma tu nueva contraseña.',
        ]);

        if (! Hash::check($validated['current_password'], $user->password)) {
            $this->addError('current_password', 'La contraseña actual es incorrecta.');

            return;
        }

        $user->update(['password' => $validated['password']]);
        $this->reset('current_password', 'password', 'password_confirmation');
        session()->flash('client_message', 'Tu contraseña fue actualizada.');
    }

    public function createTicket(): void
    {
        $user = auth()->user();
        if (! $user) {
            return;
        }

        $validated = $this->validate([
            'ticket_subject' => ['required', 'string', 'min:3', 'max:255'],
            'ticket_category' => ['required', 'in:juego,bono,sorteo,atencion,otro'],
            'ticket_line_id' => ['nullable', 'integer', 'exists:lines,id'],
            'ticket_description' => ['required', 'string', 'min:5', 'max:2000'],
        ], [
            'ticket_subject.required' => 'El asunto es obligatorio.',
            'ticket_category.required' => 'Seleccioná una categoría.',
            'ticket_description.required' => 'La descripción es obligatoria.',
        ]);

        $lineId = $validated['ticket_line_id'] ?: ($user->line_id ?: null);

        $ticket = Ticket::create([
            'user_id' => $user->id,
            'line_id' => $lineId,
            'subject' => trim($validated['ticket_subject']),
            'status' => 'open',
            'priority' => 'medium',
        ]);

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'message' => trim($validated['ticket_description']),
        ]);

        $this->reset('ticket_subject', 'ticket_category', 'ticket_line_id', 'ticket_description');
        session()->flash('ticket_success', 'Ticket creado. Código: '.$ticket->tracking_code);
    }

    public function render()
    {
        $user = auth()->user();

        $activeRaffleIds = Raffle::withoutGlobalScopes()
            ->where('status', 'active')
            ->pluck('id');

        return view('frontend.pages.client-account', [
            'myNumbers' => RaffleNumber::with('raffle')
                ->where('user_id', $user->id)
                ->latest()
                ->take(12)
                ->get(),
            'activeNumbersCount' => RaffleNumber::where('user_id', $user->id)
                ->whereIn('raffle_id', $activeRaffleIds)
                ->count(),
            'pendingCommentsCount' => Post::query()
                ->whereHas('comments', fn ($q) => $q->where('user_id', $user->id)->where('is_approved', false))
                ->count(),
            'ratingsCount' => LineRating::where('user_id', $user->id)->count(),
            'myTickets' => Ticket::where('user_id', $user->id)
                ->with('line')
                ->latest()
                ->take(6)
                ->get(),
            'recentBonuses' => BonusAssignment::where('user_id', $user->id)
                ->where('created_at', '>=', now()->subWeek())
                ->with('bonus')
                ->latest()
                ->get(),
            'availableLines' => Line::with('activePlatforms')
                ->where('status', 'active')
                ->orderBy('name')
                ->get(),
        ])->layout('frontend.layouts.app');
    }
}
