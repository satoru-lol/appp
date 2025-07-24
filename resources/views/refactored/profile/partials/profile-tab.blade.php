<div class="profile-card-v2">
    <div class="profile-card-header">
        <h5><i class="bi bi-person me-2"></i>Личная информация</h5>
    </div>
    <div class="profile-card-body">
        <!-- Аватар -->
        <div class="avatar-section">
            <div class="avatar-wrapper">
                                            <img src="{{ $user->avatar_url }}" 
                                 alt="Аватар" class="avatar-img" id="avatarImg">
                <button type="button" class="avatar-upload-btn" data-bs-toggle="modal" data-bs-target="#avatarModal">
                    <i class="bi bi-camera"></i>
                </button>
            </div>
            <h4>{{ $user->firstname }} {{ $user->lastname }}</h4>
            <p class="text-muted">{{ $user->email }}</p>
        </div>

        <!-- Статистика -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number">{{ $user->courses_count ?? 0 }}</div>
                <div class="stat-label">Курсов пройдено</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $user->meetings_count ?? 0 }}</div>
                <div class="stat-label">Встреч посещено</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $user->clubs_count ?? 0 }}</div>
                <div class="stat-label">Клубов участник</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $user->videos_watched ?? 0 }}</div>
                <div class="stat-label">Видео просмотрено</div>
            </div>
        </div>

        <!-- Форма редактирования профиля -->
        <form action="{{ route('v2.refactored.profile.update') }}" method="POST" id="profileForm">
            @csrf
            @method('PUT')
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="firstname" class="form-label">Имя *</label>
                    <input type="text" name="firstname" id="firstname" class="form-control form-control-v2" 
                           value="{{ old('firstname', $user->firstname) }}" required>
                    @error('firstname')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="lastname" class="form-label">Фамилия *</label>
                    <input type="text" name="lastname" id="lastname" class="form-control form-control-v2" 
                           value="{{ old('lastname', $user->lastname) }}" required>
                    @error('lastname')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="email" class="form-label">Email *</label>
                    <input type="email" name="email" id="email" class="form-control form-control-v2" 
                           value="{{ old('email', $user->email) }}" required>
                    @error('email')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="phone" class="form-label">Телефон</label>
                    <input type="tel" name="phone" id="phone" class="form-control form-control-v2" 
                           value="{{ old('phone', $user->phone) }}">
                    @error('phone')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="city" class="form-label">Город</label>
                <input type="text" name="city" id="city" class="form-control form-control-v2" 
                       value="{{ old('city', $user->city) }}">
                @error('city')
                    <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="about" class="form-label">О себе</label>
                <textarea name="about" id="about" class="form-control form-control-v2" rows="4" 
                          placeholder="Расскажите о себе...">{{ old('about', $user->about) }}</textarea>
                @error('about')
                    <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-v2-primary">
                    <i class="bi bi-check-lg me-2"></i>Сохранить изменения
                </button>
                <button type="button" class="btn btn-v2-outline" data-bs-toggle="modal" data-bs-target="#passwordModal">
                    <i class="bi bi-key me-2"></i>Изменить пароль
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Модальное окно для загрузки аватара -->
<div class="modal fade" id="avatarModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Изменить аватар</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('v2.refactored.profile.avatar') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="avatar" class="form-label">Выберите изображение</label>
                        <input type="file" name="avatar" id="avatar" class="form-control" 
                               accept="image/*" required>
                        <div class="form-text">
                            Максимальный размер: 2MB. Поддерживаемые форматы: JPG, PNG, GIF
                        </div>
                    </div>
                    <div id="avatarPreview" class="text-center" style="display: none;">
                        <img id="previewImg" src="" alt="Предпросмотр" 
                             style="max-width: 200px; max-height: 200px; border-radius: 8px;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-v2-primary">Загрузить</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Модальное окно для смены пароля -->
<div class="modal fade" id="passwordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Изменить пароль</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('v2.refactored.auth.password.change') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Текущий пароль</label>
                        <input type="password" name="current_password" id="current_password" 
                               class="form-control form-control-v2" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">Новый пароль</label>
                        <input type="password" name="new_password" id="new_password" 
                               class="form-control form-control-v2" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_password_confirmation" class="form-label">Подтвердите новый пароль</label>
                        <input type="password" name="new_password_confirmation" id="new_password_confirmation" 
                               class="form-control form-control-v2" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-v2-primary">Изменить пароль</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Предпросмотр аватара
    const avatarInput = document.getElementById('avatar');
    const previewDiv = document.getElementById('avatarPreview');
    const previewImg = document.getElementById('previewImg');
    
    if (avatarInput) {
        avatarInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    previewDiv.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                previewDiv.style.display = 'none';
            }
        });
    }
    
    // Валидация формы профиля
    const profileForm = document.getElementById('profileForm');
    if (profileForm) {
        profileForm.addEventListener('submit', function(e) {
            const firstname = document.getElementById('firstname').value.trim();
            const lastname = document.getElementById('lastname').value.trim();
            const email = document.getElementById('email').value.trim();
            
            if (!firstname || !lastname || !email) {
                e.preventDefault();
                alert('Пожалуйста, заполните все обязательные поля');
                return false;
            }
            
            if (!email.includes('@')) {
                e.preventDefault();
                alert('Пожалуйста, введите корректный email адрес');
                return false;
            }
        });
    }
});
</script>