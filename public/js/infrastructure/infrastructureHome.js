document.addEventListener("DOMContentLoaded", () => {

  loadFichajes();

});

// =========================
// CARGAR FICHAJES
// =========================
async function loadFichajes() {

  try {

    const res = await fetch(RUTA_URL + '/Fichaje/listFichajes');
    const text = await res.text();

    //console.log("Respuesta RAW:", text);

    let data;

    try {
      data = JSON.parse(text);
    } catch (e) {
      console.error("NO ES JSON:", text);
      return;
    }

    const lista = document.getElementById("lista");

    if (!lista) {
      console.error("No existe #lista");
      return;
    }

    lista.innerHTML = "";

    if (data.status === "success") {

      data.data.forEach((f, index) => {

        const tr = document.createElement("tr");

        tr.innerHTML = `
          <td>${index + 1}</td>
          <td>${f.nombre} ${f.apellidos}</td>
          <td>${f.fecha}</td>
          <td>${f.hora_entrada ?? '-'}</td>
          <td>${f.hora_salida ?? '-'}</td>

          <td>
            <span class="badge ${
              f.estado === 'cerrado' ? 'bg-success' :
              f.estado === 'abierto' ? 'bg-primary' :
              f.estado === 'incidencia' ? 'bg-danger' :
              'bg-secondary'
            }">
              ${f.estado}
            </span>
          </td>

          <td>
            <button 
              class="btn btn-outline-warning btn-sm btn-incidencia"
              data-bs-toggle="modal" 
              data-bs-target="#incidenciaModal"
              data-fichaje="${f.id_fichaje}"
              data-nombre="${f.nombre} ${f.apellidos}"
            >
              Añadir Incidencia
            </button>

            <button 
              class="btn btn-outline-secondary btn-sm btn-ver-incidencias"
              data-bs-toggle="modal" 
              data-bs-target="#verIncidenciaModal"
              data-fichaje="${f.id_fichaje}"
            >
              Ver Incidencias
            </button>
          </td>
        `;

        lista.appendChild(tr);
      });
      

    } else {
      lista.innerHTML = `<tr><td colspan="7">No hay fichajes</td></tr>`;
    }

  } catch (err) {
    console.error("Fetch error:", err);
  }
   document.dispatchEvent(new Event('tableReady'));
}


// =========================
// MODAL NUEVA INCIDENCIA
// =========================
document.addEventListener('click', function (e) {

  const btn = e.target.closest('.btn-incidencia');
  if (!btn) return;

  document.getElementById('inc_usuario').value = btn.dataset.nombre;
  document.getElementById('inc_id_fichaje').value = btn.dataset.fichaje;

  const now = new Date();
  const formatted = now.toISOString().slice(0, 16);
  document.querySelector('[name="fecha"]').value = formatted;

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

  contenedor.innerHTML = `<div class="text-center">Cargando...</div>`;

  try {

    const res = await fetch(`${RUTA_URL}/incidencias/getByFichaje?id=${idFichaje}`);
    const text = await res.text();

    let data;

    try {
      data = JSON.parse(text);
    } catch (e) {
      console.error("NO ES JSON incidencias:", text);
      contenedor.innerHTML = `
        <div class="text-danger text-center">
          Error de formato en servidor
        </div>
      `;
      return;
    }

    if (data.success && data.data.length > 0) {

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

        let fecha = inc.fecha 
          ? inc.fecha.replace('T', ' ').substring(0, 16) 
          : '';

        html += `
          <tr>
            <td>${inc.mensaje}</td>
            <td>${inc.respuesta || '-'}</td>
            <td>
              <span class="badge ${
                inc.estado === 'resuelto' ? 'bg-success' :
                inc.estado === 'pendiente' ? 'bg-warning' :
                'bg-secondary'
              }">
                ${inc.estado}
              </span>
            </td>
            <td>${fecha}</td>
          </tr>
        `;
      });

      html += `</tbody></table>`;

      contenedor.innerHTML = html;

    } else {

      contenedor.innerHTML = `
        <div class="text-center text-muted">
          No hay incidencias para este fichaje
        </div>
      `;
    }

  } catch (err) {

    console.error(err);

    contenedor.innerHTML = `
      <div class="text-danger text-center">
        Error cargando incidencias
      </div>
    `;
  }
 

});