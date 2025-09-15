@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Reportes de Talleres</h2>

    <div class="alert alert-warning" id="accessAlert" style="display:none;">
        No tienes permiso para acceder a esta página.
    </div>

    <div id="reportesContent" style="display:none;">
        <!-- Filtros -->
        <div class="row mb-3">
            <div class="col-md-3">
                <label>Tipo de Reporte:</label>
                <select id="tipoReporte" class="form-control">
                    <option value="por_mes">Talleres por Mes</option>
                    <option value="por_colegio">Talleres por Colegio</option>
                    <option value="por_tallerista">Talleres por Tallerista</option>
                    <option value="por_tema">Talleres por Temática</option>
                </select>
            </div>
            <div class="col-md-3">
                <label>Seleccionar Colegio:</label>
                <select id="colegioFiltro" class="form-control">
                    <option value="">Todos</option>
                    @foreach($colegios as $c)
                        <option value="{{ $c->id }}">{{ $c->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label>Desde:</label>
                <input type="date" id="fechaDesde" class="form-control">
            </div>
            <div class="col-md-2">
                <label>Hasta:</label>
                <input type="date" id="fechaHasta" class="form-control">
            </div>
            <div class="col-md-2">
                <label>&nbsp;</label>
                <button id="filtrarBtn" class="btn btn-primary w-100">Filtrar</button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered" id="tablaReportes">
                <thead>
                    <tr id="tablaHead"></tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        <button id="exportExcel" class="btn btn-success mt-3">Exportar a Excel</button>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function(){
    const userRole = document.querySelector('meta[name="user-role"]').content;
    if(!['admin','supervisor'].includes(userRole)){
        document.getElementById('accessAlert').style.display = 'block';
        return;
    }
    document.getElementById('reportesContent').style.display = 'block';

    const tablaBody = document.querySelector('#tablaReportes tbody');
    const tablaHead = document.querySelector('#tablaHead');

    async function cargarReportes(){
        const tipo = document.getElementById('tipoReporte').value;
        const colegioId = document.getElementById('colegioFiltro').value;
        const desde = document.getElementById('fechaDesde').value;
        const hasta = document.getElementById('fechaHasta').value;

        const res = await fetch(`/reportes/filtrar`, {
            method:'POST',
            headers:{
                'Content-Type':'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({tipo,colegio_id:colegioId,desde,hasta})
        });
        const data = await res.json();

        tablaBody.innerHTML = '';
        tablaHead.innerHTML = '';

        if(data.length === 0) return;

        // Headers
        Object.keys(data[0]).forEach(key=>{
            const th = document.createElement('th');
            th.innerText = key;
            tablaHead.appendChild(th);
        });

        // Rows
        data.forEach(row=>{
            const tr = document.createElement('tr');
            Object.values(row).forEach(val=>{
                const td = document.createElement('td');
                td.innerText = val;
                tr.appendChild(td);
            });
            tablaBody.appendChild(tr);
        });
    }

    document.getElementById('filtrarBtn').addEventListener('click', cargarReportes);

    document.getElementById('exportExcel').addEventListener('click', function(){
        const wb = XLSX.utils.table_to_book(document.getElementById('tablaReportes'));
        XLSX.writeFile(wb,'reportes.xlsx');
    });

    // Cargar al inicio
    cargarReportes();
});
</script>
@endpush
