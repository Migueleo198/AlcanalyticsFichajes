function escAttr(str) {
    return String(str ?? '').replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

// Convierte "2026-05-21" o "2026-05-21 14:30:00" al formato datetime-local
function toDatetimeLocal(val) {
    if (!val) return '';
    val = String(val).trim();
    if (val.includes('T')) return val.substring(0, 16);        // ya es ISO
    if (val.includes(' ')) return val.replace(' ', 'T').substring(0, 16); // datetime sin T
    return val + 'T00:00';  // solo fecha → añadir hora por defecto
}

// Calcula diferencia en horas decimales entre dos HH:MM
function calcTiempoTotal(inicio, fin) {
    if (!inicio || !fin) return '';
    const [h1, m1] = inicio.split(':').map(Number);
    const [h2, m2] = fin.split(':').map(Number);
    const mins = (h2 * 60 + m2) - (h1 * 60 + m1);
    if (mins <= 0) return '';
    return (mins / 60).toFixed(2);
}

// Devuelve la fecha/hora local actual en formato datetime-local (YYYY-MM-DDTHH:MM)
function fechaActualLocal() {
    const now   = new Date();
    const local = new Date(now.getTime() - now.getTimezoneOffset() * 60000);
    return local.toISOString().slice(0, 16);
}

// Cálculo de tiempo total por delegación de eventos — no depende del momento de carga
function setupTiempoCalcDelegation() {
    function recalcForm(form) {
        if (!form) return;
        const inicio = form.querySelector('[name="hora_inicio"]');
        const fin    = form.querySelector('[name="hora_fin"]');
        const total  = form.querySelector('[name="tiempo_total"]');
        if (inicio && fin && total) {
            total.value = calcTiempoTotal(inicio.value, fin.value);
        }
    }
    // Delegación: captura cualquier cambio en hora_inicio / hora_fin dentro de cualquier form
    ['input', 'change'].forEach(evt => {
        document.addEventListener(evt, e => {
            const f = e.target;
            if (f.name === 'hora_inicio' || f.name === 'hora_fin') {
                recalcForm(f.closest('form'));
            }
        });
    });
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

// Activar delegación de cálculo de tiempo (no necesita esperar al DOM)
setupTiempoCalcDelegation();

document.addEventListener("DOMContentLoaded", async () => {
    const response = await loadTasks();
    renderTasks(response);

    // ── Modal NUEVA TAREA: auto-rellenar fecha al abrirlo ─────────
    const addModal = document.getElementById('addModal');
    if (addModal) {
        addModal.addEventListener('show.bs.modal', () => {
            const f = addModal.querySelector('[name="fecha"]');
            if (f) f.value = fechaActualLocal();

            // Limpiar tiempo total al abrir
            const t = addModal.querySelector('[name="tiempo_total"]');
            if (t) t.value = '';
        });
    }
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

        const inicio = editBtn.dataset.hora_inicio || '';
        const fin    = editBtn.dataset.hora_fin    || '';

        modal.querySelector('[name="id"]').value          = editBtn.dataset.id;
        modal.querySelector('[name="id_fichaje"]').value  = editBtn.dataset.id_fichaje;
        modal.querySelector('[name="titulo"]').value      = editBtn.dataset.titulo;
        modal.querySelector('[name="descripcion"]').value = editBtn.dataset.descripcion;
        modal.querySelector('[name="hora_inicio"]').value = inicio;
        modal.querySelector('[name="hora_fin"]').value    = fin;
        modal.querySelector('[name="estado"]').value = editBtn.dataset.estado;
        // Si la fecha guardada está vacía, usar la fecha actual como fallback
        modal.querySelector('[name="fecha"]').value =
            toDatetimeLocal(editBtn.dataset.fecha) || fechaActualLocal();

        // Rellenar tiempo total: usar valor guardado o calcular si hay inicio+fin
        const tiempoGuardado = editBtn.dataset.tiempo_total;
        const tiempoField    = modal.querySelector('[name="tiempo_total"]');
        if (tiempoField) {
            tiempoField.value = tiempoGuardado || calcTiempoTotal(inicio, fin);
        }

        const selectTipo = modal.querySelector('[name="id_tipo"]');
        if (selectTipo) selectTipo.value = editBtn.dataset.id_tipo || '';

        return;
    }

    // =========================
    // DELETE (FULL READ ONLY LOGIC)
    // =========================
    const deleteBtn = e.target.closest('.btn-eliminar');
    if (deleteBtn) {

        const modal = document.querySelector('#deleteModal');
        if (!modal) return;

        modal.querySelector('[name="id"]').value          = deleteBtn.dataset.id;
        modal.querySelector('[name="id_fichaje"]').value  = deleteBtn.dataset.id_fichaje;
        modal.querySelector('[name="titulo"]').value      = deleteBtn.dataset.titulo;
        modal.querySelector('[name="descripcion"]').value = deleteBtn.dataset.descripcion;
        modal.querySelector('[name="hora_inicio"]').value = deleteBtn.dataset.hora_inicio || '';
        modal.querySelector('[name="hora_fin"]').value    = deleteBtn.dataset.hora_fin    || '';
        modal.querySelector('[name="tiempo_total"]').value = deleteBtn.dataset.tiempo_total || '';
        modal.querySelector('[name="estado"]').value      = deleteBtn.dataset.estado;
        modal.querySelector('[name="fecha"]').value       = toDatetimeLocal(deleteBtn.dataset.fecha);

        const tipo = modal.querySelector('[name="id_tipo"]');
        if (tipo) tipo.value = deleteBtn.dataset.id_tipo || '';
    }

});