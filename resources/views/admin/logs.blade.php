@extends('layouts.app')

@section('title', 'Logs de API - Admin')

@section('content')
    <div class="card">
        <h2 class="card-header">📋 Logs de API del Catastro</h2>

        <div class="info-box info-box-blue">
            <strong>ℹ️ Registro de llamadas a la API</strong>
            <p>Monitoreo de todas las consultas realizadas a la API del Catastro.</p>
        </div>

        <div style="margin-bottom: 16px;">
            <p style="color: #6b7280;">
                Total: <strong>{{ $logs->total() }}</strong> llamadas registradas
            </p>
        </div>

        {{-- Tabla de logs --}}
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                <thead>
                    <tr style="background: #f3f4f6;">
                        <th style="padding: 10px; text-align: left; border-bottom: 2px solid #e5e7eb;">Fecha</th>
                        <th style="padding: 10px; text-align: left; border-bottom: 2px solid #e5e7eb;">Usuario</th>
                        <th style="padding: 10px; text-align: left; border-bottom: 2px solid #e5e7eb;">Endpoint</th>
                        <th style="padding: 10px; text-align: left; border-bottom: 2px solid #e5e7eb;">Parámetros</th>
                        <th style="padding: 10px; text-align: center; border-bottom: 2px solid #e5e7eb;">Código</th>
                        <th style="padding: 10px; text-align: center; border-bottom: 2px solid #e5e7eb;">Duración</th>
                        <th style="padding: 10px; text-align: left; border-bottom: 2px solid #e5e7eb;">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                    <tr style="border-bottom: 1px solid #e5e7eb;">
                        <td style="padding: 10px; white-space: nowrap;">
                            {{ $log->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td style="padding: 10px;">
                            @if($log->usuario)
                                <span style="font-weight: 600;">{{ $log->usuario->name }}</span>
                                <br>
                                <span style="font-size: 11px; color: #6b7280;">{{ $log->usuario->email }}</span>
                            @else
                                <span style="color: #9ca3af;">Anónimo</span>
                            @endif
                        </td>
                        <td style="padding: 10px;">
                            <code style="background: #f3f4f6; padding: 2px 6px; border-radius: 4px; font-size: 11px;">
                                {{ $log->endpoint }}
                            </code>
                        </td>
                        <td style="padding: 10px; max-width: 200px;">
                            @php
                                $params = is_array($log->params_json) 
                                    ? $log->params_json 
                                    : json_decode($log->params_json, true);
                            @endphp
                            @if($params)
                                @foreach($params as $key => $value)
                                    @if($value)
                                        <div style="font-size: 11px; color: #4b5563;">
                                            <strong>{{ $key }}:</strong> {{ $value }}
                                        </div>
                                    @endif
                                @endforeach
                            @endif
                        </td>
                        <td style="padding: 10px; text-align: center;">
                            @if($log->response_code == 200)
                                <span style="background: #d1fae5; color: #065f46; padding: 4px 8px; border-radius: 4px; font-weight: 600;">
                                    {{ $log->response_code }}
                                </span>
                            @else
                                <span style="background: #fee2e2; color: #991b1b; padding: 4px 8px; border-radius: 4px; font-weight: 600;">
                                    {{ $log->response_code }}
                                </span>
                            @endif
                        </td>
                        <td style="padding: 10px; text-align: center; color: #6b7280;">
                            {{ $log->duration_ms }} ms
                        </td>
                        <td style="padding: 10px;">
                            @if($log->error_code)
                                <span style="color: #dc2626; font-weight: 600;">❌ Error</span>
                                <div style="font-size: 11px; color: #6b7280; margin-top: 2px;">
                                    {{ $log->error_desc }}
                                </div>
                            @else
                                <span style="color: #059669; font-weight: 600;">✅ OK</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        @if($logs->hasPages())
            <div style="margin-top: 24px;">
                <nav style="display: flex; justify-content: center; align-items: center; gap: 8px;">
                    @if($logs->onFirstPage())
                        <span class="btn btn-secondary" style="opacity: 0.5; cursor: not-allowed;">
                            ← Anterior
                        </span>
                    @else
                        <a href="{{ $logs->previousPageUrl() }}" class="btn btn-secondary">
                            ← Anterior
                        </a>
                    @endif

                    <span style="color: #6b7280; font-size: 14px;">
                        Página {{ $logs->currentPage() }} de {{ $logs->lastPage() }}
                    </span>

                    @if($logs->hasMorePages())
                        <a href="{{ $logs->nextPageUrl() }}" class="btn btn-secondary">
                            Siguiente →
                        </a>
                    @else
                        <span class="btn btn-secondary" style="opacity: 0.5; cursor: not-allowed;">
                            Siguiente →
                        </span>
                    @endif
                </nav>
            </div>
        @endif

        <div class="btn-group" style="margin-top: 24px;">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                ← Volver al Panel Admin
            </a>
        </div>
    </div>
@endsection