<style>
    .form-control-v2 {
        height: 45px;
        border-radius: 0.5rem;
        border: 1px solid #ced4da;
        padding: 0.5rem 1rem;
        transition: border-color 0.2s, box-shadow 0.2s;
        width: 100%;
        font-size: 0.95rem;
    }
    
    .form-control-v2:focus {
        border-color: #613482;
        box-shadow: 0 0 0 0.25rem rgba(97, 52, 130, 0.25);
        outline: none;
    }
    
    .form-label-v2 {
        font-size: 0.95rem;
        font-weight: 500;
        color: #495057;
        margin-bottom: 0.5rem;
    }
</style>

<form action="{{ route('balance.add') }}" method="POST" id="balance-form">
    @csrf
    <div class="mb-3">
        <label for="amount" class="form-label-v2">Сумма пополнения (RUB)</label>
        <input type="number" class="form-control-v2" id="amount" name="amount" placeholder="Введите сумму" required min="1">
    </div>
    <button type="submit" class="btn btn-v2-primary">
        <i class="bi bi-credit-card me-2"></i>Перейти к оплате
    </button>
</form> 