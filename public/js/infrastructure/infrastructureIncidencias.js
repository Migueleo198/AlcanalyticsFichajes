// =========================
// LOAD INCIDENCIAS
// =========================
async function loadIncidencias() {
    try {
        const RESPONSE = await fetch('/incidencias/getIncidencias');

        const text = await RESPONSE.text();

        if (!RESPONSE.ok) {
            console.log("SERVER ERROR:", text);
            return { success: false, data: [] };
        }

        try {
            return JSON.parse(text);
        } catch (e) {
            console.log("NO JSON RESPONSE:", text);
            return { success: false, data: [] };
        }

    } catch (error) {
        console.error("Error cargando incidencias:", error.message);
        return { success: false, data: [] };
    }
}


// =========================
// RENDER TABLE (NO BUTTON GAP)
// =========================
function renderIncidencias(response) {

    const lista = document.getElementById('lista');
    if (!lista) return;

    lista.innerHTML = '';

    if (response && response.success && response.data.length > 0) {

        const fragment = document.createDocumentFragment();

        response.data.forEach(incidencia => {

            let fecha = incidencia.fecha;

            if (fecha) {
                if (fecha.includes('T')) {
                    fecha = fecha.substring(0, 16).replace('T', ' ');
                } else {
                    fecha = fecha.substring(0, 16);
                }
            }

            const tr = document.createElement('tr');

            tr.innerHTML = `
                <td>${incidencia.id_incidencia}</td>
                <td>${incidencia.id_fichaje}</td>
                <td>${incidencia.mensaje}</td>
                <td>${incidencia.respuesta || '-'}</td>

                <td>
                    <span class="badge bg-${
                        incidencia.estado === 'resuelto' ? 'success' :
                        incidencia.estado === 'pendiente' ? 'warning' :
                        'secondary'
                    }">
                        ${incidencia.estado}
                    </span>
                </td>

                <td>${fecha || ''}</td>

                <!-- SINGLE CELL, NO SEPARATION -->
                <td class="text-center">
                    <button 
                        class="btn btn-outline-primary btn-sm btn-editar"
                        data-bs-toggle="modal"
                        data-bs-target="#editModal"
                        data-id="${incidencia.id_incidencia}"
                        data-fichaje="${incidencia.id_fichaje}"
                        data-mensaje="${incidencia.mensaje}"
                        data-respuesta="${incidencia.respuesta || ''}"
                        data-estado="${incidencia.estado}"
                        data-fecha="${incidencia.fecha}"
                    >
                        <i class="bi bi-pencil-square"></i>
                    </button>

                    <button 
                        class="btn btn-outline-danger btn-sm btn-eliminar"
                        data-bs-toggle="modal"
                        data-bs-target="#deleteModal"
                        data-id="${incidencia.id_incidencia}"
                        data-fichaje="${incidencia.id_fichaje}"
                        data-mensaje="${incidencia.mensaje}"
                        data-respuesta="${incidencia.respuesta || ''}"
                        data-estado="${incidencia.estado}"
                        data-fecha="${incidencia.fecha}"
                    >
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `;

            fragment.appendChild(tr);
        });

        lista.appendChild(fragment);

    } else {
        lista.innerHTML = `
            <tr>
                <td colspan="7" class="text-center text-muted">
                    No hay incidencias registradas
                </td>
            </tr>
        `;
    }

    document.dispatchEvent(new Event('tableReady'));
}


// =========================
// INIT
// =========================
document.addEventListener("DOMContentLoaded", async () => {
    const response = await loadIncidencias();
    renderIncidencias(response);
});


// =========================
// FILL EDIT MODAL
// =========================
document.addEventListener("click", (e) => {

    const btn = e.target.closest(".btn-editar");
    if (!btn) return;

    const modal = document.querySelector("#editModal");

    let fecha = btn.dataset.fecha;

    if (fecha) {
        if (fecha.includes('T')) {
            fecha = fecha.substring(0, 16).replace('T', ' ');
        } else {
            fecha = fecha.substring(0, 16);
        }
    }

    modal.querySelector("[name='id']").value = btn.dataset.id;
    modal.querySelector("[name='id_fichaje']").value = btn.dataset.fichaje;
    modal.querySelector("[name='mensaje']").value = btn.dataset.mensaje;
    modal.querySelector("[name='respuesta']").value = btn.dataset.respuesta || '';
    modal.querySelector("[name='estado']").value = btn.dataset.estado;
    modal.querySelector("[name='fecha']").value = fecha;
});


// =========================
// FILL DELETE MODAL
// =========================
document.addEventListener("click", (e) => {

    const btn = e.target.closest(".btn-eliminar");
    if (!btn) return;

    const modal = document.querySelector("#deleteModal");

    modal.querySelector("[name='id']").value = btn.dataset.id;
    modal.querySelector("[name='id_fichaje']").value = btn.dataset.fichaje;
    modal.querySelector("[name='mensaje']").value = btn.dataset.mensaje;
    modal.querySelector("[name='respuesta']").value = btn.dataset.respuesta || '';
    modal.querySelector("[name='estado']").value = btn.dataset.estado;
    modal.querySelector("[name='fecha']").value = btn.dataset.fecha;
});