document.addEventListener("DOMContentLoaded", function () {

  const buscador = document.getElementById("buscadorTabla");

  function filtrarTabla() {
    const filtro = (buscador?.value || "").toLowerCase().trim();
    const filas = document.querySelectorAll("#lista tr");

    let hayResultados = false;

    filas.forEach(fila => {

      if (fila.id === "no-results-row") return;

     
      fila.style.display = "";

      const texto = fila.innerText.toLowerCase();
      const match = texto.includes(filtro);

      if (filtro === "") {
        fila.style.display = "";
        hayResultados = true;
        return;
      }

      if (match) {
        fila.style.display = "";
        hayResultados = true;
      } else {
        fila.style.display = "none";
      }
    });

    // =========================
    // SIN RESULTADOS CONTROLADO
    // =========================
    const tbody = document.getElementById("lista");
    if (!tbody) return;

    let emptyRow = document.getElementById("no-results-row");

    if (!hayResultados && filtro !== "" && filas.length > 0) {

      if (!emptyRow) {
        emptyRow = document.createElement("tr");
        emptyRow.id = "no-results-row";
        emptyRow.innerHTML = `
          <td colspan="100%" class="text-center text-muted py-3">
            Sin resultados
          </td>
        `;
        tbody.appendChild(emptyRow);
      }

    } else {
      if (emptyRow) emptyRow.remove();
    }
  }

  if (buscador) {
    buscador.addEventListener("input", () => {
      requestAnimationFrame(filtrarTabla);
    });
  }



  
  const tablaBody = document.getElementById("lista");

  if (tablaBody) {
    const observer = new MutationObserver(() => {
      if (buscador?.value) {
        filtrarTabla();
      }
    });

    observer.observe(tablaBody, {
      childList: true,
      subtree: true
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
  // 👤 PROFILE MODAL (SAFE)
  // =========================
  const PROFILE = document.querySelector('.profile');

  if (PROFILE) {
    PROFILE.addEventListener('click', loadProfile);
  }

  // =========================
  // 🚗 MATRÍCULAS HELPERS
  // =========================
  function createMatriculaRow(value = '') {
    const wrapper = document.createElement('div');
    wrapper.className = 'd-flex gap-2 mb-1 align-items-center matricula-row';

    wrapper.innerHTML = `
      <input
        type="text"
        name="matriculas[]"
        class="form-control form-control-sm"
        value="${value.replace(/"/g, '&quot;')}"
        placeholder="Ej: 1234 ABC"
        maxlength="10"
      >
      <button type="button" class="btn btn-outline-danger btn-sm matricula-remove">&times;</button>
    `;

    wrapper.querySelector('.matricula-remove').addEventListener('click', () => {
      wrapper.remove();
    });

    return wrapper;
  }

  function renderMatriculas(container, matriculas = []) {
    container.innerHTML = '';

    if (matriculas.length === 0) {
      container.appendChild(createMatriculaRow());
    } else {
      matriculas.forEach(m => {
        container.appendChild(createMatriculaRow(m.matricula ?? m));
      });
    }

    const addBtn = document.createElement('button');
    addBtn.type = 'button';
    addBtn.className = 'btn btn-outline-secondary btn-sm mt-1';
    addBtn.textContent = '+ Añadir matrícula';

    addBtn.addEventListener('click', () => {
      container.insertBefore(createMatriculaRow(), addBtn);
    });

    container.appendChild(addBtn);
  }

  async function loadProfile() {

    const userId = PROFILE?.dataset?.userId;

    if (!userId) {
      console.error("User ID missing");
      return;
    }

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

                  <div class="mb-2"><label>Nombre</label><input name="nombre" class="form-control"></div>
                  <div class="mb-2"><label>Apellidos</label><input name="apellidos" class="form-control"></div>
                  <div class="mb-2"><label>Usuario</label><input name="usuario" class="form-control"></div>
                  <div class="mb-2"><label>DNI</label><input name="dni" class="form-control"></div>
                  <div class="mb-2"><label>Teléfono</label><input name="telefono" class="form-control"></div>
                  <div class="mb-2"><label>Email</label><input name="email" class="form-control"></div>

                  <div class="mb-3">
                    <label>Matrículas</label>
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

      if (!data.success) return;

      const user = data.data || {};
      const matriculas = data.data.matriculas || [];

      const form = modalEl.querySelector('form');

      form.querySelector('[name="id"]').value = user.id_usuario ?? '';
      form.querySelector('[name="nombre"]').value = user.nombre ?? '';
      form.querySelector('[name="apellidos"]').value = user.apellidos ?? '';
      form.querySelector('[name="usuario"]').value = user.nombre_usuario ?? '';
      form.querySelector('[name="dni"]').value = user.dni ?? '';
      form.querySelector('[name="telefono"]').value = user.telefono ?? '';
      form.querySelector('[name="email"]').value = user.email ?? '';
      form.querySelector('[name="rol"]').value = user.rol ?? '';

      const contenedor = document.getElementById('matriculasContainer');
      if (contenedor) {
        renderMatriculas(contenedor, matriculas);
      }

    } catch (err) {
      console.error(err);
    }

    modal.show();

    modalEl.addEventListener('hidden.bs.modal', () => {
      modalEl.remove();
    }, { once: true });
  }

});