document.addEventListener("DOMContentLoaded", () => {

    const buscador = document.getElementById("buscadorTablaF");
    const filterEstado = document.getElementById("filterEstado");
    const filterFecha = document.getElementById("filterFecha");
    const clearBtn = document.getElementById("clearFilters");
    const tabla = document.getElementById("tablaFichajes");

    if (!tabla) return;

    const tbody = tabla.querySelector("tbody");
    const filas = Array.from(tbody.querySelectorAll("tr"));

    function aplicarFiltros() {

        const texto = buscador?.value.toLowerCase().trim() || "";
        const estado = filterEstado?.value.toLowerCase().trim() || "";
        const fecha = filterFecha?.value || "";

        const filtradas = filas.filter(row => {

            const cols = row.querySelectorAll("td");

            const fechaTxt = cols[0]?.innerText.trim() || "";
            const usuarioTxt = cols[1]?.innerText.toLowerCase() || "";
            const estadoTxt = cols[4]?.innerText.toLowerCase() || "";

            return (
                (texto === "" || usuarioTxt.includes(texto)) &&
                (estado === "" || estadoTxt.includes(estado)) &&
                (fecha === "" || fechaTxt === fecha)
            );
        });

        tbody.innerHTML = "";
        filtradas.forEach(row => tbody.appendChild(row));

        document.dispatchEvent(new Event("tableReady"));
    }

    buscador?.addEventListener("input", aplicarFiltros);
    filterEstado?.addEventListener("change", aplicarFiltros);
    filterFecha?.addEventListener("input", aplicarFiltros);

    clearBtn?.addEventListener("click", () => {

        if (buscador) buscador.value = "";
        if (filterEstado) filterEstado.value = "";
        if (filterFecha) filterFecha.value = "";

        tbody.innerHTML = "";
        filas.forEach(row => tbody.appendChild(row));

        document.dispatchEvent(new Event("tableReady"));
    });

});