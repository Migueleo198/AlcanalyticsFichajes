<?php require_once RUTA_APP . '/views/inc/headerHome.php' ?>
<?php
$desde = $datos['desde'] ?? date('Y-m-01');
$hasta = $datos['hasta'] ?? date('Y-m-d');
$rh    = $datos['resumenHoras'];

$diasLabels = array_column($datos['horasDiarias'], 'fecha');
$diasOrd    = array_column($datos['horasDiarias'], 'ordinarias');
$diasExtra  = array_column($datos['horasDiarias'], 'extra');
$diasTotal  = array_column($datos['horasDiarias'], 'total');

$empNombres = array_map(fn($e) => $e['nombre'] . ' ' . $e['apellidos'], $datos['horasEmpleado']);
$empTotal   = array_column($datos['horasEmpleado'], 'total');
$empOrd     = array_column($datos['horasEmpleado'], 'ordinarias');
$empExtra   = array_column($datos['horasEmpleado'], 'extra');
?>

<div class="main-wrapper">
<?php require_once RUTA_APP . '/views/inc/sidebar.php'; ?>
<div class="content stats-content">

  <div class="stats-topbar d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="mb-0 fw-bold"><i class="bi bi-hourglass-split me-2 text-primary"></i>Estadísticas – Horas</h4>
      <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0 small">
        <li class="breadcrumb-item"><a href="<?= RUTA_URL ?>/home/index">Inicio</a></li>
        <li class="breadcrumb-item"><a href="<?= RUTA_URL ?>/Estadisticas/resumen">Estadísticas</a></li>
        <li class="breadcrumb-item active">Horas</li>
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
    <li class="nav-item"><a class="nav-link active" href="<?= RUTA_URL ?>/Estadisticas/horas"><i class="bi bi-hourglass-split me-1"></i>Horas</a></li>
    <li class="nav-item"><a class="nav-link" href="<?= RUTA_URL ?>/Estadisticas/retrasos"><i class="bi bi-alarm me-1"></i>Retrasos</a></li>
    <li class="nav-item"><a class="nav-link" href="<?= RUTA_URL ?>/Estadisticas/actividad"><i class="bi bi-activity me-1"></i>Actividad</a></li>
  </ul>

  <!-- KPI CARDS -->
  <div class="row g-3 mb-4">
    <div class="col-6 col-md-3"><div class="kpi-card kpi-blue"><div class="kpi-icon"><i class="bi bi-hourglass-bottom"></i></div><div class="kpi-value"><?= round($rh['total_horas'] ?? 0, 1) ?>h</div><div class="kpi-label">Total horas</div></div></div>
    <div class="col-6 col-md-3"><div class="kpi-card kpi-green"><div class="kpi-icon"><i class="bi bi-briefcase-fill"></i></div><div class="kpi-value"><?= round($rh['total_ordinarias'] ?? 0, 1) ?>h</div><div class="kpi-label">Horas ordinarias</div></div></div>
    <div class="col-6 col-md-3"><div class="kpi-card kpi-orange"><div class="kpi-icon"><i class="bi bi-plus-lg"></i></div><div class="kpi-value"><?= round($rh['total_extra'] ?? 0, 1) ?>h</div><div class="kpi-label">Horas extra</div></div></div>
    <div class="col-6 col-md-3"><div class="kpi-card kpi-purple"><div class="kpi-icon"><i class="bi bi-calendar2-day"></i></div><div class="kpi-value"><?= round($rh['media_diaria'] ?? 0, 1) ?>h</div><div class="kpi-label">Media diaria</div></div></div>
  </div>

  <!-- CHART HORAS DIARIAS -->
  <div class="chart-card mb-4">
    <div class="chart-card-header"><h6 class="mb-0"><i class="bi bi-graph-up me-2 text-primary"></i>Horas trabajadas por día</h6></div>
    <div class="chart-card-body"><canvas id="chartHorasDiarias" height="80"></canvas></div>
  </div>

  <!-- CHARTS ROW 2 -->
  <div class="row g-3 mb-4">
    <div class="col-lg-7">
      <div class="chart-card h-100">
        <div class="chart-card-header"><h6 class="mb-0"><i class="bi bi-bar-chart-steps me-2 text-info"></i>Horas por empleado</h6></div>
        <div class="chart-card-body"><canvas id="chartHorasEmp" height="160"></canvas></div>
      </div>
    </div>
    <div class="col-lg-5">
      <div class="chart-card h-100">
        <div class="chart-card-header"><h6 class="mb-0"><i class="bi bi-pie-chart me-2 text-warning"></i>Ordinarias vs Extra</h6></div>
        <div class="chart-card-body d-flex justify-content-center align-items-center"><canvas id="chartDonutHoras" height="200" style="max-width:200px;"></canvas></div>
      </div>
    </div>
  </div>

  <!-- TABLA HORAS POR EMPLEADO -->
  <div class="chart-card mb-4">
    <div class="chart-card-header"><h6 class="mb-0"><i class="bi bi-table me-2 text-primary"></i>Detalle horas por empleado</h6></div>
    <div class="chart-card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover stats-table mb-0">
          <thead><tr><th>Empleado</th><th>Días trabajados</th><th>H. Ordinarias</th><th>H. Extra</th><th>Total</th><th>% Extra</th></tr></thead>
          <tbody>
            <?php foreach ($datos['horasEmpleado'] as $e): ?>
            <?php $pctExtra = $e['total'] > 0 ? round($e['extra'] / $e['total'] * 100, 1) : 0; ?>
            <tr>
              <td class="fw-semibold"><?= htmlspecialchars($e['nombre'] . ' ' . $e['apellidos']) ?></td>
              <td><?= $e['dias_trabajados'] ?></td>
              <td><?= round($e['ordinarias'], 1) ?>h</td>
              <td><?= $e['extra'] > 0 ? '<span class="badge bg-warning text-dark">'.round($e['extra'],1).'h</span>' : '<span class="text-muted">0h</span>' ?></td>
              <td class="fw-bold"><?= round($e['total'], 1) ?>h</td>
              <td>
                <div class="d-flex align-items-center gap-2">
                  <div class="progress flex-grow-1" style="height:6px;"><div class="progress-bar bg-warning" style="width:<?= min($pctExtra,100) ?>%"></div></div>
                  <small><?= $pctExtra ?>%</small>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php if(empty($datos['horasEmpleado'])): ?><tr><td colspan="6" class="text-center text-muted py-4">No hay datos disponibles</td></tr><?php endif; ?>
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
const DO=<?= json_encode(array_map('floatval',$diasOrd)) ?>;
const DE=<?= json_encode(array_map('floatval',$diasExtra)) ?>;
const DT=<?= json_encode(array_map('floatval',$diasTotal)) ?>;
const EN=<?= json_encode($empNombres) ?>;
const ET=<?= json_encode(array_map('floatval',$empTotal)) ?>;
const EO=<?= json_encode(array_map('floatval',$empOrd)) ?>;
const EX=<?= json_encode(array_map('floatval',$empExtra)) ?>;
const totOrd=<?= round($rh['total_ordinarias'] ?? 0, 1) ?>;
const totExt=<?= round($rh['total_extra'] ?? 0, 1) ?>;

new Chart(document.getElementById('chartHorasDiarias'),{type:'bar',data:{labels:DL,datasets:[
  {label:'Ordinarias',data:DO,backgroundColor:'rgba(37,99,235,.75)',borderRadius:3,stack:'h'},
  {label:'Extra',data:DE,backgroundColor:'rgba(245,158,11,.85)',borderRadius:3,stack:'h'},
  {label:'Total (línea)',data:DT,type:'line',borderColor:'#10b981',backgroundColor:'transparent',tension:.4,pointRadius:3,yAxisID:'yT'},
]},options:{responsive:true,interaction:{mode:'index',intersect:false},plugins:{legend:{position:'top'}},scales:{y:{stacked:true,beginAtZero:true,title:{display:true,text:'Horas'}},yT:{beginAtZero:true,position:'right',grid:{drawOnChartArea:false}}}}});

new Chart(document.getElementById('chartHorasEmp'),{type:'bar',data:{labels:EN,datasets:[
  {label:'Ordinarias',data:EO,backgroundColor:'#3b82f6',borderRadius:4,stack:'s'},
  {label:'Extra',data:EX,backgroundColor:'#f59e0b',borderRadius:4,stack:'s'},
]},options:{indexAxis:'y',responsive:true,plugins:{legend:{position:'top'}},scales:{x:{stacked:true,beginAtZero:true}}}});

new Chart(document.getElementById('chartDonutHoras'),{type:'doughnut',data:{labels:['Ordinarias','Extra'],datasets:[{data:[totOrd,totExt],backgroundColor:['#3b82f6','#f59e0b'],borderWidth:2,borderColor:'#fff',hoverOffset:6}]},options:{cutout:'65%',responsive:true,plugins:{legend:{position:'bottom'}}}});
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
.kpi-orange{background:linear-gradient(135deg,#ffedd5,#fff7ed);color:#c2410c;}
.kpi-purple{background:linear-gradient(135deg,#ede9fe,#f5f3ff);color:#5b21b6;}
.chart-card{background:#fff;border-radius:14px;box-shadow:0 2px 10px rgba(0,0,0,.06);overflow:hidden;}
.chart-card-header{padding:14px 20px;border-bottom:1px solid #f3f4f6;}
.chart-card-body{padding:16px 20px;}
.stats-table thead tr th{background:#f8fafc;font-size:.8rem;text-transform:uppercase;letter-spacing:.04em;color:#6b7280;border-bottom:2px solid #e5e7eb;}
.stats-table tbody tr td{font-size:.88rem;vertical-align:middle;}
.stats-topbar{padding:16px 0 0;}
.breadcrumb-item a{color:#2563eb;text-decoration:none;}
</style>

<?php require_once RUTA_APP . '/views/inc/footer.php' ?>
