@extends('layouts.main')

@section('title','Panel de administración')

@push('css')
<style>
    .mt-10 {
        margin-top: 70px!important;
    }

    .box {
        padding-top: 230px!important;
    }

    .alga {
        top: 0 !important;
        bottom: 0 !important;
    }

    .coral {
        top: 0 !important;
        bottom: 0 !important;
    }
    
    .form-control {
        padding: 10px 23px!important;
    }

    .btn-disabled {
        background-color: #dfdfdf;
        color: black;
        cursor: not-allowed;
    }

    .btn-disabled:hover {
        background-color: #dfdfdf!important;
        color: black!important;
        cursor: not-allowed;
    }

    h5 {
        font-weight: bold;
        color: #f74219;
    }

    label {
        font-weight: bold;
        color: #033a60;
    }

    tr > td {
        text-align: left!important;
    }

    tr th::nth-child(2){
        display: none!important;
    }

    th span {
        color: white;
        font-weight: 500;
    }

    th {
        background: #f74219 !important;
    }
    
    svg {
        width: 20px;
        height: 20px;
    }

    a {
        text-decoration: none;
    }

    nav {
        text-align: center;
        margin: 10px;
        margin-bottom: 19px;
    }

    nav p {
        margin:10px 0;
    }

    nav .justify-between {
        display: none;
    }

    /* Add these new styles */
    .modal {
        z-index: 1050 !important;
    }
    
    .modal-backdrop {
        z-index: 1040 !important;
    }
    
    .modal-dialog {
        z-index: 1060 !important;
    }
    
    .modal-content {
        box-shadow: 0 0 10px rgba(0,0,0,0.3);
    }

    thead tr th {
        color: white !important;
    }

    .btn:disabled {
        background-color: grey!important;
        border: grey!important;
    }

    /* Estilos para pantallas pequeñas */
    @media (max-width: 768px) {
        table td:nth-child(2),
        table th:nth-child(2),
        table td:nth-child(3),
        table th:nth-child(3) {
            display: none;
        }
    }

    /* Estilos para la paginación */
    .pagination .page-item.active .page-link {
        background-color: #f74219 !important;
        border-color: #f74219 !important;
        color: white !important;
    }

    .pagination .page-link {
        color: #033a60;
    }

    .pagination .page-link:focus {
        box-shadow: 0 0 0 0.25rem rgba(247, 66, 25, 0.25);
    }
</style>
@endpush

@section('content')

    <!-- Modal for editing categories -->
    <div class="modal fade" id="editCategoryModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar filtro</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editCategoryForm" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="edit_category_id" name="category_id">
                        <div class="mb-3">
                            <label for="edit_name" class="form-label">Nombre del filtro</label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_description" class="form-label">Descripción</label>
                            <textarea name="description" class="form-control" id="edit_description" cols="3" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Actualizar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for adding categories -->
    <div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="categoryModalLabel">Agregar filtro</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="categoryForm" action="{{route('category.store')}}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Nombre del filtro</label>
                            <input type="text" class="form-control" id="name" name="name" vale="{{old('name')}}" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Descripción</label>
                            <textarea name="description" class="form-control" id="description" cols="3" rows="3">{{old('description')}}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Agregar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!--Crear nuevo usuario -->
    <div class="modal fade" id="newModal" tabindex="-1" aria-labelledby="newModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-nobackdrop">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="newModalLabel">Nuevo usuario</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="form" method="post" action="{{route('assessments.user.new')}}">
                    @csrf
                    <div class="row" id="institution_name" style="display: none;">
                        <div class="col-xl-12">
                            <div class="form-group">
                                <label for="name">Nombre de la institución:</label>
                                <input type="text" id="name_institution" name="name_institution" class="form-control" autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-12" id="legal_representative" style="display: none;">
                            <h5>Representante legal:</h5>
                        </div>
                        <div class="col-xl-12">
                            <div class="form-group">
                                <label for="role">Rol:</label>
                                <select name="role" id="role" class="form-control" required>
                                    @if(Auth::user()->hasRole('administrator'))
                                        <option value="administrator">Administrador</option>
                                        <option value="institution">Institución</option>
                                    @endif
                                    @if(Auth::user()->hasRole('institution'))
                                        <option value="coordinator">Coordinador</option>
                                        <option value="respondent">Evaluado</option>
                                    @else
                                        <option value="respondent">Evaluado</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-xl-12" id="institution">
                            <div class="form-group">
                                <label for="name">Selecciona una institución:</label>
                                <select name="user_id" class="form-control">
                                    @foreach($institutions as $institution)
                                        <option value="{{$institution->id}}">{{$institution->name_institution}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="form-group">
                                <label for="name">Nombre:</label>
                                <input type="text" id="name" name="name" class="form-control" autocomplete="off" required>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="form-group">
                                <label for="lastname">Apellidos:</label>
                                <input type="text" id="lastname" name="lastname" class="form-control" autocomplete="off" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="form-group">
                                <label for="gender">Género:</label>
                                <select name="gender" id="gender" class="form-control" required>
                                    <option value="M">Masculino</option>
                                    <option value="F">Femenino</option>
                                    <option value="N">No binario</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="form-group">
                                <label for="email">Correo electrónico:</label>
                                <input type="email" id="email" name="email" class="form-control" autocomplete="off" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="form-group">
                                <label for="locale">Idioma preferido:</label>
                                <select name="locale" id="locale" class="form-control" required>
                                    @foreach ($locales as $locale => $language)
                                        <option value="{{$locale}}">{{$language}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="form-group">
                                <label for="password">Contraseña:</label>
                                <input type="password" id="password" name="password" class="form-control" autocomplete="off">
                            </div>
                            <p>Si se deja en blanco, se generará una contraseña aleatoria.</p>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success send-email">Registrar usuario</button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-clear" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
        </div>
    </div>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="container" id="dashboard">
        @include('parts.user-top')
        <div class="row mt-10">
            <div class="col-12">
                @include('parts.message')
                <div class="box">
                    <img src="{{asset('assets/img/octopus.png')}}" alt="" class="octopus">
                    <img src="{{asset('assets/img/alga.png')}}" alt="" class="alga">
                    <img src="{{asset('assets/img/burbujas.png')}}" alt="" class="burbujas">
                    <img src="{{asset('assets/img/coral.png')}}" alt="" class="coral">
                    <div class="box-inner">
                        <div class="row">
                            <div class="col-xl-12">
                                <h3 class="text-center">Usuarios</h3>

                                <div class="row mb-3">
                                    <div class="col-xl-4 col-lg-6">
                                        @hasanyrole(['administrator','institution'])
                                        <div class="d-flex">
                                            <select name="category" id="category" class="form-control me-2">
                                                <option value="">Todos los usuarios</option>
                                                @foreach($categories as $category)
                                                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <button type="button" class="btn btn-success me-2" title="Agregar nuevo filtro" data-bs-toggle="modal" data-bs-target="#categoryModal">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                            <button type="button" class="btn btn-primary edit-category me-2" title="Editar filtro" {{ empty(request('category')) ? 'disabled' : '' }}>
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-danger delete-category" title="Eliminar filtro" {{ empty(request('category')) ? 'disabled' : '' }}>
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                        @endhasanyrole
                                    </div>
                                    <div class="col-xl-4 col-lg-6 offset-xl-4">
                                        <form action="{{ route('dashboard.welcome') }}" method="GET" class="d-flex">
                                            <input type="text" name="search" class="form-control" placeholder="Buscar por nombre..." value="{{ request('search') }}">
                                            <button type="submit" class="btn btn-primary ms-2">Buscar</button>
                                        </form>
                                    </div>
                                </div>

                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Nombre</th>
                                            <th>Correo</th>
                                            <th>Rol</th>
                                            @hasanyrole(['administrator','institution'])
                                            <th>Filtro</th>
                                            @endhasanyrole
                                            <th>#</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($users->isEmpty())
                                            <tr>
                                                <td colspan="5" class="text-center">No hay usuarios para mostrar</td>
                                            </tr>
                                        @else
                                            @foreach ($users as $user)
                                                <tr>
                                                    <td>{{ $user->fullname }}</td>
                                                    <td>{{ $user->emailCut }}</td>
                                                    <td>{{ $user->rol }}</td>
                                                    <td>
                                                        @hasanyrole(['administrator','institution'])
                                                        <select class="form-control category-select" data-user-id="{{ $user->id }}" style="padding: 2px 10px!important; font-size: 14px;">
                                                            <option value="">Sin filtros</option>
                                                            @foreach($categories as $category)
                                                                <option value="{{ $category->id }}" {{ $user->category_id == $category->id ? 'selected' : '' }}>
                                                                    {{ $category->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @else
                                                        {{ $user->category ? $user->category->name : 'Sin filtros' }}
                                                        @endhasanyrole
                                                    </td>
                                                    <td>
                                                        <a href="{{route('users.edit',$user->uuid)}}" class="edit btn btn-blue btn-sm" title="Editar perfil">
                                                            <i class="fas fa-pencil-alt"></i>
                                                        </a>
                                                        @if(Auth::user()->hasRole(['administrator','institution','coordinator']))
                                                            @if(!empty($user->account_id))
                                                                <a href="{{route('assessments.index',$user->account_id)}}" class="btn btn-warning btn-sm" title="Evaluaciones">
                                                                    <i class="fas fa-clipboard-list"></i>
                                                                </a>
                                                            @else
                                                                <a href="#" class="btn btn-disabled btn-sm" disabled title="Evaluaciones">
                                                                    <i class="fas fa-clipboard-list"></i>
                                                                </a>
                                                            @endif
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>

                                <div class="d-flex justify-content-center">
                                    {{ $users->links() }}
                                </div>

                                <div class="text-center">
                                    @hasrole(['administrator'])
                                        <div class="btn-group text-center" role="group">
                                            <a  href="{{route('dashboard.sync')}}" class="btn btn-warning"><i class="fas fa-sync"></i> Sincronizar usuarios</a>
                                        </div>
                                        <div class="btn-group text-center" role="group" style="display: none;">
                                            <a  href="{{route('dashboard.import')}}" class="btn btn-info"><i class="fas fa-file-import"></i> Importar usuarios</a>
                                        </div>
                                    @endhasrole
                                    @hasanyrole(['administrator','institution'])
                                        <div class="btn-group text-center" role="group">
                                            <a href="#" data-bs-toggle="modal" data-bs-target="#newModal" class="btn btn-success"><i class="fas fa-users"></i> Añadir usuario</a>
                                        </div>
                                    @endhasanyrole
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-lg-12">
                <div class="box-pink">
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="box-inner">
                            <img src="{{asset('assets/img/logo-blue.png')}}" alt="{{env('APP_NAME')}}" class="text-center logo">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
    $(document).ready(function(){
        // Existing code for send-email and modal handling
        $('.send-email').on('click', function(){
            Swal.fire({
                title: "Registrando usuario",
                text: "Espera un momento...",
                icon: "warning",
                showConfirmButton: false,
            });
        });

        $('#categoryModal').on('shown.bs.modal', function () {
            $(this).css('display', 'block');
            $('.modal-backdrop').css('z-index', '1040');
            $(this).css('z-index', '1050');
        });

        // Add category filter handling
        $('#category').on('change', function() {
            const categoryId = $(this).val();
            const currentUrl = new URL(window.location.href);
            
            if (categoryId) {
                currentUrl.searchParams.set('category', categoryId);
            } else {
                currentUrl.searchParams.delete('category');
            }
            
            // Preserve search parameter if it exists
            const searchParam = currentUrl.searchParams.get('search');
            if (searchParam) {
                currentUrl.searchParams.set('search', searchParam);
            }

            window.location.href = currentUrl.toString();
        });

        // Handle category assignment
        $('.category-select').on('change', function() {
            const userId = $(this).data('user-id');
            const categoryId = $(this).val();
            
            $.ajax({
                url: '{{route('category.updateNow')}}',
                method: 'POST',
                data: {
                    user_id: userId,
                    category_id: categoryId,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    Swal.fire({
                        title: "¡Éxito!",
                        text: "Filtro asignado correctamente",
                        icon: "success",
                        timer: 1500,
                        showConfirmButton: false
                    });
                },
                error: function() {
                    Swal.fire({
                        title: "Error",
                        text: "No se pudo asignar el filtro",
                        icon: "error"
                    });
                }
            });
        });

        // Handle edit category button click
        $('.edit-category').on('click', function() {
            const categoryId = $('#category').val();
            if (!categoryId) return;

            // Fetch category data
            $.ajax({
                url: '{{route('category.get')}}',
                method: 'POST',
                data: {
                    category_id: categoryId,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#edit_name').val(response.name);
                    $('#edit_description').val(response.description);
                    $('#edit_category_id').val(response.id);
                    $('#editCategoryForm').attr('action', '{{ route("category.updateNoww") }}');
                    
                    // Show modal using Bootstrap 5
                    const editModal = new bootstrap.Modal(document.getElementById('editCategoryModal'));
                    editModal.show();
                },
                error: function() {
                    Swal.fire({
                        title: "Error",
                        text: "No se pudo cargar la información del filtro",
                        icon: "error"
                    });
                }
            });
        });

        // Handle category update form submission
        $('#editCategoryForm').on('submit', function(e) {
            e.preventDefault();
            const form = $(this);
            const url = form.attr('action');

            $.ajax({
                url: url,
                method: 'POST',
                data: form.serialize(),
                success: function(response) {
                    // Hide modal using Bootstrap 5
                    const editModal = bootstrap.Modal.getInstance(document.getElementById('editCategoryModal'));
                    editModal.hide();
                    
                    Swal.fire({
                        title: "¡Éxito!",
                        text: "Filtro actualizado correctamente",
                        icon: "success",
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                },
                error: function() {
                    Swal.fire({
                        title: "Error",
                        text: "No se pudo actualizar el filtro",
                        icon: "error"
                    });
                }
            });
        });

        // Handle delete category button click
        $('.delete-category').on('click', function() {
            const categoryId = $('#category').val();
            if (!categoryId) return;

            Swal.fire({
                title: "¿Estás seguro?",
                text: "¿Deseas eliminar el filtro '" + $("#category option:selected").text() + "'? Esta acción no se puede deshacer",
                icon: "warning", 
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Sí, eliminar",
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{route('category.delete')}}',
                        method: 'POST',
                        data: {
                            category_id: categoryId,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            Swal.fire({
                                title: "¡Eliminado!",
                                text: "El filtro ha sido eliminado",
                                icon: "success",
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = "{{ route('dashboard.welcome') }}";
                            });
                        },
                        error: function(response) {
                            Swal.fire({
                                title: "Error",
                                text: "No se pudo eliminar el filtro",
                                icon: "error"
                            });
                        }
                    });
                }
            });
        });
    });
</script>
@endpush
