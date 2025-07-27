@extends('app', [
    'title' => 'Специалисты ассоциации',
    'keywords' => '', # Ключевые слова
    'description' => '' # Описание страницы
])

@section('content')
@use('App\Models\User', 'User')
@use('App\Models\SpecialistCategory', 'SpecialistCategory')
@use('App\Models\SpecialistReview', 'SpecialistReview')
@use('Carbon\Carbon', 'Carbon')

<div class="bread_crumb">
    <div class="container">
        <ul>
            <li><a href="{{ route('home') }}">Главная <span>—</span></a></li>
            <li>Список специалистов</li>
        </ul>
    </div>
</div>
<section class="association_specialists">
    <div class="container">
        <div class="block_association">
            <div class="title_block d-flex align-items-center justify-content-md-between">
                <h2>Специалисты ассоциации</h2>
                <div class="block_form_three my-0">
                    <div class="form_group">
                        <a href="{{ route('specialists.add') }}">Присоединиться</a>
                    </div>
                </div>
            </div>
            <div class="tab_group">
                <div class="row gap-3">
                    <a href="{{ route('specialists') }}" class="col-auto {{ Request::url() === route('specialists') ? 'avtive' : '' }}">ВСЕ</a>
                    @forelse ($categories as $item)
                    @php $active = Request::url() === route('specialists.category', $item->id) ? 'avtive' : '';
                    @endphp

                    @if ($user && $user->group === 'admin')
                    <div class="col-auto dropdown">
                        <a href="javascript:;" class="{{ $active }}" type="button" data-bs-toggle="dropdown" aria-expanded="false">{{ $item->name }}</a>
                        <ul class="dropdown-menu">
                            <li>
                                <div class="dropdown-item d-flex align-items-center gap-3" onclick="location.href = '{{ route('specialists.category', $item->id) }}'" type="button">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-link" viewBox="0 0 16 16">
                                        <path d="M6.354 5.5H4a3 3 0 0 0 0 6h3a3 3 0 0 0 2.83-4H9q-.13 0-.25.031A2 2 0 0 1 7 10.5H4a2 2 0 1 1 0-4h1.535c.218-.376.495-.714.82-1z" />
                                        <path d="M9 5.5a3 3 0 0 0-2.83 4h1.098A2 2 0 0 1 9 6.5h3a2 2 0 1 1 0 4h-1.535a4 4 0 0 1-.82 1H12a3 3 0 1 0 0-6z" />
                                    </svg>
                                    <span>Перейти</span>
                                </div>
                            </li>
                            <li>
                                <div class="dropdown-item d-flex align-items-center gap-3" type="button" data-bs-toggle="modal" data-bs-target="#addCategory" onclick="editCategory({name: '{{ $item->name }}', status: {{ $item->status }}, action: '{{ route('specialists.category.update', $item->id) }}' })">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
                                        <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325" />
                                    </svg>
                                    <span>Редактировать</span>
                                </div>
                            </li>
                            <li>
                                <div class="dropdown-item btn btn-danger d-flex align-items-center gap-3" type="button" onclick="confirm('Вы уверены, что хотите удалить данную категорию? Все курсы связанные с данной категорией будут удалены') ? location.href = '{{ route('specialists.category.destroy', $item->id) }}' : ''">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                        <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z" />
                                        <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z" />
                                    </svg>
                                    <span>Удалить</span>
                                </div>
                            </li>
                        </ul>
                    </div>
                    @else
                    <a href="{{ route('specialists.category', $item->id) }}" class="col-auto {{ $active }}">{{ $item->name }}</a>
                    @endif
                    @empty
                    @endforelse
                </div>

                @if ($user && $user->group === 'admin')
                <a href="javascript:;" class="d-flex align-items-center avtive" data-bs-toggle="modal" data-bs-target="#addCategory" onclick="resetForm()">
                    <span>Добавить</span>

                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus" viewBox="0 0 16 16">
                        <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4" />
                    </svg>
                </a>

                <div class="modal fade" id="addCategory" tabindex="-1" aria-labelledby="addCategoryLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <form action="{{ route('specialists.category.add') }}" method="POST" id="addcat">
                            @csrf
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="addCategoryLabel">Добавление категорий</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <label for="name">Название</label>
                                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror">
                                    @error('name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror

                                    <div class="my-3"></div>

                                    <label for="status">Видимость</label>
                                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                                        <option value="0">Не видно</option>
                                        <option value="1">Видно</option>
                                    </select>
                                    @error('status')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                                    <button type="submit" class="btn btn-primary" id="buttonAddOrUpdate">
                                        <span>Добавить</span>
                                        <span class="d-none">Сохранить</span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                @endif
            </div>
            <div class="tab_group mob">
                <select onchange="location.href = this.value === 0 ? '{{ route('specialists') }}' : '{{ route('specialists') }}/category/'+this.value">
                    <option value="0" {{ Request::url() === route('specialists') ? 'selected' : '' }}>ВСЕ</option>
                    @forelse ($categories as $item)
                    <option value="{{ $item->id }}" {{ Request::url() === route('specialists.category', $item->id) ? 'selected' : '' }}>{{ $item->name }}</option>
                    @empty
                    @endforelse
                </select>
            </div>
        </div>
    </div>
</section>
<section class="big_inf_block">
    <div class="container">
        <div class="ing_group">
            <div class="row">
                <div class="col-lg-4">
                    <form action="{{ route('specialists.search') }}" method="POST" id="search_filter">
                        <div class="form_group">
                            <select name="city">
                                <option>Выберите город</option>
                                <option>Москва</option>
                                <option>Тула</option>
                            </select>
                            <select name="gender">
                                <option>Пол специалиста</option>
                                <option>муж</option>
                                <option>жен</option>
                            </select>
                            <select name="price">
                                <option>Стоимость</option>
                                <option>Низкая</option>
                                <option>Высокая</option>
                            </select>
                            <select name="free_time">
                                <option>Консультация</option>
                                <option>Бесплатная</option>
                                <option>Платная</option>
                            </select>

                            <a href="javascript:;" onclick="document.getElementById('search_filter').submit()">Подобрать специалиста</a>

                            <a href="{{ route('specialists') }}">Сбросить фильтр</a>
                        </div>
                    </form>
                </div>
                <div class="col-lg-8">
                    <div class="right_inf_spe">
                        {{--
                        <div class="block_tabs">
                            <a href="#" class="active">Все</a>
                            <a href="#">Высокий рейтинг</a>
                            <a href="#">Низкая цена</a>
                            <a href="#">Бесплатно</a>
                        </div>
                        --}}
                        @forelse ($specialists as $item)
                        @php  $specialistUser = User::find($item->user_id)->first(['firstname', 'lastname']);
                        $category = SpecialistCategory::find($item->specialist_category_id)->first();
                        $category['name'] = ucfirst(strtolower($category->name));
                        @endphp

                        @if(!$specialistUser)
                        @continue
                        @endif
                        <div class="block_about_phychologist">
                            <div class="block_img">
                                <img src="/avatar/{{ $item->user_id }}" alt="">
                                <div class="bottom_text">
                                    {{--
                                    <div class="block_on">
                                        <img src="/img/online.svg" alt="">
                                        <p>Свободен</p>
                                    </div>
                                    <h4>Рейтинг специалиста: <br> <span>4.93 / 5</span></h4>
                                    <h4>На сайте <span>15</span> отзывов</h4>
                                    --}}
                                    <div class="d-flex align-items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-geo" viewBox="0 0 16 16">
                                            <path fill-rule="evenodd" d="M8 1a3 3 0 1 0 0 6 3 3 0 0 0 0-6M4 4a4 4 0 1 1 4.5 3.969V13.5a.5.5 0 0 1-1 0V7.97A4 4 0 0 1 4 3.999zm2.493 8.574a.5.5 0 0 1-.411.575c-.712.118-1.28.295-1.655.493a1.3 1.3 0 0 0-.37.265.3.3 0 0 0-.057.09V14l.002.008.016.033a.6.6 0 0 0 .145.15c.165.13.435.27.813.395.751.25 1.82.414 3.024.414s2.273-.163 3.024-.414c.378-.126.648-.265.813-.395a.6.6 0 0 0 .146-.15l.015-.033L12 14v-.004a.3.3 0 0 0-.057-.09 1.3 1.3 0 0 0-.37-.264c-.376-.198-.943-.375-1.655-.493a.5.5 0 1 1 .164-.986c.77.127 1.452.328 1.957.594C12.5 13 13 13.4 13 14c0 .426-.26.752-.544.977-.29.228-.68.413-1.116.558-.878.293-2.059.465-3.34.465s-2.462-.172-3.34-.465c-.436-.145-.826-.33-1.116-.558C3.26 14.752 3 14.426 3 14c0-.599.5-1 .961-1.243.505-.266 1.187-.467 1.957-.594a.5.5 0 0 1 .575.411"/>
                                        </svg>
                                        <span>{{ $item->location }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="block_right">
                                <div class="block_name">
                                    <h2>{{ $specialistUser->firstname }} {{ $specialistUser->lastname }}</h2>
                                    {{-- <a href="#">Рекомендуем <img src="img/great.svg" alt=""></a> --}}
                                </div>
                                <div class="block_psychologist">
                                    <p>{{ $category->name }}</p>
                                    <img src="/img/tochka.svg" alt="">
                                    <p>{{ Carbon::parse($item->birthday)->age }} лет</p>
                                    <img src="/img/tochka.svg" alt="">
                                    <p>{{ $item->degree }}</p>
                                    <img src="/img/tochka.svg" alt="">
                                    <p>стаж {{ $item->experience }} лет</p>
                                </div>
                                <div class="span_block">
                                    <div class="group_sen">
                                        <span>Стоимость онлайн:</span>
                                        <div class="about_sen">
                                            <h4>{{ $item->prices['online'] }}</h4>
                                            <img src="/img/sms.svg" alt="">
                                            <h5>/≈ {{ $item->time['online'] }} мин</h5>
                                        </div>
                                    </div>
                                    <div class="group_sen">
                                        <span>Стоимость личного приема:</span>
                                        <div class="about_sen">
                                            <h4>{{ $item->prices['reception'] }}</h4>
                                            <img src="/img/sms.svg" alt="">
                                            <h5>/≈ {{ $item->time['reception'] }} мин</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="about_me">
                                    <h5>Коротко о себе:</h5>
                                    <p>{{ $item->about }}</p>
                                </div>
                                @if ($item->free_time !== 0)
                                    <div class="block_clock">
                                        <img src="/img/clock.svg" alt="">
                                        <span>Первая {{ $item->free_time }}-минутная консультация бесплатно</span>
                                    </div>
                                @endif
                                <div class="more d-flex align-items-center gap-3">
                                    <a href="{{ route('specialists.show', $item->id) }}">Подробнее</a>

                                    @if ($user && $user->group === 'admin')
                                        @if ($item->status === 0)
                                        <form action="{{ route('specialists.approve') }}" method="POST" id="form_{{ $item->id }}">
                                            @csrf
                                            <input type="hidden" name="specialist_id" value="{{ $item->id }}">

                                            <a href="javascript:;" onclick="document.getElementById('form_{{ $item->id }}').submit()">Одобрить</a>
                                        </form>
                                        @elseif (isset(${'success_'.$item->id}))
                                        <div class="alert alert-success mb-0">Одобрен</div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="block_about_phychologist mob_p">
                            <div class="block_img text-center">
                                <img src="/avatar/{{ $item->user_id }}" alt="">
                                <div class="bottom_text">
                                    {{--
                                    <div class="block_on">
                                        <img src="/img/online.svg" alt="">
                                        <p>Свободен</p>
                                    </div>

                                    <h4>Рейтинг специалиста: <br> <span>4.93 / 5</span></h4>
                                    <h4>На сайте <span>15</span> отзывов</h4>
                                    --}}
                                    <div class="d-flex align-items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-geo" viewBox="0 0 16 16">
                                            <path fill-rule="evenodd" d="M8 1a3 3 0 1 0 0 6 3 3 0 0 0 0-6M4 4a4 4 0 1 1 4.5 3.969V13.5a.5.5 0 0 1-1 0V7.97A4 4 0 0 1 4 3.999zm2.493 8.574a.5.5 0 0 1-.411.575c-.712.118-1.28.295-1.655.493a1.3 1.3 0 0 0-.37.265.3.3 0 0 0-.057.09V14l.002.008.016.033a.6.6 0 0 0 .145.15c.165.13.435.27.813.395.751.25 1.82.414 3.024.414s2.273-.163 3.024-.414c.378-.126.648-.265.813-.395a.6.6 0 0 0 .146-.15l.015-.033L12 14v-.004a.3.3 0 0 0-.057-.09 1.3 1.3 0 0 0-.37-.264c-.376-.198-.943-.375-1.655-.493a.5.5 0 1 1 .164-.986c.77.127 1.452.328 1.957.594C12.5 13 13 13.4 13 14c0 .426-.26.752-.544.977-.29.228-.68.413-1.116.558-.878.293-2.059.465-3.34.465s-2.462-.172-3.34-.465c-.436-.145-.826-.33-1.116-.558C3.26 14.752 3 14.426 3 14c0-.599.5-1 .961-1.243.505-.266 1.187-.467 1.957-.594a.5.5 0 0 1 .575.411"/>
                                        </svg>
                                        <span>{{ $item->location }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="block_right">
                                <div class="block_name">
                                    <h2>{{ $specialistUser->firstname }} {{ $specialistUser->lastname }}</h2>
                                    {{-- <a href="#">Рекомендуем <img src="img/great.svg" alt=""></a> --}}
                                </div>
                                <div class="block_psychologist">
                                    <p>{{ $category->name }}</p>
                                    <img src="/img/tochka.svg" alt="">
                                    <p>{{ Carbon::parse($item->birthday)->age }} лет</p>
                                    <img src="/img/tochka.svg" alt="">
                                    <p>{{ $item->degree }}</p>
                                    <img src="/img/tochka.svg" alt="">
                                    <p>стаж {{ $item->experience }} лет</p>
                                </div>
                                <div class="span_block">
                                    <div class="group_sen">
                                        <span>Стоимость онлайн:</span>
                                        <div class="about_sen">
                                            <h4>{{ $item->prices['online'] }}</h4>
                                            <img src="/img/sms.svg" alt="">
                                            <h5>/≈ {{ $item->time['online'] }} мин</h5>
                                        </div>
                                    </div>
                                    <div class="group_sen">
                                        <span>Стоимость личного приема:</span>
                                        <div class="about_sen">
                                            <h4>{{ $item->prices['reception'] }}</h4>
                                            <img src="/img/sms.svg" alt="">
                                            <h5>/≈ {{ $item->time['reception'] }} мин</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="about_me">
                                    <h5>Коротко о себе:</h5>
                                    <p>{{ $item->about }}</p>
                                </div>
                                @if ($item->free_time !== 0)
                                    <div class="block_clock">
                                        <img src="/img/clock.svg" alt="">
                                        <span>Первая {{ $item->free_time }}-минутная консультация бесплатно</span>
                                    </div>
                                @endif
                                <div class="more d-flex align-items-center gap-3">
                                    <a href="{{ route('specialists.show', $item->id) }}">Подробнее</a>

                                    @if ($user && $user->group === 'admin')
                                        @if ($item->status === 0)
                                        <form action="{{ route('specialists.approve') }}" method="POST" id="form_{{ $item->id }}">
                                            @csrf
                                            <input type="hidden" name="specialist_id" value="{{ $item->id }}">

                                            <a href="javascript:;" onclick="document.getElementById('form_{{ $item->id }}').submit()">Одобрить</a>
                                        </form>
                                        @elseif (isset(${'success_'.$item->id}))
                                        <div class="alert alert-success mb-0">Одобрен</div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                        @empty
                        @endforelse
                        {{ $specialists->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    let addCategory

    document.addEventListener('DOMContentLoaded', () => {
        addCategory = new bootstrap.Modal('#addCategory')

        @error('name') addCategory.show() @enderror
        @error('status') addCategory.show() @enderror
    })

    function editCategory(data) {
        const form = document.getElementById('addcat'),
            name = document.getElementById('name'),
            status = document.querySelector('select#status option[value="' + data.status + '"]'),
            button = document.querySelectorAll('#buttonAddOrUpdate span')

        form.action = data.action

        name.value = data.name
        status.selected = true

        button[0].classList.add('d-none')
        button[1].classList.remove('d-none')

        addCategory.show()
    }

    function resetForm() {
        const form = document.getElementById('addcat'),
            name = document.getElementById('name'),
            status = document.querySelector('select#status option[value="0"]'),
            button = document.querySelectorAll('#buttonAddOrUpdate span')

        form.action = '{{ route('specialists.category.add') }}'

        name.value = ''
        status.selected = true

        button[0].classList.remove('d-none')
        button[1].classList.add('d-none')
    }
</script>
@endsection
