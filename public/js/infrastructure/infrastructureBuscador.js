document.addEventListener("DOMContentLoaded", function () {
    const buscador = document.getElementById("buscadorTabla");

    if (!buscador) return;

    buscador.addEventListener("keyup", function () {
        const filtro = this.value.toLowerCase();

        const tablas = document.querySelectorAll("table");

        tablas.forEach(tabla => {
            const filas = tabla.querySelectorAll("tbody tr");

            filas.forEach(fila => {
                const textoFila = fila.textContent.toLowerCase();

                fila.style.display = textoFila.includes(filtro) ? "" : "none";
            });
        });
    });
});



let notificaciones = [
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



const PROFILE = document.body.querySelector('.profile');

if (PROFILE) {
    PROFILE.addEventListener('click', loadProfile);
}

function loadProfile() {

    // Crear el modal solo si no existe
    if (!document.getElementById('profileModal')) {
        document.body.insertAdjacentHTML('beforeend', `
        <div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel">
          <div class="modal-dialog">
            <div class="modal-content">

              <div class="modal-header">
                <h5 class="modal-title" id="profileModalLabel">Mi Perfil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>

              <div class="modal-body">
                <form action="/user/editUser" method="POST">

                  <input type="hidden" name="id" id="edit_id">

                  <div class="mb-3">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="nombre" class="form-control" required>
                  </div>

                  <div class="mb-3">
                    <label class="form-label">Apellidos</label>
                    <input type="text" name="apellidos" class="form-control" required>
                  </div>

                  <div class="mb-3">
                    <label class="form-label">Usuario</label>
                    <input type="text" name="usuario" class="form-control" required>
                  </div>

                  <div class="mb-3">
                    <label class="form-label">DNI</label>
                    <input type="text" name="dni" class="form-control" required>
                  </div>

                  <div class="mb-3">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="telefono" class="form-control">
                  </div>

                  <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                  </div>

                  <div class="mb-3">
                    <label class="form-label">Rol</label>
                    <select name="rol" class="form-select" required>
                      <option value="">Seleccionar rol</option>
                      <option value="Administrador">Administrador</option>
                      <option value="Trabajador">Trabajador</option>
                    </select>
                  </div>

                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                  </div>

                </form>
              </div>

            </div>
          </div>
        </div>
        `);
    }

    const MODAL_ELEMENT = document.getElementById('profileModal');

    // Inicializar modal de Bootstrap correctamente
    const MODAL = bootstrap.Modal.getOrCreateInstance(MODAL_ELEMENT);

    // Eliminar del DOM al cerrarse (limpieza)
    MODAL_ELEMENT.addEventListener('hidden.bs.modal', () => {
        MODAL_ELEMENT.remove();
    }, { once: true });

    MODAL.show();
}


// Inicializar notificaciones
renderNotificaciones();