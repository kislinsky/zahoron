<div class="container navigation_pages">
    @foreach($pages_navigation as $page_navigation)
        @if(!isset($page_navigation[1]))
            <span>{{ $page_navigation[0] }}</span>
        @else
            <a href="{{ $page_navigation[1] }}">{{ $page_navigation[0] }}</a>/
        @endif
    @endforeach
</div>