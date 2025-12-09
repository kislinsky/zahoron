@extends('account.agency.components.page')
@section('title', 'Подчиненные')

@section('content')
<div class="container-fluid margin_top_20">
    <div class="row">
        <!-- Кнопка добавления пользователя -->
        <div class="col-12 mb-4">
            <button class="btn btn-primary" id="toggleUserForm" style="background-color: #0080D7; border-color: #0080D7;">
                <i class="fas fa-plus me-2"></i>Добавить пользователя
            </button>
        </div>

        <!-- Форма добавления пользователя (изначально скрыта) -->
        <div class="col-12 mb-4 ">
            <div class="card border-0 shadow-sm" id="userFormContainer" style="display: none;">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3 border-bottom">
                    <h5 class="mb-0 text-dark fw-bold">Добавить нового пользователя</h5>
                </div>
                <div class="card-body">
                    <form id="createUserForm" action="{{ route('account.agency.users.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="text_black">Фамилия *</label>
                                <input type="text" class="form-control" name="surname" required style="border-color: #e0e0e0;">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text_black">Имя *</label>
                                <input type="text" class="form-control" name="name" required style="border-color: #e0e0e0;">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="text_black">Отчество</label>
                                <input type="text" class="form-control" name="patronymic" style="border-color: #e0e0e0;">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text_black">Телефон *</label>
                                <input type="tel" class="form-control" name="phone" required style="border-color: #e0e0e0;">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="text_black">Email </label>
                            <input type="email" class="form-control" name="email"  style="border-color: #e0e0e0;">
                        </div>
                        <div class="mb-3">
                            <label class="text_black">Организация</label>
                            <select class="form-select" name="organization_id_branch" style="border-color: #e0e0e0;">
                                <option value="">Выберите организацию</option>
                                @foreach($organizations as $org)
                                    <option value="{{ $org->id }}">{{ $org->title }}-{{ $org->adres }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary" style="background-color: #0080D7; border-color: #0080D7;">
                                <i class="fas fa-save me-2"></i>Создать
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Список пользователей -->
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3 border-bottom">
                    <h5 class="mb-0 text-dark fw-bold">Управление пользователями</h5>
                    <span class="badge bg-primary rounded-pill">{{ $users->count() }}</span>
                </div>
                <div class="card-body p-0">
                    <div class="users-container">
                        @foreach($users as $user)
                        <div class="user-item bg-white border-bottom">
                            <div class="user-content p-4">
                                <div class="flex_user_agency">

                                    <!-- Основная информация -->
                                    <div class="user-main-info">
                                        <h6 class="user-name mb-2 fw-bold text-dark">
                                            {{ $user->surname }} {{ $user->name }} {{ $user->patronymic }}
                                        </h6>
                                        <div class="user-contacts">
                                            <div class="contact-item d-flex align-items-center mb-1">
                                                <i class="fas fa-phone me-2 text-muted" style="width: 16px;"></i>
                                                <span class="text_black">{{ $user->phone  }}</span>
                                            </div>
                                            <div class="contact-item d-flex align-items-center">
                                                <i class="fas fa-envelope me-2 text-muted" style="width: 16px;"></i>
                                                <span class="text_black">{{ $user->email }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Организация -->
                                    <div class="col-corganization">
                                        <div class="organization-section">
                                            <div class="text_gray">Привязанная организация</div>
                                            <div class="text_black_bold">{{ $user->organizationBranch->title ?? 'Нет привязанной организации'}}</div>
                                        </div>
                                    </div>

                                    <!-- Действия -->
                                    <div class="col-doing">
                                        <div class="user-actions d-flex gap-2 justify-content-lg-end">
                                            <form action="{{ route('account.agency.users.edit',$user->id) }}" method="get">
                                                 <button class="btn btn-outline-primary btn-sm edit-user" 
                                                        data-user-id="{{ $user->id }}"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#editUserModal"
                                                        style="border-color: #0080D7; ">
                                                    <i class="fas fa-edit me-1"></i>Изменить
                                                </button>
                                            </form>

                                            <form method="post" action="{{ route('account.agency.users.destroy',$user->id) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-outline-danger btn-sm delete-user"  data-user-id="{{ $user->id }}"style="border-color: #dc3545; "><i class="fas fa-trash me-1"></i>Удалить</button>
                                            </form>
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach

                        @if($users->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Пользователи не найдены</p>
                            <button class="btn btn-primary mt-2" id="toggleUserFormEmpty">
                                <i class="fas fa-plus me-2"></i>Добавить первого пользователя
                            </button>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection
