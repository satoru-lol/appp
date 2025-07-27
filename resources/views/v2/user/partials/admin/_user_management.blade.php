<div class="container-xl px-4 mt-4">
    <div class="user-management-card card">
        <br>
        <div class="card-title">
            <h3>Пользователи (Доступ <b>{{$authedRole}}</b>)</h3>
        </div>
        <div class="card-body">
            <button type="button" class="btn btn-dark" id="addAdmin">
                Добавить администратора
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                     fill="currentColor" class="bi bi-person-gear" viewBox="0 0 16 16">
                    <path
                        d="M11 5a3 3 0 1 1-6 0 3 3 0 0 1 6 0M8 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4m.256 7a4.5 4.5 0 0 1-.229-1.004H3c.001-.246.154-.986.832-1.664C4.484 10.68 5.711 10 8 10q.39 0 .74.025c.226-.341.496-.65.804-.918Q8.844 9.002 8 9c-5 0-6 3-6 4s1 1 1 1zm3.63-4.54c.18-.613 1.048-.613 1.229 0l.043.148a.64.64 0 0 0 .921.382l.136-.074c.561-.306 1.175.308.87.869l-.075.136a.64.64 0 0 0 .382.92l.149.045c.612.18.612 1.048 0 1.229l-.15.043a.64.64 0 0 0-.38.921l.074.136c.305.561-.309 1.175-.87.87l-.136-.075a.64.64 0 0 0-.92.382l-.045.149c-.18.612-1.048.612-1.229 0l-.043-.15a.64.64 0 0 0-.921-.38l-.136.074c-.561.305-1.175-.309-.87-.87l.075-.136a.64.64 0 0 0-.382-.92l-.148-.045c-.613-.18-.613-1.048 0-1.229l.148-.043a.64.64 0 0 0 .382-.921l-.074-.136c-.306-.561.308-1.175.869-.87l.136.075a.64.64 0 0 0 .92-.382zM14 12.5a1.5 1.5 0 1 0-3 0 1.5 1.5 0 0 0 3 0"/>
                </svg>
            </button>
            <br><br>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Эл. Почта</th>
                        <th scope="col">Роль</th>
                        <th scope="col">Телефон</th>
                        <th scope="col">Изменить роль</th>
                        <th scope="col">Изменить доступ</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($users as $key => $user)
                        <tr>
                            <th scope="row">{{$key+1}}</th>
                            <td>{{$user->email}}</td>
                            <td>{{$user->role}}</td>
                            <td>{{$user->phone}}</td>
                            <td>
                                <button id="editRoleBtn" data-user-id="{{$user->id}}"
                                        class="btn btn-primary editRoleBtn" type="button">
                                    Изменить роль
                                </button>
                            </td>
                            <td>
                                <button data-user-id="{{$user->id}}"
                                        class="btn btn-success editPerms" type="button">
                                    Изменить доступ
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="alert alert-warning" role="alert">
                                    Пользователей нет
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
                <div class="d-flex justify-content-center mt-4">
                    {{ $users->appends(['users_page' => request()->input('users_page')])->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
</div> 