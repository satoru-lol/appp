<style>
    .transaction-list-v2 {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }
    
    .transaction-list-item-v2 {
        display: flex;
        align-items: center;
        padding: 0.75rem;
        border-radius: 0.5rem;
        background-color: #fff;
        border: 1px solid #e0e0e0;
        transition: background-color 0.2s ease;
    }
    
    .transaction-list-item-v2:hover {
        background-color: #f8f9fa;
    }
    
    .transaction-icon-v2 {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: #f0f2f5;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        flex-shrink: 0;
    }
    
    .transaction-icon-v2 i {
        font-size: 1.25rem;
    }
    
    .transaction-details-v2 {
        flex-grow: 1;
        min-width: 0;
    }
    
    .transaction-description-v2 {
        font-size: 0.95rem;
        margin-bottom: 0.25rem;
    }
    
    .transaction-date-v2 {
        font-size: 0.85rem;
        color: #6c757d;
    }
    
    .transaction-amount-v2 {
        text-align: right;
        margin-left: 1rem;
        white-space: nowrap;
        font-size: 0.95rem;
    }
    
    .badge-v2 {
        display: inline-block;
        padding: 0.35rem 0.5rem;
        font-size: 0.75rem;
        font-weight: 500;
        border-radius: 0.25rem;
        text-align: center;
        white-space: nowrap;
        vertical-align: baseline;
        margin-top: 0.25rem;
    }
    
    .badge-v2-success {
        background-color: #d4edda;
        color: #155724;
    }
    
    .badge-v2-warning {
        background-color: #fff3cd;
        color: #856404;
    }
    
    .badge-v2-danger {
        background-color: #f8d7da;
        color: #721c24;
    }
    
    .badge-v2-secondary {
        background-color: #e2e3e5;
        color: #383d41;
    }
    
    .badge-v2-info {
        background-color: #d1ecf1;
        color: #0c5460;
    }
    
    @media (max-width: 576px) {
        .transaction-list-item-v2 {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .transaction-icon-v2 {
            margin-bottom: 0.5rem;
        }
        
        .transaction-amount-v2 {
            margin-left: 0;
            margin-top: 0.5rem;
            text-align: left;
            width: 100%;
        }
    }
</style>

<div class="transaction-list-v2">
    @forelse($mergedDataPaginated as $transaction)
        <div class="transaction-list-item-v2">
            <div class="transaction-icon-v2">
                @if($transaction->product)
                    @if($transaction->action == 'cancel')
                        <i class="bi bi-x-circle text-danger"></i>
                    @else
                        <i class="bi bi-patch-check-fill text-success"></i>
                    @endif
                @elseif($transaction->shop == 'donation')
                    <i class="bi bi-gift-fill text-warning"></i>
                @else
                    <i class="bi bi-wallet2 text-primary"></i>
                @endif
            </div>
            <div class="transaction-details-v2">
                <div class="transaction-description-v2">
                    <span class="text-muted" style="font-size: 0.85rem;">(ID: {{ $transaction->id }})</span>
                    @if($transaction->product)
                        <span style="font-weight: 500;">{{ $transaction->action == 'cancel' ? 'Отмена подписки' : 'Оформлена подписка' }}</span>
                        <div style="font-size: 0.9rem; color: #6c757d;">{{ $transaction->product->name }} ({{ $transaction->auto ? 'Автоплатеж' : 'Разовая' }})</div>
                    @elseif($transaction->shop == 'donation')
                        <span style="font-weight: 500;">Поддержка клуба</span>
                        @php
                            $clubId = null;
                            $club = null;
                            if (!empty($transaction->op_key) && strpos($transaction->op_key, 'club_donation_') === 0) {
                                $parts = explode('_', $transaction->op_key);
                                if (count($parts) > 2) {
                                    $clubId = $parts[2];
                                    // Получаем информацию о клубе
                                    $club = \App\Models\Club::find($clubId);
                                }
                            }
                        @endphp
                        @if($club)
                            <div style="font-size: 0.9rem;">
                                <a href="{{ route('clubShow', $club->id) }}" style="color: #613482; text-decoration: none;">
                                    <i class="bi bi-link-45deg"></i> {{ $club->title }}
                                </a>
                            </div>
                        @endif
                    @else
                        <span style="font-weight: 500;">{{ !empty($transaction->product_name) ? $transaction->product_name : "Пополнение счета" }}</span>
                    @endif
                </div>
                <div class="transaction-date-v2">
                    <i class="bi bi-clock me-1"></i>{{ \Carbon\Carbon::parse($transaction->created_at)->format('d.m.Y H:i') }}
                </div>
            </div>
            <div class="transaction-amount-v2">
                <div style="font-weight: 600; font-size: 1rem;">
                    @if($transaction->product)
                        {{ number_format($transaction->price, 2, ',', ' ') }} ₽
                    @elseif($transaction->shop == 'donation')
                        {{ number_format(abs($transaction->sum), 2, ',', ' ') }} ₽
                    @else
                        {{ number_format($transaction->sum, 2, ',', ' ') }} ₽
                    @endif
                </div>

                @php
                    $status = $transaction->state; // Теперь 'state' есть у всех
                    $statusClass = '';
                    $statusText = '';

                    switch (strtolower($status)) {
                        case 'success':
                        case 'completed': // Добавляем обработку 'completed'
                            $statusClass = 'badge-v2-success';
                            $statusText = 'Успешно';
                            break;
                        case 'pending':
                            $statusClass = 'badge-v2-warning';
                            $statusText = 'В ожидании';
                            break;
                        case 'failed':
                            $statusClass = 'badge-v2-danger';
                            $statusText = 'Ошибка';
                            break;
                        case 'cancelled':
                            $statusClass = 'badge-v2-secondary';
                            $statusText = 'Отменено';
                            break;
                        default:
                            $statusClass = 'badge-v2-info';
                            $statusText = ucfirst($status);
                            break;
                    }
                @endphp
                <div class="badge-v2 {{ $statusClass }}">{{ $statusText }}</div>
            </div>
        </div>
    @empty
        <div class="alert-v2 alert-v2-info text-center" style="padding: 1rem;">
            <i class="bi bi-info-circle me-2"></i>У вас еще не было транзакций.
        </div>
    @endforelse
</div>
<div class="pagination-container mt-4">
    <div class="pagination-summary text-muted">
        Показано с {{ $mergedDataPaginated->firstItem() }} по {{ $mergedDataPaginated->lastItem() }} из {{ $mergedDataPaginated->total() }} результатов
    </div>
    {{ $mergedDataPaginated->links('pagination::bootstrap-5') }}
</div> 