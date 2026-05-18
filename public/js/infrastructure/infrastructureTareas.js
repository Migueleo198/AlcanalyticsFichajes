function escAttr(str) {
    return String(str ?? '').replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

// =========================
// LOAD TASKS
// =========================
async function loadTasks() {
    try {
        const RESPONSE = await fetch(RUTA_URL + "/RegistroTareas/getTasks");

        if (!RESPONSE.ok) {
            throw new Error('No response from server: ' + RESPONSE.status);
        }

        return await RESPONSE.json();

    } catch (error) {
        console.log(error.message);
        return { success: false, data: [] };
    }
}


// =========================
// RENDER TABLE
// =========================
function renderTasks(response) {

    const lista = document.getElementById('lista');
    if (!lista) return;

    if (response && response.success && response.data.length > 0) {

        let html = '';

        response.data.forEach(tarea => {

            let acciones = '';

            if (USER_ROL === 'Administrador' || String(USER_ID) === String(tarea.id_usuario)) {

                acciones = `
<td>
    <button 
        class="btn btn-outline-primary btn-sm px-3 btn-editar"
        data-bs-toggle="modal" 
        data-bs-target="#editModal"

        data-id="${tarea.id_tarea}"
        data-id_fichaje="${tarea.id_fichaje}"
        data-titulo="${escAttr(tarea.titulo)}"
        data-descripcion="${escAttr(tarea.descripcion)}"
        data-hora_inicio="${tarea.hora_inicio}"
        data-hora_fin="${tarea.hora_fin}"
        data-tiempo_total="${tarea.tiempo_total}"
        data-estado="${tarea.estado}"
        data-fecha="${tarea.fecha}"
        data-id_tipo="${tarea.id_tipo ?? ''}"
    >
        <i class="bi bi-pencil-square"></i>
    </button>
</td>

<td>
    <button 
        class="btn btn-outline-danger btn-sm px-3 btn-eliminar"
        data-bs-toggle="modal" 
        data-bs-target="#deleteModal"

        data-id="${tarea.id_tarea}"
        data-id_fichaje="${tarea.id_fichaje}"
        data-titulo="${escAttr(tarea.titulo)}"
        data-descripcion="${escAttr(tarea.descripcion)}"
        data-hora_inicio="${tarea.hora_inicio || ''}"
        data-hora_fin="${tarea.hora_fin || ''}"
        data-tiempo_total="${tarea.tiempo_total || ''}"
        data-estado="${tarea.estado}"
        data-fecha="${tarea.fecha}"
        data-id_tipo="${tarea.id_tipo ?? ''}"
    >
        <i class="bi bi-trash"></i>
    </button>
</td>
`;
            } else {
                acciones = `<td colspan="2"></td>`;
            }

            html += `
<tr>
    <td>${tarea.id_tarea}</td>
    <td>${tarea.id_fichaje}</td>
    <td>${tarea.nombre_usuario}</td>
    <td>${tarea.titulo}</td>
    <td>${tarea.descripcion}</td>
    <td>${tarea.hora_inicio}</td>
    <td>${tarea.hora_fin}</td>
    <td>${tarea.tiempo_total}</td>
    <td>${tarea.estado}</td>
    <td>${tarea.fecha}</td>
    <td>
        <span class="badge bg-primary">
            ${tarea.nombre_tipo ?? `Tipo ${tarea.id_tipo}`}
        </span>
    </td>
    ${acciones}
</tr>
`;
        });

        lista.innerHTML = html;

    } else {
        lista.innerHTML = `
<tr>
    <td colspan="13" class="text-center">
        No hay tareas registradas
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
    const response = await loadTasks();
    renderTasks(response);
});


// =========================
// MODAL HANDLERS (EDIT + DELETE SAFE)
// =========================
document.addEventListener('click', function (e) {

    // =========================
    // EDIT
    // =========================
    const editBtn = e.target.closest('.btn-editar');
    if (editBtn) {

        const modal = document.querySelector('#editModal');
        if (!modal) return;

        let fecha = editBtn.dataset.fecha;

        if (fecha) {
            if (!fecha.includes('T') && fecha.includes(' ')) {
                fecha = fecha.replace(' ', 'T').substring(0, 16);
            } else {
                fecha = fecha.substring(0, 16);
            }
        }

        modal.querySelector('[name="id"]').value = editBtn.dataset.id;
        modal.querySelector('[name="id_fichaje"]').value = editBtn.dataset.id_fichaje;
        modal.querySelector('[name="titulo"]').value = editBtn.dataset.titulo;
        modal.querySelector('[name="descripcion"]').value = editBtn.dataset.descripcion;
        modal.querySelector('[name="hora_inicio"]').value = editBtn.dataset.hora_inicio;
        modal.querySelector('[name="hora_fin"]').value = editBtn.dataset.hora_fin;
        modal.querySelector('[name="tiempo_total"]').value = editBtn.dataset.tiempo_total;
        modal.querySelector('[name="estado"]').value = editBtn.dataset.estado;

        const selectTipo = modal.querySelector('[name="id_tipo"]');
        if (selectTipo) selectTipo.value = editBtn.dataset.id_tipo || '';

        modal.querySelector('[name="fecha"]').value = fecha;

        return;
    }

    // =========================
    // DELETE (FULL READ ONLY LOGIC)
    // =========================
    const deleteBtn = e.target.closest('.btn-eliminar');
    if (deleteBtn) {

        const modal = document.querySelector('#deleteModal');
        if (!modal) return;

        let fecha = deleteBtn.dataset.fecha;

        if (fecha) {
            if (!fecha.includes('T') && fecha.includes(' ')) {
                fecha = fecha.replace(' ', 'T').substring(0, 16);
            } else {
                fecha = fecha.substring(0, 16);
            }
        }

        modal.querySelector('[name="id"]').value = deleteBtn.dataset.id;
        modal.querySelector('[name="id_fichaje"]').value = deleteBtn.dataset.id_fichaje;
        modal.querySelector('[name="titulo"]').value = deleteBtn.dataset.titulo;
        modal.querySelector('[name="descripcion"]').value = deleteBtn.dataset.descripcion;
        modal.querySelector('[name="hora_inicio"]').value = deleteBtn.dataset.hora_inicio || '';
        modal.querySelector('[name="hora_fin"]').value = deleteBtn.dataset.hora_fin || '';
        modal.querySelector('[name="tiempo_total"]').value = deleteBtn.dataset.tiempo_total || '';
        modal.querySelector('[name="estado"]').value = deleteBtn.dataset.estado;
        modal.querySelector('[name="fecha"]').value = fecha;

        const tipo = modal.querySelector('[name="id_tipo"]');
        if (tipo) tipo.value = deleteBtn.dataset.id_tipo || '';
    }

});