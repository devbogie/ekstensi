@if ($paginator->hasPages())
    @php
        $current = $paginator->currentPage();
        $lastPage = $paginator->lastPage();
        $onEachSide = 2;
        $start = max(1, $current - $onEachSide);
        $end = min($lastPage, $current + $onEachSide);
        $urls = $paginator->getUrlRange($start, $end);
    @endphp
    <div class="pagination-panel">
        <div class="pagination-info">
            Menampilkan <strong>{{ $paginator->firstItem() }}–{{ $paginator->lastItem() }}</strong> dari
            <strong>{{ $paginator->total() }}</strong> hasil
        </div>
        <nav aria-label="Navigasi halaman">
            <ul class="pagination justify-content-center gap-1">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <li class="page-item disabled" aria-disabled="true" aria-label="Sebelumnya">
                        <span class="page-link" aria-hidden="true">&lsaquo;</span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev"
                            aria-label="Sebelumnya">&lsaquo;</a>
                    </li>
                @endif

                {{-- First page + separator --}}
                @if ($start > 1)
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->url(1) }}">1</a>
                    </li>
                    @if ($start > 2)
                        <li class="page-item disabled" aria-disabled="true"><span class="page-link">&hellip;</span></li>
                    @endif
                @endif

                {{-- Middle window --}}
                @foreach ($urls as $page => $url)
                    @if ($page == $current)
                        <li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span></li>
                    @else
                        <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                    @endif
                @endforeach

                {{-- Last page with separator --}}
                @if ($end < $lastPage)
                    @if ($end < $lastPage - 1)
                        <li class="page-item disabled" aria-disabled="true"><span class="page-link">&hellip;</span></li>
                    @endif
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->url($lastPage) }}">{{ $lastPage }}</a>
                    </li>
                @endif

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next"
                            aria-label="Berikutnya">&rsaquo;</a>
                    </li>
                @else
                    <li class="page-item disabled" aria-disabled="true" aria-label="Berikutnya">
                        <span class="page-link" aria-hidden="true">&rsaquo;</span>
                    </li>
                @endif
            </ul>
        </nav>
    </div>
@endif