/*
// JS for v2 profile page
document.addEventListener('DOMContentLoaded', function () {
    // CSRF Token for AJAX
    const csrfToken = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '';

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': csrfToken
        }
    });

    // Avatar upload
    const uploadAvatarButton = document.getElementById('upload-avatar-button');
    const imageInput = document.getElementById('image');
    if (uploadAvatarButton && imageInput) {
        uploadAvatarButton.addEventListener('click', () => {
            imageInput.click();
        });

        imageInput.addEventListener('change', function () {
            const file = this.files[0];
            const maxSize = 2 * 1024 * 1024; // 2MB
            const errorDiv = document.getElementById('file-error');
            const profileUploadUrl = imageInput.dataset.uploadUrl;

            if (file && file.size > maxSize) {
                errorDiv.classList.remove('d-none');
                return;
            }

            let formData = new FormData();
            formData.append('_token', csrfToken);
            formData.append('_method', 'PUT');
            formData.append('image', file);

            $.ajax({
                url: profileUploadUrl,
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    window.location.reload();
                },
                error: function (xhr) {
                    errorDiv.classList.remove('d-none');
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorDiv.innerHTML = "❌ Ошибка загрузки: " + xhr.responseJSON.message;
                    } else {
                        errorDiv.innerHTML = "❌ Ошибка загрузки. Попробуйте снова.";
                    }
                }
            });
        });
    }

    const removeAvatarButton = document.getElementById('remove-avatar-button');
    if(removeAvatarButton) {
        removeAvatarButton.addEventListener('click', function(event) {
            const removeAvatarUrl = this.dataset.removeUrl;
            $.ajax({
                url: removeAvatarUrl,
                method: 'POST',
                success: function () {
                    window.location.reload();
                },
                error: function () {
                    window.location.reload();
                }
            });
        });
    }

    // Tabs - REMOVED, now handled by Bootstrap 5 `data-bs-toggle="tab"`
    // document.querySelectorAll('.tab').forEach(function (tab) {
    //     tab.addEventListener('click', function () {
    //         let activeTab = document.querySelector('.tab.active');
    //         let activeContent = document.querySelector('.tab-content.active');
    //
    //         if(activeTab) activeTab.classList.remove('active');
    //         if(activeContent) activeContent.classList.remove('active');
    //
    //         this.classList.add('active');
    //         let tabId = this.getAttribute('data-tab');
    //         let newActiveContent = document.getElementById(tabId);
    //         if(newActiveContent) newActiveContent.classList.add('active');
    //     });
    // });

    // Subscription management
    const subscriptionContainer = document.getElementById('subscription-container');
    if (subscriptionContainer) {
        const subscriptionInput = document.getElementById('subscription');
        const subscriptionSelectContainer = document.getElementById('subscriptionSelectContainer');
        const confirmationModalElem = document.getElementById('confirmationModal');
        const downgradeModalElem = document.getElementById('downgradeModal');
        const cancellationModalElem = document.getElementById('cancellationModal');
        
        const confirmationModal = confirmationModalElem ? new bootstrap.Modal(confirmationModalElem) : null;
        const downgradeModal = downgradeModalElem ? new bootstrap.Modal(downgradeModalElem) : null;
        const cancellationModal = cancellationModalElem ? new bootstrap.Modal(cancellationModalElem) : null;
        
        const productNameSpan = document.getElementById('productName');
        const confirmCancellationBtn = document.getElementById('confirmCancellationBtn');

        const currentLevel = parseInt(subscriptionContainer.dataset.currentLevel || '0');
        let selectedProduct = '';
        let selectedProductLevel = 0;
        
        function showSelect() {
            if(subscriptionInput) subscriptionInput.style.display = 'none';
            if(subscriptionSelectContainer) {
                subscriptionSelectContainer.style.display = 'block';
                setTimeout(() => {
                    subscriptionSelectContainer.classList.add('show');
                }, 10);
            }
        }
        
        function hideSelect() {
            if(subscriptionSelectContainer) {
                subscriptionSelectContainer.classList.remove('show');
                subscriptionSelectContainer.classList.add('hide');
                setTimeout(() => {
                    subscriptionSelectContainer.style.display = 'none';
                    subscriptionSelectContainer.classList.remove('hide');
                }, 300);
            }
            if(subscriptionInput) subscriptionInput.style.display = 'block';
        }

        if(subscriptionInput) subscriptionInput.addEventListener('click', showSelect);

        document.addEventListener('click', function (event) {
            if (subscriptionContainer && !subscriptionContainer.contains(event.target)) {
                hideSelect();
            }
        });

        if (subscriptionSelectContainer) {
            subscriptionSelectContainer.addEventListener('click', function (event) {
                const buyButton = event.target.closest('.buy-btn');
                if (buyButton) {
                    buyButton.classList.add('btn-clicked');
                    setTimeout(() => buyButton.classList.remove('btn-clicked'), 200);

                    selectedProduct = buyButton.dataset.product;
                    const productIdInput = document.getElementById('productId');
                    if(productIdInput) productIdInput.value = selectedProduct;

                    selectedProductLevel = parseInt(buyButton.closest('.subscription-option').dataset.level);
                    const productName = buyButton.closest('.subscription-option').querySelector('div > div').textContent.trim();
                    if(productNameSpan) productNameSpan.textContent = productName;

                    if (buyButton.classList.contains('btn-danger') && cancellationModal) {
                        cancellationModal.show();
                    } else if (selectedProductLevel > currentLevel && confirmationModal) {
                        confirmationModal.show();
                    } else if(downgradeModal) {
                        downgradeModal.show();
                    }
                }
            });
        }

        if (confirmCancellationBtn) {
            confirmCancellationBtn.addEventListener('click', function () {
                if(cancellationModal) cancellationModal.hide();
                $.ajax({
                    url: '/cancelSubscribe', // This should probably be a named route passed via data attribute
                    type: 'POST',
                    success: function () {
                        window.location.reload();
                    },
                    error: function (xhr) {
                        alert('Произошла ошибка: ' + (xhr.responseText || 'Попробуйте снова.'));
                    }
                });
            });
        }
    }

    // === [NEW] Subscription change handler for v2 profile ===
    document.querySelectorAll('.auth-pay-button').forEach(function(btn) {
        btn.addEventListener('click', function() {
            // Открыть модалку подтверждения покупки
            const confirmationModalElem = document.getElementById('confirmationModal');
            if (!confirmationModalElem) return;
            const confirmationModal = new bootstrap.Modal(confirmationModalElem);
            // Подставить название тарифа
            const productName = btn.closest('.card-body').querySelector('.card-title').textContent.trim();
            const productNameSpan = document.getElementById('productName');
            if (productNameSpan) productNameSpan.textContent = productName;
            // Подставить product_id в форму
            const productId = btn.dataset.id;
            const productIdInput = document.getElementById('productId');
            if (productIdInput) productIdInput.value = productId;
            // Открыть модалку
            confirmationModal.show();
        });
    });

    // Перехватить submit формы покупки подписки и отправить через AJAX
    const payForm = document.getElementById('payForm');
    if (payForm) {
        payForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(payForm);
            fetch('/pay', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    window.location.reload();
                } else {
                    alert(data.message || 'Ошибка оплаты.');
                }
            })
            .catch(() => {
                alert('Ошибка оплаты. Попробуйте позже.');
            });
        });
    }

    // Admin functionality
    const userSearchButton = document.getElementById('user-search-button');
    if(userSearchButton) {
        userSearchButton.addEventListener('click', function () {
            const overlay = document.getElementById('loadingOverlay');
            if(overlay) overlay.style.display = 'flex';
            
            const queryInput = document.getElementById('user-search-query');
            const query = queryInput ? queryInput.value : '';
            const userInfoDiv = document.getElementById('user-info');

            if (query.length === 0) {
                alert('Введите телефон или почту');
                if(overlay) overlay.style.display = 'none';
                return;
            }

            $.ajax({
                url: '/admin/search-user', // This should probably be a named route passed via data attribute
                type: 'GET',
                data: { query: query },
                success: function (response) {
                    if(overlay) overlay.style.display = 'none';
                    if(userInfoDiv) userInfoDiv.innerHTML = response;
                },
                error: function (xhr) {
                    if(overlay) overlay.style.display = 'none';
                    if(userInfoDiv) userInfoDiv.innerHTML = '<div class="alert alert-danger">Произошла ошибка при поиске.</div>';
                }
            });
        });
    }

    document.querySelectorAll('.editRoleBtn').forEach(btn => {
        btn.addEventListener('click', function (event) {
            const editRoleModal = document.getElementById('editRole');
            if(editRoleModal) {
                const modal = new bootstrap.Modal(editRoleModal);
                const recipient = this.dataset.userId;
                const hidEl = editRoleModal.querySelector("#hidEl");
                if(hidEl) hidEl.innerHTML = `<input type='hidden' name='user_id' value="${recipient}">`;
                modal.show();
            }
        });
    });

    document.querySelectorAll('.closeModal').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.modal').forEach(modal => {
                const bootstrapModal = bootstrap.Modal.getInstance(modal);
                if (bootstrapModal) {
                    bootstrapModal.hide();
                }
            });
        });
    });
});
*/ 