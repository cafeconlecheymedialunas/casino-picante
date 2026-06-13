<?php

namespace App\Livewire;

use App\Models\Bonus;
use App\Models\HomeSection;
use App\Models\Line;
use App\Models\Post;
use App\Models\Raffle;
use App\Models\Vendor;
use App\Support\FontAwesomeIcons;
use App\Support\ImageStorage;
use App\Support\Permissions;
use App\Support\PublicPageContent;
use App\Traits\HasLinePermissions;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.dashboard')]
class EditorPages extends Component
{
    use HasLinePermissions, WithFileUploads;

    public string $page = 'lineas';

    public array $pageData = [];

    public array $items = [];

    #[Validate('nullable|image|max:5120')]
    public $heroImage = null;

    public ?string $currentImage = null;

    public function mount(string $page = 'lineas'): void
    {
        $this->ensureCanEditPages();
        abort_unless(isset(PublicPageContent::PAGES[$page]), 404);

        $this->page = $page;
        $this->items = $this->itemsForPage($page);
        $this->pageData = $this->loadPageData($page);
        $section = PublicPageContent::page($page);
        $this->currentImage = $section?->image;
    }

    public function removeHeroImage(): void
    {
        $this->ensureCanEditPages();
        $section = HomeSection::withoutGlobalScopes()
            ->whereNull('vendor_id')
            ->where('section_key', PublicPageContent::PAGES[$this->page]['section_key'])
            ->first();

        if ($section) {
            ImageStorage::delete($section->image);
            $section->update(['image' => null]);
        }

        $this->currentImage = null;
    }

    private function isSimplePage(): bool
    {
        return in_array($this->page, ['cajeros', 'novedades']);
    }

    public function save(array $pageData): void
    {
        $this->ensureCanEditPages();

        if ($this->isSimplePage() && isset($pageData['tabs']) && is_array($pageData['tabs'])) {
            $first = $pageData['tabs'][0] ?? [];
            $ids   = collect($pageData['tabs'])->flatMap(fn ($t) => $t['item_ids'] ?? [])->unique()->values()->all();
            $pageData['tabs'] = [[
                'key'      => 'general',
                'title'    => 'General',
                'subtitle' => '',
                'icon'     => $first['icon'] ?? '',
                'enabled'  => true,
                'item_ids' => $ids,
            ]];
        }

        $this->validateOnly('heroImage');
        $data = $this->normalizePageData($pageData);

        $section = HomeSection::withoutGlobalScopes()->firstOrNew(
            ['vendor_id' => null, 'section_key' => PublicPageContent::PAGES[$this->page]['section_key']]
        );

        if ($this->heroImage) {
            $data['image'] = ImageStorage::store($this->heroImage, 'pages', $section->image ?? null);
            $this->currentImage = $data['image'];
            $this->heroImage = null;
        } else {
            $data['image'] = $section->image;
        }

        $section->fill([
            'vendor_id'     => null,
            'enabled'       => (bool) ($data['enabled'] ?? true),
            'kicker'        => $data['kicker'] ?? '',
            'title'         => $data['title'] ?? '',
            'highlight'     => $data['highlight'] ?? '',
            'subtitle'      => $data['subtitle'] ?? '',
            'content'       => $data['content'] ?? '',
            'image'         => $data['image'] ?? null,
            'action_text'   => $data['action_text'] ?? '',
            'action_url'    => $data['action_url'] ?? '',
            'repeater_data' => $data['tabs'],
        ])->save();

        $this->pageData = $data;
        session()->flash('message_success', 'Pagina guardada correctamente.');
    }

    public function render()
    {
        $this->ensureCanEditPages();

        return view('livewire.editor-pages', [
            'fontAwesomeIcons' => FontAwesomeIcons::options(),
        ]);
    }

    private function loadPageData(string $page): array
    {
        $section = PublicPageContent::page($page);

        if (! $section) {
            return $this->normalizePageData([
                'enabled' => true,
                'kicker' => '',
                'title' => PublicPageContent::PAGES[$page]['label'],
                'highlight' => '',
                'subtitle' => '',
                'content' => '',
                'action_text' => '',
                'action_url' => '',
                'tabs' => [],
            ]);
        }

        return $this->normalizePageData([
            'enabled' => $section->enabled,
            'kicker' => $section->kicker,
            'title' => $section->title,
            'highlight' => $section->highlight,
            'subtitle' => $section->subtitle,
            'content' => $section->content,
            'action_text' => $section->action_text,
            'action_url' => $section->action_url,
            'tabs' => is_array($section->repeater_data) ? $section->repeater_data : [],
        ]);
    }

    private function normalizePageData(array $data): array
    {
        $tabs = collect($data['tabs'] ?? [])
            ->filter(fn ($tab) => is_array($tab))
            ->map(function (array $tab, int $index) {
                $title = trim((string) ($tab['title'] ?? 'Tab'));
                $key = trim((string) ($tab['key'] ?? ''));

                return [
                    'key' => $key !== '' ? (string) str($key)->slug() : 'tab-'.($index + 1),
                    'title' => $title !== '' ? $title : 'Tab '.($index + 1),
                    'subtitle' => trim((string) ($tab['subtitle'] ?? '')),
                    'icon' => trim((string) ($tab['icon'] ?? '')),
                    'enabled' => (bool) ($tab['enabled'] ?? true),
                    'item_ids' => collect($tab['item_ids'] ?? [])
                        ->map(fn ($id) => (string) $id)
                        ->filter()
                        ->unique()
                        ->values()
                        ->all(),
                ];
            })
            ->values()
            ->all();

        if (! $tabs) {
            $tabs[] = [
                'key'      => 'general',
                'title'    => 'General',
                'subtitle' => '',
                'icon'     => '',
                'enabled'  => true,
                'item_ids' => [],
            ];
        }

        return [
            'enabled' => (bool) ($data['enabled'] ?? true),
            'kicker' => trim((string) ($data['kicker'] ?? '')),
            'title' => trim((string) ($data['title'] ?? '')),
            'highlight' => trim((string) ($data['highlight'] ?? '')),
            'subtitle' => trim((string) ($data['subtitle'] ?? '')),
            'content' => trim((string) ($data['content'] ?? '')),
            'action_text' => trim((string) ($data['action_text'] ?? '')),
            'action_url' => trim((string) ($data['action_url'] ?? '')),
            'tabs' => $tabs,
        ];
    }

    private function itemsForPage(string $page): array
    {
        return match ($page) {
            'lineas' => Line::withoutGlobalScopes()
                ->where('status', 'active')
                ->with('vendor:id,name,is_direct')
                ->orderByRaw('EXISTS(SELECT 1 FROM vendors WHERE vendors.id = lines.vendor_id AND vendors.is_direct = 1) DESC')
                ->orderBy('name')
                ->get()
                ->toArray(),
            'bonos' => Bonus::withoutGlobalScopes()
                ->where('status', 'active')
                ->where('end_date', '>=', now())
                ->with('vendor:id,name,is_direct')
                ->orderByRaw('EXISTS(SELECT 1 FROM vendors WHERE vendors.id = bonuses.vendor_id AND vendors.is_direct = 1) DESC')
                ->orderBy('start_date', 'desc')
                ->get()
                ->toArray(),
            'sorteos' => Raffle::withoutGlobalScopes()
                ->whereIn('status', ['active', 'inactive'])
                ->with('vendor:id,name,is_direct')
                ->orderByRaw('EXISTS(SELECT 1 FROM vendors WHERE vendors.id = raffles.vendor_id AND vendors.is_direct = 1) DESC')
                ->orderBy('start_date')
                ->get()
                ->toArray(),
            'cajeros' => Vendor::withoutGlobalScopes()
                ->where('is_active', true)
                ->orderByDesc('is_direct')
                ->orderBy('name')
                ->get()
                ->toArray(),
            'novedades' => Post::withoutGlobalScopes()
                ->where('status', Post::STATUS_PUBLISHED)
                ->with('vendor:id,name,is_direct')
                ->orderBy('published_at', 'desc')
                ->get()
                ->toArray(),
            default => [],
        };
    }

    private function ensureCanEditPages(): void
    {
        if (! auth()->user()?->hasRole(\App\Support\Roles::ADMIN)) {
            abort(403, 'Solo el administrador puede editar páginas.');
        }
    }
}
