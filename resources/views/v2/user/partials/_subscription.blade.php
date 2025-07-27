<div class="profile-card-v2 mt-4">
    <div class="profile-card-header">
        <h5 class="mb-0">Управление подпиской</h5>
    </div>
    <div class="profile-card-body">
        <div class="vstack gap-4">
            <!-- Current Subscription Status -->
            <div class="p-3 rounded-3" style="background-color: #f8f9fa;">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                    <div class="mb-3 mb-md-0">
                        <h5 class="mb-1 text-muted">Текущий тариф:</h5>
                        <p class="h4 fw-bold mb-0" style="color: {{ $subscriptionStatus->isActive ? '#613482' : '#6c757d' }};">
                            <i class="bi bi-gem me-2"></i>{{ $subscriptionTxt ?? 'Нет подписки' }}
                        </p>
                    </div>
                    @if ($subscription->level > 0 && $subscription->expired_at)
                        <div class="text-md-end">
                            <p class="mb-0"><strong>Заканчивается:</strong> {{ \Carbon\Carbon::parse($subscription->expired_at)->format('d.m.Y') }}</p>
                            @if($daysLeft > 0)
                                <p class="text-muted mb-0">(осталось {{ $daysLeft }} {{ Lang::choice('день|дня|дней', $daysLeft) }})</p>
                            @else
                                <p class="text-danger mb-0">(подписка истекла)</p>
                            @endif
                        </div>
                    @endif
                </div>
                @if ($subscription->auto && $subscription->level > 0 && $subscriptionStatus->isActive)
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            <span class="text-muted">Автопродление включено</span>
                        </div>
                        <button class="btn btn-v2-danger btn-sm" data-bs-toggle="modal" data-bs-target="#cancelSubscriptionModal">
                            <i class="bi bi-x-circle me-1"></i>Отменить
                        </button>
                    </div>
                @endif
            </div>

            <!-- Available Plans -->
            <div>
                <h5 class="mb-3">Доступные тарифы:</h5>
                <div class="row g-3">
                    @foreach($products as $product)
                        @if($product->visible && $product->level > 0)
                            <div class="col-md-6">
                                <div class="subscription-card h-100 {{ $product->current_subscription ? 'subscription-card-active' : '' }}">
                                    <div class="subscription-card-body d-flex flex-column">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="subscription-icon me-3">
                                                 <i class="bi bi-{{ $product->icon ?? 'star' }}"></i>
                                            </div>
                                            <div>
                                                <h5 class="subscription-title mb-0">{{ $product->name }}</h5>
                                                <small class="text-muted">{{ trim(str_replace('НЕ ПОДКЛЮЧАТЬ', '', $product->description)) }}</small>
                                            </div>
                                        </div>

                                        <div class="text-center my-3">
                                            <span class="subscription-price">{{ $product->price }}</span>
                                            <span class="text-muted">₽/мес.</span>
                                        </div>

                                        @if($product->current_subscription)
                                            <button class="btn btn-v2-secondary w-100 mt-auto" disabled>
                                                <i class="bi bi-check-circle me-2"></i>Ваш текущий тариф
                                            </button>
                                        @else
                                            @php
                                                $isTransitionPlan = $product->name === 'Переходная';
                                            @endphp
                                            <button 
                                                class="btn {{ $isTransitionPlan ? 'btn-v2-secondary' : 'btn-v2-primary' }} w-100 mt-auto auth-pay-button" 
                                                type="button"
                                                data-user-id="{{auth()->user()->id}}"
                                                data-amount="{{$product->price}}"
                                                data-level="{{$product->level}}"
                                                data-first-week-amount="{{$product->first_week_price}}"
                                                data-id="{{$product->id}}"
                                                {{ $isTransitionPlan ? 'disabled' : '' }}>
                                                @if($product->current_subscription && !$subscriptionStatus->isActive)
                                                    <i class="bi bi-arrow-clockwise me-2"></i>Продлить подписку
                                                @else
                                                    <i class="bi bi-arrow-right-circle me-2"></i>Перейти на этот тариф
                                                @endif
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.subscription-card {
    background: #fff;
    border-radius: 0.75rem;
    border: 1px solid #e0e0e0;
    transition: all 0.3s ease;
    overflow: hidden;
}

.subscription-card-active {
    border-color: #613482;
    box-shadow: 0 0 0 1px #613482;
}

.subscription-card-body {
    padding: 1.25rem;
}

.subscription-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: #f0f2f5;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #613482;
    font-size: 1.25rem;
}

.subscription-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #333;
}

.subscription-price {
    font-size: 1.75rem;
    font-weight: 700;
    color: #613482;
}
</style>

<!-- Модальные окна -->
@include('v2.user.partials.modals._subscription_modals') 