@extends('account.admin.components.page')

@section('title', 'Все колумбарии')

@section('content')
<div class="table_admin">
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
        @foreach($columbariums as $columbarium)
            <tr>
                <th scope="row">{{$columbarium->id}}</th>
                <td><a href="#" class='text_black'></a>{{$columbarium->title}}</td>
                <td>{{$columbarium->city->title}}</td>
                <td><a href="#">Изменить</a></td>
                <td><a href="{{route('account.admin.columbarium.delete',$columbarium->id)}}">Удалить</a></td>
            </tr>
        @endforeach
    </tbody>
  </table>
  {{ $columbariums->withPath(route('account.admin.columbarium'))->appends($_GET)->links() }}
</div>
@endsection


