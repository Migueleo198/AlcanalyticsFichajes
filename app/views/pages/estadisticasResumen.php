<?php require_once RUTA_APP . '/views/inc/headerHome.php' ?>

<?php
$mesesLabels   = array_column($datos['fichajesMeses'], 'mes_label');
$mesesFichajes = array_column($datos['fichajesMeses'], 'total_fichajes');
$mesesHoras    = array_column($datos['fichajesMeses'], 'total_horas');
$mesesRetrasos = array_column($datos['fichajesMeses'], 'total_retrasos');
$diasLabels    = array_column($datos['diaSemana'], 'dia');
$diasTotales   = array_column($datos['diaSemana'], 'total');
$topNombres    = array_map(fn($e) => $e['nombre'] . ' ' . $e['apellidos'], $datos['topEmpleados']);
$topHoras      = array_column($datos['topEmpleados'], 'total_horas');
$r = $datos['resumen'];
?>

<div class="main-wrapper">
<?php require_once RUTA_APP . '/views/inc/sidebar.php'; ?>

<div class="content stats-content">

  <div class="stats-topbar d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="mb-0 fw-bold"><i class="bi bi-bar-chart-fill me-2 text-primary"></i>Estadísticas</h4>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0 small">
          <li class="breadcrumb-item"><a href="<?= RUTA_URL ?>/home/index">Inicio</a></li>
          <li class="breadcrumb-item active">Resumen</li>
        </ol>
      </nav>
    </div>
    <span class="badge bg-primary px-3 py-2"><i class="bi bi-calendar3 me-1"></i><?= date('F Y') ?></span>
  </div>

  <!-- NAV TABS -->
  <ul class="nav nav-pills stats-nav mb-4 flex-wrap gap-1">
    <li class="nav-item"><a class="nav-link active" href="<?= RUTA_URL ?>/Estadisticas/resumen"><i class="bi bi-grid-fill me-1"></i>Resumen</a></li>
    <li class="nav-item"><a class="nav-link" href="<?= RUTA_URL ?>/Estadisticas/fichajes"><i class="bi bi-clock-history me-1"></i>Fichajes</a></li>
    <li class="nav-item"><a class="nav-link" href="<?= RUTA_URL ?>/Estadisticas/horas"><i class="bi bi-hourglass-split me-1"></i>Horas</a></li>
    <li class="nav-item"><a class="nav-link" href="<?= RUTA_URL ?>/Estadisticas/retrasos"><i class="bi bi-alarm me-1"></i>Retrasos</a></li>
    <li class="nav-item"><a class="nav-link" href="<?= RUTA_URL ?>/Estadisticas/actividad"><i class="bi bi-activity me-1"></i>Actividad</a></li>
  </ul>

  <!-- KPI CARDS -->
  <div class="row g-3 mb-4">
    <div class="col-6 col-md-3"><div class="kpi-card kpi-blue"><div class="kpi-icon"><i class="bi bi-people-fill"></i></div><div class="kpi-value"><?= $r['empleados_activos'] ?? 0 ?></div><div class="kpi-label">Empleados activos</div></div></div>
    <div class="col-6 col-md-3"><div class="kpi-card kpi-green"><div class="kpi-icon"><i class="bi bi-clock-history"></i></div><div class="kpi-value"><?= $r['fichajes_hoy'] ?? 0 ?></div><div class="kpi-label">Fichajes hoy</div></div></div>
    <div class="col-6 col-md-3"><div class="kpi-card kpi-purple"><div class="kpi-icon"><i class="bi bi-hourglass-split"></i></div><div class="kpi-value"><?= round($r['horas_hoy'] ?? 0, 1) ?>h</div><div class="kpi-label">Horas hoy</div></div></div>
    <div class="col-6 col-md-3"><div class="kpi-card kpi-orange"><div class="kpi-icon"><i class="bi bi-exclamation-triangle-fill"></i></div><div class="kpi-value"><?= $r['retrasos_hoy'] ?? 0 ?></div><div class="kpi-label">Retrasos hoy</div></div></div>
    <div class="col-6 col-md-3"><div class="kpi-card kpi-teal"><div class="kpi-icon"><i class="bi bi-calendar-check"></i></div><div class="kpi-value"><?= $r['fichajes_mes'] ?? 0 ?></div><div class="kpi-label">Fichajes este mes</div></div></div>
    <div class="col-6 col-md-3"><div class="kpi-card kpi-indigo"><div class="kpi-icon"><i class="bi bi-graph-up-arrow"></i></div><div class="kpi-value"><?= round($r['horas_mes'] ?? 0) ?>h</div><div class="kpi-label">Horas este mes</div></div></div>
    <div class="col-6 col-md-3"><div class="kpi-card kpi-red"><div class="kpi-icon"><i class="bi bi-alarm-fill"></i></div><div class="kpi-value"><?= $r['retrasos_mes'] ?? 0 ?></div><div class="kpi-label">Retrasos este mes</div></div></div>
    <div class="col-6 col-md-3"><div class="kpi-card kpi-pink"><div class="kpi-icon"><i class="bi bi-plus-circle-fill"></i></div><div class="kpi-value"><?= round($r['horas_extra_mes'] ?? 0, 1) ?>h</div><div class="kpi-label">Horas extra mes</div></div></div>
  </div>

  <!-- CHARTS ROW 1 -->
  <div class="row g-3 mb-4">
    <div class="col-lg-8">
      <div class="chart-card">
        <div class="chart-card-header"><h6 class="mb-0"><i class="bi bi-graph-up me-2 text-primary"></i>Evolución últimos 6 meses</h6></div>
        <div class="chart-card-body"><canvas id="chartEvolucion" height="90"></canvas></div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="chart-card">
        <div class="chart-card-header"><h6 class="mb-0"><i class="bi bi-calendar-week me-2 text-success"></i>Fichajes por día de semana</h6></div>
        <div class="chart-card-body"><canvas id="chartDiaSemana" height="170"></canvas></div>
      </div>
    </div>
  </div>

  <!-- CHARTS ROW 2 -->
  <div class="row g-3 mb-4">
    <div class="col-lg-5">
      <div class="chart-card h-100">
        <div class="chart-card-header"><h6 class="mb-0"><i class="bi bi-trophy-fill me-2 text-warning"></i>Top empleados por horas (mes actual)</h6></div>
        <div class="chart-card-body"><canvas id="chartTopEmpleados" height="160"></canvas></div>
      </div>
    </div>
    <div class="col-lg-7">
      <div class="chart-card h-100">
        <div class="chart-card-header"><h6 class="mb-0"><i class="bi bi-bar-chart-steps me-2 text-info"></i>Fichajes vs Retrasos (6 meses)</h6></div>
        <div class="chart-card-body"><canvas id="chartComparativa" height="140"></canvas></div>
      </div>
    </div>
  </div>

  <!-- TABLA RANKING -->
  <div class="chart-card mb-4">
    <div class="chart-card-header"><h6 class="mb-0"><i class="bi bi-people me-2 text-primary"></i>Ranking empleados – mes actual</h6></div>
    <div class="chart-card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover stats-table mb-0">
          <thead><tr><th>#</th><th>Empleado</th><th>Horas ordinarias</th><th>Horas extra</th><th>Total horas</th><th>Progreso</th></tr></thead>
          <tbody>
            <?php
            $allHoras = array_column($datos['topEmpleados'], 'total_horas');
            $maxH = !empty($allHoras) ? (float)max($allHoras) : 0;
            $maxH = $maxH > 0 ? $maxH : 1;
            ?>
            <?php foreach ($datos['topEmpleados'] as $i => $emp): ?>
            <?php $pct = (float)($emp['total_horas'] ?? 0) / $maxH * 100; ?>
            <tr>
              <td><?php if($i===0): ?><span class="badge bg-warning text-dark"><i class="bi bi-trophy-fill"></i> 1</span><?php elseif($i===1): ?><span class="badge bg-secondary">2</span><?php elseif($i===2): ?><span class="badge" style="background:#cd7f32">3</span><?php else: ?><span class="text-muted"><?= $i+1 ?></span><?php endif; ?></td>
              <td class="fw-semibold"><?= htmlspecialchars($emp['nombre'] . ' ' . $emp['apellidos']) ?></td>
              <td><?= round($emp['horas_ordinarias'], 1) ?>h</td>
              <td><?= $emp['horas_extra'] > 0 ? '<span class="badge bg-warning text-dark">'.round($emp['horas_extra'],1).'h</span>' : '<span class="text-muted">—</span>' ?></td>
              <td class="fw-bold"><?= round($emp['total_horas'], 1) ?>h</td>
              <td style="width:140px;"><div class="progress" style="height:8px;"><div class="progress-bar <?= $i===0?'bg-warning':($i===1?'bg-info':'bg-primary') ?>" style="width:<?= round($pct) ?>%"></div></div></td>
            </tr>
            <?php endforeach; ?>
            <?php if(empty($datos['topEmpleados'])): ?><tr><td colspan="6" class="text-center text-muted py-4">No hay datos disponibles</td></tr><?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const ML = <?= json_encode($mesesLabels) ?>;
const MF = <?= json_encode(array_map('intval', $mesesFichajes)) ?>;
const MH = <?= json_encode(array_map('floatval', $mesesHoras)) ?>;
const MR = <?= json_encode(array_map('intval', $mesesRetrasos)) ?>;
const DL = <?= json_encode($diasLabels) ?>;
const DT = <?= json_encode(array_map('intval', $diasTotales)) ?>;
const TN = <?= json_encode($topNombres) ?>;
const TH = <?= json_encode(array_map('floatval', $topHoras)) ?>;

Chart.defaults.font.family="'Segoe UI',sans-serif";
Chart.defaults.color='#6b7280';

new Chart(document.getElementById('chartEvolucion'),{type:'line',data:{labels:ML,datasets:[
  {label:'Fichajes',data:MF,borderColor:'#2563eb',backgroundColor:'rgba(37,99,235,.1)',tension:.4,fill:true,pointRadius:4},
  {label:'Horas',data:MH,borderColor:'#10b981',backgroundColor:'rgba(16,185,129,.08)',tension:.4,fill:true,yAxisID:'yH',pointRadius:4},
  {label:'Retrasos',data:MR,borderColor:'#f59e0b',backgroundColor:'rgba(245,158,11,.08)',tension:.4,fill:true,pointRadius:4},
]},options:{responsive:true,interaction:{mode:'index',intersect:false},plugins:{legend:{position:'top'}},scales:{y:{beginAtZero:true},yH:{beginAtZero:true,position:'right',grid:{drawOnChartArea:false}}}}});

new Chart(document.getElementById('chartDiaSemana'),{type:'bar',data:{labels:DL,datasets:[{label:'Fichajes',data:DT,backgroundColor:['#3b82f6','#6366f1','#8b5cf6','#a855f7','#ec4899','#f59e0b','#10b981'],borderRadius:6}]},options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true}}}});

new Chart(document.getElementById('chartTopEmpleados'),{type:'bar',data:{labels:TN,datasets:[{label:'Total horas',data:TH,backgroundColor:'#2563eb',borderRadius:6}]},options:{indexAxis:'y',responsive:true,plugins:{legend:{display:false}},scales:{x:{beginAtZero:true}}}});

new Chart(document.getElementById('chartComparativa'),{type:'bar',data:{labels:ML,datasets:[
  {label:'Fichajes',data:MF,backgroundColor:'#3b82f6',borderRadius:4},
  {label:'Retrasos',data:MR,backgroundColor:'#f59e0b',borderRadius:4},
]},options:{responsive:true,plugins:{legend:{position:'top'}},scales:{y:{beginAtZero:true}}}});
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
.kpi-purple{background:linear-gradient(135deg,#ede9fe,#f5f3ff);color:#5b21b6;}
.kpi-orange{background:linear-gradient(135deg,#ffedd5,#fff7ed);color:#c2410c;}
.kpi-teal{background:linear-gradient(135deg,#ccfbf1,#f0fdfa);color:#0f766e;}
.kpi-indigo{background:linear-gradient(135deg,#e0e7ff,#eef2ff);color:#3730a3;}
.kpi-red{background:linear-gradient(135deg,#fee2e2,#fef2f2);color:#b91c1c;}
.kpi-pink{background:linear-gradient(135deg,#fce7f3,#fdf2f8);color:#9d174d;}
.chart-card{background:#fff;border-radius:14px;box-shadow:0 2px 10px rgba(0,0,0,.06);overflow:hidden;}
.chart-card-header{padding:14px 20px;border-bottom:1px solid #f3f4f6;}
.chart-card-body{padding:16px 20px;}
.stats-table thead tr th{background:#f8fafc;font-size:.8rem;text-transform:uppercase;letter-spacing:.04em;color:#6b7280;border-bottom:2px solid #e5e7eb;}
.stats-table tbody tr td{font-size:.88rem;vertical-align:middle;}
.stats-topbar{padding:16px 0 0;}
.breadcrumb-item a{color:#2563eb;text-decoration:none;}
</style>

<?php require_once RUTA_APP . '/views/inc/footer.php' ?>
