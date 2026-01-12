@extends('account.agency.components.page')
@section('title', 'Обновить пользователя')

@section('content')
<div class="container-fluid margin_top_20">
    <div class="row">
        <div class="col-12 mb-4 ">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3 border-bottom">
                    <h5 class="mb-0 text-dark fw-bold">Добавить нового пользователя</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('account.agency.users.update',$user->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="text_black">Фамилия *</label>
                                <input type="text" class="form-control" name="surname" required style="border-color: #e0e0e0;" value='{{ $user->surname }}'>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text_black">Имя *</label>
                                <input type="text" class="form-control" name="name" required style="border-color: #e0e0e0;" value='{{ $user->name }}'>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="text_black">Отчество</label>
                                <input type="text" class="form-control" name="patronymic" style="border-color: #e0e0e0;" value='{{ $user->patronymic }}'>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text_black">Телефон *</label>
                                <input type="tel" class="form-control" name="phone" required style="border-color: #e0e0e0;" value='{{ $user->phone }}'>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="text_black">Email </label>
                            <input type="email" class="form-control" name="email"  style="border-color: #e0e0e0;" value='{{ $user->email }}'>
                        </div>
                        <div class="mb-3">
                            <label class="text_black">Организация</label>
                            <select class="form-select" name="organization_id_branch" style="border-color: #e0e0e0;">
                                <option value="">Выберите организацию</option>
                               @foreach($organizations as $org)
                                    <option value="{{ $org->id }}" 
                                        @if(isset($organization_user) && $organization_user->id == $org->id) 
                                            selected 
                                        @endif>
                                        {{ $org->title }} {{ $org->adres }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary" style="background-color: #0080D7; border-color: #0080D7;">
                                <i class="fas fa-save me-2"></i>Обновить
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>    
    </div>
</div>


@endsection
