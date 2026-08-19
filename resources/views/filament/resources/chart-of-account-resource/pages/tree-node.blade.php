@props(['node', 'depth' => 0])

@php
    $hasChildren = $node->children->count() > 0;
    $isGroup = $node->is_group;
    $isExpanded = in_array($node->kode_akun, $this->expandedNodes) || !empty($this->search);
    $budget = (float) ($node->budget ?? 0);
    $formattedBudget = 'Rp ' . number_format($budget, 0, ',', '.');
@endphp

<div class="coa-node-wrapper border-b border-gray-100 dark:border-gray-800/60">
    <div class="coa-node-row group" style="padding-left: {{ ($depth * 1.75) + 0.75 }}rem">
        <div style="display: flex; align-items: center; gap: 0.5rem; min-width: 0; flex: 1;">
            {{-- Toggle Button or Spacer --}}
            @if($hasChildren)
                <button
                    type="button"
                    wire:click="toggleNode('{{ $node->kode_akun }}')"
                    style="padding: 0.25rem; border-radius: 0.25rem; border: none; background: transparent; cursor: pointer; color: #6b7280;"
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
                    <x-heroicon-m-stop class="w-3.5 h-3.5 text-gray-400" style="color: #9ca3af;" />
                @endif
            </span>

            {{-- Account Information --}}
            <div style="display: flex; align-items: center; gap: 0.5rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                <span style="font-family: monospace; font-size: 0.75rem; font-weight: 600; color: #4b5563; background-color: #f3f4f6; padding: 0.125rem 0.375rem; border-radius: 0.25rem; border: 1px solid #e5e7eb;">
                    {{ $node->kode_akun }}
                </span>
                <span style="font-size: 0.875rem; font-weight: {{ $isGroup ? '600' : '500' }}; color: #111827;" class="dark:text-gray-100">
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
                    <span style="font-size: 0.625rem; text-transform: uppercase; font-weight: 700; color: #9ca3af; letter-spacing: 0.05em;">
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

                <span style="color: #d1d5db;">|</span>

                <button
                    type="button"
                    wire:click="mountAction('addChildAccount', { parent_code: '{{ $node->kode_akun }}' })"
                    class="coa-action-link coa-action-add"
                >
                    Add Child
                </button>

                <span style="color: #d1d5db;">|</span>

                <button
                    type="button"
                    wire:click="mountAction('deleteAccount', { kode_akun: '{{ $node->kode_akun }}' })"
                    class="coa-action-link coa-action-delete"
                >
                    Delete
                </button>

                @if($node->is_postable)
                    <span style="color: #d1d5db;">|</span>

                    <a
                        href="{{ route('filament.admin.resources.vouchers.index') }}"
                        class="coa-action-link coa-action-view"
                    >
                        View Ledger
                    </a>
                @endif
            </div>

            {{-- Budget (Anggaran PKA) --}}
            <div style="text-align: right; min-width: 130px; font-family: monospace; font-size: 0.875rem; font-weight: {{ $budget > 0 ? '600' : '400' }}; color: {{ $budget > 0 ? '#111827' : '#9ca3af' }};">
                {{ $formattedBudget }}
            </div>
        </div>
    </div>

    {{-- Recursive Children Render --}}
    @if($hasChildren && $isExpanded)
        <div style="border-left: 1px dashed #e5e7eb;">
            @foreach($node->children as $child)
                @include('filament.resources.chart-of-account-resource.pages.tree-node', ['node' => $child, 'depth' => $depth + 1])
            @endforeach
        </div>
    @endif
</div>
