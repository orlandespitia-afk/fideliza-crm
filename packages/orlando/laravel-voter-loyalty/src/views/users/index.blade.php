<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - FidelizaCRM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container mt-5">

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>🛠️ Panel de Gestión de Usuarios</h2>
            <a href="{{ route('voters.index') }}" class="btn btn-secondary">← Volver a Votantes</a>
        </div>

        <div class="row">

            <div class="col-md-4 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">Registrar Nuevo Usuario</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('users.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Nombre Completo</label>
                                <input type="text" name="name" class="form-control" required
                                    placeholder="Ej. Juan Pérez"
                                    minlength="3" maxlength="100"
                                    oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '')">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nombre de Usuario (Login)</label>
                                <input type="text" name="username" class="form-control" required
                                    placeholder="Ej. juan123"
                                    minlength="4" maxlength="50"
                                    oninput="this.value = this.value.toLowerCase().replace(/[^a-z0-9]/g, '')">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Correo Electrónico</label>
                                <input type="email" name="email" class="form-control" required
                                    placeholder="correo@ejemplo.com"
                                    maxlength="150">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Contraseña Inicial</label>
                                <input type="password" name="password" class="form-control" required
                                    placeholder="Min. 6 caracteres"
                                    minlength="6" maxlength="50">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Rol del Usuario</label>
                                <select name="role" class="form-select" required>
                                    <option value="" selected disabled>-- Seleccione un Rol --</option>
                                    <option value="digitador">Digitador (Asesor Básico)</option>
                                    <option value="admin">Administrador (Campaña)</option>
                                    <option value="super_admin">Super Administrador (Todo)</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-success w-100">Crear Usuario</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-dark text-white">
                        <h5 class="card-title mb-0">Usuarios en la Base de Datos</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0 align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Usuario</th>
                                        <th>Correo</th>
                                        <th>Rol Actual</th>
                                        <th>Acción (Cambiar Rol)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $u)
                                    <tr>
                                        <td><strong>{{ $u->name }}</strong></td>
                                        <td><span class="badge bg-secondary">{{ $u->username }}</span></td>
                                        <td>{{ $u->email }}</td>
                                        <td>
                                            @if(in_array($u->role, ['super_admin']))
                                            <span class="badge bg-dark text-white">Super Admin</span>
                                            @elseif($u->role === 'admin')
                                            <span class="badge bg-danger">Admin</span>
                                            @else
                                            <span class="badge bg-info text-dark">Digitador</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $u->id }}">
                                                    ✏️ Editar
                                                </button>

                                                <form action="{{ route('users.destroy', $u->id) }}" method="POST" class="mb-0" onsubmit="return confirm('¿Estás totalmente seguro de eliminar a {{ $u->name }}? Esta acción no se puede deshacer.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">🗑️ Eliminar</button>
                                                </form>
                                            </div>

                                            <div class="modal fade" id="editUserModal{{ $u->id }}" tabindex="-1" aria-labelledby="editUserModalLabel{{ $u->id }}" aria-hidden="true">
                                                <div class="modal-dialog text-start">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-primary text-white">
                                                            <h5 class="modal-title" id="editUserModalLabel{{ $u->id }}">Editar Usuario: {{ $u->name }}</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <form action="{{ route('users.update', $u->id) }}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="modal-body text-dark">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Nombre Completo</label>
                                                                    <input type="text" name="name" class="form-control" value="{{ $u->name }}" required
                                                                        minlength="3" maxlength="100"
                                                                        oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '')">
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label">Nombre de Usuario (Login)</label>
                                                                    <input type="text" name="username" class="form-control" value="{{ $u->username }}" required
                                                                        minlength="4" maxlength="50"
                                                                        oninput="this.value = this.value.toLowerCase().replace(/[^a-z0-9]/g, '')">
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label">Correo Electrónico</label>
                                                                    <input type="email" name="email" class="form-control" value="{{ $u->email }}" required maxlength="150">
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label">Rol del Usuario</label>
                                                                    <select name="role" class="form-select" required>
                                                                        <option value="digitador" {{ $u->role === 'digitador' ? 'selected' : '' }}>Digitador</option>
                                                                        <option value="admin" {{ $u->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                                                        <option value="super_admin" {{ $u->role === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                                <button type="submit" class="btn btn-success">Guardar Cambios</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>