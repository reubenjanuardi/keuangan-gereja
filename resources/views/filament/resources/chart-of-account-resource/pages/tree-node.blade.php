@props(['node', 'depth' => 0])

@php
    $hasChildren = $node->children->count() > 0;
    $isGroup = $node->is_group;
    $isExpanded = in_array($node->kode_akun, $this->expandedNodes) || !empty($this->search);
    $budget = (float) ($node->budget ?? 0);
    $formattedBudget = 'Rp ' . number_format($budget, 0, ',', '.');
@endphp

<div class="coa-node-wrapper border-b border-gray-100 dark:border-white/5">
    <div class="coa-node-row group" style="padding-left: {{ ($depth * 1.75) + 0.75 }}rem">
        <div style="display: flex; align-items: center; gap: 0.5rem; min-width: 0; flex: 1;">
            {{-- Toggle Button or Spacer --}}
            @if($hasChildren)
                <button
                    type="button"
                    wire:click="toggleNode('{{ $node->kode_akun }}')"
                    class="p-1 rounded text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 bg-transparent border-0 cursor-pointer inline-flex items-center justify-center"
                    title="{{ $isExpanded ? 'Sembunyikan' : 'Tampilkan' }}"
                >
                    @if($isExpanded)
                        <x-heroicon-m-chevron-down class="w-4 h-4" />
                    @else
                        <x-heroicon-m-chevron-right class="w-4 h-4" />
                    @endif
                </button>
            @else
                <span style="width: 1.5rem; display: inline-block;"></span>
            @endif

            {{-- Node Icon: Folder for Group, Bullet for Postable Ledger Account --}}
            <span style="flex-shrink: 0; display: inline-flex; align-items: center;">
                @if($isGroup)
                    @if($isExpanded)
                        <x-heroicon-m-folder-open class="w-5 h-5 text-amber-500" style="color: #f59e0b;" />
                    @else
                        <x-heroicon-m-folder class="w-5 h-5 text-amber-500" style="color: #f59e0b;" />
                    @endif
                @else
                    <x-heroicon-m-stop class="w-3.5 h-3.5 text-gray-400 dark:text-gray-500" />
                @endif
            </span>

            {{-- Account Information --}}
            <div style="display: flex; align-items: center; gap: 0.5rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                <span class="coa-code-badge">
                    {{ $node->kode_akun }}
                </span>
                <span class="text-sm {{ $isGroup ? 'font-semibold' : 'font-medium' }} text-gray-900 dark:text-gray-100">
                    {{ $node->nama_akun }}
                </span>

                <span class="coa-badge {{ match($node->kategori) {
                    'Penerimaan' => 'coa-badge-penerimaan',
                    'Pengeluaran' => 'coa-badge-pengeluaran',
                    'Kas & Bank' => 'coa-badge-kas',
                    'Hutang / Piutang' => 'coa-badge-hutang',
                    default => ''
                } }}">
                    {{ $node->kategori }}
                </span>

                @if(!$node->is_postable)
                    <span class="text-[10px] uppercase font-bold text-gray-400 dark:text-gray-500 tracking-wider">
                        (Group)
                    </span>
                @endif
            </div>
        </div>

        {{-- Actions & Balance --}}
        <div style="display: flex; align-items: center; gap: 1rem; flex-shrink: 0;">
            {{-- Hover Action Buttons (ERPNext style) --}}
            <div class="coa-actions-bar">
                <button
                    type="button"
                    wire:click="mountAction('editAccount', { kode_akun: '{{ $node->kode_akun }}' })"
                    class="coa-action-link coa-action-edit"
                >
                    Edit
                </button>

                <span class="text-gray-300 dark:text-gray-600 select-none">|</span>

                <button
                    type="button"
                    wire:click="mountAction('addChildAccount', { parent_code: '{{ $node->kode_akun }}' })"
                    class="coa-action-link coa-action-add"
                >
                    Add Child
                </button>

                <span class="text-gray-300 dark:text-gray-600 select-none">|</span>

                <button
                    type="button"
                    wire:click="mountAction('deleteAccount', { kode_akun: '{{ $node->kode_akun }}' })"
                    class="coa-action-link coa-action-delete"
                >
                    Delete
                </button>

                @if($node->is_postable)
                    <span class="text-gray-300 dark:text-gray-600 select-none">|</span>

                    <a
                        href="{{ \App\Filament\Resources\VoucherResource::getUrl('index') }}"
                        class="coa-action-link coa-action-view"
                    >
                        View Ledger
                    </a>
                @endif
            </div>

            {{-- Budget (Anggaran PKA) --}}
            <div class="text-right min-w-[130px] font-mono text-sm {{ $budget > 0 ? 'font-semibold text-gray-900 dark:text-gray-200' : 'font-normal text-gray-400 dark:text-gray-500' }}">
                {{ $formattedBudget }}
            </div>
        </div>
    </div>

    {{-- Recursive Children Render --}}
    @if($hasChildren && $isExpanded)
        <div class="border-l border-dashed border-gray-200 dark:border-white/10">
            @foreach($node->children as $child)
                @include('filament.resources.chart-of-account-resource.pages.tree-node', ['node' => $child, 'depth' => $depth + 1])
            @endforeach
        </div>
    @endif
</div>
