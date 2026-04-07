
// CARGAR INCIDENCIAS

async function loadIncidencias() {
    try {
        const RESPONSE = await fetch('/incidencias/getIncidencias');

        if (!RESPONSE.ok) {
            throw new Error('Error con respuesta ' + RESPONSE.status);
        }

        const DATA = await RESPONSE.json();
        return DATA;

    } catch (error) {
        console.error("Error cargando incidencias:", error.message);
        return null;
    }
}



// PINTAR TABLA

function renderIncidencias(response) {

    const lista = document.getElementById('lista');

    if (!lista) {
        console.error("No existe #lista");
        return;
    }

    lista.innerHTML = '';

    if (response && response.success && response.data.length > 0) {

        response.data.forEach(incidencia => {

            
            let fecha = incidencia.fecha;
            if (fecha) {
                fecha = fecha.replace('T', ' ').substring(0, 16);
            }

            const row = `
                <tr>
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

                    <td>${fecha}</td>

                    <td>
                        <button 
                            class="btn btn-outline-warning btn-sm btn-editar"
                            data-bs-toggle="modal" 
                            data-bs-target="#editModal"

                            data-id="${incidencia.id_incidencia}"
                            data-fichaje="${incidencia.id_fichaje}"
                            data-mensaje="${incidencia.mensaje}"
                            data-respuesta="${incidencia.respuesta || ''}"
                            data-estado="${incidencia.estado}"
                            data-fecha="${incidencia.fecha}"
                        >
                            ✏️
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
                            🗑️
                        </button>
                    </td>
                </tr>
            `;

            lista.insertAdjacentHTML('beforeend', row);
        });

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
// MODAL HANDLERS
// =========================
document.addEventListener('click', function(e) {

    // =========================
    // EDITAR
    // =========================
    const editBtn = e.target.closest('.btn-editar');
    if (editBtn) {

        const modal = document.querySelector('#editModal');

        let fecha = editBtn.dataset.fecha;
        if (fecha) {
            fecha = fecha.replace(' ', 'T').substring(0, 16);
        }

        modal.querySelector('[name="id"]').value = editBtn.dataset.id;
        modal.querySelector('[name="id_fichaje"]').value = editBtn.dataset.fichaje;
        modal.querySelector('[name="mensaje"]').value = editBtn.dataset.mensaje;
        modal.querySelector('[name="respuesta"]').value = editBtn.dataset.respuesta || '';
        modal.querySelector('[name="estado"]').value = editBtn.dataset.estado;
        modal.querySelector('[name="fecha"]').value = fecha;

        return;
    }

    // =========================
    // ELIMINAR
    // =========================
    const deleteBtn = e.target.closest('.btn-eliminar');
    if (deleteBtn) {

        const modal = document.querySelector('#deleteModal');

        modal.querySelector('[name="id"]').value = deleteBtn.dataset.id;
        modal.querySelector('[name="id_fichaje"]').value = deleteBtn.dataset.fichaje;
        modal.querySelector('[name="mensaje"]').value = deleteBtn.dataset.mensaje;
        modal.querySelector('[name="respuesta"]').value = deleteBtn.dataset.respuesta || '';
        modal.querySelector('[name="estado"]').value = deleteBtn.dataset.estado;
        modal.querySelector('[name="fecha"]').value = deleteBtn.dataset.fecha;
    }

});


// =========================
// RECARGA AUTOMÁTICA (OPCIONAL PRO)
// =========================
function refrescarIncidencias() {
    loadIncidencias().then(renderIncidencias);
}