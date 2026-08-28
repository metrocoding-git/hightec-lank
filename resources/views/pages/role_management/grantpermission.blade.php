@extends('layouts.app')


@php
    // Fallback label for any permission that doesn't carry its own `label`.
// 'course.toggle-status' -> "Toggle Status", 'fee-group-delete' -> "Delete".
if (!function_exists('permissionLabel')) {
    function permissionLabel(array $permission): string
    {
        if (!empty($permission['label'])) {
            return $permission['label'];
        }
        $tail = collect(explode('.', $permission['name']))->last();
        $words = preg_split('/[-_]/', $tail);
        return collect($words)->map(fn($w) => ucfirst($w))->implode(' ');
    }
}

// Small icon + accent per common action word, purely cosmetic — falls
// back to a plain tag icon for anything custom (Share, Generate URL, etc).
if (!function_exists('permissionIcon')) {
    function permissionIcon(string $label): string
    {
        $key = strtolower($label);
        return match (true) {
            str_contains($key, 'view') => 'fa-eye',
            str_contains($key, 'add') => 'fa-plus',
            str_contains($key, 'edit') => 'fa-pen',
            str_contains($key, 'delete') => 'fa-trash',
            str_contains($key, 'status') => 'fa-toggle-on',
            str_contains($key, 'print') => 'fa-print',
            str_contains($key, 'share') => 'fa-share-alt',
            str_contains($key, 'bulk') => 'fa-layer-group',
            default => 'fa-tag',
            };
        }
    }
@endphp


@section('mainContent')
    <style>
        :root {
            --gp-blue: #4682B4;
            --gp-blue-dark: #36648B;
            --gp-blue-light: #E9F3FA;
            --gp-border: #e3e6f0;
            --gp-bg: #f8f9fb;
            --gp-text: #2c3e50;
            --gp-muted: #8a94a6;
        }

        .gp-shell {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .gp-toolbar {
            position: sticky;
            top: 0;
            z-index: 20;
            background: #fff;
            border: 1px solid var(--gp-border);
            border-radius: 10px;
            padding: 16px 20px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 14px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .04);
        }

        .gp-toolbar h3 {
            margin: 0;
            color: var(--gp-blue);
            font-size: 18px;
            white-space: nowrap;
        }

        .gp-search {
            flex: 1 1 240px;
            position: relative;
        }

        .gp-search input {
            width: 100%;
            padding: 9px 14px 9px 36px;
            border: 1px solid var(--gp-border);
            border-radius: 8px;
            font-size: 14px;
            transition: border-color .2s;
        }

        .gp-search input:focus {
            outline: none;
            border-color: var(--gp-blue);
        }

        .gp-search .fa-search {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gp-muted);
            font-size: 13px;
        }

        .gp-toolbar-actions {
            display: flex;
            gap: 8px;
            white-space: nowrap;
        }

        .gp-btn {
            border: 1px solid var(--gp-border);
            background: #fff;
            color: var(--gp-text);
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 13px;
            cursor: pointer;
            transition: all .2s;
        }

        .gp-btn:hover {
            background: var(--gp-blue-light);
            border-color: var(--gp-blue);
            color: var(--gp-blue-dark);
        }

        .gp-total-badge {
            background: var(--gp-blue);
            color: #fff;
            padding: 6px 14px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 600;
            white-space: nowrap;
        }

        .gp-category {
            background: #fff;
            border: 1px solid var(--gp-border);
            border-radius: 10px;
            overflow: hidden;
        }

        .gp-category-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 14px 20px;
            background: var(--gp-bg);
            border-bottom: 1px solid var(--gp-border);
            cursor: pointer;
            user-select: none;
        }

        .gp-category-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
            color: var(--gp-text);
            font-size: 15px;
        }

        .gp-category-title .fa-chevron-down {
            font-size: 11px;
            color: var(--gp-muted);
            transition: transform .2s;
        }

        .gp-category.is-collapsed .fa-chevron-down {
            transform: rotate(-90deg);
        }

        .gp-category-count {
            font-size: 12px;
            color: var(--gp-muted);
            font-weight: 500;
        }

        .gp-category-count.has-selection {
            color: var(--gp-blue-dark);
        }

        .gp-select-all-label {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: var(--gp-muted);
            cursor: pointer;
        }

        .gp-windows {
            padding: 4px 20px 14px;
        }

        .gp-category.is-collapsed .gp-windows {
            display: none;
        }

        .gp-window-row {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px 16px;
            padding: 12px 0;
            border-bottom: 1px dashed var(--gp-border);
        }

        .gp-window-row:last-child {
            border-bottom: none;
        }

        .gp-window-name {
            flex: 0 0 200px;
            font-weight: 500;
            color: var(--gp-text);
            font-size: 13.5px;
        }

        .gp-chips {
            flex: 1 1 auto;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .gp-chip {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border: 1px solid var(--gp-border);
            border-radius: 999px;
            font-size: 12.5px;
            color: var(--gp-muted);
            cursor: pointer;
            transition: all .15s;
            background: #fff;
        }

        .gp-chip input {
            display: none;
        }

        .gp-chip .fa {
            font-size: 11px;
        }

        .gp-chip:hover {
            border-color: var(--gp-blue);
            color: var(--gp-blue-dark);
        }

        .gp-chip.is-checked {
            background: var(--gp-blue);
            border-color: var(--gp-blue);
            color: #fff;
        }

        .gp-empty-state {
            text-align: center;
            padding: 40px 20px;
            color: var(--gp-muted);
            font-size: 14px;
        }

        .gp-savebar {
            position: sticky;
            bottom: 0;
            z-index: 20;
            background: #fff;
            border: 1px solid var(--gp-border);
            border-radius: 10px;
            padding: 14px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, .05);
        }

        .gp-savebar-count {
            font-size: 13px;
            color: var(--gp-muted);
        }

        .gp-savebar-count strong {
            color: var(--gp-blue-dark);
        }

        .gp-save-btn {
            background: var(--gp-blue);
            border: none;
            color: #fff;
            padding: 10px 22px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background .2s;
        }

        .gp-save-btn:hover {
            background: var(--gp-blue-dark);
        }

        [data-gp-hidden="true"] {
            display: none !important;
        }

        @media (max-width: 767px) {
            .gp-window-row {
                flex-direction: column;
                align-items: flex-start;
            }

            .gp-window-name {
                flex-basis: auto;
            }

            .gp-savebar {
                flex-direction: column;
                align-items: stretch;
            }
        }
    </style>
    <section class="sms-breadcrumb mb-40 white-box">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>Grant Permissions</h1>
                <div class="bc-pages">
                    <a href="{{ url('dashboard') }}">Dashboard</a>
                    <a href="{{ route('roles.index') }}">Role Management</a>
                    <a href="#">Grant Permissions</a>
                </div>
            </div>
        </div>
    </section>

    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <form action="{{ route('roles.savePermissions', $role->id) }}" method="POST" id="grantPermissionsForm">
                @csrf

                <div class="gp-shell">
                    <div class="gp-toolbar">
                        <h3>{{ $role->role_name }}</h3>

                        <div class="gp-search">
                            <i class="fas fa-search"></i>
                            <input type="text" id="gpSearch" placeholder="Search a section or permission…"
                                autocomplete="off">
                        </div>

                        <div class="gp-toolbar-actions">
                            <button type="button" class="gp-btn" id="gpSelectAll">Select All</button>
                            <button type="button" class="gp-btn" id="gpClearAll">Clear All</button>
                        </div>

                        <span class="gp-total-badge" id="gpTotalBadge">0 selected</span>
                    </div>

                    @foreach ($menuStructure as $category)
                        @php
                            $categoryPermissionIds = collect($category['items'] ?? [])->flatMap(
                                fn($item) => collect($item['permissions'] ?? [])->pluck('id'),
                            );
                            $categoryTotal = $categoryPermissionIds->count();
                            $categorySelected = $categoryPermissionIds
                                ->filter(fn($id) => in_array($id, $rolePermissions))
                                ->count();
                        @endphp
                        <div class="gp-category" data-gp-category>
                            <div class="gp-category-header" data-gp-category-toggle>
                                <label class="gp-select-all-label" onclick="event.stopPropagation()">
                                    <input type="checkbox" class="gp-category-checkbox" data-gp-category-select
                                        {{ $categoryTotal > 0 && $categorySelected === $categoryTotal ? 'checked' : '' }}>
                                </label>
                                <div class="gp-category-title" style="flex:1">
                                    <i class="fas fa-chevron-down"></i>
                                    <span>{{ $category['name'] }}</span>
                                </div>
                                <span class="gp-category-count" data-gp-category-count>{{ $categorySelected }} /
                                    {{ $categoryTotal }}</span>
                            </div>

                            <div class="gp-windows">
                                @foreach ($category['items'] ?? [] as $item)
                                    <div class="gp-window-row" data-gp-window
                                        data-gp-search-text="{{ strtolower($category['name'] . ' ' . $item['name']) }}">
                                        <div class="gp-window-name">{{ $item['name'] }}</div>
                                        <div class="gp-chips">
                                            @foreach ($item['permissions'] ?? [] as $permission)
                                                @php $label = permissionLabel($permission); @endphp
                                                <label
                                                    class="gp-chip {{ in_array($permission['id'], $rolePermissions) ? 'is-checked' : '' }}"
                                                    data-gp-search-text="{{ strtolower($category['name'] . ' ' . $item['name'] . ' ' . $label . ' ' . $permission['name']) }}">
                                                    <input type="checkbox" class="permission-checkbox" name="permissions[]"
                                                        value="{{ $permission['id'] }}"
                                                        {{ in_array($permission['id'], $rolePermissions) ? 'checked' : '' }}>
                                                    <i class="fas {{ permissionIcon($label) }}"></i>
                                                    <span>{{ $label }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                    <div class="gp-empty-state" id="gpEmptyState" style="display:none">
                        No sections match your search.
                    </div>

                    <div class="gp-savebar">
                        <span class="gp-savebar-count" id="gpSavebarCount"></span>
                        <button type="submit" class="gp-save-btn">
                            <i class="fas fa-check"></i> Save Permissions
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection

@push('script')
    <script>
        $(function() {
            const $categories = $('[data-gp-category]');
            const $totalBadge = $('#gpTotalBadge');
            const $savebarCount = $('#gpSavebarCount');
            const $emptyState = $('#gpEmptyState');

            function updateCounts() {
                let grandTotal = 0;
                let grandChecked = 0;

                $categories.each(function() {
                    const $cat = $(this);
                    const $boxes = $cat.find('.permission-checkbox');
                    const total = $boxes.length;
                    const checked = $boxes.filter(':checked').length;

                    $cat.find('[data-gp-category-count]').text(checked + ' / ' + total)
                        .toggleClass('has-selection', checked > 0);

                    const $catToggle = $cat.find('[data-gp-category-select]');
                    $catToggle.prop('checked', total > 0 && checked === total);
                    $catToggle.prop('indeterminate', checked > 0 && checked < total);

                    grandTotal += total;
                    grandChecked += checked;
                });

                $totalBadge.text(grandChecked + ' selected');
                $savebarCount.html(grandChecked ?
                    '<strong>' + grandChecked + '</strong> of ' + grandTotal + ' permissions selected' :
                    'No permissions selected yet');
            }

            // Chip <-> checkbox sync
            $(document).on('change', '.permission-checkbox', function() {
                $(this).closest('.gp-chip').toggleClass('is-checked', this.checked);
                updateCounts();
            });

            // Category header checkbox: select/clear every permission in that category
            $(document).on('change', '[data-gp-category-select]', function() {
                const checked = this.checked;
                $(this).closest('[data-gp-category]').find('.permission-checkbox')
                    .prop('checked', checked)
                    .closest('.gp-chip').toggleClass('is-checked', checked);
                updateCounts();
            });

            // Collapse/expand a category by clicking its header (not the checkbox)
            $(document).on('click', '[data-gp-category-toggle]', function() {
                $(this).closest('[data-gp-category]').toggleClass('is-collapsed');
            });

            // Toolbar Select All / Clear All (respects current search filter)
            $('#gpSelectAll').on('click', function() {
                $('[data-gp-window]:visible, [data-gp-window]:not([data-gp-hidden])').find(
                        '.permission-checkbox')
                    .prop('checked', true).closest('.gp-chip').addClass('is-checked');
                updateCounts();
            });
            $('#gpClearAll').on('click', function() {
                $('.permission-checkbox').prop('checked', false).closest('.gp-chip').removeClass(
                    'is-checked');
                updateCounts();
            });

            // Live search across category / window / permission names
            $('#gpSearch').on('input', function() {
                const term = $(this).val().trim().toLowerCase();
                let anyVisible = false;

                $('[data-gp-window]').each(function() {
                    const haystack = $(this).data('gp-search-text') + '';
                    const matches = term === '' || haystack.includes(term);
                    $(this).attr('data-gp-hidden', matches ? null : 'true');
                    if (matches) anyVisible = true;
                });

                $categories.each(function() {
                    const $cat = $(this);
                    const hasVisibleWindow = $cat.find('[data-gp-window]:not([data-gp-hidden])')
                        .length > 0;
                    $cat.attr('data-gp-hidden', hasVisibleWindow ? null : 'true');
                });

                $emptyState.toggle(!anyVisible);
            });

            updateCounts();
        });
    </script>
@endpush
