<h1>Panel de Administración</h1>

<form method="post" action="{{ route('propiedades.buscar') }}">
    @csrf 
    <input type="text" name="referencia" placeholder="Referencia Catastral" required>
    <button type="submit">Consulta Api</button>

</form>