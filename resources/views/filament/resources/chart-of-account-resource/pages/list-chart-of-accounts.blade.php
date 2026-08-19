<x-filament-panels::page>
    <style>
        .coa-container {
            font-family: inherit;
        }
        .coa-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            background-color: #ffffff;
            padding: 1rem;
            border-radius: 0.75rem;
            border: 1px solid #e5e7eb;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            margin-bottom: 1rem;
        }
        .dark .coa-toolbar {
            background-color: #111827;
            border-color: #1f2937;
        }
        .coa-search-wrapper {
            position: relative;
            flex: 1;
            max-width: 24rem;
        }
        .coa-search-icon {
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            padding-left: 0.75rem;
            display: flex;
            align-items: center;
            pointer-events: none;
            color: #9ca3af;
        }
        .coa-search-input {
            width: 100%;
            padding: 0.5rem 0.75rem 0.5rem 2.25rem;
            font-size: 0.875rem;
            background-color: #f9fafb;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            color: #111827;
            outline: none;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }
        .dark .coa-search-input {
            background-color: #1f2937;
            border-color: #374151;
            color: #f9fafb;
        }
        .coa-btn-secondary {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.5rem 0.75rem;
            font-size: 0.75rem;
            font-weight: 600;
            color: #374151;
            background-color: #f3f4f6;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: background-color 0.15s ease;
        }
        .coa-btn-secondary:hover {
            background-color: #e5e7eb;
        }
        .dark .coa-btn-secondary {
            color: #e5e7eb;
            background-color: #1f2937;
            border-color: #374151;
        }
        .dark .coa-btn-secondary:hover {
            background-color: #374151;
        }
        .coa-table-wrapper {
            background-color: #ffffff;
            border-radius: 0.75rem;
            border: 1px solid #e5e7eb;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }
        .dark .coa-table-wrapper {
            background-color: #111827;
            border-color: #1f2937;
        }
        .coa-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.75rem 1rem;
            background-color: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
            font-size: 0.75rem;
            font-weight: 700;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .dark .coa-header {
            background-color: #1f2937;
            border-color: #374151;
            color: #9ca3af;
        }
        .coa-node-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.625rem 1rem;
            border-bottom: 1px solid #f3f4f6;
            transition: background-color 0.15s ease;
        }
        .dark .coa-node-row {
            border-color: #1f2937;
        }
        .coa-node-row:hover {
            background-color: #f9fafb;
        }
        .dark .coa-node-row:hover {
            background-color: #1f2937;
        }
        .coa-actions-bar {
            opacity: 0;
            transition: opacity 0.15s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            padding: 0.125rem 0.5rem;
            border-radius: 0.375rem;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }
        .dark .coa-actions-bar {
            background: #111827;
            border-color: #374151;
        }
        .coa-node-row:hover .coa-actions-bar {
            opacity: 1;
        }
        .coa-action-link {
            font-size: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            background: none;
            border: none;
            padding: 0.125rem 0.375rem;
            border-radius: 0.25rem;
            text-decoration: none;
        }
        .coa-action-edit { color: #4f46e5; }
        .coa-action-edit:hover { background-color: #eef2ff; }
        .coa-action-add { color: #059669; }
        .coa-action-add:hover { background-color: #ecfdf5; }
        .coa-action-delete { color: #dc2626; }
        .coa-action-delete:hover { background-color: #fef2f2; }
        .coa-action-view { color: #2563eb; }
        .coa-action-view:hover { background-color: #eff6ff; }

        .coa-badge {
            display: inline-block;
            font-size: 0.7rem;
            font-weight: 600;
            padding: 0.125rem 0.5rem;
            border-radius: 9999px;
            margin-left: 0.5rem;
        }
        .coa-badge-penerimaan { background-color: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .coa-badge-pengeluaran { background-color: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .coa-badge-kas { background-color: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }
        .coa-badge-hutang { background-color: #fef3c7; color: #92400e; border: 1px solid #fde68a; }

        .coa-tree-wrapper svg {
            width: 1.125rem !important;
            height: 1.125rem !important;
            max-width: 1.125rem !important;
            max-height: 1.125rem !important;
            min-width: 1.125rem !important;
            min-height: 1.125rem !important;
            display: inline-block !important;
            vertical-align: middle !important;
            flex-shrink: 0 !important;
        }
        .coa-tree-wrapper svg.w-3-5, .coa-tree-wrapper svg.w-3\.5 {
            width: 0.875rem !important;
            height: 0.875rem !important;
            max-width: 0.875rem !important;
            max-height: 0.875rem !important;
            min-width: 0.875rem !important;
            min-height: 0.875rem !important;
        }
        .coa-tree-wrapper svg.w-4 {
            width: 1rem !important;
            height: 1rem !important;
            max-width: 1rem !important;
            max-height: 1rem !important;
            min-width: 1rem !important;
            min-height: 1rem !important;
        }
        .coa-tree-wrapper svg.w-12 {
            width: 3rem !important;
            height: 3rem !important;
            max-width: 3rem !important;
            max-height: 3rem !important;
            min-width: 3rem !important;
            min-height: 3rem !important;
        }
    </style>

    <div class="coa-container coa-tree-wrapper">
        {{-- Toolbar / Filter Bar (ERPNext Style) --}}
        <div class="coa-toolbar">
            {{-- Search Bar --}}
            <div class="coa-search-wrapper">
                <div class="coa-search-icon">
                    <x-heroicon-m-magnifying-glass class="w-4 h-4" />
                </div>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Cari kode atau nama akun..."
                    class="coa-search-input"
                />
            </div>

            {{-- Controls: Expand All / Collapse All --}}
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <button
                    type="button"
                    wire:click="expandAll"
                    class="coa-btn-secondary"
                >
                    <x-heroicon-m-arrows-pointing-out class="w-4 h-4" />
                    Expand All
                </button>

                <button
                    type="button"
                    wire:click="collapseAll"
                    class="coa-btn-secondary"
                >
                    <x-heroicon-m-arrows-pointing-in class="w-4 h-4" />
                    Collapse All
                </button>
            </div>
        </div>

        {{-- Tree View Container --}}
        <div class="coa-table-wrapper">
            {{-- Table Header Bar --}}
            <div class="coa-header">
                <div>Nama & Kode Akun</div>
                <div>Budget (Anggaran PKA)</div>
            </div>

            {{-- Tree Content --}}
            @php $treeNodes = $this->tree_nodes; @endphp

            @if($treeNodes->isEmpty())
                <div style="padding: 2rem; text-align: center; color: #6b7280;">
                    <x-heroicon-o-folder-open class="w-12 h-12" style="margin: 0 auto 0.75rem auto; color: #9ca3af;" />
                    <p style="font-size: 1rem; font-weight: 500;">Tidak ada data Chart of Accounts ditemukan.</p>
                    <p style="font-size: 0.75rem; margin-top: 0.25rem; color: #9ca3af;">Coba ubah kata kunci pencarian atau tambah akun baru.</p>
                </div>
            @else
                <div>
                    @foreach($treeNodes as $rootNode)
                        @include('filament.resources.chart-of-account-resource.pages.tree-node', ['node' => $rootNode, 'depth' => 0])
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Filament Action Modals --}}
    <x-filament-actions::modals />
</x-filament-panels::page>
