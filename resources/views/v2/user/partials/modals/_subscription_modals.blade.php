<!-- Модальное окно для подтверждения отмены подписки -->
<div class="modal fade cancellationModal" id="cancellationModal" tabindex="-1"
     aria-labelledby="cancellationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cancellationModalLabel">Подтверждение отмены
                    подписки</h5>
            </div>
            <div class="modal-body">
                <div>    Вы уверены, что хотите отменить подписку? Вы потеряете доступ ко всем
                    специальным курсам, мероприятиям, клубам и другому контенту. <br/>
                    <span class="text-danger">При отмене деньги за подписку не возвращаются</span>
                </div>
                @if(isset($subscription) && $subscription->level == 6 && auth()->user()->balance >= ($final_pr ?? 0))
                    <form method="POST" action="/changeToHigher" id="upgradeForm">
                        @csrf
                        <div class="mt-4 d-flex p-2" style="background: rgb(184 218 202)">
                            <div>  Вы можете перейти на подписку за {{$prem_pr}} руб, заплатив {{ $final_pr}} руб</div>
                            <button type="submit" class="btn btn-success upgrade-btn" >
                                Перейти
                            </button>
                        </div>
                    </form>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary cancel-subs">Назад</button>
                <button type="button" class="btn btn-primary" id="confirmCancellationBtn">
                    Подтвердить
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно для подтверждения покупки -->
<div class="modal fade" id="confirmationModal" tabindex="-1"
     aria-labelledby="confirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmationModalLabel">Подтверждение
                    покупки</h5>
            </div>
            <div class="modal-body">
                Вы уверены, что хотите купить подписку <span id="productName"></span>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary cancel-subs"
                        data-bs-dismiss="modal">Отмена
                </button>
                <form method="post" action="{{ route('v2.profile.subscription.handle') }}" id="payForm">
                    @csrf
                    <input type="hidden" name="product_id" id="productId" value="">
                    <button type="submit" class="btn btn-primary" id="">Купить</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно для понижения уровня подписки -->
<div class="modal fade" id="downgradeModal" tabindex="-1"
     aria-labelledby="downgradeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="downgradeModalLabel">Подтверждение понижения
                    уровня</h5>
            </div>
            <div class="modal-body">
                Вы уверены, что хотите понизить уровень подписки? Вы потеряете доступ к
                части специальных курсов, мероприятий, клубов и другого контента.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary cancel-subs"
                        data-bs-dismiss="modal">Отмена
                </button>
                <button type="button" class="btn btn-primary" id="confirmDowngradeBtn">
                    Подтвердить
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно для ввода кода подтверждения -->
<div class="modal fade" id="verificationModal" tabindex="-1"
     aria-labelledby="verificationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verificationModalLabel">Введите код
                    подтверждения</h5>
            </div>
            <div class="modal-body">
                Пожалуйста, введите код подтверждения, отправленный на ваш телефон.
                <input type="text" class="form-control mt-2" id="verificationCode"
                       placeholder="Код подтверждения">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary cancel-subs"
                        data-bs-dismiss="modal">Отмена
                </button>
                <button type="button" class="btn btn-primary" id="confirmVerificationBtn">
                    Подтвердить
                </button>
            </div>
        </div>
    </div>
</div> 