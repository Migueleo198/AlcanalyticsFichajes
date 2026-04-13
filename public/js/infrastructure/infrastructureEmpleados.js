async function loadUsers() {
    try {
        const RESPONSE = await fetch('/user/getUsers');

        if (!RESPONSE.ok) {
            throw new Error('Error con respuesta ' + RESPONSE.status);
        }

        const DATA = await RESPONSE.json();
        return DATA;

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

            let matriculasHTML = '';

            if (usuario.matriculas && usuario.matriculas.length > 0) {

                matriculasHTML = `
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-dark dropdown-toggle"
                                type="button"
                                data-bs-toggle="dropdown">
                                Ver (${usuario.matriculas.length})
                        </button>

                        <ul class="dropdown-menu">
                            ${usuario.matriculas.map(m => `
                                <li>
                                    <span class="dropdown-item">
                                         ${m.matricula}
                                    </span>
                                </li>
                            `).join('')}
                        </ul>
                    </div>
                `;

            } else {
                matriculasHTML = `
                    <span class="text-muted">Sin matrículas</span>
                `;
            }

            // 👉 FORMATEO FECHA
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

                <!-- NUEVO -->
                <td>${fechaFormateada}</td>

                <td>
                    ${matriculasHTML}
                </td>

                <td>
                    <span class="badge bg-primary">
                        ${usuario.rol}
                    </span>
                </td>

                <td>
                    <button 
                        class="btn btn-outline-secondary btn-sm px-3 btn-editar"
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
                        data-fecha="${usuario.fecha_nacimiento || ''}"
                    >
                        ✏️
                    </button>
                </td>

                <td>
                    <button 
                        class="btn btn-outline-secondary btn-sm px-3 btn-eliminar"
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
                        data-fecha="${usuario.fecha_nacimiento || ''}"
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
// EDIT MODAL HANDLER
// =========================
document.addEventListener('click', function(e) {

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

    // 👉 NUEVO
    modal.querySelector('[name="fecha_nacimiento"]').value = btn.dataset.fecha;

});


// =========================
// DELETE MODAL HANDLER
// =========================
document.addEventListener('click', function(e) {

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

    // 👉 NUEVO
    modal.querySelector('[name="fecha_nacimiento"]').value = btn.dataset.fecha;
});