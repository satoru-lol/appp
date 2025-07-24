{{--
    Custom Laravel pagination view for Bootstrap 5
    Removes the summary text ("Показано с ... по ... из ... результатов")
    Only renders the pagination links
    On mobile, shows only 6 page buttons
--}}
@if ($paginator->hasPages())
    <nav style="
	    margin: -10px;
    ">
        <ul class="pagination" style="gap: 4px;">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true" aria-label="« Предыдущий">
                    <span class="page-link" aria-hidden="true">‹</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="« Предыдущий">‹</a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="page-item disabled" aria-disabled="true"><span class="page-link">{{ $element }}</span></li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @php $i = 0; @endphp
                    @foreach ($element as $page => $url)
                        @php $i++; @endphp
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active {{ $i > 6 ? 'd-none d-sm-inline' : '' }}" aria-current="page"><span class="page-link">{{ $page }}</span></li>
                        @else
                            <li class="page-item {{ $i > 6 ? 'd-none d-sm-inline' : '' }}"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Следующий »">›</a>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true" aria-label="Следующий »">
                    <span class="page-link" aria-hidden="true">›</span>
                </li>
            @endif
        </ul>
    </nav>
@endif 