<?php

declare(strict_types=1);

namespace App\Orchid\Screens\User;

use App\Models\Product;
use App\Models\Subscription;
use App\Models\SubscriptionPays;
use App\Orchid\Layouts\User\UserEditLayout;
use App\Orchid\Layouts\User\UserFiltersLayout;
use App\Orchid\Layouts\User\UserListLayout;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Orchid\Platform\Models\User;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UserListScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(Request $request): iterable
    {
        $query = User::query()
            ->leftJoin('subscriptions', 'subscriptions.user_id', '=', 'users.id')
            ->select('users.*', 'subscriptions.level as subscription_level');

        $filters = $request->input('filter', []);

        if (!empty($filters)) {
            foreach ($filters as $key => $value) {
                if ($value === null || $value === '') {
                    continue;
                }

                if ($key === 'subscription_level') {
                    if ($value === 'no_subscription') {
                        $query->where(function($q) {
                            $q->whereNull('subscriptions.level')->orWhere('subscriptions.level', '0');
                        });
                    } elseif ($value !== 'all') {
                        $query->where('subscriptions.level', $value);
                    }
                    continue;
                }
                
                $column = 'users.' . $key;
                
                if (Schema::hasColumn('users', $key)) {
                    $query->where($column, 'like', '%' . $value . '%');
                }
            }
        }

        $sort = $request->input('sort');

        if ($sort !== null && str_contains($sort, 'subscription_level')) {
            $direction = $sort[0] === '-' ? 'desc' : 'asc';
            $query->orderByRaw('CASE WHEN subscriptions.level IS NULL THEN 1 ELSE 0 END, subscriptions.level ' . $direction);
        } else if ($sort) {
            $direction = 'asc';
            if (str_starts_with($sort, '-')) {
                $direction = 'desc';
                $sort = substr($sort, 1);
            }

            if (Schema::hasColumn('users', $sort)) {
                $query->orderBy('users.' . $sort, $direction);
            } else {
                $query->orderBy('users.id', 'desc');
            }
        } else {
            $query->orderBy('users.id', 'desc');
        }

        return [
            'users' => $query->paginate(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return 'User Management';
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return 'Всего зарегистрировано пользователей: ' . User::count();
    }

    public function permission(): ?iterable
    {
        return [
            'platform.systems.users',
        ];
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make(__('Add'))
                ->icon('bs.plus-circle')
                ->route('platform.systems.users.create'),
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return string[]|\Orchid\Screen\Layout[]
     */
    public function layout(): iterable
    {
        return [
            UserListLayout::class,

            Layout::modal('asyncEditUserModal', UserEditLayout::class)
                ->async('asyncGetUser'),
        ];
    }

    /**
     * @return array
     */
    public function asyncGetUser(User $user): iterable
    {
        return [
            'user' => $user,
        ];
    }

    public function saveUser(Request $request, User $user): void
    {
        $request->validate([
            'user.email' => [
                'required',
                Rule::unique(User::class, 'email')->ignore($user),
            ],
        ]);

        $user->fill($request->input('user'))->save();

        Toast::info(__('User was saved.'));
    }

    public function remove(Request $request): void
    {
        $userId = $request->get('id');

        // Удаление связанных записей платежей
        $subscription = Subscription::where('user_id', $userId)->first();
        if ($subscription) {
            // Удаляем платежи по подписке
            SubscriptionPays::where('subscription_id', $subscription->id)->delete();
        }

        // Удаление подписки пользователя
        Subscription::where('user_id', $userId)->delete();

        // Удаление пользователя
        User::findOrFail($userId)->delete();

        Toast::info(__('User was removed'));
    }
}
