document.addEventListener("DOMContentLoaded", () => {

    // =========================
    // 🔍 BUSCADOR TABLAS
    // =========================
    const buscador = document.getElementById('buscadorTabla');

    if (buscador) {
        buscador.addEventListener("input", function () {
            const value = this.value.toLowerCase();
            const rows = document.querySelectorAll("#lista tr");

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(value) ? "" : "none";
            });
        });
    }

    // =========================
    // 🔔 NOTIFICACIONES
    // =========================
    const notificaciones = [
        "Nuevo fichaje registrado",
        "Contrato por vencer",
        "Actualización completada"
    ];

    function renderNotificaciones() {
        const lista = document.getElementById("listaNotificaciones");
        const contador = document.getElementById("contadorNotificaciones");

        if (!lista || !contador) return;

        lista.innerHTML = '<li class="fw-bold mb-2">Notificaciones</li>';

        notificaciones.forEach(n => {
            const li = document.createElement("li");
            li.className = "dropdown-item";
            li.textContent = n;
            lista.appendChild(li);
        });

        contador.textContent = notificaciones.length;
    }

    renderNotificaciones();

    // =========================
    // 📦 LOAD USERS
    // =========================
    async function loadUsers() {
        try {
            const RESPONSE = await fetch('/user/getUsers');

            if (!RESPONSE.ok) {
                throw new Error('Error con respuesta ' + RESPONSE.status);
            }

            return await RESPONSE.json();

        } catch (error) {
            console.log(error.message);
        }
    }

    loadUsers().then((response) => {

        const lista = document.getElementById('lista');
        lista.innerHTML = '';

        if (response && response.success && response.data.length > 0) {

            const fragment = document.createDocumentFragment();

            response.data.forEach(usuario => {

                const matriculas = usuario.matriculas || [];

                const matriculasHTML = matriculas.length > 0
                    ? `
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-dark dropdown-toggle"
                                    type="button"
                                    data-bs-toggle="dropdown">
                                    Ver (${matriculas.length})
                            </button>

                            <ul class="dropdown-menu">
                                ${matriculas.map(m => `
                                    <li>
                                        <span class="dropdown-item">🚗 ${m.matricula}</span>
                                    </li>
                                `).join('')}
                            </ul>
                        </div>
                    `
                    : `<span class="text-muted">Sin matrículas</span>`;

                const fechaFormateada = usuario.fecha_nacimiento
                    ? new Date(usuario.fecha_nacimiento).toLocaleDateString()
                    : '-';

                const tr = document.createElement('tr');

                tr.innerHTML = `
                    <td>${usuario.id_usuario}</td>
                    <td>${usuario.nombre} ${usuario.apellidos}</td>
                    <td>${usuario.nombre_usuario}</td>
                    <td>${usuario.dni}</td>
                    <td>${usuario.telefono}</td>
                    <td>${usuario.email}</td>
                    <td>${fechaFormateada}</td>

                    <td>${matriculasHTML}</td>

                    <td>
                        <span class="badge bg-primary">${usuario.rol}</span>
                    </td>

                    <td>
                        <button 
                            class="btn btn-outline-secondary btn-sm btn-editar"
                            data-bs-toggle="modal"
                            data-bs-target="#editModal"
                            data-id="${usuario.id_usuario}"
                            data-nombre="${usuario.nombre}"
                            data-apellidos="${usuario.apellidos}"
                            data-usuario="${usuario.nombre_usuario}"
                            data-dni="${usuario.dni}"
                            data-telefono="${usuario.telefono}"
                            data-email="${usuario.email}"
                            data-rol="${usuario.rol}"
                            data-matriculas='${JSON.stringify(matriculas)}'
                        >
                            ✏️
                        </button>
                    </td>

                    <td>
                        <button 
                            class="btn btn-outline-secondary btn-sm btn-eliminar"
                            data-bs-toggle="modal"
                            data-bs-target="#deleteModal"
                            data-id="${usuario.id_usuario}"
                            data-nombre="${usuario.nombre}"
                            data-apellidos="${usuario.apellidos}"
                            data-usuario="${usuario.nombre_usuario}"
                            data-dni="${usuario.dni}"
                            data-telefono="${usuario.telefono}"
                            data-email="${usuario.email}"
                            data-rol="${usuario.rol}"
                            data-matriculas='${JSON.stringify(matriculas)}'
                        >
                            🗑️
                        </button>
                    </td>
                `;

                fragment.appendChild(tr);
            });

            lista.appendChild(fragment);

        } else {
            lista.innerHTML = `
                <tr>
                    <td colspan="10" class="text-center">
                        No hay empleados registrados
                    </td>
                </tr>
            `;
        }

        document.dispatchEvent(new Event('tableReady'));
    });

    // =========================
    // ✏️ EDIT MODAL
    // =========================
    document.addEventListener('click', function (e) {

        const btn = e.target.closest('.btn-editar');
        if (!btn) return;

        const modal = document.querySelector('#editModal');

        modal.querySelector('[name="id"]').value = btn.dataset.id;
        modal.querySelector('[name="nombre"]').value = btn.dataset.nombre;
        modal.querySelector('[name="apellidos"]').value = btn.dataset.apellidos;
        modal.querySelector('[name="usuario"]').value = btn.dataset.usuario;
        modal.querySelector('[name="dni"]').value = btn.dataset.dni;
        modal.querySelector('[name="telefono"]').value = btn.dataset.telefono;
        modal.querySelector('[name="email"]').value = btn.dataset.email;
        modal.querySelector('[name="rol"]').value = btn.dataset.rol;

        const matriculas = JSON.parse(btn.dataset.matriculas || "[]");

        const select = modal.querySelector('#editMatriculasSelect');
        const input = modal.querySelector('#editMatriculaInput');

        if (!select) return;

        select.innerHTML = '';

        matriculas.forEach(m => {
            const option = document.createElement("option");
            option.value = m.matricula;
            option.textContent = m.matricula;
            option.selected = true;
            select.appendChild(option);
        });

        // When selecting, load into input
        select.addEventListener('change', () => {
            input.value = select.value;
        });
    });

    // =========================
    // ✏️ UPDATE MATRICULA (UI ONLY)
    // =========================
    document.getElementById('saveMatriculaBtn')?.addEventListener('click', () => {

        const select = document.getElementById('editMatriculasSelect');
        const input = document.getElementById('editMatriculaInput');

        if (!select || !input.value.trim()) return;

        const selectedOption = select.selectedOptions[0];

        if (selectedOption) {
            selectedOption.value = input.value.trim();
            selectedOption.textContent = input.value.trim();
        }
    });

    // =========================
    // 💾 SUBMIT EDIT USER
    // =========================
    document.querySelector('#editModal form').addEventListener('submit', async (e) => {

        e.preventDefault();

        const form = e.target;
        const data = new FormData(form);

        const select = form.querySelector('#editMatriculasSelect');

        if (select) {

            const matriculas = Array.from(select.options)
                .map(opt => opt.value);

            data.delete('matriculas[]');

            matriculas.forEach(m => {
                data.append('matriculas[]', m);
            });
        }

        const res = await fetch('/user/editUser', {
            method: 'POST',
            body: data
        });

        const json = await res.json();

        if (json.success) {
            location.reload();
        } else {
            console.error(json);
        }
    });

    // =========================
    // 🗑️ DELETE MODAL
    // =========================
    document.addEventListener('click', function (e) {

        const btn = e.target.closest('.btn-eliminar');
        if (!btn) return;

        const modal = document.querySelector('#deleteModal');

        modal.querySelector('[name="id"]').value = btn.dataset.id;
        modal.querySelector('[name="nombre"]').value = btn.dataset.nombre;
        modal.querySelector('[name="apellidos"]').value = btn.dataset.apellidos;
        modal.querySelector('[name="usuario"]').value = btn.dataset.usuario;
        modal.querySelector('[name="dni"]').value = btn.dataset.dni;
        modal.querySelector('[name="telefono"]').value = btn.dataset.telefono;
        modal.querySelector('[name="email"]').value = btn.dataset.email;
        modal.querySelector('[name="rol"]').value = btn.dataset.rol;

        const matriculas = JSON.parse(btn.dataset.matriculas || "[]");

        let container = modal.querySelector('#deleteMatriculasContainer');

        if (!container) {
            container = document.createElement('div');
            container.id = 'deleteMatriculasContainer';
            container.className = "mb-3";
            modal.querySelector('.modal-body').appendChild(container);
        }

        container.innerHTML = matriculas.length
            ? matriculas.map(m => `<div>🚗 ${m.matricula}</div>`).join('')
            : `<span class="text-muted">Sin matrículas</span>`;
    });

});