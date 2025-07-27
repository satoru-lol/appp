<style>
    /* Стили для клубов */
    .clubs-grid-v2 {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .clubs-header-v2 {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        background-color: #f8f9fa;
        padding: 0.5rem 0.75rem;
        border-radius: 0.5rem;
        font-weight: 500;
        color: #495057;
        font-size: 0.9rem;
    }
    
    .club-item-v2 {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        padding: 0.75rem;
        border-radius: 0.5rem;
        background-color: #fff;
        border: 1px solid #e0e0e0;
        transition: background-color 0.2s ease;
    }
    
    .club-item-v2:hover {
        background-color: #f8f9fa;
    }
    
    .club-col-v2 {
        padding: 0.25rem;
        font-size: 0.95rem;
    }
    
    @media (max-width: 768px) {
        .clubs-header-v2 {
            display: none;
        }
        
        .club-item-v2 {
            grid-template-columns: 1fr;
            gap: 0.5rem;
        }
        
        .club-col-v2::before {
            content: attr(data-label);
            font-weight: 600;
            display: block;
            margin-bottom: 0.25rem;
            color: #495057;
            font-size: 0.85rem;
        }
    }
</style>

<div class="clubs-grid-v2">
    @if ($clubs->isEmpty())
        <div class="alert-v2 alert-v2-info text-center">
            <i class="bi bi-info-circle me-2"></i>В данный момент активных клубов нет.
        </div>
    @else
        <div class="clubs-header-v2">
            <div>Название клуба</div>
            <div>Ближайшее занятие</div>
            <div>Действие</div>
        </div>
        @foreach($clubs as $c)
            <div class="club-item-v2">
                <div class="club-col-v2" data-label="Название">
                    <a href="{{ route('v2.club.show', $c->id) }}" style="color: #613482; text-decoration: none;">{{$c->title}}</a>
                </div>
                <div class="club-col-v2" data-label="Ближайшее занятие">
                    @if($c->clubDates)
                        <i class="bi bi-calendar-check me-1"></i>{{ \Carbon\Carbon::parse($c->clubDates->date)->format('d.m.Y') }} в {{ $c->clubDates->start_time }}
                    @else
                        <span class="text-muted">Нет запланированных занятий</span>
                    @endif
                </div>
                <div class="club-col-v2" data-label="Действие">
                    @if($c->feedback)
                        <a href="{{$c->feedback}}" target="_blank" class="btn btn-v2-primary btn-sm d-inline-flex align-items-center">
                            <i class="bi bi-camera-video-fill me-1"></i>Подключиться
                        </a>
                    @else
                        <button class="btn btn-v2-secondary btn-sm d-inline-flex align-items-center" disabled>
                           <i class="bi bi-hourglass-split me-1"></i>Ожидается
                        </button>
                    @endif
                </div>
            </div>
        @endforeach
    @endif
</div> 