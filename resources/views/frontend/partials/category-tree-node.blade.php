@php
    $level = $level ?? 3;
    $items = $items ?? [];
    $parentUrl = $parentUrl ?? '';
@endphp

@foreach($items as $idx => $item)
    @php
        $name = $item['name'] ?? 'Category';
        $slug = $item['slug'] ?? 'item-' . $idx;
        $url = $item['url'] ?? ($parentUrl ? $parentUrl . '&lvl' . $level . '=' . $slug : url('/' . $slug));
        $children = $item['children'] ?? $item['subcategories'] ?? [];
        $hasKids = !empty($children);
    @endphp

    @if($level === 3)
        <div class="mega-child-block {{ $hasKids ? 'has-subchildren' : '' }}">
            <a href="{{ $url }}" class="mega-child-link">
                <span class="mega-child-text">{{ $name }}</span>
            </a>

            @if($hasKids)
                <div class="mega-subchild-tags">
                    @include('frontend.partials.category-tree-node', [
                        'items' => $children,
                        'level' => 4,
                        'parentUrl' => $url
                    ])
                </div>
            @endif
        </div>
    @elseif($level === 4)
        <div class="mega-subchild-wrapper">
            <a href="{{ $url }}" class="mega-subchild-tag {{ $hasKids ? 'has-kids' : '' }}">
                {{ $name }}
            </a>
            @if($hasKids)
                <div class="mega-deepchild-pills">
                    @include('frontend.partials.category-tree-node', [
                        'items' => $children,
                        'level' => 5,
                        'parentUrl' => $url
                    ])
                </div>
            @endif
        </div>
    @else
        {{-- Level 5 and deeper recursive tags --}}
        <a href="{{ $url }}" class="mega-deepchild-tag" title="{{ $name }}">
            {{ $name }}
        </a>
        @if($hasKids)
            @include('frontend.partials.category-tree-node', [
                'items' => $children,
                'level' => $level + 1,
                'parentUrl' => $url
            ])
        @endif
    @endif
@endforeach
