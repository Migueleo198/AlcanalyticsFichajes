// =========================
// LOAD JORNADAS
// =========================
async function loadJornadas() {
    try {
        const RESPONSE = await fetch(RUTA_URL + "/AsignarJornadas/getJornadas");

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
        console.log("FETCH ERROR:", error.message);
        return { success: false, data: [] };
    }
}


// =========================
// RENDER TABLE
// =========================
function renderJornadas(response) {

    const lista = document.getElementById("listaJornadas");
    if (!lista) return;

    if (response && response.success && response.data.length > 0) {

        let html = "";

        response.data.forEach(j => {

            html += `
                <tr>
                    <td>${j.id_jornada}</td>

                    <td>${j.nombre_usuario ?? ''} ${j.apellidos ?? ''}</td>

                    <td>${j.horas_dia}</td>
                    <td>${j.horas_semana}</td>
                    <td>${j.fecha_inicio ?? ''}</td>
                    <td>${j.fecha_fin ?? ''}</td>

                    <td class="text-center">

                        <!-- EDIT (BLUE PEN) -->
                        <button
                            class="btn btn-sm text-primary btn-edit"
                            data-bs-toggle="modal"
                            data-bs-target="#editModal"
                            data-id="${j.id_jornada}"
                            data-id_usuario="${j.id_usuario}"
                            data-horas_dia="${j.horas_dia}"
                            data-horas_semana="${j.horas_semana}"
                            data-fecha_inicio="${j.fecha_inicio}"
                            data-fecha_fin="${j.fecha_fin}"
                        >
                            <i class="bi bi-pencil"></i>
                        </button>

                        <!-- DELETE (RED TRASH) -->
                        <button
                            class="btn btn-sm text-danger btn-delete"
                            data-bs-toggle="modal"
                            data-bs-target="#deleteModal"
                            data-id="${j.id_jornada}"
                        >
                            <i class="bi bi-trash"></i>
                        </button>

                    </td>
                </tr>
            `;
        });

        lista.innerHTML = html;

    } else {
        lista.innerHTML = `
            <tr>
                <td colspan="7" class="text-center text-muted">
                    No hay jornadas registradas
                </td>
            </tr>
        `;
    }

    document.dispatchEvent(new Event("tableReady"));
}


// =========================
// INIT
// =========================
document.addEventListener("DOMContentLoaded", async () => {
    const data = await loadJornadas();
    renderJornadas(data);
});


// =========================
// CREATE
// =========================
document.querySelector("#formAddJornada")?.addEventListener("submit", async (e) => {
    e.preventDefault();

    const res = await fetch(RUTA_URL + "/AsignarJornadas/addJornada", {
        method: "POST",
        body: new FormData(e.target)
    });

    const data = await res.json();

    if (data.success) {
        bootstrap.Modal.getInstance(document.getElementById("addModal"))?.hide();

        const refreshed = await loadJornadas();
        renderJornadas(refreshed);
    } else {
        alert("Error creando jornada");
    }
});


// =========================
// EDIT
// =========================
document.querySelector("#formEditJornada")?.addEventListener("submit", async (e) => {
    e.preventDefault();

    const res = await fetch(RUTA_URL + "/AsignarJornadas/editJornada", {
        method: "POST",
        body: new FormData(e.target)
    });

    const data = await res.json();

    if (data.success) {
        bootstrap.Modal.getInstance(document.getElementById("editModal"))?.hide();

        const refreshed = await loadJornadas();
        renderJornadas(refreshed);
    } else {
        alert("Error editando jornada");
    }
});


// =========================
// DELETE
// =========================
document.querySelector("#formDeleteJornada")?.addEventListener("submit", async (e) => {
    e.preventDefault();

    const res = await fetch(RUTA_URL + "/AsignarJornadas/removeJornada", {
        method: "POST",
        body: new FormData(e.target)
    });

    const data = await res.json();

    if (data.success) {
        bootstrap.Modal.getInstance(document.getElementById("deleteModal"))?.hide();

        const refreshed = await loadJornadas();
        renderJornadas(refreshed);
    } else {
        alert("Error eliminando jornada");
    }
});


// =========================
// FILL EDIT MODAL
// =========================
document.addEventListener("click", (e) => {

    const btn = e.target.closest(".btn-edit");
    if (!btn) return;

    const modal = document.querySelector("#editModal");

    modal.querySelector("[name='id']").value = btn.dataset.id;
    modal.querySelector("[name='id_usuario']").value = btn.dataset.id_usuario;
    modal.querySelector("[name='horas_dia']").value = btn.dataset.horas_dia;
    modal.querySelector("[name='horas_semana']").value = btn.dataset.horas_semana;
    modal.querySelector("[name='fecha_inicio']").value = btn.dataset.fecha_inicio;
    modal.querySelector("[name='fecha_fin']").value = btn.dataset.fecha_fin;
});


// =========================
// FILL DELETE MODAL
// =========================
document.addEventListener("click", (e) => {

    const btn = e.target.closest(".btn-delete");
    if (!btn) return;

    const modal = document.querySelector("#deleteModal");
    modal.querySelector("[name='id']").value = btn.dataset.id;
});