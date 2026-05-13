<?php require_once RUTA_APP . '/views/inc/headerHome.php' ?>
<?php
$desde = $datos['desde'] ?? date('Y-m-01');
$hasta = $datos['hasta'] ?? date('Y-m-d');
$rr    = $datos['resumenRetrasos'];

$diasLabels  = array_column($datos['retrasosDiarios'], 'fecha');
$diasRet     = array_column($datos['retrasosDiarios'], 'retrasos');
$diasTotal   = array_column($datos['retrasosDiarios'], 'total_fichajes');

$empNombres  = array_map(fn($e) => $e['nombre'] . ' ' . $e['apellidos'], $datos['retrasosEmpleado']);
$empRetrasos = array_column($datos['retrasosEmpleado'], 'retrasos');
?>

<div class="main-wrapper">
<?php require_once RUTA_APP . '/views/inc/sidebar.php'; ?>
<div class="content stats-content">

  <div class="stats-topbar d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="mb-0 fw-bold"><i class="bi bi-alarm me-2 text-primary"></i>Estadísticas – Retrasos</h4>
      <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0 small">
        <li class="breadcrumb-item"><a href="<?= RUTA_URL ?>/home/index">Inicio</a></li>
        <li class="breadcrumb-item"><a href="<?= RUTA_URL ?>/Estadisticas/resumen">Estadísticas</a></li>
        <li class="breadcrumb-item active">Retrasos</li>
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
    <li class="nav-item"><a class="nav-link active" href="<?= RUTA_URL ?>/Estadisticas/retrasos"><i class="bi bi-alarm me-1"></i>Retrasos</a></li>
    <li class="nav-item"><a class="nav-link" href="<?= RUTA_URL ?>/Estadisticas/actividad"><i class="bi bi-activity me-1"></i>Actividad</a></li>
  </ul>

  <!-- KPI CARDS -->
  <div class="row g-3 mb-4">
    <div class="col-6 col-md-3"><div class="kpi-card kpi-orange"><div class="kpi-icon"><i class="bi bi-alarm-fill"></i></div><div class="kpi-value"><?= $rr['total_retrasos'] ?? 0 ?></div><div class="kpi-label">Total retrasos</div></div></div>
    <div class="col-6 col-md-3"><div class="kpi-card kpi-blue"><div class="kpi-icon"><i class="bi bi-clock-history"></i></div><div class="kpi-value"><?= $rr['total_fichajes'] ?? 0 ?></div><div class="kpi-label">Total fichajes</div></div></div>
    <div class="col-6 col-md-3"><div class="kpi-card kpi-red"><div class="kpi-icon"><i class="bi bi-percent"></i></div><div class="kpi-value"><?= $rr['pct_retraso'] ?? 0 ?>%</div><div class="kpi-label">Tasa de retraso</div></div></div>
    <div class="col-6 col-md-3"><div class="kpi-card kpi-purple"><div class="kpi-icon"><i class="bi bi-person-fill-exclamation"></i></div><div class="kpi-value" style="font-size:1.1rem;"><?= htmlspecialchars($rr['empleado_mas_retrasos'] ?? '—') ?></div><div class="kpi-label">Mayor número retrasos</div></div></div>
  </div>

  <!-- CHARTS ROW 1 -->
  <div class="row g-3 mb-4">
    <div class="col-lg-8">
      <div class="chart-card">
        <div class="chart-card-header"><h6 class="mb-0"><i class="bi bi-bar-chart me-2 text-danger"></i>Retrasos por día vs total fichajes</h6></div>
        <div class="chart-card-body"><canvas id="chartRetrasosDia" height="90"></canvas></div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="chart-card">
        <div class="chart-card-header"><h6 class="mb-0"><i class="bi bi-bar-chart-fill me-2 text-warning"></i>Retrasos por empleado</h6></div>
        <div class="chart-card-body"><canvas id="chartRetrasosEmp" height="170"></canvas></div>
      </div>
    </div>
  </div>

  <!-- TABLA HORA DE ENTRADA MEDIA -->
  <div class="chart-card mb-4">
    <div class="chart-card-header"><h6 class="mb-0"><i class="bi bi-clock-fill me-2 text-info"></i>Hora media de entrada por empleado</h6></div>
    <div class="chart-card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover stats-table mb-0">
          <thead><tr><th>Empleado</th><th>Hora media entrada</th><th>Retrasos registrados</th><th>Puntualidad</th></tr></thead>
          <tbody>
            <?php foreach ($datos['horaEntrada'] as $e): ?>
            <?php $esBueno = $e['retrasos'] <= 1; ?>
            <tr>
              <td class="fw-semibold"><?= htmlspecialchars($e['nombre'] . ' ' . $e['apellidos']) ?></td>
              <td><span class="badge bg-light text-dark fs-6"><i class="bi bi-clock me-1"></i><?= $e['hora_media_entrada'] ?></span></td>
              <td><?php if($e['retrasos'] > 0): ?><span class="badge bg-danger"><?= $e['retrasos'] ?> retraso<?= $e['retrasos'] > 1 ? 's' : '' ?></span><?php else: ?><span class="badge bg-success">Sin retrasos</span><?php endif; ?></td>
              <td><?php if($e['retrasos'] == 0): ?>
                <span class="text-success fw-semibold"><i class="bi bi-check-circle-fill me-1"></i>Excelente</span>
              <?php elseif($e['retrasos'] <= 2): ?>
                <span class="text-warning fw-semibold"><i class="bi bi-dash-circle-fill me-1"></i>Regular</span>
              <?php else: ?>
                <span class="text-danger fw-semibold"><i class="bi bi-x-circle-fill me-1"></i>Mejorable</span>
              <?php endif; ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if(empty($datos['horaEntrada'])): ?><tr><td colspan="4" class="text-center text-muted py-4">No hay datos disponibles</td></tr><?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- TABLA RETRASOS POR EMPLEADO -->
  <div class="chart-card mb-4">
    <div class="chart-card-header"><h6 class="mb-0"><i class="bi bi-people me-2 text-danger"></i>Ranking retrasos por empleado</h6></div>
    <div class="chart-card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover stats-table mb-0">
          <thead><tr><th>Empleado</th><th>Fichajes</th><th>Retrasos</th><th>% Retraso</th><th>Estado</th></tr></thead>
          <tbody>
            <?php foreach ($datos['retrasosEmpleado'] as $e):
              $pct = (float)($e['pct_retraso'] ?? 0);
              $retrasos = (int)($e['retrasos'] ?? 0);
            ?>
            <tr>
              <td class="fw-semibold"><?= htmlspecialchars($e['nombre'] . ' ' . $e['apellidos']) ?></td>
              <td><?= (int)($e['total_fichajes'] ?? 0) ?></td>
              <td><?php if($retrasos > 0): ?><span class="badge bg-danger"><?= $retrasos ?></span><?php else: ?><span class="text-muted">0</span><?php endif; ?></td>
              <td>
                <div class="d-flex align-items-center gap-2">
                  <div class="progress flex-grow-1" style="height:6px;"><div class="progress-bar <?= $pct > 20 ? 'bg-danger' : ($pct > 10 ? 'bg-warning' : 'bg-success') ?>" style="width:<?= min($pct, 100) ?>%"></div></div>
                  <small><?= $pct ?>%</small>
                </div>
              </td>
              <td><?php if($retrasos == 0): ?><span class="badge bg-success-subtle text-success">Puntual</span><?php elseif($retrasos <= 2): ?><span class="badge bg-warning-subtle text-warning-emphasis">Ocasional</span><?php else: ?><span class="badge bg-danger-subtle text-danger">Frecuente</span><?php endif; ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if(empty($datos['retrasosEmpleado'])): ?><tr><td colspan="5" class="text-center text-muted py-4">No hay datos disponibles</td></tr><?php endif; ?>
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
const DR=<?= json_encode(array_map('intval',$diasRet)) ?>;
const DT=<?= json_encode(array_map('intval',$diasTotal)) ?>;
const EN=<?= json_encode($empNombres) ?>;
const ER=<?= json_encode(array_map('intval',$empRetrasos)) ?>;

new Chart(document.getElementById('chartRetrasosDia'),{type:'bar',data:{labels:DL,datasets:[
  {label:'Total fichajes',data:DT,backgroundColor:'rgba(37,99,235,.4)',borderRadius:4},
  {label:'Retrasos',data:DR,backgroundColor:'rgba(239,68,68,.8)',borderRadius:4},
]},options:{responsive:true,interaction:{mode:'index',intersect:false},plugins:{legend:{position:'top'}},scales:{y:{beginAtZero:true}}}});

new Chart(document.getElementById('chartRetrasosEmp'),{type:'bar',data:{labels:EN,datasets:[{label:'Retrasos',data:ER,backgroundColor:ER.map(v=>v===0?'#10b981':v<=2?'#f59e0b':'#ef4444'),borderRadius:6}]},options:{indexAxis:'y',responsive:true,plugins:{legend:{display:false}},scales:{x:{beginAtZero:true}}}});
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
.kpi-orange{background:linear-gradient(135deg,#ffedd5,#fff7ed);color:#c2410c;}
.kpi-red{background:linear-gradient(135deg,#fee2e2,#fef2f2);color:#b91c1c;}
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
