@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">Diagnóstico de Subida de Archivos</div>

                <div class="card-body">
                    <h3>Información del Sistema</h3>
                    
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Propiedad</th>
                                <th>Valor</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($info as $key => $value)
                                <tr>
                                    <td>{{ $key }}</td>
                                    <td>{{ $value }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    
                    <hr>
                    
                    <h3>Prueba de Subida Manual</h3>
                    <form action="{{ route('upload.test.manual') }}" method="POST" enctype="multipart/form-data" class="mb-4">
                        @csrf
                        <div class="form-group">
                            <label for="file">Seleccionar Archivo:</label>
                            <input type="file" class="form-control" name="file" id="file">
                        </div>
                        <button type="submit" class="btn btn-primary mt-2">Subir Archivo</button>
                    </form>
                    
                    @if(session('test_result'))
                        <div class="alert alert-info">
                            <h4>Resultados de la Prueba:</h4>
                            <pre>{{ json_encode(session('test_result'), JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection