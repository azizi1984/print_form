<div class="group-accordion-card" 
     id="group_card_{{ $group['slug'] }}" 
     data-group="{{ $group['slug'] }}" 
     data-group-name="{{ strtolower($group['group_name']) }}">
    
    {{-- Accordion Header Bar --}}
    <div class="group-accordion-header" 
         data-target="#group_body_{{ $group['slug'] }}"
         data-toggle-id="{{ $group['slug'] }}">
        <div class="d-flex align-items-center">
            <i class="bi bi-folder2 group-folder-icon"></i>
            <span class="group-name-title">{{ $group['group_name'] }}</span>
        </div>
        <div class="d-flex align-items-center">
            <span class="group-badge">
                <span class="group-checked-count" id="count_{{ $group['slug'] }}">{{ $group['checked_count'] }}</span>/{{ $group['total_count'] }} สิทธิ์
            </span>
            <span class="group-toggle-symbol" id="toggle_icon_{{ $group['slug'] }}">+</span>
        </div>
    </div>

    {{-- Accordion Body with Tree Container --}}
    <div id="group_body_{{ $group['slug'] }}" class="group-accordion-body d-none">
        <div class="group-tree-inner-box">
            @foreach($group['nodes'] as $node)
                @include('admin.roles.partials.tree_node', [
                    'node' => $node, 
                    'group' => $group
                ])
            @endforeach
        </div>
    </div>
</div>
