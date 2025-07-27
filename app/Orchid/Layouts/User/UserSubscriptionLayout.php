<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\User;

use App\Models\Product;
use App\Models\Subscription;
use App\Models\SubscriptionPays;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Label;
use Orchid\Screen\Layouts\Rows;
use Illuminate\Support\Carbon;

class UserSubscriptionLayout extends Rows
{
    /**
     * Get the fields elements to be displayed.
     *
     * @return Field[]
     */
    protected function fields(): array
    {
        // Получаем все продукты для выбора уровня подписки
        $products = Product::all();
        $subscriptionOptions = [
            '0' => 'Без подписки',
        ];
        
        foreach ($products as $product) {
            $subscriptionOptions[(string)$product->level] = $product->name . ' (Уровень ' . $product->level . ')';
        }
        
        // Получаем текущую подписку пользователя для отображения информации
        $subscription = $this->query->get('subscription');
        $currentSubscriptionName = 'Нет активной подписки';
        $paymentInfo = '';
        
        if ($subscription && $subscription->exists && $subscription->level > 0) {
            $product = Product::where('level', $subscription->level)->first();
            $currentSubscriptionName = $product ? $product->name : 'Неизвестная подписка (Уровень ' . $subscription->level . ')';
            
            // Получаем информацию о последнем платеже
            $lastPayment = SubscriptionPays::where('subscription_id', $subscription->id)
                ->orderBy('created_at', 'desc')
                ->first();
                
            if ($lastPayment) {
                $paymentInfo = sprintf(
                    'Последний платеж: %s (%.2f руб.) от %s',
                    $lastPayment->action ?? 'Платеж',
                    $lastPayment->price ?? 0,
                    $lastPayment->created_at ? Carbon::parse($lastPayment->created_at)->format('d.m.Y') : 'неизвестно'
                );
            }
        }

        return [
            Label::make('subscription_info')
                ->title('Текущая подписка')
                ->value($currentSubscriptionName),
                
            Label::make('subscription_status')
                ->title('Статус')
                ->value($subscription && $subscription->exists && $subscription->is_active ? 'Активна' : 'Неактивна'),
                
            Label::make('subscription_expiry')
                ->title('Срок действия')
                ->value($subscription && $subscription->exists && $subscription->expired_at ? 
                    Carbon::parse($subscription->expired_at)->format('d.m.Y') : 'Бессрочно'),
                
            Label::make('subscription_test')
                ->title('Тестовый период')
                ->value($subscription && $subscription->exists && $subscription->test_period ? 'Да' : 'Нет'),
                
            Label::make('payment_info')
                ->title('Информация о платеже')
                ->value($paymentInfo ?: 'Нет данных о платежах'),
            
            Group::make([
                Select::make('subscription.level')
                    ->title('Уровень подписки')
                    ->options($subscriptionOptions)
                    ->help('Выберите уровень подписки для пользователя'),

                CheckBox::make('subscription.is_active')
                    ->title('Активная подписка')
                    ->sendTrueOrFalse()
                    ->help('Если отключено, пользователь не сможет использовать возможности подписки'),
            ]),

            Group::make([
                DateTimer::make('subscription.expired_at')
                    ->title('Срок действия до')
                    ->allowInput()
                    ->format('Y-m-d')
                    ->help('Оставьте пустым для бессрочной подписки'),

                CheckBox::make('subscription.test_period')
                    ->title('Тестовый период')
                    ->sendTrueOrFalse()
                    ->help('Отметьте, если это тестовый период подписки'),
            ]),
        ];
    }
} 