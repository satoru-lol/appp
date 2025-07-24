<div class="profile-card-v2">
    <div class="profile-card-header">
        <h5><i class="bi bi-people me-2"></i>Мои клубы</h5>
    </div>
    <div class="profile-card-body">
        @if(count($clubs) > 0)
            <div class="clubs-grid">
                @foreach($clubs as $club)
                    <div class="club-card">
                        <div class="club-title">{{ $club->title }}</div>
                        <div class="club-description">
                            {{ Str::limit($club->description, 100) }}
                        </div>
                        <div class="club-meta">
                            <span>
                                <i class="bi bi-people me-1"></i>
                                {{ $club->members_count ?? 0 }} участников
                            </span>
                            <span>
                                <i class="bi bi-calendar me-1"></i>
                                {{ $club->created_at->format('d.m.Y') }}
                            </span>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('v2.refactored.clubs.show', $club->id) }}" 
                               class="btn btn-v2-primary btn-sm me-2">
                                <i class="bi bi-eye me-1"></i>Перейти
                            </a>
                            @if($club->pivot && $club->pivot->role === 'admin')
                                <span class="badge bg-warning text-dark">Администратор</span>
                            @elseif($club->pivot && $club->pivot->role === 'moderator')
                                <span class="badge bg-info">Модератор</span>
                            @else
                                <span class="badge bg-secondary">Участник</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Пагинация -->
            @if(method_exists($clubs, 'links'))
                <div class="d-flex justify-content-center mt-4">
                    {{ $clubs->links() }}
                </div>
            @endif

        @else
            <div class="text-center py-5">
                <i class="bi bi-people display-1 text-muted"></i>
                <h5 class="mt-3 text-muted">Вы пока не участвуете в клубах</h5>
                <p class="text-muted mb-4">
                    Присоединяйтесь к клубам по интересам и общайтесь с единомышленниками
                </p>
                <a href="{{ route('v2.refactored.clubs.index') }}" class="btn btn-v2-primary">
                    <i class="bi bi-search me-2"></i>Найти клубы
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Статистика участия в клубах -->
@if(count($clubs) > 0)
    <div class="profile-card-v2 mt-4">
        <div class="profile-card-header">
            <h5><i class="bi bi-bar-chart me-2"></i>Статистика участия</h5>
        </div>
        <div class="profile-card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-number">{{ count($clubs) }}</div>
                        <div class="stat-label">Всего клубов</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-number">
                            {{ $clubs->where('pivot.role', 'admin')->count() }}
                        </div>
                        <div class="stat-label">Администрирую</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-number">
                            {{ $clubs->where('pivot.role', 'moderator')->count() }}
                        </div>
                        <div class="stat-label">Модерирую</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-number">
                            {{ $clubs->sum('posts_count') ?? 0 }}
                        </div>
                        <div class="stat-label">Мои сообщения</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Последние активности в клубах -->
    <div class="profile-card-v2 mt-4">
        <div class="profile-card-header">
            <h5><i class="bi bi-activity me-2"></i>Последние активности</h5>
        </div>
        <div class="profile-card-body">
            @if(isset($recent_activities) && count($recent_activities) > 0)
                <div class="list-group list-group-flush">
                    @foreach($recent_activities as $activity)
                        <div class="list-group-item border-0 px-0">
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0 me-3">
                                    @if($activity->type === 'post')
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="bi bi-chat-dots"></i>
                                        </div>
                                    @elseif($activity->type === 'like')
                                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="bi bi-heart-fill"></i>
                                        </div>
                                    @else
                                        <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="bi bi-person-plus"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">{{ $activity->title }}</h6>
                                            <p class="mb-1 text-muted small">{{ $activity->description }}</p>
                                            <small class="text-muted">
                                                <i class="bi bi-clock me-1"></i>
                                                {{ $activity->created_at->diffForHumans() }}
                                            </small>
                                        </div>
                                        <a href="{{ $activity->url }}" class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-3">
                    <i class="bi bi-clock-history display-4 text-muted"></i>
                    <p class="text-muted mt-2">Пока нет активности в клубах</p>
                </div>
            @endif
        </div>
    </div>
@endif

<style>
.clubs-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
}

.club-card {
    background: white;
    border-radius: 0.75rem;
    padding: 1.5rem;
    border: 1px solid #e0e0e0;
    transition: transform 0.2s, box-shadow 0.2s;
}

.club-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(97, 52, 130, 0.1);
}

.club-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #333;
    margin-bottom: 0.5rem;
}

.club-description {
    color: #6c757d;
    font-size: 0.9rem;
    margin-bottom: 1rem;
    line-height: 1.4;
}

.club-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.8rem;
    color: #6c757d;
    margin-bottom: 1rem;
}

.club-meta span {
    display: flex;
    align-items: center;
}

.list-group-item {
    border-bottom: 1px solid #f0f0f0 !important;
}

.list-group-item:last-child {
    border-bottom: none !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Анимация появления карточек клубов
    const clubCards = document.querySelectorAll('.club-card');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, index) => {
            if (entry.isIntersecting) {
                setTimeout(() => {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }, index * 100);
            }
        });
    });
    
    clubCards.forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        observer.observe(card);
    });
});
</script>