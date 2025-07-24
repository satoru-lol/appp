<div class="bg-white rounded shadow-sm p-4 mb-3">
    <h4>Информация о текущей подписке</h4>
    <div class="pt-2">
        <div class="row mb-2">
            <div class="col-md-4"><strong>Текущая подписка:</strong></div>
            <div class="col-md-8">{{ $info['subscriptionName'] }}</div>
        </div>
        <div class="row mb-2">
            <div class="col-md-4"><strong>Статус:</strong></div>
            <div class="col-md-8">{{ $info['status'] }}</div>
        </div>
        <div class="row mb-2">
            <div class="col-md-4"><strong>Срок действия:</strong></div>
            <div class="col-md-8">{{ $info['expiredAt'] }}</div>
        </div>
        <div class="row mb-2">
            <div class="col-md-4"><strong>Тестовый период:</strong></div>
            <div class="col-md-8">{{ $info['testPeriod'] }}</div>
        </div>
        @if(!empty($info['paymentInfo']))
        <div class="row mb-2">
            <div class="col-md-4"><strong>Платеж:</strong></div>
            <div class="col-md-8">{{ $info['paymentInfo'] }}</div>
        </div>
        @endif
    </div>
</div> 