<?php

namespace App\Http\Controllers;

use App\Models\Pendiente;
use App\Models\PendienteAdjunto;
use App\Models\PendienteSeguimiento;
use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PendienteController extends Controller
{
    private const PRIORIDADES = ['alta', 'media', 'baja'];
    private const ESTATUS = ['pendiente', 'seguimiento', 'pausa', 'programado', 'finalizado'];

    public function index(Request $request): View
    {
        $pendientes = Pendiente::with(['responsable', 'adjuntos'])
            ->latest()
            ->get();

        $fechaCalendario = $request->input('fecha_calendario', now()->toDateString());
        $fechaProgramados = $request->input('fecha_programados', now()->toDateString());

        $eventosCalendario = Pendiente::with('responsable')
            ->whereDate('fecha_inicio', $fechaCalendario)
            ->orWhereDate('fecha_finalizacion', $fechaCalendario)
            ->orderBy('fecha_inicio')
            ->get();

        $programadosCalendario = Pendiente::with('responsable')
            ->where('estatus', 'programado')
            ->whereDate('fecha_programada', $fechaProgramados)
            ->orderBy('fecha_programada')
            ->get();

        return view('pendientes.index', [
            'pendientes' => $pendientes,
            'usuarios' => Usuario::orderBy('Nombre')->get(),
            'prioridades' => self::PRIORIDADES,
            'estatuses' => self::ESTATUS,
            'fechaCalendario' => $fechaCalendario,
            'fechaProgramados' => $fechaProgramados,
            'eventosCalendario' => $eventosCalendario,
            'programadosCalendario' => $programadosCalendario,
        ]);
    }

    public function show(Pendiente $pendiente): View
    {
        $pendiente->load(['responsable', 'adjuntos', 'seguimientos.usuario']);

        return view('pendientes.show', [
            'pendiente' => $pendiente,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'titulo' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'area_origen' => ['required', 'string', 'max:255'],
            'area_destino' => ['required', 'string', 'max:255'],
            'usuario_responsable_id' => ['required', 'integer', 'exists:usuarios,idUsuario'],
            'prioridad' => ['required', 'in:' . implode(',', self::PRIORIDADES)],
            'estatus' => ['required', 'in:' . implode(',', self::ESTATUS)],
            'fecha_inicio' => ['nullable', 'date'],
            'fecha_programada' => ['nullable', 'date'],
            'adjuntos' => ['nullable', 'array'],
            'adjuntos.*' => ['file', 'max:5120', 'mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx,txt,zip,rar'],
        ]);

        if ($data['estatus'] === 'programado' && empty($data['fecha_programada'])) {
            return back()->withErrors([
                'fecha_programada' => 'La fecha programada es obligatoria cuando el estatus es Programado.',
            ])->withInput();
        }

        if (empty($data['fecha_inicio'])) {
            $data['fecha_inicio'] = now()->toDateString();
        }

        $pendiente = Pendiente::create($data);

        if ($request->hasFile('adjuntos')) {
            foreach ($request->file('adjuntos') as $archivo) {
                $nombreSanitizado = Str::slug(pathinfo($archivo->getClientOriginalName(), PATHINFO_FILENAME));
                $extension = $archivo->getClientOriginalExtension();
                $nombreFinal = $nombreSanitizado . '-' . now()->timestamp . '-' . Str::random(6) . '.' . $extension;
                $ruta = $archivo->storeAs('pendientes_adjuntos', $nombreFinal, 'public');

                $pendiente->adjuntos()->create([
                    'nombre_original' => $archivo->getClientOriginalName(),
                    'ruta_archivo' => $ruta,
                    'mime_type' => $archivo->getClientMimeType(),
                    'tamano_bytes' => $archivo->getSize(),
                ]);
            }
        }

        PendienteSeguimiento::create([
            'pendiente_id' => $pendiente->id,
            'usuario_id' => $data['usuario_responsable_id'],
            'estatus_anterior' => null,
            'estatus_nuevo' => $data['estatus'],
            'comentario' => 'Pendiente registrado en el sistema.',
        ]);

        return redirect()->route('pendientes.index')->with('success', 'Pendiente creado correctamente.');
    }

    public function updateStatus(Request $request, Pendiente $pendiente): RedirectResponse
    {
        $data = $request->validate([
            'estatus' => ['required', 'in:' . implode(',', self::ESTATUS)],
            'usuario_id' => ['required', 'integer', 'exists:usuarios,idUsuario'],
            'comentario' => ['nullable', 'string'],
            'fecha_programada' => ['nullable', 'date'],
        ]);

        $estatusAnterior = $pendiente->estatus;

        $pendiente->estatus = $data['estatus'];

        if ($data['estatus'] === 'programado') {
            $pendiente->fecha_programada = $data['fecha_programada'] ?? $pendiente->fecha_programada;
        }

        if ($data['estatus'] === 'seguimiento' && empty($pendiente->fecha_inicio)) {
            $pendiente->fecha_inicio = Carbon::today();
        }

        if ($data['estatus'] === 'finalizado') {
            $pendiente->fecha_finalizacion = now();
        }

        $pendiente->save();

        PendienteSeguimiento::create([
            'pendiente_id' => $pendiente->id,
            'usuario_id' => $data['usuario_id'],
            'estatus_anterior' => $estatusAnterior,
            'estatus_nuevo' => $data['estatus'],
            'comentario' => $data['comentario'] ?? null,
        ]);

        return redirect()->route('pendientes.index')->with('success', 'Estatus actualizado correctamente.');
    }

    public function downloadAdjunto(PendienteAdjunto $adjunto): StreamedResponse
    {
        if (! Storage::disk('public')->exists($adjunto->ruta_archivo)) {
            abort(404, 'El archivo adjunto no existe o fue eliminado.');
        }

        return Storage::disk('public')->download($adjunto->ruta_archivo, $adjunto->nombre_original);
    }

    public function calendarIcs(Request $request): Response
    {
        $pendientes = Pendiente::with('responsable')
            ->orderByDesc('created_at')
            ->get();

        $lineas = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//PAGINATAREAS//Pendientes//ES',
            'CALSCALE:GREGORIAN',
            'METHOD:PUBLISH',
        ];

        foreach ($pendientes as $pendiente) {
            $inicio = $pendiente->fecha_programada
                ?? $pendiente->fecha_inicio
                ?? $pendiente->created_at?->copy()->startOfDay()
                ?? now()->startOfDay();

            $fin = $pendiente->fecha_finalizacion
                ?? $inicio->copy()->addHour();

            $lineas[] = 'BEGIN:VEVENT';
            $lineas[] = 'UID:pendiente-' . $pendiente->id . '@paginatareas.local';
            $lineas[] = 'DTSTAMP:' . now()->utc()->format('Ymd\THis\Z');
            $lineas[] = 'DTSTART:' . Carbon::parse($inicio)->format('Ymd\THis');
            $lineas[] = 'DTEND:' . Carbon::parse($fin)->format('Ymd\THis');
            $lineas[] = 'SUMMARY:' . $this->escapeIcsText($pendiente->titulo);
            $lineas[] = 'DESCRIPTION:' . $this->escapeIcsText(($pendiente->descripcion ?? 'Sin descripción') . ' | Estatus: ' . $pendiente->estatus . ' | Prioridad: ' . $pendiente->prioridad);
            $lineas[] = 'LOCATION:' . $this->escapeIcsText(($pendiente->area_origen ?? 'N/A') . ' -> ' . ($pendiente->area_destino ?? 'N/A'));
            $lineas[] = 'STATUS:' . ($pendiente->estatus === 'finalizado' ? 'COMPLETED' : 'CONFIRMED');
            $lineas[] = 'END:VEVENT';
        }

        $lineas[] = 'END:VCALENDAR';

        $contenido = implode("\r\n", $lineas) . "\r\n";

        return response($contenido, 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'inline; filename="pendientes.ics"',
        ]);
    }

    private function escapeIcsText(string $texto): string
    {
        $texto = str_replace('\\', '\\\\', $texto);
        $texto = str_replace(';', '\\;', $texto);
        $texto = str_replace(',', '\\,', $texto);

        return str_replace(["\r\n", "\n", "\r"], '\\n', $texto);
    }
}
