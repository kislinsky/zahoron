@extends('account.admin.components.page')

@section('title', 'Все морги')

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
        @foreach($mortuaries as $mortuary)
            <tr>
                <th scope="row">{{$mortuary->id}}</th>
                <td><a href="#" class='text_black'></a>{{$mortuary->title}}</td>
                <td>{{$mortuary->city->title}}</td>
                <td><a href="#">Изменить</a></td>
                <td><a href="{{route('account.admin.mortuary.delete',$mortuary->id)}}">Удалить</a></td>
            </tr>
        @endforeach
    </tbody>
  </table>
  {{ $mortuaries->withPath(route('account.admin.mortuary'))->appends($_GET)->links() }}

@endsection


