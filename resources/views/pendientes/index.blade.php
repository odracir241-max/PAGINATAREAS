<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Gestión de pendientes por áreas</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="/css/pendientes.css" />
  </head>
  <body class="bg-body-tertiary">
    @php
      $usuarios = $usuarios ?? collect();
      $pendientes = $pendientes ?? collect();
      $prioridades = $prioridades ?? ['alta', 'media', 'baja'];
      $estatuses = $estatuses ?? ['pendiente', 'seguimiento', 'pausa', 'programado', 'finalizado'];
      $eventosCalendario = $eventosCalendario ?? collect();
      $programadosCalendario = $programadosCalendario ?? collect();
      $fechaCalendario = $fechaCalendario ?? now()->toDateString();
      $fechaProgramados = $fechaProgramados ?? now()->toDateString();
    @endphp

    <nav class="navbar navbar-dark bg-dark shadow-sm">
      <div class="container">
        <span class="navbar-brand mb-0 h1">Gestión de pendientes</span>
      </div>
    </nav>

    <main class="container py-4">
      @if (session('success'))
        <div class="alert alert-success" role="alert">{{ session('success') }}</div>
      @endif

      <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">Registrar nuevo pendiente</div>
        <div class="card-body">
          <form method="POST" action="{{ route('pendientes.store') }}" class="row g-3" enctype="multipart/form-data">
            @csrf
            <div class="col-md-6">
              <label class="form-label" for="titulo">Título</label>
              <input class="form-control" id="titulo" name="titulo" value="{{ old('titulo') }}" required />
            </div>
            <div class="col-md-3">
              <label class="form-label" for="area_origen">Área solicitante</label>
              <input class="form-control" id="area_origen" name="area_origen" value="{{ old('area_origen') }}" required />
            </div>
            <div class="col-md-3">
              <label class="form-label" for="area_destino">Área responsable</label>
              <input class="form-control" id="area_destino" name="area_destino" value="{{ old('area_destino') }}" required />
            </div>
            <div class="col-md-4">
              <label class="form-label" for="usuario_responsable_id">Responsable</label>
              <select class="form-select" id="usuario_responsable_id" name="usuario_responsable_id" required>
                <option value="">Selecciona un usuario</option>
                @foreach ($usuarios as $usuario)
                  <option value="{{ $usuario->idUsuario }}" @selected(old('usuario_responsable_id') == $usuario->idUsuario)>
                    {{ $usuario->Nombre }} ({{ $usuario->area ?? 'Sin área' }})
                  </option>
                @endforeach
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label" for="prioridad">Prioridad</label>
              <select class="form-select" id="prioridad" name="prioridad" required>
                @foreach ($prioridades as $prioridad)
                  <option value="{{ $prioridad }}" @selected(old('prioridad', 'media') === $prioridad)>{{ ucfirst($prioridad) }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label" for="estatus">Estatus</label>
              <select class="form-select" id="estatus" name="estatus" required>
                @foreach ($estatuses as $estatus)
                  <option value="{{ $estatus }}" @selected(old('estatus', 'pendiente') === $estatus)>{{ ucfirst($estatus) }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-2">
              <label class="form-label" for="fecha_inicio">Inicio</label>
              <input class="form-control" id="fecha_inicio" name="fecha_inicio" type="date" value="{{ old('fecha_inicio', now()->toDateString()) }}" />
            </div>
            <div class="col-md-2">
              <label class="form-label" for="fecha_programada">Programado</label>
              <input class="form-control" id="fecha_programada" name="fecha_programada" type="date" value="{{ old('fecha_programada') }}" />
            </div>
            <div class="col-12">
              <label class="form-label" for="descripcion">Detalle</label>
              <textarea class="form-control" id="descripcion" name="descripcion" rows="3">{{ old('descripcion') }}</textarea>
            </div>
            <div class="col-12">
              <label class="form-label" for="adjuntos">Archivos adjuntos</label>
              <input class="form-control" id="adjuntos" name="adjuntos[]" type="file" multiple />
              <div class="form-text">Máximo 5MB por archivo. Formatos: PDF, Word, Excel, imágenes, TXT, ZIP, RAR.</div>
            </div>
            <div class="col-12">
              <button class="btn btn-primary" type="submit">Guardar pendiente</button>
            </div>
          </form>
        </div>
      </div>

      <div class="row g-4 mb-4">
        <div class="col-lg-6">
          <div class="card shadow-sm h-100">
            <div class="card-header">Calendario de tareas (inicio y finalización)</div>
            <div class="card-body">
              <form method="GET" action="{{ route('pendientes.index') }}" class="row g-2 align-items-end mb-3">
                <div class="col-sm-8">
                  <label class="form-label" for="fecha_calendario">Fecha</label>
                  <input class="form-control" type="date" id="fecha_calendario" name="fecha_calendario" value="{{ $fechaCalendario }}" />
                </div>
                <div class="col-sm-4 d-grid">
                  <button class="btn btn-outline-primary" type="submit">Consultar</button>
                </div>
              </form>
              <ul class="list-group list-group-flush small">
                @forelse ($eventosCalendario as $evento)
                  <li class="list-group-item px-0">
                    <strong>{{ $evento->titulo }}</strong><br />
                    Inicio: {{ $evento->fecha_inicio?->format('Y-m-d') ?? '—' }} · Fin: {{ $evento->fecha_finalizacion?->format('Y-m-d H:i') ?? '—' }}
                  </li>
                @empty
                  <li class="list-group-item px-0 text-muted">Sin tareas para la fecha seleccionada.</li>
                @endforelse
              </ul>
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="card shadow-sm h-100">
            <div class="card-header">Calendario de programados</div>
            <div class="card-body">
              <form method="GET" action="{{ route('pendientes.index') }}" class="row g-2 align-items-end mb-3">
                <div class="col-sm-8">
                  <label class="form-label" for="fecha_programados">Fecha programada</label>
                  <input class="form-control" type="date" id="fecha_programados" name="fecha_programados" value="{{ $fechaProgramados }}" />
                </div>
                <div class="col-sm-4 d-grid">
                  <button class="btn btn-outline-primary" type="submit">Consultar</button>
                </div>
              </form>
              <ul class="list-group list-group-flush small">
                @forelse ($programadosCalendario as $programado)
                  <li class="list-group-item px-0">
                    <strong>{{ $programado->titulo }}</strong><br />
                    Responsable: {{ $programado->responsable->Nombre ?? 'Sin asignar' }} · Programado: {{ $programado->fecha_programada?->format('Y-m-d') ?? '—' }}
                  </li>
                @empty
                  <li class="list-group-item px-0 text-muted">Sin pendientes programados para esta fecha.</li>
                @endforelse
              </ul>
            </div>
          </div>
        </div>
      </div>

      <div class="card shadow-sm">
        <div class="card-header d-flex flex-wrap gap-3 align-items-center justify-content-between">
          <span class="fw-semibold">Pendientes en curso</span>
          <div class="d-flex flex-wrap gap-2 small">
            <span class="badge rounded-pill text-bg-success">0 a 24 hrs</span>
            <span class="badge rounded-pill text-bg-warning">24 hrs a 7 días</span>
            <span class="badge rounded-pill semaforo-morado">7 a 15 días</span>
            <span class="badge rounded-pill text-bg-danger">15 días a 1 mes</span>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table table-striped table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>Pendiente</th>
                <th>Origen</th>
                <th>Destino</th>
                <th>Responsable</th>
                <th>Prioridad</th>
                <th>Estatus</th>
                <th>Semáforo</th>
                <th>Adjuntos</th>
                <th>Fechas</th>
                <th>Detalle</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($pendientes as $pendiente)
                @php
                  $horas = $pendiente->created_at?->diffInHours(now()) ?? 0;
                  $dias = $pendiente->created_at?->diffInDays(now()) ?? 0;
                  if ($horas <= 24) {
                      $semaforoClass = 'text-bg-success';
                      $semaforoTexto = $horas . ' hrs';
                  } elseif ($dias <= 7) {
                      $semaforoClass = 'text-bg-warning';
                      $semaforoTexto = $dias . ' días';
                  } elseif ($dias <= 15) {
                      $semaforoClass = 'semaforo-morado';
                      $semaforoTexto = $dias . ' días';
                  } else {
                      $semaforoClass = 'text-bg-danger';
                      $semaforoTexto = $dias . ' días';
                  }

                  $prioridadClass = match($pendiente->prioridad) {
                      'alta' => 'text-bg-danger',
                      'media' => 'text-bg-warning',
                      default => 'text-bg-success',
                  };
                @endphp
                <tr>
                  <td>{{ $pendiente->titulo }}</td>
                  <td>{{ $pendiente->area_origen }}</td>
                  <td>{{ $pendiente->area_destino }}</td>
                  <td>{{ $pendiente->responsable->Nombre ?? 'Sin asignar' }}</td>
                  <td><span class="badge {{ $prioridadClass }}">{{ ucfirst($pendiente->prioridad) }}</span></td>
                  <td>
                    <div class="d-grid gap-2">
                      <span class="badge text-bg-secondary">{{ ucfirst($pendiente->estatus) }}</span>
                      <form method="POST" action="{{ route('pendientes.update-status', $pendiente) }}" class="d-flex gap-2">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="usuario_id" value="{{ $pendiente->usuario_responsable_id }}" />
                        <select class="form-select form-select-sm" name="estatus">
                          @foreach ($estatuses as $estatus)
                            <option value="{{ $estatus }}" @selected($pendiente->estatus === $estatus)>{{ ucfirst($estatus) }}</option>
                          @endforeach
                        </select>
                        <button class="btn btn-sm btn-outline-primary" type="submit">Actualizar</button>
                      </form>
                    </div>
                  </td>
                  <td><span class="badge {{ $semaforoClass }}">{{ $semaforoTexto }}</span></td>
                  <td>
                    @if ($pendiente->adjuntos->isEmpty())
                      <span class="text-muted small">Sin adjuntos</span>
                    @else
                      <ul class="list-unstyled mb-0 small">
                        @foreach ($pendiente->adjuntos as $adjunto)
                          <li><a href="{{ route('pendientes.adjuntos.download', $adjunto) }}" class="link-primary">Descargar: {{ $adjunto->nombre_original }}</a></li>
                        @endforeach
                      </ul>
                    @endif
                  </td>
                  <td class="small">
                    Inicio: {{ $pendiente->fecha_inicio?->format('Y-m-d') ?? '—' }}<br />
                    Fin: {{ $pendiente->fecha_finalizacion?->format('Y-m-d H:i') ?? '—' }}<br />
                    Programado: {{ $pendiente->fecha_programada?->format('Y-m-d') ?? '—' }}
                  </td>
                  <td>
                    <a href="{{ route('pendientes.show', $pendiente) }}" class="btn btn-sm btn-outline-secondary">Ver detalle</a>
                  </td>
                </tr>
              @empty
                <tr><td colspan="10" class="text-center py-4 text-muted">No hay pendientes registrados.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </main>
  </body>
</html>
