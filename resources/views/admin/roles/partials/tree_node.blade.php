<div class="tree-node-wrapper level-{{ $node['level'] }}" data-node-id="{{ $node['id'] }}">
    {{-- Node Permission Row --}}
    <div class="tree-item-row permission-item" 
         data-perm-name="{{ strtolower($node['permission']->name) }}" 
         data-perm-title="{{ strtolower($node['title']) }}"
         data-group="{{ $group['slug'] }}"
         data-order="{{ $node['order'] }}"
         data-node-id="{{ $node['id'] }}"
         data-parent-id="{{ $node['parent_id'] ?? '' }}">
        
        <div class="form-check form-switch tree-switch d-inline-flex align-items-center mb-0">
            <input class="form-check-input perm-switch {{ empty($node['children']) ? 'child-switch' : 'parent-switch' }}" 
                   type="checkbox" 
                   name="permissions[]" 
                   value="{{ $node['permission']->name }}" 
                   id="perm_{{ $node['permission']->id }}" 
                   data-group="{{ $group['slug'] }}" 
                   data-node-id="{{ $node['permission']->id }}" 
                   data-order="{{ $node['order'] }}"
                   data-parent-id="{{ $node['parent_id'] ?? '' }}"
                   {{ $node['is_checked'] ? 'checked' : '' }}>
                   
            <label class="form-check-label" for="perm_{{ $node['permission']->id }}">
                <span class="{{ $node['level'] === 1 ? 'tree-title-bold' : ($node['level'] === 2 ? 'tree-title-semibold' : 'tree-title-normal') }} perm-label-text">
                    {{ $node['title'] }}
                </span>
                <span class="tree-code-muted perm-label-code">({{ $node['permission']->name }})</span>
            </label>
        </div>
    </div>

    {{-- Child Permissions (Recursive) based on parent_id --}}
    @if(!empty($node['children']))
        <div class="tree-children-container" data-parent-node-id="{{ $node['permission']->id }}">
            @foreach($node['children'] as $child)
                @include('admin.roles.partials.tree_node', [
                    'node' => $child, 
                    'group' => $group
                ])
            @endforeach
        </div>
    @endif
</div>
