<div class="card mb-4 shadow border-0">
    <div class="card-header bg-white">Оплата</div>
    <div class="card-body">
        <p>Для продления или смены подписки, пожалуйста, воспользуйтесь формой ниже.</p>
        
        <x-robokassa-payment 
            :invoice-id="(int)$invID" 
            :amount="100" {{-- Сумма по умолчанию, можно изменить --}}
            description="Оплата подписки" 
        />

        @session('pay_error')
        <div class="alert alert-danger alert-dismissible custom-alert mt-3" role="alert">
            <div>{{ $value }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"
                    aria-label="Close"></button>
        </div>
        @endsession

        @session('pay_success')
        <div class="alert alert-success alert-dismissible custom-alert mt-3" role="alert">
            <div>{{ $value }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"
                    aria-label="Close"></button>
        </div>
        @endsession
    </div>
</div> 