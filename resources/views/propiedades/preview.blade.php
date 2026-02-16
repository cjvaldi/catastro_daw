<h2>Vista previa propiedad</h2>

<p><strong>Referencia :</strong> {{ $referencia }}<p>

<form method="POST" action="{{ route('propiedades.guardar') }}">
    @csrf
    <input type="hidden" name="referencia" value="{{ $referencia }}">
    <input type="hidden" name="raw_json" value="{{ json_encode($datos) }}">

    @if(auth()->user()->rol !== 'visitante')
        <button type="submit">Guardar propiedad</button>
    @endif
</form>