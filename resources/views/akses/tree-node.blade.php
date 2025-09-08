<li>
    <input type="checkbox"
           data-id="{{ $node['id'] }}"
           class="{{ count($node['children']) ? 'check-parent' : 'check-single' }}"
           {{ in_array($node['id'], $checkedMenuIds ?? []) ? 'checked' : '' }}>
    {{ $node['text'] }}

    @if (!empty($node['children']))
        <ul>
            @foreach($node['children'] as $child)
                @include('akses.tree-node', [
                    'node' => $child,
                    'checkedMenuIds' => $checkedMenuIds
                ])
            @endforeach
        </ul>
    @endif
</li>
