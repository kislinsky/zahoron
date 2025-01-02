@extends('account.admin.components.page')

@section('title', 'Все кладбища')

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
        @foreach($cemeteries as $cemetery)
            <tr>
                <th scope="row">{{$cemetery->id}}</th>
                <td><a href="#" class='text_black'></a>{{$cemetery->title}}</td>
                <td>{{$cemetery->city->title}}</td>
                <td><a href="#">Изменить</a></td>
                <td><a href="{{route('account.admin.cemetery.delete',$cemetery->id)}}">Удалить</a></td>
            </tr>
        @endforeach
    </tbody>
  </table>
  {{ $cemeteries->withPath(route('account.admin.cemetery'))->appends($_GET)->links() }}
</div>
  

@endsection


