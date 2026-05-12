<?php require_once RUTA_APP . '/views/inc/headerHome.php' ?>
<?php
$desde = $datos['desde'] ?? date('Y-m-01');
$hasta = $datos['hasta'] ?? date('Y-m-d');
$ra    = $datos['resumenActividad'];
$av    = $datos['ausenciasVacas'];

$diasLabels = array_column($datos['actividadDiaria'], 'fecha');
$diasEmp    = array_column($datos['actividadDiaria'], 'empleados_activos');
$diasFich   = array_column($datos['actividadDiaria'], 'total_fichajes');
$diasHoras  = array_column($datos['actividadDiaria'], 'horas_totales');

// Heatmap por hora (rellenar huecos 6-22)
$horasMap = [];
foreach ($datos['porHora'] as $h) { $horasMap[(int)$h['hora']] = (int)$h['entradas']; }
$horasLabels = [];
$horasData   = [];
for ($i = 6; $i <= 21; $i++) {
    $horasLabels[] = sprintf('%02d:00', $i);
    $horasData[]   = $horasMap[$i] ?? 0;
}
$maxHoraEntradas = max($horasData ?: [1]);
?>

<div class="main-wrapper">
<?php require_once RUTA_APP . '/views/inc/sidebar.php'; ?>
<div class="content stats-content">

  <div class="stats-topbar d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="mb-0 fw-bold"><i class="bi bi-activity me-2 text-primary"></i>Estadísticas – Actividad</h4>
      <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0 small">
        <li class="breadcrumb-item"><a href="<?= RUTA_URL ?>/home/index">Inicio</a></li>
        <li class="breadcrumb-item"><a href="<?= RUTA_URL ?>/Estadisticas/resumen">Estadísticas</a></li>
        <li class="breadcrumb-item active">Actividad</li>
      </ol></nav>
    </div>
    <form method="GET" class="d-flex gap-2 align-items-center flex-wrap">
      <input type="date" name="desde" value="<?= $desde ?>" class="form-control form-control-sm" style="width:150px;">
      <span class="text-muted small">–</span>
      <input type="date" name="hasta" value="<?= $hasta ?>" class="form-control form-control-sm" style="width:150px;">
      <button class="btn btn-primary btn-sm"><i class="bi bi-funnel me-1"></i>Filtrar</button>
    </form>
  </div>

  <!-- NAV TABS -->
  <ul class="nav nav-pills stats-nav mb-4 flex-wrap gap-1">
    <li class="nav-item"><a class="nav-link" href="<?= RUTA_URL ?>/Estadisticas/resumen"><i class="bi bi-grid-fill me-1"></i>Resumen</a></li>
    <li class="nav-item"><a class="nav-link" href="<?= RUTA_URL ?>/Estadisticas/fichajes"><i class="bi bi-clock-history me-1"></i>Fichajes</a></li>
    <li class="nav-item"><a class="nav-link" href="<?= RUTA_URL ?>/Estadisticas/horas"><i class="bi bi-hourglass-split me-1"></i>Horas</a></li>
    <li class="nav-item"><a class="nav-link" href="<?= RUTA_URL ?>/Estadisticas/retrasos"><i class="bi bi-alarm me-1"></i>Retrasos</a></li>
    <li class="nav-item"><a class="nav-link active" href="<?= RUTA_URL ?>/Estadisticas/actividad"><i class="bi bi-activity me-1"></i>Actividad</a></li>
  </ul>

  <!-- KPI CARDS -->
  <div class="row g-3 mb-4">
    <div class="col-6 col-md-3"><div class="kpi-card kpi-blue"><div class="kpi-icon"><i class="bi bi-person-check-fill"></i></div><div class="kpi-value"><?= $ra['empleados_registrados'] ?? 0 ?></div><div class="kpi-label">Empleados con actividad</div></div></div>
    <div class="col-6 col-md-3"><div class="kpi-card kpi-green"><div class="kpi-icon"><i class="bi bi-calendar2-check-fill"></i></div><div class="kpi-value"><?= $ra['dias_con_actividad'] ?? 0 ?></div><div class="kpi-label">Días con actividad</div></div></div>
    <div class="col-6 col-md-3"><div class="kpi-card kpi-teal"><div class="kpi-icon"><i class="bi bi-calendar-x-fill"></i></div><div class="kpi-value"><?= $av['total_ausencias'] ?? 0 ?></div><div class="kpi-label">Ausencias</div></div></div>
    <div class="col-6 col-md-3"><div class="kpi-card kpi-indigo"><div class="kpi-icon"><i class="bi bi-umbrella-fill"></i></div><div class="kpi-value"><?= $av['vacaciones_aprobadas'] ?? 0 ?></div><div class="kpi-label">Vacaciones aprobadas</div></div></div>
  </div> 

  <!-- CHART ACTIVIDAD DIARIA -->
  <div class="chart-card mb-4">
    <div class="chart-card-header"><h6 class="mb-0"><i class="bi bi-people-fill me-2 text-primary"></i>Empleados activos por día</h6></div>
    <div class="chart-card-body"><canvas id="chartActDiaria" height="75"></canvas></div>
  </div>

  <!-- CHARTS ROW 2 -->
  <div class="row g-3 mb-4">
    <div class="col-lg-7">
      <div class="chart-card h-100">
        <div class="chart-card-header"><h6 class="mb-0"><i class="bi bi-clock-fill me-2 text-warning"></i>Distribución de entradas por hora del día</h6></div>
        <div class="chart-card-body">
          <canvas id="chartHoraEntrada" height="110"></canvas>
        </div>
      </div>
    </div>
    <div class="col-lg-5">
      <div class="chart-card h-100">
        <div class="chart-card-header"><h6 class="mb-0"><i class="bi bi-pie-chart-fill me-2 text-info"></i>Ausencias y vacaciones</h6></div>
        <div class="chart-card-body d-flex flex-column justify-content-center gap-3 py-2">

          <div class="d-flex align-items-center gap-3">
            <div class="activity-icon-box bg-danger-subtle text-danger"><i class="bi bi-calendar-x-fill fs-4"></i></div>
            <div>
              <div class="fw-bold fs-4"><?= $av['total_ausencias'] ?? 0 ?></div>
              <div class="text-muted small">Ausencias registradas</div>
            </div>
          </div>

          <div class="d-flex align-items-center gap-3">
            <div class="activity-icon-box bg-success-subtle text-success"><i class="bi bi-umbrella-fill fs-4"></i></div>
            <div>
              <div class="fw-bold fs-4"><?= $av['vacaciones_aprobadas'] ?? 0 ?></div>
              <div class="text-muted small">Vacaciones aprobadas</div>
            </div>
          </div>

          <div class="d-flex align-items-center gap-3">
            <div class="activity-icon-box bg-warning-subtle text-warning"><i class="bi bi-hourglass-split fs-4"></i></div>
            <div>
              <div class="fw-bold fs-4"><?= $av['vacaciones_pendientes'] ?? 0 ?></div>
              <div class="text-muted small">Vacaciones pendientes</div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>

  <!-- TABLA PRESENCIA POR EMPLEADO -->
  <div class="chart-card mb-4">
    <div class="chart-card-header"><h6 class="mb-0"><i class="bi bi-table me-2 text-primary"></i>Presencia y actividad por empleado</h6></div>
    <div class="chart-card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover stats-table mb-0">
          <thead><tr><th>Empleado</th><th>Días presentes</th><th>Horas totales</th><th>Retrasos</th><th>Índice actividad</th></tr></thead>
          <tbody>
            <?php
            $maxDias = max(array_column($datos['presencia'], 'dias_presentes') ?: [1]);
            foreach ($datos['presencia'] as $e):
              $pct = $maxDias > 0 ? ($e['dias_presentes'] / $maxDias * 100) : 0;
            ?>
            <tr>
              <td class="fw-semibold"><?= htmlspecialchars($e['nombre'] . ' ' . $e['apellidos']) ?></td>
              <td><span class="badge bg-primary"><?= $e['dias_presentes'] ?> días</span></td>
              <td><?= round($e['horas_totales'], 1) ?>h</td>
              <td><?= $e['retrasos'] > 0 ? '<span class="badge bg-warning text-dark">'.$e['retrasos'].'</span>' : '<span class="text-success small"><i class="bi bi-check-circle-fill"></i> 0</span>' ?></td>
              <td style="width:160px;">
                <div class="d-flex align-items-center gap-2">
                  <div class="progress flex-grow-1" style="height:8px;">
                    <div class="progress-bar bg-primary" style="width:<?= round($pct) ?>%"></div>
                  </div>
                  <small class="text-muted"><?= round($pct) ?>%</small>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php if(empty($datos['presencia'])): ?><tr><td colspan="5" class="text-center text-muted py-4">No hay datos disponibles</td></tr><?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
Chart.defaults.font.family="'Segoe UI',sans-serif";Chart.defaults.color='#6b7280';
const DL=<?= json_encode($diasLabels) ?>;
const DE=<?= json_encode(array_map('intval',$diasEmp)) ?>;
const DF=<?= json_encode(array_map('intval',$diasFich)) ?>;
const DH=<?= json_encode(array_map('floatval',$diasHoras)) ?>;
const HL=<?= json_encode($horasLabels) ?>;
const HD=<?= json_encode($horasData) ?>;
const maxH=<?= $maxHoraEntradas ?>;

new Chart(document.getElementById('chartActDiaria'),{type:'line',data:{labels:DL,datasets:[
  {label:'Empleados activos',data:DE,borderColor:'#2563eb',backgroundColor:'rgba(37,99,235,.1)',tension:.4,fill:true,pointRadius:3},
  {label:'Fichajes',data:DF,borderColor:'#10b981',backgroundColor:'rgba(16,185,129,.08)',tension:.4,fill:true,pointRadius:3,yAxisID:'yF'},
]},options:{responsive:true,interaction:{mode:'index',intersect:false},plugins:{legend:{position:'top'}},scales:{y:{beginAtZero:true,title:{display:true,text:'Empleados'}},yF:{beginAtZero:true,position:'right',title:{display:true,text:'Fichajes'},grid:{drawOnChartArea:false}}}}});

new Chart(document.getElementById('chartHoraEntrada'),{type:'bar',data:{labels:HL,datasets:[{label:'Entradas',data:HD,backgroundColor:HD.map(v=>{ const i=v/maxH; return `rgba(37,99,235,${0.2+i*0.8})`; }),borderRadius:4}]},options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true,title:{display:true,text:'Nº entradas'}}}}});
</script>

<style>
.stats-nav .nav-link{color:#374151;border-radius:8px;font-weight:500;font-size:.88rem;}
.stats-nav .nav-link.active{background:#2563eb;color:#fff;}
.stats-nav .nav-link:hover:not(.active){background:#f3f4f6;}
.kpi-card{border-radius:14px;padding:18px 16px;display:flex;flex-direction:column;gap:4px;box-shadow:0 2px 10px rgba(0,0,0,.06);transition:transform .2s,box-shadow .2s;}
.kpi-card:hover{transform:translateY(-3px);box-shadow:0 8px 20px rgba(0,0,0,.12);}
.kpi-icon{font-size:1.6rem;margin-bottom:4px;opacity:.8;}
.kpi-value{font-size:1.8rem;font-weight:700;line-height:1;}
.kpi-label{font-size:.78rem;font-weight:500;opacity:.75;}
.kpi-blue{background:linear-gradient(135deg,#dbeafe,#eff6ff);color:#1d4ed8;}
.kpi-green{background:linear-gradient(135deg,#d1fae5,#ecfdf5);color:#065f46;}
.kpi-teal{background:linear-gradient(135deg,#ccfbf1,#f0fdfa);color:#0f766e;}
.kpi-indigo{background:linear-gradient(135deg,#e0e7ff,#eef2ff);color:#3730a3;}
.chart-card{background:#fff;border-radius:14px;box-shadow:0 2px 10px rgba(0,0,0,.06);overflow:hidden;}
.chart-card-header{padding:14px 20px;border-bottom:1px solid #f3f4f6;}
.chart-card-body{padding:16px 20px;}
.stats-table thead tr th{background:#f8fafc;font-size:.8rem;text-transform:uppercase;letter-spacing:.04em;color:#6b7280;border-bottom:2px solid #e5e7eb;}
.stats-table tbody tr td{font-size:.88rem;vertical-align:middle;}
.stats-topbar{padding:16px 0 0;}
.breadcrumb-item a{color:#2563eb;text-decoration:none;}
.activity-icon-box{width:52px;height:52px;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
</style>

<?php require_once RUTA_APP . '/views/inc/footer.php' ?>
