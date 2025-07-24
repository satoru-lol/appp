@extends('app')

@section('title', $title ?? 'Клуб - АЧПП')
@section('description', $description ?? 'Онлайн-клуб АЧПП')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8">
            <!-- Club Header -->
            <div class="card mb-4">
                <div class="card-body">
                    @if($club->image)
                        <img src="{{ asset('storage/' . $club->image) }}" 
                             alt="{{ $club->title }}" 
                             class="img-fluid rounded mb-3" 
                             style="width: 100%; height: 300px; object-fit: cover;">
                    @endif
                    
                    <h1 class="mb-3">{{ $club->title }}</h1>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="text-muted mb-1">
                                <i class="bi bi-people me-2"></i>
                                {{ $participants->count() }} участников
                            </p>
                        </div>
                        @if(isset($club->formatted_date))
                            <div class="col-md-6">
                                <p class="text-muted mb-1">
                                    <i class="bi bi-calendar me-2"></i>
                                    {{ $club->formatted_date }}
                                    @if(isset($club->formatted_time))
                                        в {{ $club->formatted_time }}
                                    @endif
                                </p>
                            </div>
                        @endif
                    </div>
                    
                    @if($club->description)
                        <div class="mb-4">
                            {!! $club->description !!}
                        </div>
                    @endif
                    
                    <!-- Action Buttons -->
                    <div class="d-flex gap-3">
                        @auth
                            @if($isParticipant)
                                <form action="{{ route('v2.refactored.clubs.leave', $club->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger"
                                            onclick="return confirm('Вы уверены, что хотите покинуть клуб?')">
                                        <i class="bi bi-box-arrow-right me-2"></i>Покинуть клуб
                                    </button>
                                </form>
                            @elseif($canJoin)
                                <form action="{{ route('v2.refactored.clubs.join', $club->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-plus me-2"></i>Присоединиться к клубу
                                    </button>
                                </form>
                            @else
                                <button class="btn btn-secondary" disabled>
                                    <i class="bi bi-lock me-2"></i>Недоступно для вашего уровня
                                </button>
                            @endif
                        @else
                            <a href="{{ route('v2.refactored.auth.login') }}" class="btn btn-primary">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Войти для участия
                            </a>
                        @endauth
                        
                        <a href="{{ route('v2.refactored.clubs.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Все клубы
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <!-- Participants -->
            @if($participants && $participants->count() > 0)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-people me-2"></i>Участники ({{ $participants->count() }})
                        </h5>
                    </div>
                    <div class="card-body">
                        @foreach($participants->take(10) as $participant)
                            <div class="d-flex align-items-center mb-2">
                                <img src="{{ $participant->avatar ? asset('storage/' . $participant->avatar) : asset('img/default-avatar.png') }}" 
                                     alt="{{ $participant->firstname }}" 
                                     class="rounded-circle me-2" 
                                     style="width: 32px; height: 32px; object-fit: cover;">
                                <span class="small">{{ $participant->firstname }} {{ $participant->lastname }}</span>
                            </div>
                        @endforeach
                        
                        @if($participants->count() > 10)
                            <p class="text-muted small mb-0">
                                и еще {{ $participants->count() - 10 }} участников...
                            </p>
                        @endif
                    </div>
                </div>
            @endif
            
            <!-- Similar Clubs -->
            @if($similarClubs && $similarClubs->count() > 0)
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-lightbulb me-2"></i>Похожие клубы
                        </h5>
                    </div>
                    <div class="card-body">
                        @foreach($similarClubs as $similarClub)
                            <div class="mb-3">
                                <h6 class="mb-1">
                                    <a href="{{ route('v2.refactored.clubs.show', $similarClub->id) }}" 
                                       class="text-decoration-none">
                                        {{ $similarClub->title }}
                                    </a>
                                </h6>
                                <p class="text-muted small mb-1">
                                    {{ Str::limit(strip_tags($similarClub->description), 80) }}
                                </p>
                                <p class="text-muted small mb-0">
                                    <i class="bi bi-people me-1"></i>{{ $similarClub->participants_count ?? 0 }} участников
                                </p>
                            </div>
                            @if(!$loop->last)
                                <hr class="my-3">
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection