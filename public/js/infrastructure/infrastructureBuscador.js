document.addEventListener("DOMContentLoaded", function () {

  const buscador = document.getElementById("buscadorTabla");
  const tbody    = document.getElementById("lista");

  if (!buscador || !tbody) return;

  // ─────────────────────────────────────────────
  // 📦 ROW CACHE
  // ─────────────────────────────────────────────
  let rowCache  = [];
  let ownReload = false;

  document.addEventListener("tableReady", () => {

    if (ownReload) {
      ownReload = false;
      return;
    }

    rowCache = Array.from(tbody.querySelectorAll("tr"))
      .filter(tr => tr.id !== "no-results-row")
      .map(tr => ({
        el: tr,
        text: tr.innerText.toLowerCase()
      }));
  });

  // ─────────────────────────────────────────────
  // 🔍 FILTER
  // ─────────────────────────────────────────────
  function filtrarTabla() {

    const filtro = (buscador.value ?? "")
      .toLowerCase()
      .trim();

    document.getElementById("no-results-row")?.remove();

    // ─────────────────────────────
    // RESTORE TABLE
    // ─────────────────────────────
    if (filtro === "") {

      tbody.innerHTML = "";

      rowCache.forEach(r => {

        r.el.style.display = "";

        tbody.appendChild(r.el);
      });

      ownReload = true;

      document.dispatchEvent(
        new Event("tableReady")
      );

      return;
    }

    // ─────────────────────────────
    // FILTER ACTIVE
    // ─────────────────────────────
    rowCache.forEach(r => {

      if (!tbody.contains(r.el)) {
        tbody.appendChild(r.el);
      }
    });

    let hayResultados = false;

    rowCache.forEach(r => {

      const match = r.text.includes(filtro);

      r.el.style.display = match ? "" : "none";

      if (match) {
        hayResultados = true;
      }
    });

    if (!hayResultados) {

      const empty = document.createElement("tr");

      empty.id = "no-results-row";

      empty.innerHTML = `
        <td colspan="100%" class="text-center text-muted py-3">
          Sin resultados
        </td>
      `;

      tbody.appendChild(empty);
    }
  }

  // ─────────────────────────────────────────────
  // 🔔 NOTIFICACIONES
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

    lista.innerHTML = `
      <li class="fw-bold mb-2">
        Notificaciones
      </li>
    `;

    notificaciones.forEach(n => {

      const li = document.createElement("li");

      li.className = "dropdown-item";
      li.textContent = n;

      lista.appendChild(li);
    });

    contador.textContent = notificaciones.length;
  }

  renderNotificaciones();

  // ─────────────────────────────────────────────
  // 🚗 MATRÍCULAS HELPERS
  // ─────────────────────────────────────────────
  function createMatriculaRow(value = "") {

    const wrapper = document.createElement("div");

    wrapper.className =
      "d-flex gap-2 mb-1 align-items-center matricula-row";

    const safe = (value ?? "").replace(/"/g, "&quot;");

    wrapper.innerHTML = `
      <input
        type="text"
        name="matriculas[]"
        class="form-control form-control-sm"
        value="${safe}"
        placeholder="Ej: 1234 ABC"
        maxlength="10"
      >

      <button
        type="button"
        class="btn btn-outline-danger btn-sm matricula-remove"
      >
        &times;
      </button>
    `;

    wrapper
      .querySelector(".matricula-remove")
      .addEventListener("click", () => wrapper.remove());

    return wrapper;
  }

  function renderMatriculas(container, matriculas = []) {

    container.innerHTML = "";

    const list = matriculas.length
      ? matriculas
      : [""];

    list.forEach(m => {

      container.appendChild(
        createMatriculaRow(m.matricula ?? m)
      );
    });

    const addBtn = document.createElement("button");

    addBtn.type = "button";
    addBtn.className =
      "btn btn-outline-secondary btn-sm mt-1";

    addBtn.textContent = "+ Añadir matrícula";

    addBtn.addEventListener("click", () => {

      container.insertBefore(
        createMatriculaRow(),
        addBtn
      );
    });

    container.appendChild(addBtn);
  }

  // ─────────────────────────────────────────────
  // 👤 PROFILE LOADER
  // ─────────────────────────────────────────────
  const PROFILE = document.querySelector(".profile");

  if (PROFILE) {
    PROFILE.addEventListener("click", loadProfile);
  }

  async function loadProfile() {

    const userId = PROFILE?.dataset?.userId;

    if (!userId) {
      console.error("User ID missing");
      return;
    }

    // ─────────────────────────────
    // CREATE MODAL
    // ─────────────────────────────
    if (!document.getElementById("profileModal")) {

      document.body.insertAdjacentHTML("beforeend", `
        <div class="modal fade" id="profileModal" tabindex="-1">

          <div class="modal-dialog">

            <div class="modal-content">

              <div class="modal-header">

                <h5 class="modal-title">
                  Mi Perfil
                </h5>

                <button
                  type="button"
                  class="btn-close"
                  data-bs-dismiss="modal"
                ></button>

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

                    <label>Matrículas</label>

                    <div
                      id="matriculasContainer"
                      class="border rounded p-2 bg-light"
                    ></div>

                  </div>

                  <!-- 🔥 ROL SOLO LECTURA -->
                  <div class="mb-2">

                    <label>Rol</label>

                    <input
                      type="text"
                      class="form-control"
                      id="profileRolVisible"
                      disabled
                    >

                    <input
                      type="hidden"
                      name="rol"
                      id="profileRolHidden"
                    >

                  </div>

                  <button class="btn btn-primary w-100">
                    Guardar
                  </button>

                </form>

              </div>

            </div>

          </div>

        </div>
      `);
    }

    const modalEl = document.getElementById("profileModal");

    const modal =
      bootstrap.Modal.getOrCreateInstance(modalEl);

    try {

      const res = await fetch(
        `/user/getUser?id=${userId}`,
        {
          headers: {
            Accept: "application/json"
          }
        }
      );

      const data = await res.json();

      if (!data.success) return;

      const user =
        data.data ?? {};

      const matriculas =
        data.data.matriculas ?? [];

      const form =
        modalEl.querySelector("form");

      form.querySelector('[name="id"]').value =
        user.id_usuario ?? "";

      form.querySelector('[name="nombre"]').value =
        user.nombre ?? "";

      form.querySelector('[name="apellidos"]').value =
        user.apellidos ?? "";

      form.querySelector('[name="usuario"]').value =
        user.nombre_usuario ?? "";

      form.querySelector('[name="dni"]').value =
        user.dni ?? "";

      form.querySelector('[name="telefono"]').value =
        user.telefono ?? "";

      form.querySelector('[name="email"]').value =
        user.email ?? "";

      // 🔥 ROL SOLO VISUAL
      document.getElementById("profileRolVisible").value =
        user.rol ?? "";

      document.getElementById("profileRolHidden").value =
        user.rol ?? "";

      const contenedor =
        document.getElementById("matriculasContainer");

      if (contenedor) {
        renderMatriculas(contenedor, matriculas);
      }

    } catch (err) {

      console.error(err);
    }

    modal.show();

    modalEl.addEventListener(
      "hidden.bs.modal",
      () => modalEl.remove(),
      { once: true }
    );
  }

  // ─────────────────────────────────────────────
  // 🔌 WIRE UP
  // ─────────────────────────────────────────────
  buscador.addEventListener(
    "input",
    filtrarTabla
  );

});