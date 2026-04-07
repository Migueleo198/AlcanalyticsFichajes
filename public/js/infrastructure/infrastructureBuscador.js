document.addEventListener("DOMContentLoaded", function () {

    // =========================
    // 🔍 BUSCADOR TABLAS
    // =========================
    const buscador = document.getElementById("buscadorTabla");

    if (buscador) {
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
    }

    // =========================
    // 🔔 NOTIFICACIONES
    // =========================
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

    renderNotificaciones();

    // =========================
    // 👤 PROFILE MODAL
    // =========================
    const PROFILE = document.querySelector('.profile');

    if (PROFILE) {
        PROFILE.addEventListener('click', loadProfile);
    }

    function loadProfile() {

        const PROFILE = document.querySelector('.profile');
        const userId = PROFILE?.dataset.userId;

        if (!userId) {
            console.error("User ID not found in data-user-id");
            return;
        }

        // Crear modal si no existe
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

                      <input type="hidden" name="id">

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
        const MODAL = bootstrap.Modal.getOrCreateInstance(MODAL_ELEMENT);

        // Fetch user data
        fetch(`/user/getUser?id=${userId}`)
            .then(res => res.json())
            .then(response => {
                if (!response.success) {
                    console.error("Error fetching user");
                    return;
                }

                const user = response.data;

                const form = MODAL_ELEMENT.querySelector('form');

                form.querySelector('[name="id"]').value = user.id || '';
                form.querySelector('[name="nombre"]').value = user.nombre || '';
                form.querySelector('[name="apellidos"]').value = user.apellidos || '';
                form.querySelector('[name="usuario"]').value = user.nombre_usuario || '';
                form.querySelector('[name="dni"]').value = user.dni || '';
                form.querySelector('[name="telefono"]').value = user.telefono || '';
                form.querySelector('[name="email"]').value = user.email || '';
                form.querySelector('[name="rol"]').value = user.rol || '';
            })
            .catch(err => console.error("Fetch error:", err));

        // Cleanup modal on close
        MODAL_ELEMENT.addEventListener('hidden.bs.modal', () => {
            MODAL_ELEMENT.remove();
        }, { once: true });

        MODAL.show();
    }

});