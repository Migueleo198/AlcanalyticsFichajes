document.addEventListener("DOMContentLoaded", function () {

  const buscador = document.getElementById("buscadorTabla");
  const tbody    = document.getElementById("lista");

  // ─────────────────────────────────────────────
  // 📦  ROW CACHE
  // ─────────────────────────────────────────────
  let rowCache  = [];
  let ownReload = false;

  document.addEventListener("tableReady", () => {
    if (ownReload) { ownReload = false; return; }

    if (!tbody) return;

    rowCache = Array.from(tbody.querySelectorAll("tr"))
      .filter(tr => tr.id !== "no-results-row")
      .map(tr => ({ el: tr, text: tr.innerText.toLowerCase() }));
  });

  // ─────────────────────────────────────────────
  // 🔍  FILTER
  // ─────────────────────────────────────────────
  if (buscador && tbody) {
    buscador.addEventListener("input", filtrarTabla);
  }

  function filtrarTabla() {
    const filtro = (buscador.value ?? "").toLowerCase().trim();

    document.getElementById("no-results-row")?.remove();

    if (filtro === "") {
      tbody.innerHTML = "";
      rowCache.forEach(r => {
        r.el.style.display = "";
        tbody.appendChild(r.el);
      });
      ownReload = true;
      document.dispatchEvent(new Event("tableReady"));
      return;
    }

    rowCache.forEach(r => {
      if (!tbody.contains(r.el)) tbody.appendChild(r.el);
    });

    let hayResultados = false;

    rowCache.forEach(r => {
      const match = r.text.includes(filtro);
      r.el.style.display = match ? "" : "none";
      if (match) hayResultados = true;
    });

    if (!hayResultados) {
      const empty = document.createElement("tr");
      empty.id = "no-results-row";
      empty.innerHTML = `<td colspan="100%" class="text-center text-muted py-3">Sin resultados</td>`;
      tbody.appendChild(empty);
    }
  }

  // ─────────────────────────────────────────────
  // 🔔  NOTIFICACIONES
  // ─────────────────────────────────────────────
  const notificaciones = [
    "Nuevo fichaje registrado",
    "Contrato por vencer",
    "Actualización completada"
  ];

  function renderNotificaciones() {
    const lista    = document.getElementById("listaNotificaciones");
    const contador = document.getElementById("contadorNotificaciones");
    if (!lista || !contador) return;

    lista.innerHTML = '<li class="fw-bold mb-2">Notificaciones</li>';
    notificaciones.forEach(n => {
      const li = document.createElement("li");
      li.className   = "dropdown-item";
      li.textContent = n;
      lista.appendChild(li);
    });

    contador.textContent = notificaciones.length;
  }

  renderNotificaciones();

  // ─────────────────────────────────────────────
  // 🚗  MATRÍCULAS HELPERS
  // ─────────────────────────────────────────────
  function createMatriculaRow(value = "") {
    const wrapper = document.createElement("div");
    wrapper.className = "d-flex gap-2 mb-1 align-items-center matricula-row";
    const safe = (value ?? "").replace(/"/g, "&quot;");
    wrapper.innerHTML = `
      <input type="text" name="matriculas[]" class="form-control form-control-sm"
        value="${safe}" placeholder="Ej: 1234 ABC" maxlength="10">
      <button type="button" class="btn btn-outline-danger btn-sm matricula-remove">&times;</button>
    `;
    wrapper.querySelector(".matricula-remove").addEventListener("click", () => wrapper.remove());
    return wrapper;
  }

  function renderMatriculas(container, matriculas = []) {
    container.innerHTML = "";
    const list = matriculas.length ? matriculas : [""];
    list.forEach(m => container.appendChild(createMatriculaRow(m.matricula ?? m)));

    const addBtn = document.createElement("button");
    addBtn.type        = "button";
    addBtn.className   = "btn btn-outline-secondary btn-sm mt-1";
    addBtn.textContent = "+ Añadir matrícula";
    addBtn.addEventListener("click", () => container.insertBefore(createMatriculaRow(), addBtn));
    container.appendChild(addBtn);
  }

  // ─────────────────────────────────────────────
  // 📦  LOAD & RENDER USERS
  // ─────────────────────────────────────────────
  async function loadUsers() {
    try {
      const res = await fetch('/user/getUsers');
      if (!res.ok) throw new Error('Error con respuesta ' + res.status);
      return await res.json();
    } catch (err) {
      console.error(err.message);
    }
  }

  function renderUsers(response) {
    if (!tbody) return;

    tbody.innerHTML = '';

    if (response?.success && response.data.length > 0) {

      const fragment = document.createDocumentFragment();

      response.data.forEach(usuario => {

        const matriculas     = usuario.matriculas || [];
        const fechaFormateada = usuario.fecha_nacimiento
          ? new Date(usuario.fecha_nacimiento).toLocaleDateString()
          : '-';

        const matriculasHTML = matriculas.length > 0
          ? `<div class="dropdown">
               <button class="btn btn-sm btn-outline-dark dropdown-toggle" type="button"
                       data-bs-toggle="dropdown">Ver (${matriculas.length})</button>
               <ul class="dropdown-menu">
                 ${matriculas.map(m => `<li><span class="dropdown-item">🚗 ${m.matricula}</span></li>`).join('')}
               </ul>
             </div>`
          : `<span class="text-muted">Sin matrículas</span>`;

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
          <td><span class="badge bg-primary">${usuario.rol}</span></td>
          <td>
            <button class="btn btn-outline-primary btn-sm btn-editar"
              data-bs-toggle="modal" data-bs-target="#editModal"
              data-id="${usuario.id_usuario}"
              data-nombre="${usuario.nombre}"
              data-apellidos="${usuario.apellidos}"
              data-usuario="${usuario.nombre_usuario}"
              data-dni="${usuario.dni}"
              data-telefono="${usuario.telefono}"
              data-email="${usuario.email}"
              data-rol="${usuario.rol}"
              data-fecha_nacimiento="${usuario.fecha_nacimiento || ''}"
              data-matriculas='${JSON.stringify(matriculas)}'>
              <i class="bi bi-pencil-square"></i>
            </button>
            <button class="btn btn-outline-danger btn-sm btn-eliminar"
              data-bs-toggle="modal" data-bs-target="#deleteModal"
              data-id="${usuario.id_usuario}"
              data-nombre="${usuario.nombre}"
              data-apellidos="${usuario.apellidos}"
              data-usuario="${usuario.nombre_usuario}"
              data-dni="${usuario.dni}"
              data-telefono="${usuario.telefono}"
              data-email="${usuario.email}"
              data-rol="${usuario.rol}"
              data-fecha_nacimiento="${usuario.fecha_nacimiento || ''}"
              data-matriculas='${JSON.stringify(matriculas)}'>
              <i class="bi bi-trash"></i>
            </button>
          </td>
        `;

        fragment.appendChild(tr);
      });

      tbody.appendChild(fragment);

    } else {
      tbody.innerHTML = `
        <tr>
          <td colspan="10" class="text-center">No hay empleados registrados</td>
        </tr>
      `;
    }

    document.dispatchEvent(new Event('tableReady'));
  }

  if (tbody) {
    loadUsers().then(renderUsers);
  }

  // ─────────────────────────────────────────────
  // ➕  ADD MODAL
  // ─────────────────────────────────────────────
  const addModalEl = document.querySelector('#addModal');

  addModalEl?.addEventListener('hidden.bs.modal', () => {
    const form = addModalEl.querySelector('form');
    form?.querySelector('.add-alert')?.remove();
    const btn = form?.querySelector('button[type="submit"], button:not([type])');
    if (btn?.dataset.originalText) {
      btn.disabled    = false;
      btn.textContent = btn.dataset.originalText;
    }
  });

  addModalEl?.querySelector('form')?.addEventListener('submit', async (e) => {
    e.preventDefault();

    const form = e.target;
    const data = new FormData(form);

    const fechaInput = form.querySelector('[name="fecha_nacimiento"]');
    if (fechaInput) data.set('fecha_nacimiento', fechaInput.value?.trim() || '');

    const submitBtn = form.querySelector('button[type="submit"], button:not([type])');
    if (submitBtn) {
      submitBtn.dataset.originalText = submitBtn.textContent;
      submitBtn.disabled    = true;
      submitBtn.textContent = 'Guardando...';
    }

    form.querySelector('.add-alert')?.remove();

    try {
      const res  = await fetch('/user/addUser', { method: 'POST', body: data });
      const json = await res.json();

      const alertEl = document.createElement('div');
      alertEl.className   = `alert mt-3 add-alert ${json.success ? 'alert-success' : 'alert-danger'}`;
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
      alertEl.className   = 'alert alert-danger mt-3 add-alert';
      alertEl.textContent = 'Error de conexión. Inténtalo de nuevo.';
      form.appendChild(alertEl);
      if (submitBtn) { submitBtn.disabled = false; submitBtn.textContent = submitBtn.dataset.originalText; }
    }
  });

  // ─────────────────────────────────────────────
  // ✏️  EDIT MODAL — populate
  // ─────────────────────────────────────────────
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
    const select     = modal.querySelector('#editMatriculasSelect');
    const input      = modal.querySelector('#editMatriculaInput');

    if (select) {
      select.innerHTML = '';
      matriculas.forEach(m => {
        const option       = document.createElement("option");
        option.value       = m.matricula;
        option.textContent = m.matricula;
        option.selected    = true;
        select.appendChild(option);
      });
      if (input) select.onchange = () => { input.value = select.value; };
    }
  });

  // ─────────────────────────────────────────────
  // ✏️  EDIT MODAL — reset on close
  // ─────────────────────────────────────────────
  const editModalEl = document.querySelector('#editModal');

  editModalEl?.addEventListener('hidden.bs.modal', () => {
    const form = editModalEl.querySelector('form');
    form?.querySelector('.edit-alert')?.remove();
    const btn = form?.querySelector('button[type="submit"], button:not([type])');
    if (btn?.dataset.originalText) {
      btn.disabled    = false;
      btn.textContent = btn.dataset.originalText;
    }
  });

  // ─────────────────────────────────────────────
  // 💾  SAVE MATRICULA UI
  // ─────────────────────────────────────────────
  document.getElementById('saveMatriculaBtn')?.addEventListener('click', () => {
    const select = document.getElementById('editMatriculasSelect');
    const input  = document.getElementById('editMatriculaInput');
    if (!select || !input?.value.trim()) return;

    const option = select.selectedOptions[0];
    if (option) {
      option.value       = input.value.trim();
      option.textContent = input.value.trim();
    }
  });

  // ─────────────────────────────────────────────
  // 💾  SUBMIT EDIT USER
  // ─────────────────────────────────────────────
  editModalEl?.querySelector('form')?.addEventListener('submit', async (e) => {
    e.preventDefault();

    const form = e.target;
    const data = new FormData(form);

    const fechaInput = form.querySelector('[name="fecha_nacimiento"]');
    if (fechaInput) data.set('fecha_nacimiento', fechaInput.value?.trim() || '');

    const select = form.querySelector('#editMatriculasSelect');
    if (select) {
      data.delete('matriculas[]');
      Array.from(select.options).forEach(opt => data.append('matriculas[]', opt.value));
    }

    const submitBtn = form.querySelector('button[type="submit"], button:not([type])');
    if (submitBtn) {
      submitBtn.dataset.originalText = submitBtn.textContent;
      submitBtn.disabled    = true;
      submitBtn.textContent = 'Guardando...';
    }

    form.querySelector('.edit-alert')?.remove();

    try {
      const res  = await fetch('/user/editUser', { method: 'POST', body: data });
      const json = await res.json();

      const alertEl = document.createElement('div');
      alertEl.className   = `alert mt-3 edit-alert ${json.success ? 'alert-success' : 'alert-danger'}`;
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
      alertEl.className   = 'alert alert-danger mt-3 edit-alert';
      alertEl.textContent = 'Error de conexión. Inténtalo de nuevo.';
      form.appendChild(alertEl);
      if (submitBtn) { submitBtn.disabled = false; submitBtn.textContent = submitBtn.dataset.originalText; }
    }
  });

  // ─────────────────────────────────────────────
  // 🗑️  DELETE MODAL — populate
  // ─────────────────────────────────────────────
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
      container           = document.createElement('div');
      container.id        = 'deleteMatriculasContainer';
      container.className = 'mb-3';
      modal.querySelector('.modal-body').appendChild(container);
    }

    container.innerHTML = matriculas.length
      ? matriculas.map(m => `<div>🚗 ${m.matricula}</div>`).join('')
      : `<span class="text-muted">Sin matrículas</span>`;
  });

  // ─────────────────────────────────────────────
  // 🗑️  DELETE MODAL — reset on close
  // ─────────────────────────────────────────────
  const deleteModalEl = document.querySelector('#deleteModal');

  deleteModalEl?.addEventListener('hidden.bs.modal', () => {
    const form = deleteModalEl.querySelector('form');
    form?.querySelector('.delete-alert')?.remove();
    const btn = form?.querySelector('button[type="submit"], button:not([type="button"])');
    if (btn?.dataset.originalText) {
      btn.disabled    = false;
      btn.textContent = btn.dataset.originalText;
    }
  });

  // ─────────────────────────────────────────────
  // 🗑️  SUBMIT DELETE USER
  // ─────────────────────────────────────────────
  deleteModalEl?.querySelector('form')?.addEventListener('submit', async (e) => {
    e.preventDefault();

    const form = e.target;
    const data = new FormData(form);

    const submitBtn = form.querySelector('button[type="submit"], button:not([type="button"])');
    if (submitBtn) {
      submitBtn.dataset.originalText = submitBtn.textContent;
      submitBtn.disabled    = true;
      submitBtn.textContent = 'Eliminando...';
    }

    form.querySelector('.delete-alert')?.remove();

    try {
      const res  = await fetch('/user/removeUser', { method: 'POST', body: data });
      const json = await res.json();

      const alertEl = document.createElement('div');
      alertEl.className   = `alert mt-3 delete-alert ${json.success ? 'alert-success' : 'alert-danger'}`;
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
      alertEl.className   = 'alert alert-danger mt-3 delete-alert';
      alertEl.textContent = 'Error de conexión. Inténtalo de nuevo.';
      form.appendChild(alertEl);
      if (submitBtn) { submitBtn.disabled = false; submitBtn.textContent = submitBtn.dataset.originalText; }
    }
  });

  // ─────────────────────────────────────────────
  // 👤  PROFILE LOADER
  // ─────────────────────────────────────────────
  const PROFILE = document.querySelector(".profile");
  if (PROFILE) PROFILE.addEventListener("click", loadProfile);

  async function loadProfile() {
    const userId = PROFILE?.dataset?.userId;
    if (!userId) { console.error("User ID missing"); return; }

    if (!document.getElementById("profileModal")) {
      document.body.insertAdjacentHTML("beforeend", `
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
                    <input type="hidden" name="rol" id="profileRolHidden">
                    <select id="profileRolDisplay" class="form-select"
                            style="pointer-events:none;background-color:#e9ecef;opacity:1">
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

    const modalEl = document.getElementById("profileModal");
    const modal   = bootstrap.Modal.getOrCreateInstance(modalEl);

    try {
      const res  = await fetch(`/user/getUser?id=${userId}`, { headers: { Accept: "application/json" } });
      const data = await res.json();
      if (!data.success) return;

      const user       = data.data ?? {};
      const matriculas = data.data.matriculas ?? [];
      const form       = modalEl.querySelector("form");

      form.querySelector('[name="id"]').value        = user.id_usuario     ?? "";
      form.querySelector('[name="nombre"]').value    = user.nombre         ?? "";
      form.querySelector('[name="apellidos"]').value = user.apellidos      ?? "";
      form.querySelector('[name="usuario"]').value   = user.nombre_usuario ?? "";
      form.querySelector('[name="dni"]').value       = user.dni            ?? "";
      form.querySelector('[name="telefono"]').value  = user.telefono       ?? "";
      form.querySelector('[name="email"]').value     = user.email          ?? "";

      const rol = user.rol ?? "";
      document.getElementById("profileRolHidden").value  = rol;
      document.getElementById("profileRolDisplay").value = rol;

      const contenedor = document.getElementById("matriculasContainer");
      if (contenedor) renderMatriculas(contenedor, matriculas);
    } catch (err) {
      console.error(err);
    }

    modal.show();
    modalEl.addEventListener("hidden.bs.modal", () => modalEl.remove(), { once: true });
  }

});