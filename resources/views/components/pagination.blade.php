@if($paginator->hasPages())
<nav class="pagination" aria-label="Halaman produk"><p>Menampilkan {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} dari {{ $paginator->total() }} produk</p><div class="pagination-buttons">
@if($paginator->onFirstPage())<span aria-disabled="true">← Sebelumnya</span>@else<a href="{{ $paginator->previousPageUrl() }}" rel="prev">← Sebelumnya</a>@endif
@foreach($elements as $element)
@if(is_string($element))<span>{{ $element }}</span>@endif
@if(is_array($element))@foreach($element as $page => $url)@if($page == $paginator->currentPage())<span aria-current="page">{{ $page }}</span>@else<a href="{{ $url }}" aria-label="Halaman {{ $page }}">{{ $page }}</a>@endif @endforeach @endif
@endforeach
@if($paginator->hasMorePages())<a href="{{ $paginator->nextPageUrl() }}" rel="next">Berikutnya →</a>@else<span aria-disabled="true">Berikutnya →</span>@endif
</div></nav>
@endif
