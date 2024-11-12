{{view('forms.add-review-product',compact('product'))}}
<div class="content_product">
    <div class="title_news">Отзывы клиентов</div>
    <div class="blue_btn" data-bs-toggle="modal" data-bs-target="#add_review_form">Оставить отзыв</div>
    @if (isset($comments))
        @if (count($comments)>0)
            @foreach ($comments as $comment)
                <div class="comment">
                    <div class="flex_comment">
                        <div class="text_comment">{{ $comment->name }} {{ $comment->surname }}</div>
                        <div class="text_comment">{{ $comment->created_at }}</div>
                    </div>
                    <div class="text_comment">{{ $comment->content }}</div>
                </div>
            @endforeach
        @endif
    @endif
</div>