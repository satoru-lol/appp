<div class="profile-card-v2">
    <div class="profile-card-header">
        <h5><i class="bi bi-credit-card me-2"></i>Управление подпиской</h5>
    </div>
    <div class="profile-card-body">
        @if($subscription && $subscription->is_active)
            <div class="subscription-status subscription-active">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="mb-1"><i class="bi bi-check-circle me-2"></i>Подписка активна</h6>
                        <p class="mb-0">
                            Действует до: <strong>{{ $subscription->expires_at->format('d.m.Y') }}</strong>
                        </p>
                    </div>
                    <div class="text-end">
                        <div class="badge bg-success fs-6">{{ $subscription->type }}</div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="stat-card">
                        <div class="stat-number">{{ $subscription->days_left }}</div>
                        <div class="stat-label">Дней осталось</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card">
                        <div class="stat-number">{{ $subscription->price }}₽</div>
                        <div class="stat-label">Стоимость</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card">
                        <div class="stat-number">{{ $subscription->auto_renewal ? 'Да' : 'Нет' }}</div>
                        <div class="stat-label">Автопродление</div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 flex-wrap">
                @if($subscription->auto_renewal)
                    <form action="{{ route('v2.refactored.profile.cancel-subscription') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-v2-outline" 
                                onclick="return confirm('Вы уверены, что хотите отменить автопродление?')">
                            <i class="bi bi-x-circle me-2"></i>Отменить автопродление
                        </button>
                    </form>
                @else
                    <button type="button" class="btn btn-v2-primary" data-bs-toggle="modal" data-bs-target="#renewModal">
                        <i class="bi bi-arrow-clockwise me-2"></i>Продлить подписку
                    </button>
                @endif
                
                <button type="button" class="btn btn-v2-outline" data-bs-toggle="modal" data-bs-target="#subscriptionHistoryModal">
                    <i class="bi bi-clock-history me-2"></i>История платежей
                </button>
            </div>

        @else
            <div class="subscription-status subscription-inactive">
                <div class="text-center">
                    <h6 class="mb-2"><i class="bi bi-exclamation-triangle me-2"></i>Подписка не активна</h6>
                    <p class="mb-3">
                        Для доступа ко всем функциям платформы необходимо оформить подписку
                    </p>
                </div>
            </div>

            <!-- Планы подписки -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="card h-100 border-2" style="border-color: #613482;">
                        <div class="card-body text-center">
                            <h5 class="card-title text-primary">Месячная подписка</h5>
                            <div class="display-6 text-primary mb-3">990₽</div>
                            <ul class="list-unstyled">
                                <li><i class="bi bi-check text-success me-2"></i>Доступ ко всем курсам</li>
                                <li><i class="bi bi-check text-success me-2"></i>Участие во встречах</li>
                                <li><i class="bi bi-check text-success me-2"></i>Доступ к видеотеке</li>
                                <li><i class="bi bi-check text-success me-2"></i>Участие в клубах</li>
                            </ul>
                            <form action="{{ route('v2.refactored.profile.subscribe') }}" method="POST">
                                @csrf
                                <input type="hidden" name="plan" value="monthly">
                                <button type="submit" class="btn btn-v2-primary w-100">
                                    Оформить подписку
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 mb-3">
                    <div class="card h-100 border-2 border-success position-relative">
                        <div class="position-absolute top-0 start-50 translate-middle">
                            <span class="badge bg-success">Выгодно</span>
                        </div>
                        <div class="card-body text-center">
                            <h5 class="card-title text-success">Годовая подписка</h5>
                            <div class="display-6 text-success mb-1">9990₽</div>
                            <small class="text-muted mb-3 d-block">
                                <s>11880₽</s> Экономия 1890₽
                            </small>
                            <ul class="list-unstyled">
                                <li><i class="bi bi-check text-success me-2"></i>Доступ ко всем курсам</li>
                                <li><i class="bi bi-check text-success me-2"></i>Участие во встречах</li>
                                <li><i class="bi bi-check text-success me-2"></i>Доступ к видеотеке</li>
                                <li><i class="bi bi-check text-success me-2"></i>Участие в клубах</li>
                                <li><i class="bi bi-check text-success me-2"></i>Приоритетная поддержка</li>
                            </ul>
                            <form action="{{ route('v2.refactored.profile.subscribe') }}" method="POST">
                                @csrf
                                <input type="hidden" name="plan" value="yearly">
                                <button type="submit" class="btn btn-success w-100">
                                    Оформить подписку
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Модальное окно продления подписки -->
<div class="modal fade" id="renewModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Продление подписки</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Выберите период продления:</p>
                <div class="row">
                    <div class="col-6">
                        <div class="card">
                            <div class="card-body text-center">
                                <h6>1 месяц</h6>
                                <div class="h5 text-primary">990₽</div>
                                <form action="{{ route('v2.refactored.profile.renew') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="period" value="1">
                                    <button type="submit" class="btn btn-v2-primary btn-sm">Продлить</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card border-success">
                            <div class="card-body text-center">
                                <h6>12 месяцев</h6>
                                <div class="h5 text-success">9990₽</div>
                                <small class="text-muted"><s>11880₽</s></small>
                                <form action="{{ route('v2.refactored.profile.renew') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="period" value="12">
                                    <button type="submit" class="btn btn-success btn-sm">Продлить</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно истории платежей -->
<div class="modal fade" id="subscriptionHistoryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">История платежей</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                @if(isset($user->payments) && $user->payments->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Дата</th>
                                    <th>Сумма</th>
                                    <th>Период</th>
                                    <th>Статус</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($user->payments->take(10) as $payment)
                                    <tr>
                                        <td>{{ $payment->created_at->format('d.m.Y H:i') }}</td>
                                        <td>{{ $payment->amount }}₽</td>
                                        <td>{{ $payment->period_months }} мес.</td>
                                        <td>
                                            @if($payment->status === 'completed')
                                                <span class="badge bg-success">Оплачено</span>
                                            @elseif($payment->status === 'pending')
                                                <span class="badge bg-warning">В обработке</span>
                                            @else
                                                <span class="badge bg-danger">Отклонено</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-receipt display-1 text-muted"></i>
                        <p class="text-muted mt-2">История платежей пуста</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Обработка форм подписки
    const subscriptionForms = document.querySelectorAll('form[action*="subscribe"], form[action*="renew"]');
    
    subscriptionForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Обработка...';
            submitBtn.disabled = true;
            
            // Если форма не отправится, восстанавливаем кнопку через 5 секунд
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 5000);
        });
    });
});
</script>