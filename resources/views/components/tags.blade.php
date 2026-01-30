
@if(isset($tags) && $tags->count()>0)
    <div class="ul_tags">
        @foreach ($tags as $tag)
            <div class="li_tag text_black">{{ $tag->name }}</div>
        @endforeach
    </div>
@endif