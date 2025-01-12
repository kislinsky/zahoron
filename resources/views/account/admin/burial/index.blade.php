@extends('account.admin.components.page')

@section('title', 'Все захоронения')

@section('content')

<div class="table_admin">
  <table class="table admin_table">
    <thead>
      <tr>
        <th scope="col">Id</th>
        <th scope="col">Статус</th>
        <th scope="col">Город</th>
        <th scope="col">Кладбище</th>
        <th scope="col">Изменить</th>
        <th scope="col">Удалить</th>
      </tr>
    </thead>
    <tbody>
        @foreach($burials as $burial)
            <tr>
                <th scope="row">{{$burial->id}}</th>
                <td><div class='text_black'> {{statusBurial($burial->status)}}</div></td>
                <td>{{$burial->cemetery->city->title}}</td>
                <td>{{$burial->cemetery->title}}</td>
                <td><a href="#">Изменить</a></td>
                <td>
                  <form action="{{route('account.admin.burial.delete',$burial->id)}}" method='post' class='form_admin_delete'>
                      @csrf
                      @method('DELETE')
                      <button class='red_btn'>Удалить</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
  </table>
  {{ $burials->withPath(route('account.admin.burial'))->appends($_GET)->links() }}
</div>
  

@endsection


