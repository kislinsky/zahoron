@extends('account.admin.components.page')

@section('title', "Изменение robots.txt")

@section('content')


    <div class="">
        <form class='default_admin_form' method="post" enctype="multipart/form-data" action="{{ route('account.admin.settings-site.robots-txt.update') }}">
            @csrf
            <div class="mb-3">
  <label for="content" class="form-label">Ваш текст</label>
  <textarea class="form-control" id="content" name="content" rows="10">{!! $content !!}</textarea>
</div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary mb-3">Обновить</button>
            </div>   
        </form>
    </div>
  

@endsection