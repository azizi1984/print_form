@php
    $level = $node['level'] ?? 1;
    $indentPx = ($level - 1) * 28;
    $hasChildren = !empty($node['children']);
    $perm = $node['permission'];
@endphp

<tr class="perm-table-row level-{{ $level }}" 
    data-perm-id="{{ $node['id'] }}" 
    data-perm-name="{{ strtolower($perm->name) }}"
    data-perm-title="{{ strtolower($node['title']) }}"
    data-menu-id="{{ $menuId }}">
    
    {{-- Permission Title & Code with Indentation --}}
    <td class="ps-4 py-2">
        <div class="d-flex align-items-center" style="padding-left: {{ $indentPx }}px;">
            {{-- Tree Branch Visual --}}
            @if($level > 1)
                <span class="text-muted me-2 font-monospace" style="opacity: 0.6; font-size: 13px;">└─</span>
            @endif

            {{-- Node Icon --}}
            <span class="me-2">
                @if($hasChildren)
                    <i class="bi bi-folder-fill text-warning" style="font-size: 15px;"></i>
                @elseif($level === 1)
                    <i class="bi bi-grid-fill text-primary" style="font-size: 15px;"></i>
                @else
                    <i class="bi bi-shield-check text-success" style="font-size: 15px;"></i>
                @endif
            </span>

            {{-- Titles --}}
            <div>
                <div class="d-flex align-items-center gap-2">
                    <span class="{{ $level === 1 ? 'fw-bold text-dark' : ($level === 2 ? 'fw-semibold text-dark' : 'text-body') }}" style="font-size: 13.5px;">
                        {{ $node['title'] }}
                    </span>
                    @if($level === 1)
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill" style="font-size: 10px;">
                            Root
                        </span>
                    @endif
                </div>
                <div class="d-flex align-items-center gap-2 mt-0">
                    <code class="text-muted small" style="font-size: 11.5px;">{{ $perm->name }}</code>
                    <span class="text-secondary opacity-50" style="font-size: 10px;">• Guard: {{ $perm->guard_name }}</span>
                </div>
            </div>
        </div>
    </td>

    {{-- Position / Level --}}
    <td class="text-center py-2">
        @if($level === 1)
            <span class="badge bg-primary-subtle text-primary rounded-pill px-2 py-1" style="font-size: 11px;">
                ระดับ 1: Root
            </span>
        @elseif($level === 2)
            <span class="badge bg-info-subtle text-info-emphasis rounded-pill px-2 py-1" style="font-size: 11px;">
                ระดับ 2: Sub Menu
            </span>
        @else
            <span class="badge bg-light text-secondary border rounded-pill px-2 py-1" style="font-size: 11px;">
                ระดับ {{ $level }}: Action
            </span>
        @endif
    </td>

    {{-- Order --}}
    <td class="text-center py-2">
        <span class="badge bg-secondary-subtle text-dark fw-bold rounded-pill px-2 py-1" style="font-size: 11px;">
            #{{ $node['order'] }}
        </span>
    </td>

    {{-- Roles Count --}}
    <td class="text-center py-2">
        <span class="badge bg-light text-dark border rounded-pill px-3 py-1" style="font-size: 11.5px;">
            <i class="bi bi-people me-1 text-muted"></i>{{ $perm->roles_count ?? 0 }} บทบาท
        </span>
    </td>

    {{-- Actions --}}
    <td class="text-end pe-4 py-2">
        <div class="d-inline-flex align-items-center gap-1">
            {{-- Quick Add Sub-permission --}}
            @can('QuickAddSubPermission')
                <button type="button" class="btn btn-action btn-action-view quick-add-sub-perm-btn" 
                        data-menu-id="{{ $menuId }}"
                        data-menu-name="{{ $menuName }}"
                        data-parent-id="{{ $perm->id }}"
                        data-parent-name="{{ $node['title'] }} ({{ $perm->name }})"
                        title="เพิ่มสิทธิ์ย่อยใต้สิทธิ์นี้ (Quick Add Sub-Permission)">
                    <i class="bi bi-plus-lg"></i>
                </button>
            @endcan

            {{-- Edit Permission --}}
            @can('EditPermission')
                <button type="button" class="btn btn-action btn-action-edit edit-perm-btn"
                        data-id="{{ $perm->id }}"
                        data-name="{{ $perm->name }}"
                        data-guard="{{ $perm->guard_name }}"
                        data-menu-id="{{ $perm->menu_id }}"
                        data-parent-id="{{ $perm->parent_id }}"
                        data-order="{{ $perm->order }}"
                        title="แก้ไขสิทธิ์">
                    <i class="bi bi-pencil"></i>
                </button>
            @endcan

            {{-- Delete Permission --}}
            @can('DeletePermission')
                <button type="button" class="btn btn-action btn-action-delete delete-perm-btn"
                        data-id="{{ $perm->id }}"
                        data-name="{{ $perm->name }}"
                        data-title="{{ $node['title'] }}"
                        data-has-children="{{ $hasChildren ? '1' : '0' }}"
                        title="ลบสิทธิ์">
                    <i class="bi bi-trash"></i>
                </button>
            @endcan
        </div>
    </td>
</tr>

{{-- Recursive Rendering of Child Nodes --}}
@if($hasChildren)
    @foreach($node['children'] as $childNode)
        @include('admin.roles.partials.permission_table_row', [
            'node' => $childNode,
            'menuId' => $menuId,
            'menuName' => $menuName
        ])
    @endforeach
@endif
