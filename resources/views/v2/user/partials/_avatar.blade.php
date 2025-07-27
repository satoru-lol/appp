<div class="profile-avatar-container text-center">
    <div class="avatar-wrapper mx-auto mb-3">
        <img id="avatarPreview" src="{{ route('v2.avatar', ['userId' => $user->id]) }}?t={{ time() }}"
             class="avatar-image rounded-circle" alt="Avatar">
        <div class="avatar-overlay rounded-circle">
            <label for="avatarInput" class="avatar-upload-label">
                <i class="fas fa-camera"></i>
                <span class="d-block mt-1" id="avatarUploadText">{{ $user->avatar ? 'Сменить' : 'Загрузить' }}</span>
            </label>
            <input type="file" name="avatar" id="avatarInput" accept=".jpg, .jpeg, .png" class="d-none">
        </div>
    </div>

    <button type="button" id="removeAvatarBtn" class="btn btn-sm btn-outline-danger mb-2 @if(!$user->avatar) d-none @endif">
        <i class="fas fa-trash-alt me-1"></i>
        Удалить фото
    </button>

    <div class="form-text">
        JPG, PNG. Макс. 2MB.
    </div>

    <div id="avatar-error" class="alert alert-danger d-none mt-2 p-2 small"></div>
</div> 