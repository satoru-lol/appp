<div class="table-responsive">
    <table class="table-v2">
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Название</th>
            <th scope="col">Ссылка встречи</th>
            <th scope="col">Дата</th>
            <th scope="col">Действия</th>
        </tr>
        </thead>
        <tbody>
        @forelse($meetings as $key => $meeting)
            <tr>
                <td>{{ $meetings->firstItem() + $key }}</td>
                <td>{{ $meeting->name }}</td>
                <td><a target="_blank" href="/ourMeetings/{{ $meeting->id }}" class="text-decoration-none" style="color: #613482; font-weight: 500;">Ссылка</a></td>
                <td>{{ $meeting->created_at->format('d.m.Y H:i') }}</td>
                <td>
                    <button onclick="window.location.href = '/takePart/delete/{{ $meeting->id }}/{{ auth()->user()->id }}'" class="btn btn-v2-danger btn-sm">
                        <i class="bi bi-x-octagon me-1"></i>
                        Отменить
                    </button>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center">
                    <div class="alert-v2 alert-v2-info my-3">
                        <i class="bi bi-info-circle me-2"></i>
                        У вас пока нет запланированных встреч.
                    </div>
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
    @if ($meetings->hasPages())
    <div class="pagination-container mt-4">
        <div class="pagination-summary text-muted">
            Показано с {{ $meetings->firstItem() }} по {{ $meetings->lastItem() }} из {{ $meetings->total() }} результатов
        </div>
        {{ $meetings->links('pagination::bootstrap-4') }}
    </div>
    @endif
</div> 