<?php

namespace App\Livewire;

use App\Models\Line;
use App\Models\Raffle;
use App\Models\RaffleNumber;
use App\Models\Role;
use App\Models\User;
use App\Support\ImageStorage;
use App\Support\Permissions;
use App\Support\Roles;
use App\Traits\HasLinePermissions;
use App\Traits\SendsNotifications;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;

class Sorteos extends Component
{
    use HasLinePermissions, SendsNotifications, WithFileUploads;

    public string $search = '';

    public string $filterStatus = 'all';

    public string $viewMode = 'board';

    public ?int $selectedRaffleId = null;

    public bool $showModal = false;

    public ?Raffle $editingRaffle = null;

    public string $title = '';

    public string $description = '';

    public string $status = 'inactive';

    public string $start_date = '';

    public string $start_time = '00:00';

    public string $end_date = '';

    public string $end_time = '23:59';

    public int $start_number = 1;

    public int $end_number = 1000;

    public bool $unlimitedNumbers = false;

    public string $numbersLimit = '1000';

    public string $platform_id = '';

    public array $lineIds = [];

    public array $prizes = [];

    public array $prizeUploads = [];

    public string $assignUserId = '';

    public array $selectedNumbers = [];

    public bool $showWinnerModal = false;

    public bool $finalizeOnSave = false;

    public array $winnerPrizes = [];

    public string $numbersSearch = '';

    public string $participantsSearch = '';

    public string $participantsLineFilter = 'all';

    protected function rules(): array
    {
        return [
            'title' => 'required|min:2|max:180',
            'description' => 'nullable|max:2000',
            'status' => 'required|in:active,inactive,finished',
            'start_date' => 'required|date',
            'start_time' => 'required',
            'end_date' => 'required|date',
            'end_time' => 'required',
            'start_number' => 'required|integer|min:0',
            'numbersLimit' => $this->unlimitedNumbers ? 'nullable' : 'required|integer|min:1',
            'platform_id' => 'nullable|exists:platforms,id',
            'lineIds' => 'required|array|min:1',
            'lineIds.*' => 'integer|exists:lines,id',
            'prizes' => 'array',
            'prizes.*.position' => 'nullable|integer|min:1',
            'prizes.*.name' => 'nullable|string|max:180',
            'prizeUploads.*' => 'nullable|image|mimes:png|max:4096',
        ];
    }

    public function openCreate(): void
    {
        $this->checkLinePermission(Permissions::SORTEO_CREATE);
        $this->resetForm();
        $this->start_date = now()->format('Y-m-d');
        $this->end_date = now()->addDays(30)->format('Y-m-d');
        $this->lineIds = array_filter([(int) (session('active_line_id') ?: $this->availableLines()->first()?->id)]);
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $this->checkLinePermission(Permissions::SORTEO_UPDATE);
        $raffle = Raffle::with('lines')->findOrFail($id);
        $this->editingRaffle = $raffle;
        $this->title = $raffle->title;
        $this->description = $raffle->description ?? '';
        $this->status = $raffle->status;
        $this->start_date = $raffle->start_date->format('Y-m-d');
        $this->start_time = $raffle->start_date->format('H:i');
        $this->end_date = $raffle->end_date->format('Y-m-d');
        $this->end_time = $raffle->end_date->format('H:i');
        $this->start_number = (int) $raffle->start_number;
        $this->end_number = (int) $raffle->end_number;
        $this->unlimitedNumbers = $raffle->numbers_limit === null;
        $this->numbersLimit = $raffle->numbers_limit ? (string) $raffle->numbers_limit : '';
        $this->platform_id = (string) ($raffle->platform_id ?? '');
        $this->lineIds = $raffle->lines->pluck('id')->map(fn ($id) => (int) $id)->toArray() ?: array_filter([(int) $raffle->line_id]);
        $this->prizes = collect($raffle->prizes ?? [])->map(fn ($prize) => [
            'position' => (string) ($prize['position'] ?? ''),
            'name' => $prize['name'] ?? '',
            'image' => $prize['image'] ?? '',
        ])->values()->toArray();
        $this->prizeUploads = [];

        if (empty($this->prizes)) {
            $this->prizes = [['position' => '1', 'name' => '', 'image' => '']];
        }

        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->editingRaffle = null;
        $this->resetForm();
    }

    public function resetForm(): void
    {
        $this->title = '';
        $this->description = '';
        $this->status = 'inactive';
        $this->start_date = '';
        $this->start_time = '00:00';
        $this->end_date = '';
        $this->end_time = '23:59';
        $this->start_number = 1;
        $this->end_number = 1000;
        $this->unlimitedNumbers = false;
        $this->numbersLimit = '1000';
        $this->platform_id = '';
        $this->lineIds = [];
        $this->prizes = [['position' => '1', 'name' => '', 'image' => '']];
        $this->prizeUploads = [];
        $this->resetValidation();
    }

    public function save(): void
    {
        $this->compactEmptyPrizes();
        $this->lineIds = collect($this->lineIds)
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values()
            ->toArray();
        $this->validate();
        $this->authorizeLineChoices();

        $start = Carbon::parse($this->start_date.' '.$this->start_time);
        $end = Carbon::parse($this->end_date.' '.$this->end_time);

        if ($end->lt($start)) {
            $this->addError('end_date', 'La fecha y hora de fin debe ser posterior al inicio.');
            return;
        }

        $numbersLimit = $this->unlimitedNumbers ? null : max(1, (int) $this->numbersLimit);
        $endNumber = $numbersLimit ? ((int) $this->start_number + $numbersLimit - 1) : max((int) $this->end_number, (int) $this->start_number + 999);

        $data = [
            'title' => trim($this->title),
            'description' => trim($this->description) ?: null,
            'status' => $this->status,
            'start_date' => $start,
            'end_date' => $end,
            'start_number' => $this->start_number,
            'end_number' => $endNumber,
            'numbers_limit' => $numbersLimit,
            'platform_id' => $this->platform_id ?: null,
            'line_id' => $this->lineIds[0] ?? session('active_line_id'),
            'prizes' => $this->normalizedPrizes(),
        ];

        DB::transaction(function () use ($data): void {
            if ($this->editingRaffle) {
                $this->checkLinePermission(Permissions::SORTEO_UPDATE);
                $this->editingRaffle->update($data);
                $this->editingRaffle->lines()->sync($this->lineIds);
                session()->flash('message', 'Sorteo actualizado');
                $this->notify('Sorteo actualizado', "El sorteo {$this->editingRaffle->title} fue actualizado.", 'raffles', '/sorteos', 'info');

                return;
            }

            $this->checkLinePermission(Permissions::SORTEO_CREATE);
            $raffle = Raffle::create($data);
            $raffle->lines()->sync($this->lineIds);
            session()->flash('message', 'Sorteo creado');
            $this->notify('Nuevo sorteo creado', "El sorteo {$raffle->title} fue creado exitosamente.", 'raffles', '/sorteos', 'success');
        });

        $this->closeModal();
    }

    public function delete(int $id): void
    {
        $this->checkLinePermission(Permissions::SORTEO_DELETE);
        $raffle = Raffle::findOrFail($id);
        $raffleTitle = $raffle->title;
        $raffle->delete();

        if ($this->selectedRaffleId === $id) {
            $this->selectedRaffleId = null;
        }

        session()->flash('message', 'Sorteo eliminado');
        $this->notify('Sorteo eliminado', "El sorteo {$raffleTitle} fue eliminado del sistema.", 'raffles', '/sorteos', 'danger');
    }

    public function selectRaffle(int $id): void
    {
        $raffle = Raffle::findOrFail($id);
        $allowed = $this->availableLines()->pluck('id');
        $raffleLineIds = $raffle->lines()->pluck('lines.id')->push($raffle->line_id)->filter()->unique();
        if (! $this->isAdminMode() && $raffleLineIds->intersect($allowed)->isEmpty()) {
            abort(403, 'Sin acceso a este sorteo.');
        }

        $this->selectedRaffleId = $id;
        $this->assignUserId = '';
        $this->numbersSearch = '';
        $this->participantsSearch = '';
        $this->participantsLineFilter = 'all';
        $this->selectedNumbers = [];
    }

    public function clearSelectedNumbers(): void
    {
        $this->selectedNumbers = [];
    }

    public function assignSelectedNumbers(): void
    {
        $this->saveSelectedNumbers();
    }

    public function saveSelectedNumbersFromClient(array $numbers): void
    {
        $this->selectedNumbers = $this->normalizeSelectedNumbers($numbers);
        $this->saveSelectedNumbers();
    }

    public function saveSelectedNumbers(): void
    {
        $this->checkLinePermission(Permissions::SORTEO_READ);
        $this->validate([
            'assignUserId' => 'required|integer|exists:users,id',
            'selectedNumbers' => 'required|array|min:1',
            'selectedNumbers.*' => 'integer',
        ]);

        $raffle = Raffle::findOrFail($this->selectedRaffleId);
        $user = User::find((int) $this->assignUserId);

        if (! $user || ! $this->canAssignInRaffle($raffle)) {
            return;
        }

        if (! $this->userCanBeAssignedToRaffle($user, $raffle)) {
            $this->addError('assignUserId', 'Selecciona un cliente activo.');

            return;
        }

        $numbers = collect($this->selectedNumbers)
            ->map(fn ($number) => (int) $number)
            ->unique()
            ->sort()
            ->values();

        if ($numbers->isEmpty()) {
            session()->flash('info', 'Selecciona al menos un numero del tablero.');
            return;
        }

        $maxNumber = $this->boardEndNumber($raffle);
        $errors = [];
        $createdCount = 0;
        $updatedCount = 0;
        $lineId = $this->assignmentLineId($raffle);
        $occupied = RaffleNumber::where('raffle_id', $raffle->id)
            ->whereIn('number', $numbers)
            ->get()
            ->keyBy('number');

        foreach ($numbers as $number) {
            if ($number < $raffle->start_number || $number > $maxNumber) {
                $errors[] = "Numero {$number} fuera del limite disponible";
                continue;
            }

            $existingNumber = $occupied->get($number);

            if ($existingNumber) {
                if ((int) $existingNumber->user_id !== (int) $user->id || (int) $existingNumber->line_id !== (int) $lineId) {
                    $existingNumber->update([
                        'user_id' => $user->id,
                        'line_id' => $lineId,
                    ]);
                    $updatedCount++;
                }

                continue;
            }

            RaffleNumber::create([
                'raffle_id' => $raffle->id,
                'user_id' => $user->id,
                'line_id' => $lineId,
                'number' => $number,
            ]);
            $createdCount++;
        }

        $changedCount = $createdCount + $updatedCount;

        if ($changedCount > 0) {
            $message = trim(collect([
                $createdCount > 0 ? "{$createdCount} asignado(s)" : null,
                $updatedCount > 0 ? "{$updatedCount} reasignado(s)" : null,
            ])->filter()->join(', '));

            session()->flash('message', "Seleccion guardada: {$message}.");
            $this->notify('Numeros guardados', "{$changedCount} numero(s) guardados para el cliente {$user->name}.", 'raffles', '/sorteos', 'success');
            $this->selectedNumbers = [];
        } else {
            session()->flash('info', 'La seleccion ya estaba asignada a ese cliente.');
        }

        if (! empty($errors)) {
            session()->flash('error', implode('. ', $errors));
        }
    }

    public function unassignSelectedNumbersFromClient(array $numbers): void
    {
        $this->selectedNumbers = $this->normalizeSelectedNumbers($numbers);
        $this->unassignSelectedNumbers();
    }

    public function unassignSelectedNumbers(): void
    {
        $this->checkLinePermission(Permissions::SORTEO_READ);
        $this->validate([
            'selectedNumbers' => 'required|array|min:1',
            'selectedNumbers.*' => 'integer',
        ]);

        $raffle = Raffle::findOrFail($this->selectedRaffleId);

        $occupiedSelected = RaffleNumber::where('raffle_id', $raffle->id)
            ->whereIn('number', collect($this->selectedNumbers)->map(fn ($number) => (int) $number)->unique()->values())
            ->pluck('number')
            ->map(fn ($number) => (int) $number);

        if ($occupiedSelected->isEmpty()) {
            session()->flash('info', 'No hay numeros asignados dentro de la seleccion para desasignar.');
            return;
        }

        $deleted = RaffleNumber::where('raffle_id', $raffle->id)
            ->whereIn('number', $occupiedSelected)
            ->delete();

        $this->selectedNumbers = collect($this->selectedNumbers)
            ->map(fn ($number) => (int) $number)
            ->diff($occupiedSelected)
            ->values()
            ->toArray();

        if ($deleted > 0) {
            session()->flash('message', "{$deleted} numero(s) desasignados correctamente");
            $this->notify('Numeros desasignados', "{$deleted} numero(s) desasignados del sorteo {$raffle->title}.", 'raffles', '/sorteos', 'warning');
            return;
        }

        session()->flash('info', 'No habia numeros asignados dentro de la seleccion.');
    }

    public function addConsecutiveNumbers(int $qty): void
    {
        $this->checkLinePermission(Permissions::SORTEO_READ);

        if (! $this->selectedRaffleId) {
            return;
        }

        $raffle = Raffle::with('numbers')->findOrFail($this->selectedRaffleId);
        $end = $this->boardEndNumber($raffle);
        $taken = $raffle->numbers->pluck('number')->map(fn ($n) => (int) $n)->flip();
        $alreadySelected = collect($this->selectedNumbers)->map(fn ($n) => (int) $n)->flip();

        $added = 0;
        for ($n = (int) $raffle->start_number; $n <= $end && $added < $qty; $n++) {
            if ($taken->has($n) || $alreadySelected->has($n)) {
                continue;
            }
            $this->selectedNumbers[] = $n;
            $alreadySelected->put($n, true);
            $added++;
        }

        $this->selectedNumbers = collect($this->selectedNumbers)
            ->map(fn ($n) => (int) $n)
            ->unique()
            ->sort()
            ->values()
            ->toArray();
    }

    public function toggleNumber(int $number): void
    {
        $this->checkLinePermission(Permissions::SORTEO_READ);

        if (! $this->selectedRaffleId) {
            return;
        }

        $raffle = Raffle::findOrFail($this->selectedRaffleId);

        if ($number < $raffle->start_number || $number > $this->boardEndNumber($raffle)) {
            return;
        }

        $selected = collect($this->selectedNumbers)->map(fn ($value) => (int) $value);

        if ($selected->contains($number)) {
            $this->selectedNumbers = $selected->reject(fn ($value) => $value === $number)->values()->toArray();
            return;
        }

        $this->selectedNumbers = $selected->push($number)->unique()->sort()->values()->toArray();
    }

    private function normalizeSelectedNumbers(array $numbers): array
    {
        return collect($numbers)
            ->map(fn ($number) => (int) $number)
            ->filter(fn ($number) => $number >= 0)
            ->unique()
            ->sort()
            ->values()
            ->toArray();
    }

    public function removeNumber(int $numberId): void
    {
        $this->checkLinePermission(Permissions::SORTEO_UPDATE);
        RaffleNumber::findOrFail($numberId)->delete();
        session()->flash('message', 'Numero eliminado');
    }

    public function openWinnerModal(int $raffleId): void
    {
        $this->checkLinePermission(Permissions::SORTEO_UPDATE);
        $raffle = Raffle::findOrFail($raffleId);
        $this->selectedRaffleId = $raffleId;
        $this->finalizeOnSave = false;
        $this->winnerPrizes = collect($raffle->prizes ?? [])
            ->map(fn ($prize, $i) => [
                'position'    => (int) ($prize['position'] ?? $i + 1),
                'name'        => $prize['name'] ?? '',
                'image'       => $prize['image'] ?? null,
                'winner_number' => (string) ($prize['winner_number'] ?? ''),
            ])
            ->values()
            ->toArray();

        if (empty($this->winnerPrizes)) {
            $this->winnerPrizes = [['position' => 1, 'name' => 'Premio principal', 'image' => null, 'winner_number' => '']];
        }

        $this->showWinnerModal = true;
    }

    public function saveWinner(): void
    {
        $this->checkLinePermission(Permissions::SORTEO_UPDATE);
        $this->validate([
            'winnerPrizes'                   => 'array',
            'winnerPrizes.*.winner_number'   => 'nullable|integer',
        ]);

        $raffle = Raffle::with('numbers.user', 'numbers.line')->findOrFail($this->selectedRaffleId);
        $numbersByValue = $raffle->numbers->keyBy('number');

        $firstWinnerUserId = null;
        $firstWinnerNumber = null;

        $prizes = collect($raffle->prizes ?? [])
            ->values()
            ->map(function ($prize, $index) use ($raffle, $numbersByValue, &$firstWinnerUserId, &$firstWinnerNumber) {
                $raw = trim((string) ($this->winnerPrizes[$index]['winner_number'] ?? ''));
                $numberModel = $raw !== '' ? $numbersByValue->get((int) $raw) : null;

                if ($numberModel && ! $firstWinnerUserId) {
                    $firstWinnerUserId = (int) $numberModel->user_id;
                    $firstWinnerNumber = (int) $raw;
                }

                return array_merge($prize, [
                    'winner_number'               => $raw !== '' ? (int) $raw : null,
                    'winner_user_id'              => $numberModel?->user_id,
                    'winner_line_id'              => $numberModel?->line_id,
                    'winner_username'             => $numberModel?->user?->username,
                    'winner_name'                 => $numberModel?->user?->name,
                    'winner_line_name'            => $numberModel?->line?->name,
                    'winner_participations_count' => $numberModel
                        ? $raffle->numbers->where('user_id', $numberModel->user_id)->count()
                        : null,
                    'winner_awarded_at'           => $raw !== '' ? now()->toDateTimeString() : null,
                ]);
            })
            ->toArray();

        $updateData = [
            'prizes'         => $prizes,
            'winner_user_id' => $firstWinnerUserId,
            'winner_number'  => $firstWinnerNumber,
        ];

        if ($this->finalizeOnSave) {
            $updateData['status'] = 'finished';
        }

        $raffle->update($updateData);

        $this->showWinnerModal = false;
        $this->winnerPrizes = [];
        $this->finalizeOnSave = false;

        $message = $this->finalizeOnSave ? 'Sorteo finalizado' : 'Resultados registrados';
        session()->flash('message', $message);
        $this->notify('Resultados registrados', "Se cargaron los resultados del sorteo {$raffle->title}.", 'raffles', '/sorteos', 'success');
    }

    public function reopenRaffle(int $id): void
    {
        $this->checkLinePermission(Permissions::SORTEO_UPDATE);
        Raffle::findOrFail($id)->update(['status' => 'inactive']);
        session()->flash('message', 'Sorteo reabierto');
    }

    public function toggleStatus(int $id): void
    {
        $this->checkLinePermission(Permissions::SORTEO_UPDATE);
        $raffle = Raffle::findOrFail($id);

        if ($raffle->isFinished()) {
            return;
        }

        $raffle->update(['status' => $raffle->status === 'active' ? 'inactive' : 'active']);
        session()->flash('message', 'Estado actualizado');
        $this->notify('Estado de sorteo cambiado', "El sorteo {$raffle->title} fue ".($raffle->status === 'active' ? 'activado' : 'pausado').'.', 'raffles', '/sorteos', 'warning');
    }

    public function addPrize(): void
    {
        $this->prizes[] = ['position' => (string) (count($this->prizes) + 1), 'name' => '', 'image' => ''];
    }

    public function removePrize(int $index): void
    {
        ImageStorage::delete($this->prizes[$index]['image'] ?? null);
        unset($this->prizes[$index], $this->prizeUploads[$index]);
        $this->prizes = array_values($this->prizes);
        $this->prizeUploads = array_values($this->prizeUploads);
    }

    public function removePrizeImage(int $index): void
    {
        ImageStorage::delete($this->prizes[$index]['image'] ?? null);
        $this->prizes[$index]['image'] = '';
        unset($this->prizeUploads[$index]);
    }

    public function getRaffles()
    {
        $this->backfillRaffleLines();

        return Raffle::with(['lines', 'platform'])->withCount('numbers')
            ->when($this->filterStatus !== 'all', fn ($q) => $q->where('status', $this->filterStatus))
            ->when($this->search, fn ($q) => $q->where('title', 'like', '%'.$this->search.'%'))
            ->latest()
            ->get();
    }

    public function getSelectedRaffle(): ?Raffle
    {
        if (! $this->selectedRaffleId) {
            return null;
        }

        return Raffle::with(['winner', 'lines', 'platform', 'numbers' => function ($q) {
            $q->with(['user', 'line'])
                ->when($this->numbersSearch, function ($nq) {
                    $search = '%'.$this->numbersSearch.'%';
                    $nq->whereHas('user', fn ($uq) => $uq
                        ->where('name', 'like', $search)
                        ->orWhere('username', 'like', $search)
                        ->orWhere('email', 'like', $search));
                })
                ->orderBy('number');
        }])->find($this->selectedRaffleId);
    }

    public function render()
    {
        $this->checkLinePermission(Permissions::SORTEO_READ);
        $raffles = $this->getRaffles();
        $selectedRaffle = $this->getSelectedRaffle();
        $users = $this->assignableUsers($selectedRaffle);
        $participants = $this->participants();
        $totalHistorical = Raffle::withoutGlobalScopes()->count();
        $availableLines = $this->availableLines();
        $assignmentLine = Line::find($this->assignmentLineId($selectedRaffle));

        $line = Line::find(session('active_line_id'));
        $platforms = $line ? $line->platforms : collect();

        return view('livewire.sorteos', compact(
            'raffles',
            'selectedRaffle',
            'users',
            'platforms',
            'participants',
            'totalHistorical',
            'availableLines',
            'assignmentLine'
        ))->layout('layouts.dashboard');
    }

    private function normalizedPrizes(): array
    {
        $existingPrizes = collect($this->editingRaffle?->prizes ?? [])->keyBy('position');

        return collect($this->prizes)
            ->map(function ($prize, $index) use ($existingPrizes) {
                $image = $prize['image'] ?? '';

                if (isset($this->prizeUploads[$index]) && $this->prizeUploads[$index]) {
                    $image = ImageStorage::store($this->prizeUploads[$index], 'sorteos/premios', $image ?: null);
                }

                $position = (int) ($prize['position'] ?? $index + 1);
                $existing = $existingPrizes->get($position, []);

                return [
                    'position'                    => $position,
                    'name'                        => trim($prize['name'] ?? ''),
                    'image'                       => $image ?: null,
                    'winner_number'               => $existing['winner_number'] ?? null,
                    'winner_user_id'              => $existing['winner_user_id'] ?? null,
                    'winner_line_id'              => $existing['winner_line_id'] ?? null,
                    'winner_username'             => $existing['winner_username'] ?? null,
                    'winner_name'                 => $existing['winner_name'] ?? null,
                    'winner_line_name'            => $existing['winner_line_name'] ?? null,
                    'winner_participations_count' => $existing['winner_participations_count'] ?? null,
                    'winner_awarded_at'           => $existing['winner_awarded_at'] ?? null,
                ];
            })
            ->filter(fn ($prize) => $prize['position'] > 0 && $prize['name'] !== '')
            ->sortBy('position')
            ->values()
            ->toArray();
    }

    private function compactEmptyPrizes(): void
    {
        $prizes = [];
        $prizeUploads = [];

        foreach ($this->prizes as $index => $prize) {
            $upload = $this->prizeUploads[$index] ?? null;
            $normalized = [
                'position' => $prize['position'] ?? '',
                'name' => trim($prize['name'] ?? ''),
                'image' => $prize['image'] ?? '',
            ];

            if ($normalized['name'] === '' && $normalized['image'] === '' && ! $upload) {
                continue;
            }

            $prizes[] = $normalized;
            $prizeUploads[count($prizes) - 1] = $upload;
        }

        $this->prizes = $prizes;
        $this->prizeUploads = $prizeUploads;
    }

    private function availableLines()
    {
        if ($this->isAdminMode()) {
            return Line::orderBy('name')->get();
        }

        return Line::whereHas('lineAgents', fn ($query) => $query
            ->where('agent_id', session('active_agent_id'))
            ->where('is_active', true)
        )->orderBy('name')->get();
    }

    private function authorizeLineChoices(): void
    {
        $allowed = $this->availableLines()->pluck('id');

        if (collect($this->lineIds)->map(fn ($id) => (int) $id)->diff($allowed)->isNotEmpty()) {
            abort(403, 'No podes crear sorteos para lineas fuera de tu alcance.');
        }
    }

    private function assignmentLineId(?Raffle $raffle = null): ?int
    {
        $lineId = session('active_line_id');

        if ($lineId && (! $raffle || $this->lineParticipates($raffle, (int) $lineId))) {
            return (int) $lineId;
        }

        if ($raffle) {
            $raffleLineId = $raffle->lines()->value('lines.id') ?: $raffle->line_id;

            return $raffleLineId ? (int) $raffleLineId : null;
        }

        return (int) ($this->availableLines()->first()?->id ?: 0) ?: null;
    }

    private function lineParticipates(Raffle $raffle, ?int $lineId): bool
    {
        if (! $lineId) {
            return false;
        }

        return $raffle->lines()->where('lines.id', $lineId)->exists()
            || (int) $raffle->line_id === (int) $lineId;
    }

    private function boardEndNumber(Raffle $raffle): int
    {
        if ($raffle->numbers_limit) {
            return (int) $raffle->start_number + (int) $raffle->numbers_limit - 1;
        }

        return min((int) $raffle->end_number, (int) $raffle->start_number + 999);
    }

    private function canAssignInRaffle(Raffle $raffle): bool
    {
        if (! $this->lineParticipates($raffle, $this->assignmentLineId($raffle))) {
            session()->flash('error', 'La linea activa no participa de este sorteo.');
            return false;
        }

        return true;
    }

    private function assignableUsers(?Raffle $raffle)
    {
        $clientRoleId = Role::where('name', Roles::CLIENTE)->value('id');

        return User::query()
            ->when($clientRoleId, fn ($query) => $query->where('role_id', $clientRoleId))
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'username', 'name', 'email']);
    }

    private function userCanBeAssignedToRaffle(User $user, Raffle $raffle): bool
    {
        $lineId = $this->assignmentLineId($raffle);

        if (! $lineId) {
            return false;
        }

        $clientRoleId = Role::where('name', Roles::CLIENTE)->value('id');

        return (! $clientRoleId || (int) $user->role_id === (int) $clientRoleId)
            && $user->status === 'active';
    }

    private function participants()
    {
        if (! $this->selectedRaffleId) {
            return collect();
        }

        return RaffleNumber::with(['user', 'line'])
            ->where('raffle_id', $this->selectedRaffleId)
            ->when($this->participantsLineFilter !== 'all', fn ($query) => $query->where('line_id', $this->participantsLineFilter))
            ->when($this->participantsSearch, function ($query) {
                $search = '%'.$this->participantsSearch.'%';
                $query->whereHas('user', fn ($user) => $user
                    ->where('id', $this->participantsSearch)
                    ->orWhere('username', 'like', $search)
                    ->orWhere('name', 'like', $search)
                    ->orWhere('email', 'like', $search));
            })
            ->latest()
            ->take(10)
            ->get()
            ->map(function (RaffleNumber $number) {
                $number->total_for_user = RaffleNumber::where('raffle_id', $number->raffle_id)
                    ->where('user_id', $number->user_id)
                    ->count();

                return $number;
            });
    }

    private function backfillRaffleLines(): void
    {
        Raffle::withoutGlobalScopes()
            ->whereDoesntHave('lines')
            ->whereNotNull('line_id')
            ->get()
            ->each(fn (Raffle $raffle) => $raffle->lines()->syncWithoutDetaching([(int) $raffle->line_id]));
    }
}
