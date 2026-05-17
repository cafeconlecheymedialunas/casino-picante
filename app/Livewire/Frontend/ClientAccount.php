<?php

namespace App\Livewire\Frontend;

use App\Models\BonusAssignment;
use App\Models\Line;
use App\Models\Raffle;
use App\Models\RaffleNumber;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\UserNotification;
use App\Support\AvatarLibrary;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

class ClientAccount extends Component
{
    public string $activeTab = 'perfil';

    public string $ticketFilter = 'all';

    public string $bonusFilter = 'all';

    public int $numbersPage = 1;

    public bool $showTicketForm = true;

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

    public function setTab(string $tab): void
    {
        if (in_array($tab, ['perfil', 'password', 'tickets', 'sorteo', 'bonos', 'todos_bonos', 'notificaciones'], true)) {
            $this->activeTab = $tab;
        }
    }

    public function setTicketFilter(string $filter): void
    {
        $this->ticketFilter = $filter;
    }

    public function setBonusFilter(string $filter): void
    {
        $this->bonusFilter = $filter;
    }

    public function closeTicket(int $ticketId): void
    {
        $ticket = Ticket::where('user_id', auth()->id())->whereKey($ticketId)->first();
        if ($ticket && $ticket->status !== 'closed') {
            $ticket->update(['status' => 'closed']);
            session()->flash('ticket_success', 'Ticket cerrado correctamente.');
        }
    }

    public function markNotificationRead(int $notificationId): void
    {
        UserNotification::where('user_id', auth()->id())
            ->whereKey($notificationId)
            ->first()
            ?->markRead();
    }

    public function markAllNotificationsRead(): void
    {
        UserNotification::where('user_id', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    protected $rules = [
        'name' => ['required', 'string', 'min:2', 'max:255'],
        'apellido' => ['nullable', 'string', 'max:255'],
        'username' => ['required', 'string', 'min:3', 'max:40', 'alpha_dash'],
        'email' => ['required', 'email', 'max:255'],
        'phone' => ['nullable', 'string', 'max:50'],
        'contact' => ['nullable', 'string', 'max:500'],
        'avatar' => ['nullable', 'string'],
        'preferred_line_id' => ['nullable', 'integer', 'min:0'],
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
            'apellido' => ['nullable', 'string', 'max:255'],
            'username' => ['required', 'string', 'min:3', 'max:40', 'alpha_dash', "unique:users,username,{$user->id}"],
            'email' => ['required', 'email', 'max:255', "unique:users,email,{$user->id}"],
            'phone' => ['nullable', 'string', 'max:50'],
            'contact' => ['nullable', 'string', 'max:500'],
            'avatar' => ['nullable', 'string', function ($attribute, $value, $fail) {
                if ($value && ! AvatarLibrary::isValid($value)) {
                    $fail('Selecciona un avatar valido.');
                }
            }],
            'preferred_line_id' => ['nullable', 'integer', 'min:0'],
        ], [], [
            'name' => 'Nombre',
            'apellido' => 'Apellido',
            'username' => 'Nombre de Cliente',
            'email' => 'Email',
            'phone' => 'Celular',
            'contact' => 'Contacto extra',
            'avatar' => 'Avatar',
            'preferred_line_id' => 'Línea preferida',
        ]);

        if (($validated['preferred_line_id'] ?? 0) > 0 && ! Line::whereKey($validated['preferred_line_id'])->where('status', 'active')->exists()) {
            $this->addError('preferred_line_id', 'Selecciona una linea valida.');

            return;
        }

        $user->update([
            'name' => $validated['name'],
            'apellido' => $validated['apellido'] ?? null,
            'username' => $validated['username'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'contact' => $validated['contact'] ?? null,
            'avatar' => $validated['avatar'] ?: AvatarLibrary::default(),
            'line_id' => $validated['preferred_line_id'] ?: null,
        ]);
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
            'ticket_line_id' => ['nullable', 'integer', 'min:0'],
            'ticket_description' => ['required', 'string', 'min:5', 'max:2000'],
        ], [
            'ticket_subject.required' => 'El asunto es obligatorio.',
            'ticket_category.required' => 'Seleccioná una categoría.',
            'ticket_description.required' => 'La descripción es obligatoria.',
        ]);

        if (($validated['ticket_line_id'] ?? 0) > 0 && ! Line::whereKey($validated['ticket_line_id'])->where('status', 'active')->exists()) {
            $this->addError('ticket_line_id', 'Selecciona una linea valida.');

            return;
        }

        $lineId = $validated['ticket_line_id'] ?: ($user->line_id ?: null);
        if ($lineId && ! Line::whereKey($lineId)->where('status', 'active')->exists()) {
            $lineId = null;
        }

        $ticket = Ticket::create([
            'user_id' => $user->id,
            'line_id' => $lineId,
            'subject' => trim($validated['ticket_subject']),
            'category' => $validated['ticket_category'],
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
        $this->showTicketForm = false;
    }

    public function showTicketForm(): void
    {
        $this->showTicketForm = true;
    }

    public function render()
    {
        $user = auth()->user();

        $activeRaffleIds = Raffle::withoutGlobalScopes()
            ->where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->pluck('id');

        $bonusQuery = BonusAssignment::where('user_id', $user->id)->with('bonus.line');

        $recentBonuses = (clone $bonusQuery)
            ->where('created_at', '>=', now()->subWeek())
            ->latest()
            ->get();

        $allBonuses = (clone $bonusQuery)->latest()->get()->map(function ($assignment) {
            $bonus = $assignment->bonus;
            $status = $assignment->status;
            if ($status === 'active' && $bonus?->end_date?->isPast()) {
                $status = 'expired';
            }
            $assignment->computed_status = $status;

            return $assignment;
        });

        $filteredBonuses = $allBonuses->filter(function ($assignment) {
            if ($this->bonusFilter === 'all') {
                return true;
            }

            return $assignment->computed_status === $this->bonusFilter;
        })->take(50);

        $ticketQuery = Ticket::where('user_id', $user->id)->with(['line', 'messages']);
        $myTickets = (clone $ticketQuery)->latest()->take(20)->get();
        $filteredTickets = $myTickets->filter(function ($ticket) {
            if ($this->ticketFilter === 'all') {
                return true;
            }

            return $ticket->status === $this->ticketFilter;
        })->take(20);

        return view('frontend.pages.client-account', [
            'myNumbers' => RaffleNumber::with(['raffle', 'line'])
                ->where('user_id', $user->id)
                ->latest()
                ->take(50)
                ->get(),
            'activeNumbersCount' => RaffleNumber::where('user_id', $user->id)
                ->whereIn('raffle_id', $activeRaffleIds)
                ->count(),
            'myTickets' => $filteredTickets,
            'allTicketsCount' => $myTickets->count(),
            'recentBonuses' => $recentBonuses,
            'allBonuses' => $filteredBonuses,
            'allBonusesCount' => $allBonuses->count(),
            'notifications' => UserNotification::where('user_id', $user->id)
                ->latest()
                ->take(8)
                ->get(),
            'unreadNotificationsCount' => UserNotification::where('user_id', $user->id)
                ->whereNull('read_at')
                ->count(),
            'availableLines' => Line::with('activePlatforms')
                ->where('status', 'active')
                ->orderBy('name')
                ->get(),
        ])->layout('frontend.layouts.app');
    }
}
