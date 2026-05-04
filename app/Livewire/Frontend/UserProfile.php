<?php

namespace App\Livewire\Frontend;

use App\Models\Bonus;
use App\Models\BonusAssignment;
use App\Models\Raffle;
use App\Models\UserNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class UserProfile extends Component
{
    public $tab = 'bonos'; // password / notifications / tickets / bonos / sorteo

    // Password change
    public $current_password = '';

    public $new_password = '';

    public $new_password_confirmation = '';

    public function setTab($tab)
    {
        $this->tab = $tab;
    }

    // ----- PASSWORD -----
    public function changePassword()
    {
        $this->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (! Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'La contraseña actual es incorrecta');

            return;
        }

        $user->update(['password' => Hash::make($this->new_password)]);

        $this->current_password = '';
        $this->new_password = '';
        $this->new_password_confirmation = '';

        session()->flash('message', 'Contraseña actualizada correctamente');
    }

    // ----- NOTIFICATIONS -----
    public function markNotificationRead($id)
    {
        UserNotification::where('id', $id)
            ->where('user_id', Auth::id())
            ->first()
            ?->markRead();
    }

    public function markAllRead()
    {
        UserNotification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    // ----- BONOS -----
    public function getMyBonuses()
    {
        $userId = Auth::id();

        return Bonus::visibleToUser($userId)->latest()->get();
    }

    public function getMyBonusAssignment(int $bonusId)
    {
        return BonusAssignment::where('bonus_id', $bonusId)
            ->where('user_id', Auth::id())
            ->first();
    }

    // ----- SORTEO -----
    public function getActiveRaffle()
    {
        return Raffle::where('status', 'active')
            ->with(['positions' => function ($q) {
                $q->orderBy('position');
            }])
            ->latest()
            ->first();
    }

    public function getMyNumbers()
    {
        $raffle = $this->getActiveRaffle();
        if (! $raffle) {
            return collect();
        }

        return $raffle->numbers()
            ->where('user_id', Auth::id())
            ->with('raffle')
            ->orderBy('number')
            ->get();
    }

    public function getEndedRaffleWithWinners()
    {
        return Raffle::where('status', 'ended')
            ->has('positions.winner')
            ->with(['positions' => function ($q) {
                $q->with('winner')->orderBy('position');
            }])
            ->latest('end_date')
            ->first();
    }

    public function render()
    {
        $user = Auth::user();

        $notifications = UserNotification::where('user_id', Auth::id())
            ->latest()
            ->take(50)
            ->get();

        $unreadCount = $notifications->whereNull('read_at')->count();

        $bonuses = $this->getMyBonuses();
        $activeRaffle = $this->getActiveRaffle();
        $myNumbers = $this->getMyNumbers();
        $endedRaffle = $this->getEndedRaffleWithWinners();

        $tickets = Ticket::with('messages')
            ->where('user_id', Auth::id())
            ->latest()
            ->take(20)
            ->get();

        return view('livewire.frontend.user-profile', compact(
            'user', 'notifications', 'unreadCount',
            'bonuses', 'activeRaffle', 'myNumbers', 'endedRaffle', 'tickets'
        ))->layout('layouts.frontend');
    }
}
