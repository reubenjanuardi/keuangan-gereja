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
            background-color: #18181b;
            border-color: rgba(255, 255, 255, 0.1);
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
        .dark .coa-search-icon {
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
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        .coa-search-input:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2);
        }
        .dark .coa-search-input {
            background-color: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.1);
            color: #f9fafb;
        }
        .dark .coa-search-input::placeholder {
            color: #6b7280;
        }
        .dark .coa-search-input:focus {
            border-color: #818cf8;
            box-shadow: 0 0 0 2px rgba(129, 140, 248, 0.25);
        }
        .coa-btn-secondary {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.5rem 0.75rem;
            font-size: 0.75rem;
            font-weight: 600;
            color: #374151;
            background-color: #ffffff;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: all 0.15s ease;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }
        .coa-btn-secondary:hover {
            background-color: #f9fafb;
            color: #111827;
            border-color: #9ca3af;
        }
        .dark .coa-btn-secondary {
            color: #d1d5db;
            background-color: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.1);
            box-shadow: none;
        }
        .dark .coa-btn-secondary:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: #ffffff;
            border-color: rgba(255, 255, 255, 0.2);
        }
        .coa-table-wrapper {
            background-color: #ffffff;
            border-radius: 0.75rem;
            border: 1px solid #e5e7eb;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }
        .dark .coa-table-wrapper {
            background-color: #18181b;
            border-color: rgba(255, 255, 255, 0.1);
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
            background-color: rgba(255, 255, 255, 0.02);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
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
            border-color: rgba(255, 255, 255, 0.05);
        }
        .coa-node-row:hover {
            background-color: #f9fafb;
        }
        .dark .coa-node-row:hover {
            background-color: rgba(255, 255, 255, 0.03);
        }
        .coa-code-badge {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 0.75rem;
            font-weight: 600;
            color: #4b5563;
            background-color: #f3f4f6;
            padding: 0.125rem 0.375rem;
            border-radius: 0.25rem;
            border: 1px solid #e5e7eb;
        }
        .dark .coa-code-badge {
            color: #d1d5db;
            background-color: rgba(255, 255, 255, 0.06);
            border-color: rgba(255, 255, 255, 0.1);
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
            background: #18181b;
            border-color: rgba(255, 255, 255, 0.15);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.5);
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
            transition: all 0.15s ease;
        }
        .coa-action-edit { color: #4f46e5; }
        .coa-action-edit:hover { background-color: #eef2ff; }
        .dark .coa-action-edit { color: #818cf8; }
        .dark .coa-action-edit:hover { background-color: rgba(99, 102, 241, 0.15); }

        .coa-action-add { color: #059669; }
        .coa-action-add:hover { background-color: #ecfdf5; }
        .dark .coa-action-add { color: #34d399; }
        .dark .coa-action-add:hover { background-color: rgba(16, 185, 129, 0.15); }

        .coa-action-delete { color: #dc2626; }
        .coa-action-delete:hover { background-color: #fef2f2; }
        .dark .coa-action-delete { color: #f87171; }
        .dark .coa-action-delete:hover { background-color: rgba(239, 68, 68, 0.15); }

        .coa-action-view { color: #2563eb; }
        .coa-action-view:hover { background-color: #eff6ff; }
        .dark .coa-action-view { color: #60a5fa; }
        .dark .coa-action-view:hover { background-color: rgba(37, 99, 235, 0.15); }

        .coa-badge {
            display: inline-block;
            font-size: 0.7rem;
            font-weight: 600;
            padding: 0.125rem 0.5rem;
            border-radius: 9999px;
            margin-left: 0.5rem;
        }
        .coa-badge-penerimaan { background-color: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
        .dark .coa-badge-penerimaan { background-color: rgba(16, 185, 129, 0.15); color: #34d399; border-color: rgba(52, 211, 153, 0.3); }

        .coa-badge-pengeluaran { background-color: #fff1f2; color: #be123c; border: 1px solid #fecdd3; }
        .dark .coa-badge-pengeluaran { background-color: rgba(244, 63, 94, 0.15); color: #fb7185; border-color: rgba(251, 113, 133, 0.3); }

        .coa-badge-kas { background-color: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
        .dark .coa-badge-kas { background-color: rgba(59, 130, 246, 0.15); color: #60a5fa; border-color: rgba(96, 165, 250, 0.3); }

        .coa-badge-hutang { background-color: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
        .dark .coa-badge-hutang { background-color: rgba(245, 158, 11, 0.15); color: #fbbf24; border-color: rgba(251, 191, 36, 0.3); }

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
                <div class="py-8 text-center text-gray-500 dark:text-gray-400">
                    <x-heroicon-o-folder-open class="w-12 h-12 mx-auto mb-3 text-gray-400 dark:text-gray-600" />
                    <p class="text-base font-medium text-gray-700 dark:text-gray-300">Tidak ada data Chart of Accounts ditemukan.</p>
                    <p class="text-xs mt-1 text-gray-400 dark:text-gray-500">Coba ubah kata kunci pencarian atau tambah akun baru.</p>
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
