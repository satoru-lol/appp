@extends('app', ['title' => 'Диагностика подписок'])

@section('content')
<style>
    .diagnostics-container { max-width: 1200px; margin: 2rem auto; }
    .card-v2 { background: #fff; border-radius: 1rem; box-shadow: 0 4px 24px rgba(0,0,0,0.08); margin-bottom: 2rem; }
    .card-header-v2 { padding: 1rem 1.5rem; background-color: #f8f9fa; border-bottom: 1px solid #dee2e6; font-size: 1.2rem; font-weight: 600; color: #343a40; }
    .card-body-v2 { padding: 1.5rem; }
    .table-v2 { width: 100%; border-collapse: collapse; }
    .table-v2 th, .table-v2 td { padding: 0.75rem 1rem; border: 1px solid #dee2e6; text-align: left; }
    .table-v2 thead th { background-color: #e9ecef; }
    .table-v2 tbody tr:nth-child(odd) { background-color: #f8f9fa; }
    .alert-v2 { padding: 1rem; border-radius: 0.5rem; }
    .alert-v2-warning { background-color: #fff3cd; color: #856404; }
    .alert-v2-info { background-color: #d1ecf1; color: #0c5460; }
</style>

<div class="container-fluid py-4">
    <h1 class="h3 mb-4">Диагностика системы подписок</h1>

    <div class="card-v2">
        <div class="card-header-v2">Сводка по подпискам</div>
        <div class="card-body-v2">
            @if($subscriptionSummary->isEmpty())
                <div class="alert-v2 alert-v2-info">Нет данных для анализа.</div>
            @else
            <table class="table-v2">
                <thead>
                    <tr>
                        <th>Уровень (Level)</th>
                        <th>Название</th>
                        <th>Количество пользователей</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($subscriptionSummary as $summary)
                        <tr>
                            <td>{{ $summary->level }}</td>
                            <td>{{ $levelToNameMap[$summary->level] ?? 'Неизвестный уровень (' . $summary->level . ')' }}</td>
                            <td>{{ $summary->user_count }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>

    <div class="card-v2 mt-4">
        <div class="card-header-v2 text-white bg-danger">Массовые действия</div>
        <div class="card-body-v2">
            <h5 class="card-title">Отмена транзитных подписок (Уровень 8)</h5>
            <p class="card-text">
                Это действие найдет всех пользователей с уровнем подписки "Transitional" (уровень 8)
                и отменит их подписку, установив уровень 0 (Без подписки).
                Это необходимо, чтобы пользователи без активных платежей перешли на платную модель.
                Действие необратимо.
            </p>
            <form action="{{ route('v2.admin.subscription.revoke_transitional') }}" method="POST" onsubmit="return confirm('Вы уверены, что хотите отменить все транзитные подписки? Это действие необратимо.');">
                @csrf
                <button type="submit" class="btn btn-danger">Отменить все транзитные подписки</button>
            </form>
        </div>
    </div>

    <div class="card-v2 mt-4">
        <div class="card-header-v2">
            Проблемы синхронизации оплат и подписок
        </div>
        <div class="card-body-v2">
            @if($usersWithPaymentIssues->isEmpty())
                <div class="alert-v2 alert-v2-info">Не найдено пользователей с рассинхронизацией оплат и подписок.</div>
            @else
            <table class="table-v2">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Имя</th>
                        <th>Email</th>
                        <th>Проблема</th>
                        <th>Оплаченный продукт</th>
                        <th>Дата платежа</th>
                        <th>Действие</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($usersWithPaymentIssues as $item)
                    <tr>
                        <td>{{ $item['user']['id'] }}</td>
                        <td>{{ $item['user']['firstname'] }} {{ $item['user']['lastname'] }}</td>
                        <td>{{ $item['user']['email'] }}</td>
                        <td>
                            <span class="badge bg-warning text-dark">{{ $item['issue'] }}</span>
                        </td>
                        <td>{{ $item['payment_product'] }}</td>
                        <td>{{ $item['payment_date']->format('d.m.Y H:i') }}</td>
                        <td>
                            <form action="{{ route('v2.admin.subscription.fix') }}" method="POST">
                                @csrf
                                <input type="hidden" name="payment_id" value="{{ $item['payment_id'] }}">
                                <button type="submit" class="btn btn-sm btn-outline-primary">Исправить</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success mt-3">
            {{ session('success') }}
        </div>
    @endif
</div>
@endsection 