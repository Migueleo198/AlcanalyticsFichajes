<?php require_once RUTA_APP . '/views/inc/headerHome.php' ?>
<?php
$desde = $datos['desde'] ?? date('Y-m-01');
$hasta = $datos['hasta'] ?? date('Y-m-d');
$rf    = $datos['resumenFichajes'];

// Chart data
$diasLabels  = array_column($datos['diarios'], 'fecha');
$diasTotal   = array_column($datos['diarios'], 'total_fichajes');
$diasRet     = array_column($datos['diarios'], 'retrasos');
$diasComp    = array_column($datos['diarios'], 'completados');

$estadoData  = [];
$estadoLabels = [];
foreach ($datos['porEstado'] as $e) { $estadoLabels[] = ucfirst($e['estado']); $estadoData[] = (int)$e['total']; }
?>

<div class="main-wrapper">
<?php require_once RUTA_APP . '/views/inc/sidebar.php'; ?>
<div class="content stats-content">

  <div class="stats-topbar d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2 text-primary"></i>Estadísticas – Fichajes</h4>
      <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0 small">
        <li class="breadcrumb-item"><a href="<?= RUTA_URL ?>/home/index">Inicio</a></li>
        <li class="breadcrumb-item"><a href="<?= RUTA_URL ?>/Estadisticas/resumen">Estadísticas</a></li>
        <li class="breadcrumb-item active">Fichajes</li>
      </ol></nav>
    </div>
    <!-- FILTRO FECHAS -->
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
    <li class="nav-item"><a class="nav-link active" href="<?= RUTA_URL ?>/Estadisticas/fichajes"><i class="bi bi-clock-history me-1"></i>Fichajes</a></li>
    <li class="nav-item"><a class="nav-link" href="<?= RUTA_URL ?>/Estadisticas/horas"><i class="bi bi-hourglass-split me-1"></i>Horas</a></li>
    <li class="nav-item"><a class="nav-link" href="<?= RUTA_URL ?>/Estadisticas/retrasos"><i class="bi bi-alarm me-1"></i>Retrasos</a></li>
    <li class="nav-item"><a class="nav-link" href="<?= RUTA_URL ?>/Estadisticas/actividad"><i class="bi bi-activity me-1"></i>Actividad</a></li>
  </ul>

  <!-- KPI CARDS -->
  <div class="row g-3 mb-4">
    <div class="col-6 col-md-3"><div class="kpi-card kpi-blue"><div class="kpi-icon"><i class="bi bi-clock-history"></i></div><div class="kpi-value"><?= $rf['total'] ?? 0 ?></div><div class="kpi-label">Total fichajes</div></div></div>
    <div class="col-6 col-md-3"><div class="kpi-card kpi-green"><div class="kpi-icon"><i class="bi bi-check-circle-fill"></i></div><div class="kpi-value"><?= $rf['normales'] ?? 0 ?></div><div class="kpi-label">Fichajes normales</div></div></div>
    <div class="col-6 col-md-3"><div class="kpi-card kpi-orange"><div class="kpi-icon"><i class="bi bi-exclamation-triangle-fill"></i></div><div class="kpi-value"><?= $rf['retrasos'] ?? 0 ?></div><div class="kpi-label">Con retraso</div></div></div>
    <div class="col-6 col-md-3"><div class="kpi-card kpi-teal"><div class="kpi-icon"><i class="bi bi-stopwatch-fill"></i></div><div class="kpi-value"><?= round($rf['duracion_media_horas'] ?? 0, 1) ?>h</div><div class="kpi-label">Duración media</div></div></div>
  </div>

  <!-- CHARTS ROW 1 -->
  <div class="row g-3 mb-4">
    <div class="col-lg-8">
      <div class="chart-card">
        <div class="chart-card-header"><h6 class="mb-0"><i class="bi bi-bar-chart me-2 text-primary"></i>Fichajes diarios</h6></div>
        <div class="chart-card-body"><canvas id="chartDiarios" height="90"></canvas></div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="chart-card">
        <div class="chart-card-header"><h6 class="mb-0"><i class="bi bi-pie-chart me-2 text-success"></i>Distribución por estado</h6></div>
        <div class="chart-card-body d-flex justify-content-center"><canvas id="chartEstado" height="180" style="max-width:220px;"></canvas></div>
      </div>
    </div>
  </div>

  <!-- TABLA POR EMPLEADO -->
  <div class="chart-card mb-4">
    <div class="chart-card-header d-flex justify-content-between align-items-center">
      <h6 class="mb-0"><i class="bi bi-people me-2 text-primary"></i>Fichajes por empleado</h6>
      <span class="badge bg-light text-dark"><?= $desde ?> → <?= $hasta ?></span>
    </div>
    <div class="chart-card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover stats-table mb-0" id="tablaFichajes">
          <thead><tr><th>Empleado</th><th>Total fichajes</th><th>Retrasos</th><th>Horas totales</th><th>% Retraso</th></tr></thead>
          <tbody>
            <?php foreach ($datos['porEmpleado'] as $e): ?>
            <?php $pct = $e['total_fichajes'] > 0 ? round($e['retrasos'] / $e['total_fichajes'] * 100, 1) : 0; ?>
            <tr>
              <td class="fw-semibold"><?= htmlspecialchars($e['nombre'] . ' ' . $e['apellidos']) ?></td>
              <td><span class="badge bg-primary"><?= $e['total_fichajes'] ?></span></td>
              <td><?php if($e['retrasos']>0): ?><span class="badge bg-warning text-dark"><?= $e['retrasos'] ?></span><?php else: ?><span class="text-muted">0</span><?php endif; ?></td>
              <td><?= round($e['total_horas'], 1) ?>h</td>
              <td>
                <div class="d-flex align-items-center gap-2">
                  <div class="progress flex-grow-1" style="height:6px;">
                    <div class="progress-bar <?= $pct > 20 ? 'bg-danger' : ($pct > 10 ? 'bg-warning' : 'bg-success') ?>" style="width:<?= min($pct,100) ?>%"></div>
                  </div>
                  <small><?= $pct ?>%</small>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php if(empty($datos['porEmpleado'])): ?><tr><td colspan="5" class="text-center text-muted py-4">No hay datos para el período seleccionado</td></tr><?php endif; ?>
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
const DT=<?= json_encode(array_map('intval',$diasTotal)) ?>;
const DR=<?= json_encode(array_map('intval',$diasRet)) ?>;
const DC=<?= json_encode(array_map('intval',$diasComp)) ?>;
const EL=<?= json_encode($estadoLabels) ?>;
const ED=<?= json_encode($estadoData) ?>;

new Chart(document.getElementById('chartDiarios'),{type:'bar',data:{labels:DL,datasets:[
  {label:'Total',data:DT,backgroundColor:'rgba(37,99,235,.7)',borderRadius:4,stack:'s'},
  {label:'Retrasos',data:DR,backgroundColor:'rgba(245,158,11,.8)',borderRadius:4,stack:'s2'},
]},options:{responsive:true,interaction:{mode:'index',intersect:false},plugins:{legend:{position:'top'}},scales:{y:{beginAtZero:true}}}});

new Chart(document.getElementById('chartEstado'),{type:'doughnut',data:{labels:EL,datasets:[{data:ED,backgroundColor:['#2563eb','#10b981','#f59e0b','#ef4444','#8b5cf6'],hoverOffset:6,borderWidth:2,borderColor:'#fff'}]},options:{responsive:true,cutout:'65%',plugins:{legend:{position:'bottom',labels:{font:{size:12}}}}}});
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
.kpi-teal{background:linear-gradient(135deg,#ccfbf1,#f0fdfa);color:#0f766e;}
.chart-card{background:#fff;border-radius:14px;box-shadow:0 2px 10px rgba(0,0,0,.06);overflow:hidden;}
.chart-card-header{padding:14px 20px;border-bottom:1px solid #f3f4f6;}
.chart-card-body{padding:16px 20px;}
.stats-table thead tr th{background:#f8fafc;font-size:.8rem;text-transform:uppercase;letter-spacing:.04em;color:#6b7280;border-bottom:2px solid #e5e7eb;}
.stats-table tbody tr td{font-size:.88rem;vertical-align:middle;}
.stats-topbar{padding:16px 0 0;}
.breadcrumb-item a{color:#2563eb;text-decoration:none;}
</style>

<?php require_once RUTA_APP . '/views/inc/footer.php' ?>
