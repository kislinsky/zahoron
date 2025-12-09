<div class="block_location">
    <div class="title_news">Область</div>
    <div class="ul_location">
        @foreach ($edges as $edge)
            <div data-id="{{ $edge->id }}" class="li_location li_edge" >{{ $edge->title }}</div>
        @endforeach
    </div>
</div>
