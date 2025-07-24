@if($transactions && $transactions->count() > 0)
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Дата</th>
                    <th>Тип</th>
                    <th>Описание</th>
                    <th>Сумма</th>
                    <th>Статус</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $transaction)
                    <tr>
                        <td>
                            <small>{{ $transaction->created_at->format('d.m.Y H:i') }}</small>
                        </td>
                        <td>
                            @if($transaction->type === 'deposit')
                                <span class="badge bg-success">Пополнение</span>
                            @elseif($transaction->type === 'payment')
                                <span class="badge bg-primary">Оплата</span>
                            @elseif($transaction->type === 'bonus')
                                <span class="badge bg-warning">Бонус</span>
                            @elseif($transaction->type === 'refund')
                                <span class="badge bg-info">Возврат</span>
                            @else
                                <span class="badge bg-secondary">Другое</span>
                            @endif
                        </td>
                        <td>
                            <div class="fw-medium">{{ $transaction->description }}</div>
                            @if($transaction->reference)
                                <small class="text-muted">{{ $transaction->reference }}</small>
                            @endif
                        </td>
                        <td>
                            <span class="{{ $transaction->amount > 0 ? 'text-success' : 'text-danger' }}">
                                {{ $transaction->amount > 0 ? '+' : '' }}{{ number_format($transaction->amount, 0, ',', ' ') }}₽
                            </span>
                        </td>
                        <td>
                            @if($transaction->status === 'completed')
                                <span class="badge bg-success">Завершено</span>
                            @elseif($transaction->status === 'pending')
                                <span class="badge bg-warning">В обработке</span>
                            @elseif($transaction->status === 'failed')
                                <span class="badge bg-danger">Ошибка</span>
                            @elseif($transaction->status === 'cancelled')
                                <span class="badge bg-secondary">Отменено</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst($transaction->status) }}</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    @if(method_exists($transactions, 'links'))
        <div class="d-flex justify-content-center mt-3">
            {{ $transactions->appends(request()->query())->links() }}
        </div>
    @endif
@else
    <div class="text-center py-4">
        <i class="bi bi-receipt display-1 text-muted"></i>
        <h6 class="mt-3 text-muted">Транзакции не найдены</h6>
        <p class="text-muted">Попробуйте изменить параметры поиска</p>
    </div>
@endif