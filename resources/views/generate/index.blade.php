@extends('app', [
    'title' => 'Генерация курса',
    'keywords' => '', # Ключевые слова
    'description' => '' # Описание страницы
])


@section('content')
    <div class="container mt-5">
        <h1>Генерация курса</h1>
        <p>Демо-форма для генерации расписания курсов</p>
        <p>Для примера были взяты следующие пожелания преподавателей:</p>
        <p>Булах Л.С. - только по субботам(09:00-17:00)</p>
        <p>Царева Т.О. - понедельник, четверг, пятница(14:00-19:00)</p>
        <p>Скалаухова Т.С. - вторник, среда(12:00-16:00)</p>
        <p>Шептура А.В. - по будням(12:00-19:00)</p>
        <p>Кузьмина А.В. - по будням(10:00-15:00)</p>
        <p>Рат Д.А. - по будням(10:00-19:00)</p>
        <p>Абязова Ю.А. - по будням(10:00-17:00)</p>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#courseModal">
            Добавить курс
        </button>

        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#teacherModal">Добавить пожелание</button>
    </div>
    <div class="modal fade" id="courseModal" tabindex="-1" aria-labelledby="courseModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="courseModalLabel">Добавить курс</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                </div>
                <div class="modal-body">
                    <form action="{{route('generateCourse')}}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="courseName" class="form-label">Название курса</label>
                            <select name="courseName" class="form-control">
                                <option value="1">Детская нейропсихология. Формирование и диагностика высших корковых функций. Практикум</option>
                                <option value="2">Детская нейропсихология. Специфические расстройства развития учебных навыков. Дискалькулия</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="courseStart" class="form-label">Начало курса</label>
                            <input type="date" class="form-control" id="courseStart" name="courseStart">
                        </div>
                        <div class="mb-3">
                            <label for="courseDuration" class="form-label">Длительность (в днях)</label>
                            <input type="number" class="form-control" id="courseDuration" name="courseDuration" placeholder="Введите количество дней">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                            <button type="submit" class="btn btn-success">Сохранить</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>


    {{--<div class="modal fade" id="teacherModal" tabindex="-1" aria-labelledby="teacherModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="courseModalLabel">Добавить пожелание</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                </div>
                <div class="modal-body">
                    <form>
                        @csrf
                        <div class="mb-3">
                            <label for="courseName" class="form-label">Преподаватели</label>
                            <select name="teacher" class="form-control">
                                <option value="1">Булах Л.С.</option>
                                <option value="2">Царева Т.О.</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="courseStart" class="form-label">Начало курса</label>
                            <input type="date" class="form-control" id="courseStart" name="courseStart">
                        </div>
                        <div class="mb-3">
                            <label for="courseDuration" class="form-label">Длительность (в днях)</label>
                            <input type="number" class="form-control" id="courseDuration" name="courseDuration" placeholder="Введите количество дней">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                            <button type="submit" class="btn btn-success">Сохранить</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>--}}
@endsection
