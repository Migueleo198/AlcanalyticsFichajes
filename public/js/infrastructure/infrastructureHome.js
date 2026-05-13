document.addEventListener("DOMContentLoaded", () => {
  loadFichajes();
});

// =========================
// CARGAR FICHAJES
// =========================
async function loadFichajes() {

  const lista = document.getElementById("lista");

  if (!lista) {
    console.error("No existe #lista");
    return;
  }

  lista.innerHTML = `
    <tr>
      <td colspan="8" class="text-center">
        Cargando fichajes...
      </td>
    </tr>
  `;

  try {

    const res = await fetch(RUTA_URL + '/Fichaje/listFichajes');

    if (!res.ok) {
      throw new Error(`HTTP ${res.status}`);
    }

    const text = await res.text();

    console.log("Respuesta RAW:", text);

    let data;

    try {

      data = JSON.parse(text);
      console.log("JSON PARSEADO:", data);

    } catch (e) {

      console.error("NO ES JSON:", text);

      lista.innerHTML = `
        <tr>
          <td colspan="8" class="text-danger text-center">
            Error: el servidor no devolvió JSON válido
          </td>
        </tr>
      `;

      return;
    }

    lista.innerHTML = "";

    // =========================
    // VALIDAR RESPUESTA
    // =========================
    if (
      (data.success || data.status === "success") &&
      Array.isArray(data.data)
    ) {

      if (data.data.length === 0) {

        lista.innerHTML = `
          <tr>
            <td colspan="8" class="text-center">
              No hay fichajes
            </td>
          </tr>
        `;

        return;
      }

      data.data.forEach((f, index) => {

        const tr = document.createElement("tr");

        const estadoClass =
          f.estado === 'cerrado' ? 'bg-success' :
          f.estado === 'abierto' ? 'bg-primary' :
          f.estado === 'incidencia' ? 'bg-danger' :
          'bg-secondary';

        const horasMostrar = f.horas_totales !== null && f.horas_totales !== undefined
          ? `<span class="badge bg-light text-dark border">${parseFloat(f.horas_totales).toFixed(1)}h</span>`
          : '<span class="text-muted">—</span>';

        tr.innerHTML = `
          <td>${index + 1}</td>

          <td>
            ${(f.nombre || '')} ${(f.apellidos || '')}
          </td>

          <td>${f.fecha || '-'}</td>

          <td>${f.hora_entrada || '-'}</td>

          <td>${f.hora_salida || '-'}</td>

          <td>${horasMostrar}</td>

          <td>
            <span class="badge ${estadoClass}">
              ${f.estado || 'desconocido'}
            </span>
          </td>

          <td class="d-flex gap-2">

            <button 
              class="btn btn-outline-primary btn-sm btn-incidencia"
              data-bs-toggle="modal"
              data-bs-target="#incidenciaModal"
              data-fichaje="${f.id_fichaje || ''}"
              data-nombre="${(f.nombre || '')} ${(f.apellidos || '')}"
            >
              Añadir Incidencia
            </button>

            <button 
              class="btn btn-outline-secondary btn-sm btn-ver-incidencias"
              data-bs-toggle="modal"
              data-bs-target="#verIncidenciaModal"
              data-fichaje="${f.id_fichaje || ''}"
            >
              Ver Incidencias
            </button>

          </td>
        `;

        lista.appendChild(tr);
      });

    } else {

      console.warn("Respuesta inesperada:", data);

      lista.innerHTML = `
        <tr>
          <td colspan="8" class="text-center text-muted">
            No hay fichajes disponibles
          </td>
        </tr>
      `;
    }

  } catch (err) {

    console.error("Fetch error:", err);

    lista.innerHTML = `
      <tr>
        <td colspan="8" class="text-danger text-center">
          Error cargando fichajes
        </td>
      </tr>
    `;

  } finally {

    document.dispatchEvent(new Event('tableReady'));
  }
}


// =========================
// MODAL NUEVA INCIDENCIA
// =========================
document.addEventListener('click', function (e) {

  const btn = e.target.closest('.btn-incidencia');

  if (!btn) return;

  const usuarioInput = document.getElementById('inc_usuario');
  const fichajeInput = document.getElementById('inc_id_fichaje');
  const fechaInput = document.querySelector('[name="fecha"]');

  if (usuarioInput) {
    usuarioInput.value = btn.dataset.nombre || '';
  }

  if (fichajeInput) {
    fichajeInput.value = btn.dataset.fichaje || '';
  }

  if (fechaInput) {

    const now = new Date();

    const formatted = now.toISOString().slice(0, 16);

    fechaInput.value = formatted;
  }
});


// =========================
// VER INCIDENCIAS (MODAL)
// =========================
document.addEventListener('click', async function (e) {

  const btn = e.target.closest('.btn-ver-incidencias');

  if (!btn) return;

  const idFichaje = btn.dataset.fichaje;

  const contenedor = document.getElementById('listaIncidencias');

  if (!contenedor) {
    console.error("No existe #listaIncidencias");
    return;
  }

  contenedor.innerHTML = `
    <div class="text-center">
      Cargando incidencias...
    </div>
  `;

  try {

    const res = await fetch(
      `${RUTA_URL}/incidencias/getByFichaje?id=${idFichaje}`
    );

    if (!res.ok) {
      throw new Error(`HTTP ${res.status}`);
    }

    const text = await res.text();

    console.log("RAW incidencias:", text);

    let data;

    try {

      data = JSON.parse(text);
      console.log("JSON incidencias:", data);

    } catch (e) {

      console.error("NO ES JSON incidencias:", text);

      contenedor.innerHTML = `
        <div class="text-danger text-center">
          Error de formato en servidor
        </div>
      `;

      return;
    }

    if (
      data.success &&
      Array.isArray(data.data) &&
      data.data.length > 0
    ) {

      let html = `
        <table class="table table-sm table-striped">
          <thead>
            <tr>
              <th>Mensaje</th>
              <th>Respuesta</th>
              <th>Estado</th>
              <th>Fecha</th>
            </tr>
          </thead>
          <tbody>
      `;

      data.data.forEach(inc => {

        const estadoClass =
          inc.estado === 'resuelto'
            ? 'bg-success'
            : inc.estado === 'pendiente'
            ? 'bg-warning'
            : 'bg-secondary';

        let fecha = '-';

        if (inc.fecha) {
          fecha = inc.fecha.replace('T', ' ').substring(0, 16);
        }

        html += `
          <tr>

            <td>${inc.mensaje || '-'}</td>

            <td>${inc.respuesta || '-'}</td>

            <td>
              <span class="badge ${estadoClass}">
                ${inc.estado || '-'}
              </span>
            </td>

            <td>${fecha}</td>

          </tr>
        `;
      });

      html += `
          </tbody>
        </table>
      `;

      contenedor.innerHTML = html;

    } else {

      contenedor.innerHTML = `
        <div class="text-center text-muted">
          No hay incidencias para este fichaje
        </div>
      `;
    }

  } catch (err) {

    console.error("Error incidencias:", err);

    contenedor.innerHTML = `
      <div class="text-danger text-center">
        Error cargando incidencias
      </div>
    `;
  }
});