# 🔧 Исправление ошибки: Table 'specialists' doesn't exist

## ❌ **Проблема:**
```sql
SQLSTATE[42S02]: Base table or view not found: 1146 Table 'apspy_beta_db.specialists' doesn't exist
```

**Причина:** В базе данных отсутствовала таблица `specialists`, но контроллер `HomeController` пытался её использовать в строке 183:

```php
$specialists = Specialist::where('status', 1)->orderBy('views')->limit(10)->get();
```

## ✅ **Решение:**

### 1. **Создана миграция для таблицы specialists:**

**Файл:** `database/migrations/2024_01_01_000013_create_specialists_table.php`

```php
Schema::create('specialists', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->foreignId('specialist_category_id')->nullable()->constrained('categories')->onDelete('set null');
    $table->date('birthday')->nullable();
    $table->string('degree')->nullable();
    $table->integer('experience')->default(0)->comment('Опыт работы в годах');
    $table->string('location')->nullable();
    $table->text('about')->nullable();
    $table->json('prices')->nullable()->comment('Прайс-лист услуг');
    $table->json('time')->nullable()->comment('Расписание работы');
    $table->enum('gender', ['male', 'female', 'other'])->default('other');
    $table->text('free_time')->nullable()->comment('Свободное время');
    $table->decimal('rating', 3, 2)->default(0.00)->comment('Рейтинг от 0.00 до 5.00');
    $table->boolean('status')->default(false)->comment('Статус активности');
    $table->integer('views')->default(0)->comment('Количество просмотров');
    $table->timestamps();

    // Индексы для оптимизации
    $table->index(['status', 'views']);
    $table->index(['status', 'rating']);
    $table->index('user_id');
    $table->index('specialist_category_id');
});
```

### 2. **Улучшена модель Specialist:**

**Добавлены связи:**
```php
public function user()
{
    return $this->belongsTo(User::class);
}

public function category()
{
    return $this->belongsTo(Category::class, 'specialist_category_id');
}
```

**Добавлены скоупы:**
```php
public function scopeActive($query)
{
    return $query->where('status', true);
}

public function scopePopular($query)
{
    return $query->orderBy('views', 'desc');
}

public function scopeTopRated($query)
{
    return $query->orderBy('rating', 'desc');
}
```

### 3. **Оптимизирован запрос в HomeController:**

**Было:**
```php
$specialists = Specialist::where('status', 1)->orderBy('views')->limit(10)->get();
```

**Стало:**
```php
$specialists = Specialist::with(['user', 'category'])
    ->active()
    ->orderBy('views', 'asc')
    ->limit(10)
    ->get();
```

**Преимущества:**
- ✅ Использование eager loading для избежания N+1 проблем
- ✅ Использование скоупа `active()` для читаемости
- ✅ Загрузка связанных данных пользователя и категории

---

## 🗄️ **Структура таблицы specialists:**

| Поле | Тип | Описание |
|------|-----|----------|
| `id` | bigint | Первичный ключ |
| `user_id` | bigint | Связь с пользователем |
| `specialist_category_id` | bigint | Категория специалиста |
| `birthday` | date | Дата рождения |
| `degree` | varchar | Образование |
| `experience` | int | Опыт работы (годы) |
| `location` | varchar | Местоположение |
| `about` | text | О специалисте |
| `prices` | json | Прайс-лист услуг |
| `time` | json | Расписание работы |
| `gender` | enum | Пол (male/female/other) |
| `free_time` | text | Свободное время |
| `rating` | decimal(3,2) | Рейтинг (0.00-5.00) |
| `status` | boolean | Активность |
| `views` | int | Количество просмотров |

---

## 🚀 **Как применить исправления:**

### 1. **Запустить миграцию:**
```bash
php artisan migrate
```

### 2. **Проверить создание таблицы:**
```sql
DESCRIBE specialists;
```

### 3. **Добавить тестовые данные (опционально):**
```php
// В сидере или tinker
Specialist::create([
    'user_id' => 1,
    'specialist_category_id' => 1,
    'degree' => 'Психолог',
    'experience' => 5,
    'location' => 'Москва',
    'about' => 'Опытный психолог-консультант',
    'rating' => 4.8,
    'status' => true,
    'views' => 150
]);
```

---

## ✅ **Результат:**

После применения этих исправлений:

1. ✅ **Ошибка исчезнет** - таблица `specialists` будет создана
2. ✅ **Производительность улучшится** - благодаря индексам и eager loading
3. ✅ **Код станет более читаемым** - благодаря скоупам и связям
4. ✅ **Архитектура станет более правильной** - с правильными связями между моделями

**Теперь главная страница будет работать корректно!** 🎉