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

            const tr = document.createElement('tr');

            tr.innerHTML = `
                <td>${usuario.id_usuario}</td>
                <td>${usuario.nombre} ${usuario.apellidos}</td>
                <td>${usuario.nombre_usuario}</td>
                <td>${usuario.dni}</td>
                <td>${usuario.telefono}</td>
                <td>${usuario.email}</td>
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
                <td colspan="9" class="text-center">
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

});


// =========================
// DELETE MODAL HANDLER
// =========================
document.addEventListener('click', function(e) {

    const btn = e.target.closest('.btn-eliminar');
    if (!btn) return;

    const modal = document.querySelector('#deleteModal');

    // Set ID in hidden input
    modal.querySelector('[name="id"]').value = btn.dataset.id;

    modal.querySelector('[name="id"]').value = btn.dataset.id;
    modal.querySelector('[name="nombre"]').value = btn.dataset.nombre;
    modal.querySelector('[name="apellidos"]').value = btn.dataset.apellidos;
    modal.querySelector('[name="usuario"]').value = btn.dataset.usuario;
    modal.querySelector('[name="dni"]').value = btn.dataset.dni;
    modal.querySelector('[name="telefono"]').value = btn.dataset.telefono;
    modal.querySelector('[name="email"]').value = btn.dataset.email;
    modal.querySelector('[name="rol"]').value = btn.dataset.rol;
});