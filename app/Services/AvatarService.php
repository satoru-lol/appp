<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class AvatarService
{
    /**
     * Загрузить и сохранить аватар пользователя
     */
    public function uploadAvatar(User $user, UploadedFile $file): array
    {
        try {
            // Удаляем старый аватар, если есть
            $this->deleteAvatar($user);

            // Проверяем тип файла
            if (!in_array($file->getClientMimeType(), ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'])) {
                return [
                    'success' => false,
                    'message' => 'Неподдерживаемый тип файла. Разрешены: JPEG, PNG, JPG, WEBP'
                ];
            }

            // Проверяем размер файла (максимум 5MB)
            if ($file->getSize() > 5 * 1024 * 1024) {
                return [
                    'success' => false,
                    'message' => 'Размер файла не должен превышать 5MB'
                ];
            }

            // Генерируем уникальное имя файла
            $filename = 'avatars/' . $user->id . '_' . Str::random(10) . '_' . time() . '.jpg';

            // Обрабатываем изображение
            $image = Image::make($file);
            
            // Изменяем размер и обрезаем до квадрата
            $image->fit(300, 300, function ($constraint) {
                $constraint->upsize();
            });

            // Конвертируем в JPEG для экономии места
            $image->encode('jpg', 85);

            // Сохраняем в storage/app/public
            Storage::put('public/' . $filename, (string) $image);

            // Обновляем пользователя
            $user->update([
                'avatar' => $filename,
                'avatar_original_name' => $file->getClientOriginalName()
            ]);

            return [
                'success' => true,
                'message' => 'Аватар успешно загружен',
                'avatar_url' => $user->avatar_url
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Ошибка при загрузке аватара: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Удалить аватар пользователя
     */
    public function deleteAvatar(User $user): array
    {
        try {
            if ($user->avatar) {
                // Удаляем файл из storage
                Storage::delete('public/' . $user->avatar);
                
                // Очищаем поля в БД
                $user->update([
                    'avatar' => null,
                    'avatar_original_name' => null
                ]);
            }

            return [
                'success' => true,
                'message' => 'Аватар успешно удален'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Ошибка при удалении аватара: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Получить URL аватара пользователя
     */
    public function getAvatarUrl(User $user): string
    {
        // Если есть загруженный аватар
        if ($user->avatar && Storage::exists('public/' . $user->avatar)) {
            return asset('storage/' . $user->avatar);
        }

        // Если есть дефолтный аватар
        if (file_exists(public_path('img/default-avatar.png'))) {
            return asset('img/default-avatar.png');
        }

        // Генерируем аватар с инициалами
        return $this->generateAvatarUrl($user);
    }

    /**
     * Сгенерировать URL для аватара с инициалами
     */
    public function generateAvatarUrl(User $user): string
    {
        $initials = $user->initials;
        $color = str_replace('#', '', $user->avatar_color);
        
        return "https://ui-avatars.com/api/?name={$initials}&background={$color}&color=fff&size=300&font-size=0.6&rounded=true";
    }

    /**
     * Мигрировать старые аватары в новую систему
     */
    public function migrateOldAvatars(): array
    {
        $migrated = 0;
        $errors = [];

        $users = User::whereNull('avatar')->get();

        foreach ($users as $user) {
            try {
                // Ищем старые файлы аватаров
                $pattern = public_path('img/avatars/') . md5($user->id . $user->phone) . '.*';
                $files = glob($pattern);

                if (!empty($files) && file_exists($files[0])) {
                    $oldFile = $files[0];
                    $extension = pathinfo($oldFile, PATHINFO_EXTENSION);
                    
                    // Новое имя файла
                    $newFilename = 'avatars/' . $user->id . '_migrated_' . time() . '.' . $extension;
                    
                    // Копируем файл в новое место
                    $image = Image::make($oldFile);
                    $image->fit(300, 300);
                    $image->encode('jpg', 85);
                    
                    Storage::put('public/' . $newFilename, (string) $image);
                    
                    // Обновляем пользователя
                    $user->update([
                        'avatar' => $newFilename,
                        'avatar_original_name' => 'migrated_' . basename($oldFile)
                    ]);
                    
                    $migrated++;
                }
            } catch (\Exception $e) {
                $errors[] = "User {$user->id}: " . $e->getMessage();
            }
        }

        return [
            'migrated' => $migrated,
            'errors' => $errors
        ];
    }
}