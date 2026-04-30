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

    function renderUsers(response) {
        const lista = document.getElementById('lista');
        if (!lista) return;

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
                            class="btn btn-outline-primary btn-sm btn-editar"
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
                            data-fecha_nacimiento="${usuario.fecha_nacimiento || ''}"
                            data-matriculas='${JSON.stringify(matriculas)}'
                        >
                            <i class="bi bi-pencil-square"></i>
                        </button>

                        <button 
                            class="btn btn-outline-danger btn-sm btn-eliminar"
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
                            data-fecha_nacimiento="${usuario.fecha_nacimiento || ''}"
                            data-matriculas='${JSON.stringify(matriculas)}'
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
                    <td colspan="10" class="text-center">
                        No hay empleados registrados
                    </td>
                </tr>
            `;
        }

        document.dispatchEvent(new Event('tableReady'));
    }

    loadUsers().then(renderUsers);

    // =========================
    // ➕ ADD MODAL — reset on close
    // =========================
    const addModalEl = document.querySelector('#addModal');

    addModalEl?.addEventListener('hidden.bs.modal', () => {
        const form = addModalEl.querySelector('form');
        form?.querySelector('.add-alert')?.remove();
        const btn = form?.querySelector('button[type="submit"], button:not([type])');
        if (btn && btn.dataset.originalText) {
            btn.disabled = false;
            btn.textContent = btn.dataset.originalText;
        }
    });

    // =========================
    // ➕ SUBMIT ADD USER
    // =========================
    addModalEl?.querySelector('form')?.addEventListener('submit', async (e) => {

        e.preventDefault();

        const form = e.target;
        const data = new FormData(form);

        // FIX: explicitly set fecha_nacimiento (mirrors edit form behaviour)
        const fechaInput = form.querySelector('[name="fecha_nacimiento"]');
        if (fechaInput) {
            data.set('fecha_nacimiento', fechaInput.value?.trim() || '');
        }

        const submitBtn = form.querySelector('button[type="submit"], button:not([type])');
        if (submitBtn) {
            submitBtn.dataset.originalText = submitBtn.textContent;
            submitBtn.disabled = true;
            submitBtn.textContent = 'Guardando...';
        }

        form.querySelector('.add-alert')?.remove();

        try {
            const res  = await fetch('/user/addUser', { method: 'POST', body: data });
            const json = await res.json();

            const alertEl = document.createElement('div');
            alertEl.className = `alert mt-3 add-alert ${json.success ? 'alert-success' : 'alert-danger'}`;
            alertEl.textContent = json.message ?? (json.success ? 'Usuario creado.' : 'Error al crear.');
            form.appendChild(alertEl);

            if (json.success) {
                form.reset();
                setTimeout(() => {
                    bootstrap.Modal.getInstance(addModalEl)?.hide();
                    loadUsers().then(renderUsers);
                }, 1500);
            } else {
                if (submitBtn) { submitBtn.disabled = false; submitBtn.textContent = submitBtn.dataset.originalText; }
            }

        } catch (err) {
            console.error('Add error:', err);
            const alertEl = document.createElement('div');
            alertEl.className = 'alert alert-danger mt-3 add-alert';
            alertEl.textContent = 'Error de conexión. Inténtalo de nuevo.';
            form.appendChild(alertEl);
            if (submitBtn) { submitBtn.disabled = false; submitBtn.textContent = submitBtn.dataset.originalText; }
        }
    });

    // =========================
    // ✏️ EDIT MODAL — populate
    // =========================
    document.addEventListener('click', function (e) {

        const btn = e.target.closest('.btn-editar');
        if (!btn) return;

        const modal = document.querySelector('#editModal');
        if (!modal) return;

        modal.querySelector('[name="id"]').value        = btn.dataset.id;
        modal.querySelector('[name="nombre"]').value    = btn.dataset.nombre;
        modal.querySelector('[name="apellidos"]').value = btn.dataset.apellidos;
        modal.querySelector('[name="usuario"]').value   = btn.dataset.usuario;
        modal.querySelector('[name="dni"]').value       = btn.dataset.dni;
        modal.querySelector('[name="telefono"]').value  = btn.dataset.telefono;
        modal.querySelector('[name="email"]').value     = btn.dataset.email;
        modal.querySelector('[name="rol"]').value       = btn.dataset.rol;

        const fechaEdit = modal.querySelector('[name="fecha_nacimiento"]');
        if (fechaEdit) {
            fechaEdit.value = btn.dataset.fecha_nacimiento
                ? btn.dataset.fecha_nacimiento.split('T')[0]
                : '';
        }

        const matriculas = JSON.parse(btn.dataset.matriculas || "[]");
        const select = modal.querySelector('#editMatriculasSelect');
        const input  = modal.querySelector('#editMatriculaInput');

        if (select) {
            select.innerHTML = '';

            matriculas.forEach(m => {
                const option = document.createElement("option");
                option.value = m.matricula;
                option.textContent = m.matricula;
                option.selected = true;
                select.appendChild(option);
            });

            select.onchange = () => { input.value = select.value; };
        }
    });

    // =========================
    // ✏️ EDIT MODAL — reset on close
    // =========================
    const editModalEl = document.querySelector('#editModal');

    editModalEl?.addEventListener('hidden.bs.modal', () => {
        const form = editModalEl.querySelector('form');
        form?.querySelector('.edit-alert')?.remove();
        const btn = form?.querySelector('button[type="submit"], button:not([type])');
        if (btn && btn.dataset.originalText) {
            btn.disabled = false;
            btn.textContent = btn.dataset.originalText;
        }
    });

    // =========================
    // 💾 SAVE MATRICULA UI
    // =========================
    document.getElementById('saveMatriculaBtn')?.addEventListener('click', () => {

        const select = document.getElementById('editMatriculasSelect');
        const input  = document.getElementById('editMatriculaInput');

        if (!select || !input.value.trim()) return;

        const option = select.selectedOptions[0];
        if (option) {
            option.value = input.value.trim();
            option.textContent = input.value.trim();
        }
    });

    // =========================
    // 💾 SUBMIT EDIT USER
    // =========================
    editModalEl?.querySelector('form')?.addEventListener('submit', async (e) => {

        e.preventDefault();

        const form = e.target;
        const data = new FormData(form);

        const fechaInput = form.querySelector('[name="fecha_nacimiento"]');
        if (fechaInput) {
            data.set('fecha_nacimiento', fechaInput.value?.trim() || '');
        }

        const select = form.querySelector('#editMatriculasSelect');
        if (select) {
            data.delete('matriculas[]');
            Array.from(select.options).forEach(opt => data.append('matriculas[]', opt.value));
        }

        const submitBtn = form.querySelector('button[type="submit"], button:not([type])');
        if (submitBtn) {
            submitBtn.dataset.originalText = submitBtn.textContent;
            submitBtn.disabled = true;
            submitBtn.textContent = 'Guardando...';
        }

        form.querySelector('.edit-alert')?.remove();

        try {
            const res  = await fetch('/user/editUser', { method: 'POST', body: data });
            const json = await res.json();

            const alertEl = document.createElement('div');
            alertEl.className = `alert mt-3 edit-alert ${json.success ? 'alert-success' : 'alert-danger'}`;
            alertEl.textContent = json.message ?? (json.success ? 'Cambios guardados.' : 'Error al guardar.');
            form.appendChild(alertEl);

            if (json.success) {
                setTimeout(() => {
                    bootstrap.Modal.getInstance(editModalEl)?.hide();
                    loadUsers().then(renderUsers);
                }, 1500);
            } else {
                if (submitBtn) { submitBtn.disabled = false; submitBtn.textContent = submitBtn.dataset.originalText; }
            }

        } catch (err) {
            console.error('Submit error:', err);
            const alertEl = document.createElement('div');
            alertEl.className = 'alert alert-danger mt-3 edit-alert';
            alertEl.textContent = 'Error de conexión. Inténtalo de nuevo.';
            form.appendChild(alertEl);
            if (submitBtn) { submitBtn.disabled = false; submitBtn.textContent = submitBtn.dataset.originalText; }
        }
    });

    // =========================
    // 🗑️ DELETE MODAL — populate
    // =========================
    document.addEventListener('click', function (e) {

        const btn = e.target.closest('.btn-eliminar');
        if (!btn) return;

        const modal = document.querySelector('#deleteModal');
        if (!modal) return;

        modal.querySelector('[name="id"]').value        = btn.dataset.id;
        modal.querySelector('[name="nombre"]').value    = btn.dataset.nombre;
        modal.querySelector('[name="apellidos"]').value = btn.dataset.apellidos;
        modal.querySelector('[name="usuario"]').value   = btn.dataset.usuario;
        modal.querySelector('[name="dni"]').value       = btn.dataset.dni;
        modal.querySelector('[name="telefono"]').value  = btn.dataset.telefono;
        modal.querySelector('[name="email"]').value     = btn.dataset.email;
        modal.querySelector('[name="rol"]').value       = btn.dataset.rol;

        const fechaDelete = modal.querySelector('[name="fecha_nacimiento"]');
        if (fechaDelete) {
            fechaDelete.value = btn.dataset.fecha_nacimiento
                ? btn.dataset.fecha_nacimiento.split('T')[0]
                : '';
        }

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

    // =========================
    // 🗑️ DELETE MODAL — reset on close
    // =========================
    const deleteModalEl = document.querySelector('#deleteModal');

    deleteModalEl?.addEventListener('hidden.bs.modal', () => {
        const form = deleteModalEl.querySelector('form');
        form?.querySelector('.delete-alert')?.remove();
        const btn = form?.querySelector('button[type="submit"], button:not([type="button"])');
        if (btn && btn.dataset.originalText) {
            btn.disabled = false;
            btn.textContent = btn.dataset.originalText;
        }
    });

    // =========================
    // 🗑️ SUBMIT DELETE USER
    // =========================
    deleteModalEl?.querySelector('form')?.addEventListener('submit', async (e) => {

        e.preventDefault();

        const form = e.target;
        const data = new FormData(form);

        const submitBtn = form.querySelector('button[type="submit"], button:not([type="button"])');
        if (submitBtn) {
            submitBtn.dataset.originalText = submitBtn.textContent;
            submitBtn.disabled = true;
            submitBtn.textContent = 'Eliminando...';
        }

        form.querySelector('.delete-alert')?.remove();

        try {
            const res  = await fetch('/user/removeUser', { method: 'POST', body: data });
            const json = await res.json();

            const alertEl = document.createElement('div');
            alertEl.className = `alert mt-3 delete-alert ${json.success ? 'alert-success' : 'alert-danger'}`;
            alertEl.textContent = json.message ?? (json.success ? 'Usuario eliminado.' : 'Error al eliminar.');
            form.appendChild(alertEl);

            if (json.success) {
                setTimeout(() => {
                    bootstrap.Modal.getInstance(deleteModalEl)?.hide();
                    loadUsers().then(renderUsers);
                }, 1500);
            } else {
                if (submitBtn) { submitBtn.disabled = false; submitBtn.textContent = submitBtn.dataset.originalText; }
            }

        } catch (err) {
            console.error('Delete error:', err);
            const alertEl = document.createElement('div');
            alertEl.className = 'alert alert-danger mt-3 delete-alert';
            alertEl.textContent = 'Error de conexión. Inténtalo de nuevo.';
            form.appendChild(alertEl);
            if (submitBtn) { submitBtn.disabled = false; submitBtn.textContent = submitBtn.dataset.originalText; }
        }
    });

});