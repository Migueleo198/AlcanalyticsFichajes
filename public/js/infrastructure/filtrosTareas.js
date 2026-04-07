const buscador = document.getElementById("buscadorTabla");

const filterTitulo = document.getElementById("filterTitulo");
const filterUsuario = document.getElementById("filterUsuario");
const filterEstado = document.getElementById("filterEstado");
const filterTipo = document.getElementById("filterTipo");
const filterFecha = document.getElementById("filterFecha");

const clearBtn = document.getElementById("clearFilters");

const filas = () => document.querySelectorAll("#lista tr");

function aplicarFiltros() {

  const textoBusqueda = buscador?.value.toLowerCase() || "";
  const titulo = filterTitulo?.value.toLowerCase() || "";
  const usuario = filterUsuario?.value.toLowerCase() || "";
  const estado = filterEstado?.value.toLowerCase() || "";
  const tipo = filterTipo?.value.toLowerCase() || "";
  const fecha = filterFecha?.value || "";

  filas().forEach(fila => {

    const columnas = fila.querySelectorAll("td");
    if (columnas.length === 0) return;

    const idFichaje = columnas[1].innerText.toLowerCase();
    const usuarioTxt = columnas[2].innerText.toLowerCase();
    const tituloTxt = columnas[3].innerText.toLowerCase();
    const estadoTxt = columnas[8].innerText.toLowerCase();
    const fechaTxt = columnas[9].innerText;

    const tipoTxt = columnas[10].innerText.toLowerCase();

    // 🔎 búsqueda global
    const coincideBusqueda =
      tituloTxt.includes(textoBusqueda) ||
      usuarioTxt.includes(textoBusqueda) ||
      idFichaje.includes(textoBusqueda);

    // 🎯 filtros específicos
    const coincideTitulo = titulo === "" || tituloTxt.includes(titulo);
    const coincideUsuario = usuario === "" || usuarioTxt.includes(usuario);
    const coincideEstado = estado === "" || estadoTxt.includes(estado);
    const coincideTipo = tipo === "" || tipoTxt.includes(tipo);

    const coincideFecha = fecha === "" || fechaTxt.includes(fecha);

    if (
      coincideBusqueda &&
      coincideTitulo &&
      coincideUsuario &&
      coincideEstado &&
      coincideTipo &&
      coincideFecha
    ) {
      fila.style.display = "";
    } else {
      fila.style.display = "none";
    }

  });
}

// eventos
buscador?.addEventListener("input", aplicarFiltros);
filterTitulo?.addEventListener("input", aplicarFiltros);
filterUsuario?.addEventListener("input", aplicarFiltros);
filterEstado?.addEventListener("change", aplicarFiltros);
filterTipo?.addEventListener("input", aplicarFiltros);
filterFecha?.addEventListener("change", aplicarFiltros);

// limpiar filtros
clearBtn?.addEventListener("click", () => {
  if (filterTitulo) filterTitulo.value = "";
  if (filterUsuario) filterUsuario.value = "";
  if (filterEstado) filterEstado.value = "";
  if (filterTipo) filterTipo.value = "";
  if (filterFecha) filterFecha.value = "";
  if (buscador) buscador.value = "";

  aplicarFiltros();
});