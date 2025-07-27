<div class="table-responsive">
    <table class="table-v2">
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Курс</th>
            <th scope="col">Тема/Занятие</th>
            <th scope="col">Ссылка</th>
            <th scope="col">Дата</th>
            <th scope="col">Статус</th>
        </tr>
        </thead>
        <tbody>
        @forelse($courseContents as $key => $content)
            <tr>
                <td>{{$key+1}}</td>
                <td>{{ $content->course ? $content->course->title : 'Без курса' }}</td>
                <td>{{ $content->title }}</td>
                <td>
                    @if($content->course && $content->course->feedback)
                        <a target="_blank" href="{{$content->course->feedback}}" class="text-decoration-none" style="color: #613482; font-weight: 500;">Подключиться</a>
                    @else
                        <span class="text-muted">Нет ссылки</span>
                    @endif
                </td>
                <td>
                    @if($content->date)
                        {{ date('H:i', strtotime($content->start_time)) . ' ' . date('d.m.Y', strtotime($content->date)) }}
                    @else
                        <span class="text-muted">Нет даты</span>
                    @endif
                </td>
                <td>
                    <span class="badge bg-success" style="border-radius: 0.5rem; padding: 0.4rem 0.6rem;">Записаны</span>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6">
                    <div class="alert-v2 alert-v2-info" role="alert">
                        У вас пока нет записей на занятия. Вы можете записаться на курсы в разделе <a href="{{ route('courses-v2.index') }}" class="alert-link" style="color: #613482; font-weight: 500; text-decoration: none;">Курсы</a>.
                    </div>
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div> 