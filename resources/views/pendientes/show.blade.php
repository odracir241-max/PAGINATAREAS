<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Detalle de pendiente</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="/css/pendientes.css" />
  </head>
  <body class="bg-body-tertiary">
    <main class="container py-4">
      <a href="{{ route('pendientes.index') }}" class="btn btn-sm btn-outline-secondary mb-3">← Regresar</a>

      <div class="card shadow-sm mb-4">
        <div class="card-header bg-dark text-white">Detalle del pendiente</div>
        <div class="card-body">
          <h4 class="mb-3">{{ $pendiente->titulo }}</h4>
          <div class="row g-3 small">
            <div class="col-md-4"><strong>Área origen:</strong> {{ $pendiente->area_origen }}</div>
            <div class="col-md-4"><strong>Área destino:</strong> {{ $pendiente->area_destino }}</div>
            <div class="col-md-4"><strong>Responsable:</strong> {{ $pendiente->responsable->Nombre ?? 'Sin asignar' }}</div>
            <div class="col-md-4"><strong>Prioridad:</strong> {{ ucfirst($pendiente->prioridad) }}</div>
            <div class="col-md-4"><strong>Estatus:</strong> {{ ucfirst($pendiente->estatus) }}</div>
            <div class="col-md-4"><strong>Inicio:</strong> {{ $pendiente->fecha_inicio?->format('Y-m-d') ?? '—' }}</div>
            <div class="col-md-4"><strong>Programado:</strong> {{ $pendiente->fecha_programada?->format('Y-m-d') ?? '—' }}</div>
            <div class="col-md-4"><strong>Finalización:</strong> {{ $pendiente->fecha_finalizacion?->format('Y-m-d H:i') ?? '—' }}</div>
          </div>
          <hr />
          <p class="mb-0">{{ $pendiente->descripcion ?: 'Sin descripción.' }}</p>
        </div>
      </div>

      <div class="card shadow-sm mb-4">
        <div class="card-header">Adjuntos</div>
        <div class="card-body">
          @if ($pendiente->adjuntos->isEmpty())
            <span class="text-muted">Sin adjuntos</span>
          @else
            <ul class="mb-0">
              @foreach ($pendiente->adjuntos as $adjunto)
                <li>
                  <a href="{{ route('pendientes.adjuntos.download', $adjunto) }}">{{ $adjunto->nombre_original }}</a>
                </li>
              @endforeach
            </ul>
          @endif
        </div>
      </div>

      <div class="card shadow-sm">
        <div class="card-header">Historial de seguimiento</div>
        <div class="card-body p-0">
          <table class="table mb-0">
            <thead>
              <tr>
                <th>Fecha</th>
                <th>Usuario</th>
                <th>Estatus anterior</th>
                <th>Estatus nuevo</th>
                <th>Comentario</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($pendiente->seguimientos as $seg)
                <tr>
                  <td>{{ $seg->created_at?->format('Y-m-d H:i') }}</td>
                  <td>{{ $seg->usuario->Nombre ?? 'N/A' }}</td>
                  <td>{{ $seg->estatus_anterior ?: '—' }}</td>
                  <td>{{ $seg->estatus_nuevo }}</td>
                  <td>{{ $seg->comentario ?: '—' }}</td>
                </tr>
              @empty
                <tr><td colspan="5" class="text-center text-muted">Sin seguimientos</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </main>
  </body>
</html>
