<div class="profile-card-v2">
    <div class="profile-card-header">
        <h5 class="mb-0">Личные данные</h5>
    </div>
    <div class="profile-card-body">
        <form method="POST" action="{{ route('v2.profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="profile-details-grid">
                @php
                    $avatarPath = public_path('img/avatars/') . md5($user->id . $user->phone);
                    $avatarExists = false;
                    foreach(['jpg','jpeg','png','gif','webp'] as $ext) {
                        if (file_exists($avatarPath . '.' . $ext)) {
                            $avatarExists = true;
                            break;
                        }
                    }
                @endphp
                <div class="profile-avatar-container text-center">
                    <label for="avatarInput" class="avatar-wrapper mx-auto mb-3" id="avatarWrapper">
                        <img id="avatarPreview" src="/avatar/{{ $user->id }}?t={{ time() }}" class="avatar-image rounded-circle" alt="Avatar">
                        <div class="avatar-overlay rounded-circle">
                            <span class="d-block w-100 text-center avatar-upload-label" id="avatarUploadText">
                                <i class="bi bi-camera"></i><br>{{ $avatarExists ? 'Сменить' : 'Загрузить' }}
                            </span>
                        </div>
                    </label>
                    <input type="file" name="avatar" id="avatarInput" accept=".jpg, .jpeg, .png" class="d-none">
                    @if($avatarExists)
                    <button type="button" id="removeAvatarBtn" class="btn btn-v2-danger btn-sm mb-2">
                        <i class="fas fa-trash-alt me-1"></i>
                        Удалить фото
                    </button>
                    @endif
                    <div class="form-text">
                        JPG, PNG. Макс. 2MB.
                    </div>
                    <div id="avatar-error" class="alert-v2 alert-v2-danger d-none mt-2 p-2 small"></div>
                </div>

                <div class="profile-details-form">
                    <div class="row gx-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label-v2" for="phone">Номер телефона</label>
                            <input class="form-control-v2" id="phone" name="phone" type="tel" value="{{ $user->phone }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-v2" for="email">Почта</label>
                            <input class="form-control-v2" id="email" type="email" value="{{ $user->email }}" disabled>
                        </div>
                    </div>
                    <div class="row gx-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label-v2" for="inputFirstName">Имя</label>
                            <input class="form-control-v2 @error('firstname') is-invalid @enderror" name="firstname" id="inputFirstName" type="text" placeholder="Введите ваше имя" value="{{ $user->firstname ?? '' }}">
                            @error('firstname')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-v2" for="inputLastName">Фамилия</label>
                            <input class="form-control-v2 @error('lastname') is-invalid @enderror" name="lastname" id="inputLastName" type="text" placeholder="Введите вашу фамилию" value="{{ $user->lastname ?? '' }}">
                            @error('lastname')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                         <a href="/password/reset/{{ \Illuminate\Support\Facades\Password::getRepository()->create(auth()->user()) }}/{{ auth()->user()->email }}" class="btn btn-v2-secondary">Сменить пароль</a>
                        <button class="btn btn-v2-primary" type="submit">Сохранить</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
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

.avatar-wrapper {
    position: relative;
    width: 150px;
    height: 150px;
    margin: 0 auto;
    transition: transform 0.3s ease;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}
.avatar-image {
    width: 150px;
    height: 150px;
    object-fit: cover;
    border-radius: 50%;
    display: block;
}
.avatar-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 150px;
    height: 150px;
    background-color: rgba(0,0,0,0.5);
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    opacity: 0;
    transition: background-color 0.3s, opacity 0.3s;
    border-radius: 50%;
    pointer-events: none;
}
.avatar-wrapper:hover .avatar-overlay {
    opacity: 1 !important;
    pointer-events: auto;
}
.avatar-upload-label {
    color: white;
    text-align: center;
    font-size: 1.2rem;
    font-weight: 500;
    cursor: pointer;
    user-select: none;
    letter-spacing: 0.5px;
}
.avatar-upload-label .bi-camera {
    font-size: 2rem;
    margin-bottom: 4px;
}

.alert-v2-danger {
    background-color: #f8d7da;
    border-color: #f5c6cb;
    color: #721c24;
    padding: 0.75rem 1.25rem;
    border-radius: 0.5rem;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const avatarInput = document.getElementById('avatarInput');
    const removeAvatarBtn = document.getElementById('removeAvatarBtn');
    const avatarPreview = document.getElementById('avatarPreview');
    const avatarError = document.getElementById('avatar-error');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const uploadUrl = "{{ route('v2.profile.avatar.update') }}";
    const removeUrl = "{{ route('v2.profile.avatar.remove') }}";

    if (avatarInput) {
        avatarInput.addEventListener('change', function () {
            const file = this.files[0];
            if (!file) return;

            if (avatarError) avatarError.classList.add('d-none');

            const formData = new FormData();
            formData.append('avatar', file);

            fetch(uploadUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.avatar_url) {
                    avatarPreview.src = data.avatar_url + '?t=' + new Date().getTime();
                    if (removeAvatarBtn) {
                        removeAvatarBtn.classList.remove('d-none');
                    }
                } else {
                    if (avatarError) {
                        avatarError.textContent = data.message || 'Ошибка загрузки.';
                        avatarError.classList.remove('d-none');
                    }
                }
            })
            .catch(err => {
                if (avatarError) {
                    avatarError.textContent = 'Произошла ошибка. Пожалуйста, попробуйте снова.';
                    avatarError.classList.remove('d-none');
                }
            });
        });
    }

    if (removeAvatarBtn) {
        removeAvatarBtn.addEventListener('click', function () {
            if (avatarError) avatarError.classList.add('d-none');

            fetch(removeUrl, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    avatarPreview.src = "{{ asset('img/no-avatar.svg') }}";
                    removeAvatarBtn.classList.add('d-none');
                } else {
                     if (avatarError) {
                        avatarError.textContent = data.message || 'Ошибка удаления.';
                        avatarError.classList.remove('d-none');
                    }
                }
            })
            .catch(err => {
                if (avatarError) {
                    avatarError.textContent = 'Произошла ошибка. Пожалуйста, попробуйте снова.';
                    avatarError.classList.remove('d-none');
                }
            });
        });
    }
});
</script> 