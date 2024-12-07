@extends('account.admin.components.page')

@section('title', 'Все крематории')

@section('content')
<table class="table admin_table">
    <thead>
      <tr>
        <th scope="col">Id</th>
        <th scope="col">Название</th>
        <th scope="col">Город</th>
        <th scope="col">Изменить</th>
        <th scope="col">Удалить</th>
      </tr>
    </thead>
    <tbody>
        @foreach($crematoriums as $crematorium)
            <tr>
                <th scope="row">{{$crematorium->id}}</th>
                <td><a href="#" class='text_black'></a>{{$crematorium->title}}</td>
                <td>{{$crematorium->city->title}}</td>
                <td><a href="#">Изменить</a></td>
                <td><a href="{{route('account.admin.crematorium.delete',$crematorium->id)}}">Удалить</a></td>
            </tr>
        @endforeach
    </tbody>
  </table>
  {{ $crematoriums->withPath(route('account.admin.crematorium'))->appends($_GET)->links() }}

@endsection


