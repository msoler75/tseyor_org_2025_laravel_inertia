<style>
    .w-5 {width: 14px}
    .h-5 {height: 14px}
</style>
<ul>
@foreach($comunicados as $item)
    <li><a href="/test/docx/{{ ltrim($item->numero, '0') }}?page={{$page??1}}">{{ $item->titulo }}</a></li>
@endforeach
</ul>

{{ $comunicados->links() }}


@if ($md ?? '')
<div style="display: grid; grid-template-columns: 1fr 1fr; gap:20px; width: 100%; padding: 20px">
    <div> {!! $html !!}</div>
    <div> {{$md}}</div>
</div>
@endif
