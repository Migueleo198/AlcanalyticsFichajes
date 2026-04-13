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
          const texto = fila.textContent.toLowerCase();
          fila.style.display = texto.includes(filtro) ? "" : "none";
        });
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
  // 👤 PROFILE MODAL
  // =========================
  const PROFILE = document.querySelector('.profile');

  if (!PROFILE) return;

  PROFILE.addEventListener('click', loadProfile);

  async function loadProfile() {

    const userId = PROFILE?.dataset?.userId;

    if (!userId) {
      console.error("User ID missing");
      return;
    }

    // =========================
    // Crear modal si no existe
    // =========================
    if (!document.getElementById('profileModal')) {
      document.body.insertAdjacentHTML('beforeend', `
        <div class="modal fade" id="profileModal" tabindex="-1">
          <div class="modal-dialog">
            <div class="modal-content">

              <div class="modal-header">
                <h5 class="modal-title">Mi Perfil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>

              <div class="modal-body">
                <form action="/user/editUser" method="POST">

                  <input type="hidden" name="id">

                  <div class="mb-2">
                    <label>Nombre</label>
                    <input name="nombre" class="form-control">
                  </div>

                  <div class="mb-2">
                    <label>Apellidos</label>
                    <input name="apellidos" class="form-control">
                  </div>

                  <div class="mb-2">
                    <label>Usuario</label>
                    <input name="usuario" class="form-control">
                  </div>

                  <div class="mb-2">
                    <label>DNI</label>
                    <input name="dni" class="form-control">
                  </div>

                  <div class="mb-2">
                    <label>Teléfono</label>
                    <input name="telefono" class="form-control">
                  </div>

                  <div class="mb-2">
                    <label>Email</label>
                    <input name="email" class="form-control">
                  </div>

                  <div class="mb-3">
                    <label class="form-label">Matrículas</label>
                    <div id="matriculasContainer" class="border rounded p-2 bg-light"></div>
                  </div>

                  <div class="mb-2">
                    <label>Rol</label>
                    <select name="rol" class="form-select">
                      <option value="Administrador">Administrador</option>
                      <option value="Trabajador">Trabajador</option>
                    </select>
                  </div>

                  <button class="btn btn-primary w-100">Guardar</button>

                </form>
              </div>

            </div>
          </div>
        </div>
      `);
    }

    const modalEl = document.getElementById('profileModal');
    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);

    try {
      const res = await fetch(`/user/getUser?id=${userId}`, {
        headers: { 'Accept': 'application/json' }
      });

      const data = await res.json();

      if (!data.success) {
        console.error(data.message);
        return;
      }

      const user = data.data || {};
      const matriculas = data.data.matriculas || [];

      const form = modalEl.querySelector('form');

      // =========================
      // USER FIELDS (FIX ID CORRECTO)
      // =========================
      form.querySelector('[name="id"]').value = user.id_usuario ?? '';
      form.querySelector('[name="nombre"]').value = user.nombre ?? '';
      form.querySelector('[name="apellidos"]').value = user.apellidos ?? '';
      form.querySelector('[name="usuario"]').value = user.nombre_usuario ?? '';
      form.querySelector('[name="dni"]').value = user.dni ?? '';
      form.querySelector('[name="telefono"]').value = user.telefono ?? '';
      form.querySelector('[name="email"]').value = user.email ?? '';
      form.querySelector('[name="rol"]').value = user.rol ?? '';

      // =========================
      // MATRÍCULAS (FROM BACKEND)
      // =========================
      const contenedor = document.getElementById('matriculasContainer');

      if (contenedor) {
        if (matriculas.length === 0) {
          contenedor.innerHTML = `<span class="text-muted">Sin matrículas registradas</span>`;
        } else {
          contenedor.innerHTML = matriculas.map(m => `
            <div class="d-flex justify-content-between border-bottom py-1">
              <span> ${m.matricula}</span>
            </div>
          `).join('');
        }
      }

    } catch (err) {
      console.error("Fetch error:", err);
    }

    modal.show();

    modalEl.addEventListener('hidden.bs.modal', () => {
      modalEl.remove();
    }, { once: true });
  }

});