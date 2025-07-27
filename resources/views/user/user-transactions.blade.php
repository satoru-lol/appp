@if($user)
    <div class="container-xl px-4 mt-4 user-info-card card">
        <br>
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Данные пользователя</h4>
        </div>
        <div class="card-body">
            <div class="row mb-3 align-items-center">
                <div class="col-md-3 text-center">
                    <div class="profile-picture-wrapper">
                        <img src="/avatar/{{ $user->id }}" alt="Profile Picture" class="profile-picture rounded-circle shadow">
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Имя:</strong> {{$user->firstname}}</p>
                            <p><strong>Фамилия:</strong> {{$user->lastname}}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Телефон:</strong> {{$user->phone}}</p>
                            <p><strong>Почта:</strong> {{$user->email}}</p>
                        </div>
                    </div>
                </div>
            </div>

            <h5 class="mb-3">История транзакций</h5>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="thead-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Сумма</th>
                        <th scope="col">Подписка</th>
                        <th scope="col">Согласие автосписания</th>
                        <th scope="col">Дата оплаты</th>
                        <th scope="col">Статус</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($transactions as $key => $transaction)
                        <tr>
                            <th scope="row">{{$key+1}}</th>
                            <td>{{$transaction->sum}} руб.</td>
                            <td>{{!empty($transaction->product_name) ? $transaction->product_name : "Пополнение счета"}}</td>
                            <td>{{$transaction->accepted_perms ? 'Принято' : 'Без автосписания'}}</td>
                            <td>{{$transaction->created_at}}</td>
                            <td class="@if($transaction->state == 'failed') text-danger
                                           @elseif($transaction->state == 'success') text-success
                                           @elseif($transaction->state == 'pending') text-warning @endif">
                                @if($transaction->state == 'failed') Ошибка
                                @elseif($transaction->state == 'success') Успешно
                                @elseif($transaction->state == 'pending') Ожидание @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="alert alert-warning" role="alert">Транзакций нет</div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="mt-4">
        <button type="button" id="exportButton" data-user-id="{{$user->id}}" class="btn btn-success">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-excel" viewBox="0 0 16 16">
                <path d="M5.884 6.68a.5.5 0 1 0-.768.64L7.349 10l-2.233 2.68a.5.5 0 0 0 .768.64L8 10.781l2.116 2.54a.5.5 0 0 0 .768-.641L8.651 10l2.233-2.68a.5.5 0 0 0-.768-.64L8 9.219l-2.116-2.54z"/>
                <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2M9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5z"/>
            </svg> Выгрузить данные
        </button>
    </div>
    <script>
        document.getElementById('exportButton').addEventListener('click', function() {
            debugger;
            var id = $(this).data("user-id");
            const overlay = document.getElementById('loadingOverlay');
            overlay.style.display = 'flex';
            // Trigger file download
            window.location.href = '/export-users/' + id;

            // Remove loading spinner after 3 seconds (simulating the file download)
            setTimeout(function() {
                overlay.style.display = 'none';
            }, 3000);
        });
    </script>
@endif
