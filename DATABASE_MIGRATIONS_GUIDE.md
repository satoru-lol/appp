# 📊 Руководство по миграциям базы данных

## 🎯 Обзор

Созданы миграции для всех основных таблиц проекта на основе дампа базы данных, с добавлением полей для рефакторенной архитектуры V2.

## 📁 Структура миграций

### **Основные таблицы (созданы с нуля):**

#### 1. **2024_01_01_000001_create_users_table.php**
```php
- id, firstname, lastname, group, phone, email, password
- verification_code, phone_verified_at, email_verified_at
- balance, auto, used_sub, permissions
- avatar, bio, quick_access_token (новые поля)
- last_login_at, last_login_ip, preferences (новые поля)
```

#### 2. **2024_01_01_000002_create_subscriptions_table.php**
```php
- id, user_id, level, expired_at, is_active
- test, test_period, auto
- cancelled_at, cancel_reason, metadata (новые поля)
```

#### 3. **2024_01_01_000003_create_products_table.php**
```php
- id, name, price, description, level
- custom_price, first_week_price, visible
- slug, image, features, sort_order, is_featured (новые поля)
```

#### 4. **2024_01_01_000004_create_subscription_pays_table.php**
```php
- id, user_id, subscription_id, invoice_id, active
- product_id, action, price, auto
- payment_method, transaction_id, status, payment_data, paid_at (новые поля)
```

#### 5. **2024_01_01_000005_create_blogs_table.php** (встречи)
```php
- id, user_id, blog_category_id, blog_content_id, name
- image, views, status, date, is_meeting, format_id
- slug, short_description, tags (новые поля)
- likes_count, dislikes_count, comments_count, participants_count (новые поля)
- is_featured, seo_data (новые поля)
```

#### 6. **2024_01_01_000006_create_club_table.php**
```php
- id, image, title, date, speakers, theory, practice
- text, pay_method, video, product_level, is_hidden
- slug, short_description, max_participants, current_participants (новые поля)
- price, tags, is_featured, seo_data, views (новые поля)
```

#### 7. **2024_01_01_000007_create_courses_table.php**
```php
- id, course_category_id, title, image, times, speakers
- theory, practice, status, views, feedback, product_level
- slug, description, short_description, price, duration_hours (новые поля)
- max_participants, current_participants, tags, is_featured (новые поля)
- seo_data, start_date, end_date, certificate_template (новые поля)
```

#### 8. **2024_01_01_000008_create_categories_table.php** (видеотека)
```php
- id, name, parent_id
- slug, description, image, sort_order (новые поля)
- is_active, videos_count, seo_data (новые поля)
```

#### 9. **2024_01_01_000009_create_content_video_table.php**
```php
- id, title, url, category_id
- slug, description, thumbnail, duration, views (новые поля)
- access_level, is_featured, is_active, tags, seo_data (новые поля)
```

#### 10. **2024_01_01_000010_create_likes_dislikes_tables.php**
```php
Likes: id, user_id, object_name, object_id (полиморфные связи)
Dislikes: id, user_id, object_name, object_id (полиморфные связи)
```

#### 11. **2024_01_01_000011_create_additional_tables.php**
```php
- meeting_format: форматы встреч
- participant_actions: участие в объектах
- object_view_control: контроль просмотров
- transactions: транзакции с дополнительными полями
- introductions: введения пользователей
- club_dates: даты клубов
- product_permissions: разрешения продуктов
- blog_comments: комментарии
- blog_contents: содержимое блогов
- course_categories: категории курсов
```

#### 12. **2024_01_01_000012_add_refactored_fields_to_existing_tables.php**
```php
Добавляет новые поля к существующим таблицам для рефакторенной архитектуры
```

#### 13. **2024_01_01_000013_create_specialists_table.php**
```php
Создает таблицу специалистов с полной информацией и связями
```
- **Основные поля**: user_id, specialist_category_id, birthday, degree, experience
- **Дополнительные поля**: location, about, prices (JSON), time (JSON), gender, free_time
- **Метрики**: rating, status, views
- **Связи**: с пользователями и категориями
- **Индексы**: для оптимизации поиска по статусу, рейтингу и просмотрам

---

## 🚀 **Новые поля для рефакторенной архитектуры:**

### **SEO и оптимизация:**
- `slug` - SEO-дружественные URL
- `seo_data` - JSON с мета-данными (title, description, keywords, og_image)
- `tags` - JSON массив тегов для фильтрации
- `short_description` - краткое описание

### **Статистика и аналитика:**
- `views` - количество просмотров
- `likes_count`, `dislikes_count` - счетчики лайков/дизлайков
- `comments_count` - количество комментариев
- `participants_count` - количество участников

### **Функциональность:**
- `is_featured` - рекомендуемый контент
- `sort_order` - порядок сортировки
- `max_participants`, `current_participants` - управление участниками
- `access_level` - уровень доступа для контента

### **Пользователи:**
- `avatar` - путь к аватару
- `bio` - биография пользователя
- `quick_access_token` - токен быстрого доступа
- `last_login_at`, `last_login_ip` - данные последнего входа
- `preferences` - JSON настройки пользователя

### **Оплата:**
- `payment_method` - способ оплаты
- `transaction_id` - ID транзакции
- `status` - статус платежа
- `payment_data` - JSON данные платежа
- `paid_at` - время оплаты

---

## 🔧 **Индексы для оптимизации:**

### **Составные индексы:**
```sql
- ['user_id', 'is_active'] - для подписок
- ['object_name', 'object_id'] - для полиморфных связей
- ['is_meeting', 'status', 'date'] - для встреч
- ['product_level', 'is_hidden'] - для контента по уровням
- ['is_featured', 'views'] - для популярного контента
```

### **Уникальные индексы:**
```sql
- slug - для SEO URL
- ['user_id', 'object_name', 'object_id'] - для участия/лайков
```

---

## 📋 **Инструкции по применению:**

### **1. Для нового проекта:**
```bash
# Запустить все миграции
php artisan migrate

# Заполнить базовые данные
php artisan db:seed
```

### **2. Для существующего проекта:**
```bash
# Создать резервную копию БД
mysqldump -u user -p database_name > backup.sql

# Применить только новые поля к существующим таблицам
php artisan migrate --path=database/migrations/2024_01_01_000012_add_refactored_fields_to_existing_tables.php

# Или применить все миграции (осторожно!)
php artisan migrate
```

### **3. Откат миграций:**
```bash
# Откатить последнюю миграцию
php artisan migrate:rollback

# Откатить определенное количество миграций
php artisan migrate:rollback --step=5

# Полный откат (ОСТОРОЖНО!)
php artisan migrate:reset
```

---

## ⚠️ **Важные замечания:**

### **Совместимость:**
- ✅ Все поля из оригинального дампа сохранены
- ✅ Добавлены только новые поля для рефакторенной архитектуры
- ✅ Обратная совместимость со старым кодом

### **Безопасность:**
- 🔒 Внешние ключи настроены с CASCADE DELETE
- 🔒 Уникальные ограничения для предотвращения дублей
- 🔒 Индексы для быстрого поиска

### **Производительность:**
- ⚡ Составные индексы для частых запросов
- ⚡ JSON поля для гибкого хранения метаданных
- ⚡ Денормализация счетчиков для быстрого доступа

---

## 🎯 **Результат:**

✅ **Полная совместимость** с существующим дампом БД  
✅ **Расширенная функциональность** для рефакторенной архитектуры  
✅ **Оптимизированные индексы** для высокой производительности  
✅ **SEO-готовность** с slug и мета-данными  
✅ **Аналитика** с счетчиками и статистикой  

**Миграции готовы к применению!** 🚀