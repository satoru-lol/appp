<div class="profile-card-v2">
    <div class="profile-card-header">
        <h5><i class="bi bi-wallet me-2"></i>Баланс и платежи</h5>
    </div>
    <div class="profile-card-body">
        <!-- Текущий баланс -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="stat-card" style="background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);">
                    <div class="stat-number text-success">{{ number_format($user->balance ?? 0, 0, ',', ' ') }}₽</div>
                    <div class="stat-label">Текущий баланс</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-number">{{ number_format($user->total_spent ?? 0, 0, ',', ' ') }}₽</div>
                    <div class="stat-label">Всего потрачено</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-number">{{ $user->payments_count ?? 0 }}</div>
                    <div class="stat-label">Количество платежей</div>
                </div>
            </div>
        </div>

        <!-- Пополнение баланса -->
        <div class="row mb-4">
            <div class="col-lg-6">
                <div class="card border-primary">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Пополнить баланс</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('v2.refactored.profile.add-balance') }}" method="POST" id="balanceForm">
                            @csrf
                            <div class="mb-3">
                                <label for="amount" class="form-label">Сумма пополнения</label>
                                <div class="input-group">
                                    <input type="number" name="amount" id="amount" class="form-control form-control-v2" 
                                           min="100" max="50000" step="10" placeholder="1000" required>
                                    <span class="input-group-text">₽</span>
                                </div>
                                <div class="form-text">Минимальная сумма: 100₽, максимальная: 50 000₽</div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Способ оплаты</label>
                                <div class="d-flex gap-2 flex-wrap">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" 
                                               id="card" value="card" checked>
                                        <label class="form-check-label" for="card">
                                            <i class="bi bi-credit-card me-1"></i>Банковская карта
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" 
                                               id="yandex" value="yandex">
                                        <label class="form-check-label" for="yandex">
                                            <i class="bi bi-wallet me-1"></i>ЮMoney
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" 
                                               id="qiwi" value="qiwi">
                                        <label class="form-check-label" for="qiwi">
                                            <i class="bi bi-phone me-1"></i>QIWI
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-v2-primary w-100">
                                <i class="bi bi-credit-card me-2"></i>Пополнить баланс
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="card border-success">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0"><i class="bi bi-gift me-2"></i>Бонусная программа</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>Бонусные баллы:</span>
                                <strong>{{ $user->bonus_points ?? 0 }} баллов</strong>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-success" role="progressbar" 
                                     style="width: {{ min(($user->bonus_points ?? 0) / 1000 * 100, 100) }}%"></div>
                            </div>
                            <small class="text-muted">До следующего уровня: {{ max(1000 - ($user->bonus_points ?? 0), 0) }} баллов</small>
                        </div>
                        
                        <div class="text-center">
                            <p class="small mb-3">1 балл = 1₽ при оплате подписки</p>
                            @if(($user->bonus_points ?? 0) >= 100)
                                <button type="button" class="btn btn-success btn-sm" 
                                        data-bs-toggle="modal" data-bs-target="#bonusModal">
                                    <i class="bi bi-arrow-right-circle me-1"></i>Использовать баллы
                                </button>
                            @else
                                <small class="text-muted">Минимум для использования: 100 баллов</small>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Быстрые суммы -->
        <div class="mb-4">
            <label class="form-label">Быстрое пополнение:</label>
            <div class="d-flex gap-2 flex-wrap">
                <button type="button" class="btn btn-outline-primary btn-sm quick-amount" data-amount="500">500₽</button>
                <button type="button" class="btn btn-outline-primary btn-sm quick-amount" data-amount="1000">1000₽</button>
                <button type="button" class="btn btn-outline-primary btn-sm quick-amount" data-amount="2000">2000₽</button>
                <button type="button" class="btn btn-outline-primary btn-sm quick-amount" data-amount="5000">5000₽</button>
                <button type="button" class="btn btn-outline-primary btn-sm quick-amount" data-amount="10000">10000₽</button>
            </div>
        </div>
    </div>
</div>

<!-- История транзакций -->
<div class="profile-card-v2 mt-4">
    <div class="profile-card-header">
        <h5><i class="bi bi-clock-history me-2"></i>История транзакций</h5>
    </div>
    <div class="profile-card-body">
        @if(isset($user->transactions) && $user->transactions->count() > 0)
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
                        @foreach($user->transactions->take(10) as $transaction)
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
                                    @else
                                        <span class="badge bg-secondary">{{ $transaction->status }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="text-center mt-3">
                <button type="button" class="btn btn-v2-outline" data-bs-toggle="modal" data-bs-target="#allTransactionsModal">
                    <i class="bi bi-list me-2"></i>Показать все транзакции
                </button>
            </div>
        @else
            <div class="text-center py-4">
                <i class="bi bi-receipt display-1 text-muted"></i>
                <h6 class="mt-3 text-muted">История транзакций пуста</h6>
                <p class="text-muted">Здесь будут отображаться все ваши платежи и пополнения</p>
            </div>
        @endif
    </div>
</div>

<!-- Модальное окно использования бонусов -->
<div class="modal fade" id="bonusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Использовать бонусные баллы</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('v2.refactored.profile.use-bonus') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Доступно бонусных баллов: <strong>{{ $user->bonus_points ?? 0 }}</strong></p>
                    <div class="mb-3">
                        <label for="bonus_amount" class="form-label">Количество баллов для использования</label>
                        <input type="number" name="bonus_amount" id="bonus_amount" class="form-control" 
                               min="100" max="{{ $user->bonus_points ?? 0 }}" step="10" required>
                        <div class="form-text">Минимум: 100 баллов</div>
                    </div>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Баллы будут зачислены на ваш баланс в соотношении 1:1
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-success">Использовать баллы</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Модальное окно всех транзакций -->
<div class="modal fade" id="allTransactionsModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Все транзакции</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <div class="row">
                        <div class="col-md-4">
                            <input type="text" class="form-control" placeholder="Поиск по описанию..." id="transactionSearch">
                        </div>
                        <div class="col-md-4">
                            <select class="form-select" id="transactionType">
                                <option value="">Все типы</option>
                                <option value="deposit">Пополнения</option>
                                <option value="payment">Оплаты</option>
                                <option value="bonus">Бонусы</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select class="form-select" id="transactionStatus">
                                <option value="">Все статусы</option>
                                <option value="completed">Завершено</option>
                                <option value="pending">В обработке</option>
                                <option value="failed">Ошибка</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div id="transactionsContainer">
                    <!-- Здесь будут загружаться транзакции через AJAX -->
                    <div class="text-center py-4">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Загрузка...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Быстрое пополнение
    const quickAmountBtns = document.querySelectorAll('.quick-amount');
    const amountInput = document.getElementById('amount');
    
    quickAmountBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const amount = this.dataset.amount;
            amountInput.value = amount;
            
            // Подсветка выбранной кнопки
            quickAmountBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
        });
    });
    
    // Валидация формы пополнения
    const balanceForm = document.getElementById('balanceForm');
    if (balanceForm) {
        balanceForm.addEventListener('submit', function(e) {
            const amount = parseInt(amountInput.value);
            
            if (amount < 100 || amount > 50000) {
                e.preventDefault();
                alert('Сумма должна быть от 100 до 50 000 рублей');
                return false;
            }
            
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Обработка...';
            submitBtn.disabled = true;
        });
    }
    
    // Загрузка всех транзакций при открытии модального окна
    const allTransactionsModal = document.getElementById('allTransactionsModal');
    if (allTransactionsModal) {
        allTransactionsModal.addEventListener('shown.bs.modal', function() {
            loadAllTransactions();
        });
    }
    
    // Фильтры транзакций
    const searchInput = document.getElementById('transactionSearch');
    const typeSelect = document.getElementById('transactionType');
    const statusSelect = document.getElementById('transactionStatus');
    
    if (searchInput && typeSelect && statusSelect) {
        [searchInput, typeSelect, statusSelect].forEach(element => {
            element.addEventListener('change', debounce(loadAllTransactions, 300));
        });
    }
});

function loadAllTransactions() {
    const container = document.getElementById('transactionsContainer');
    const search = document.getElementById('transactionSearch')?.value || '';
    const type = document.getElementById('transactionType')?.value || '';
    const status = document.getElementById('transactionStatus')?.value || '';
    
    container.innerHTML = '<div class="text-center py-4"><div class="spinner-border" role="status"></div></div>';
    
    fetch(`{{ route('v2.refactored.profile.transactions') }}?search=${search}&type=${type}&status=${status}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                container.innerHTML = data.html;
            } else {
                container.innerHTML = '<div class="alert alert-danger">Ошибка загрузки данных</div>';
            }
        })
        .catch(error => {
            container.innerHTML = '<div class="alert alert-danger">Ошибка загрузки данных</div>';
        });
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}
</script>

<style>
.quick-amount.active {
    background-color: #613482;
    border-color: #613482;
    color: white;
}

.table th {
    font-weight: 600;
    font-size: 0.9rem;
    border-bottom: 2px solid #e0e0e0;
}

.table td {
    vertical-align: middle;
    font-size: 0.9rem;
}

.progress {
    border-radius: 10px;
}

.progress-bar {
    border-radius: 10px;
}
</style>