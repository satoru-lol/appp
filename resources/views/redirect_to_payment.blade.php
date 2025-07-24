<form id="paymentForm" action="{{ $paymentUrl }}" method="POST">
    <input type="hidden" name="MerchantLogin" value="{{ $merchantLogin }}">
    <input type="hidden" name="InvoiceID" value="{{ $invoiceID }}">
    <input type="hidden" name="Description" value="{{ $description }}">
    <input type="hidden" name="OutSum" value="{{ $price }}">
    <input type="hidden" name="SignatureValue" value="{{ $signatureValue }}">
    <input type="hidden" name="Receipt" value="{{ $receipt }}">
    <input type="hidden" name="Recurring" value="true">
</form>

<script type="text/javascript">
    document.getElementById('paymentForm').submit(); // Автоматическая отправка формы
</script>
