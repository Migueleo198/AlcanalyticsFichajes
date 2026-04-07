const buscador = document.getElementById("buscadorTabla");

const filterNombre = document.getElementById("filterNombre");
const filterUsuario = document.getElementById("filterUsuario");
const filterDni = document.getElementById("filterDni");
const filterRol = document.getElementById("filterRol");

const clearBtn = document.getElementById("clearFilters");

const filas = () => document.querySelectorAll("#lista tr");

function aplicarFiltros() {

  const textoBusqueda = buscador?.value.toLowerCase() || "";
  const nombre = filterNombre?.value.toLowerCase() || "";
  const usuario = filterUsuario?.value.toLowerCase() || "";
  const dni = filterDni?.value.toLowerCase() || "";
  const rol = filterRol?.value.toLowerCase() || "";

  filas().forEach(fila => {

    const columnas = fila.querySelectorAll("td");
    if (columnas.length === 0) return;

    const nombreTxt = columnas[1].innerText.toLowerCase();
    const usuarioTxt = columnas[2].innerText.toLowerCase();
    const dniTxt = columnas[3].innerText.toLowerCase();
    const rolTxt = columnas[6].innerText.toLowerCase();

    // 🔎 buscador global
    const coincideBusqueda =
      nombreTxt.includes(textoBusqueda) ||
      usuarioTxt.includes(textoBusqueda) ||
      dniTxt.includes(textoBusqueda);

    // 🎯 filtros específicos
    const coincideNombre = nombre === "" || nombreTxt.includes(nombre);
    const coincideUsuario = usuario === "" || usuarioTxt.includes(usuario);
    const coincideDni = dni === "" || dniTxt.includes(dni);
    const coincideRol = rol === "" || rolTxt.includes(rol);

    if (
      coincideBusqueda &&
      coincideNombre &&
      coincideUsuario &&
      coincideDni &&
      coincideRol
    ) {
      fila.style.display = "";
    } else {
      fila.style.display = "none";
    }

  });
}

// eventos
buscador?.addEventListener("input", aplicarFiltros);
filterNombre?.addEventListener("input", aplicarFiltros);
filterUsuario?.addEventListener("input", aplicarFiltros);
filterDni?.addEventListener("input", aplicarFiltros);
filterRol?.addEventListener("change", aplicarFiltros);

// limpiar filtros
clearBtn?.addEventListener("click", () => {

  if (filterNombre) filterNombre.value = "";
  if (filterUsuario) filterUsuario.value = "";
  if (filterDni) filterDni.value = "";
  if (filterRol) filterRol.value = "";
  if (buscador) buscador.value = "";

  aplicarFiltros();
});