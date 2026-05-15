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

  // Notificaciones se inicializan fuera del guard (ver abajo)

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

// =============================================================
// 🔔 NOTIFICACIONES — sin guard, corre en TODAS las páginas
// =============================================================
document.addEventListener("DOMContentLoaded", async () => {

  const lista    = document.getElementById("listaNotificaciones");
  const contador = document.getElementById("contadorNotificaciones");

  if (!lista || !contador) return;

  // Estado de carga
  contador.textContent = "…";
  lista.innerHTML = `<li class="px-3 py-2 text-muted small">Cargando…</li>`;

  try {
    const res  = await fetch((typeof RUTA_URL !== "undefined" ? RUTA_URL : "") + "/Notificaciones/getNotificaciones");
    const data = await res.json();

    if (!data.success || !data.items) throw new Error("bad response");

    if (data.total === 0) {
      // Sin notificaciones
      contador.style.display = "none";
      lista.innerHTML = `
        <li class="px-3 py-3 text-center text-muted small">
          <i class="bi bi-check-circle me-1 text-success"></i>Sin notificaciones pendientes
        </li>`;
      return;
    }

    // Badge con total
    contador.style.display = "";
    contador.textContent   = data.total > 99 ? "99+" : data.total;

    // Cabecera
    lista.innerHTML = `
      <li class="px-3 pb-2 pt-1">
        <span class="fw-bold">Notificaciones</span>
        <span class="badge bg-danger ms-1">${data.total}</span>
      </li>
      <li><hr class="dropdown-divider my-0"></li>`;

    const colores = {
      danger:  { bg: "#fef2f2", color: "#b91c1c" },
      warning: { bg: "#fffbeb", color: "#92400e" },
      success: { bg: "#f0fdf4", color: "#14532d" },
      primary: { bg: "#eff6ff", color: "#1e40af" },
      info:    { bg: "#f0f9ff", color: "#0c4a6e" },
    };

    data.items.forEach(item => {
      const col = colores[item.tipo] || colores.primary;
      const href = (typeof RUTA_URL !== "undefined" ? RUTA_URL : "") + item.url;

      const li = document.createElement("li");
      li.innerHTML = `
        <a class="dropdown-item d-flex align-items-center gap-2 py-2 px-3 notif-item"
           href="${href}"
           style="border-left:3px solid ${col.color}; background:${col.bg};">
          <i class="bi ${item.icono} fs-5" style="color:${col.color};flex-shrink:0"></i>
          <span class="small flex-grow-1" style="color:#1f2937;">${item.mensaje}</span>
          <span class="badge rounded-pill" style="background:${col.color}; color:#fff;">${item.count}</span>
        </a>`;
      lista.appendChild(li);
    });

    // Pie con enlace de gestión
    const pieLi = document.createElement("li");
    pieLi.innerHTML = `
      <hr class="dropdown-divider my-0">
      <a class="dropdown-item text-center small text-primary py-2" href="${(typeof RUTA_URL !== "undefined" ? RUTA_URL : "")}/home/index">
        Ver todas
      </a>`;
    lista.appendChild(pieLi);

  } catch (err) {
    console.error("Error cargando notificaciones:", err);
    contador.textContent = "!";
    lista.innerHTML = `<li class="px-3 py-2 text-danger small">Error al cargar</li>`;
  }
});