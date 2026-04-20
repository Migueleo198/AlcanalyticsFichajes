document.addEventListener("DOMContentLoaded", () => {

    const buscador = document.getElementById("buscadorTablaF");
    const filterEstado = document.getElementById("filterEstado");
    const filterFecha = document.getElementById("filterFecha");
    const clearBtn = document.getElementById("clearFilters");
    const tabla = document.getElementById("tablaFichajes");

    if (!tabla) return;

    const tbody = tabla.querySelector("tbody");

    // ✅ CRITICAL FIX: immutable dataset (DO NOT MODIFY)
    const filasOriginales = Array.from(tbody.querySelectorAll("tr"));

    function aplicarFiltros() {

        const usuarioSeleccionado = buscador?.value || "";
        const estadoSeleccionado = filterEstado?.value.toLowerCase().trim() || "";
        const fechaSeleccionada = filterFecha?.value || "";

        const filtradas = filasOriginales.filter(row => {

            const cols = row.querySelectorAll("td");

            const fechaTxt = cols[0]?.innerText.trim() || "";
            const usuarioTxt = cols[1]?.innerText.toLowerCase().trim() || "";
            const estadoTxt = cols[4]?.innerText.toLowerCase().trim() || "";

            return (
                (usuarioSeleccionado === "" || usuarioTxt === usuarioSeleccionado) &&
                (estadoSeleccionado === "" || estadoTxt.includes(estadoSeleccionado)) &&
                (fechaSeleccionada === "" || fechaTxt === fechaSeleccionada)
            );
        });

        // ✅ SAFE REBUILD EVERY TIME
        tbody.innerHTML = "";
        filtradas.forEach(row => tbody.appendChild(row));

        document.dispatchEvent(new Event("tableReady"));
    }

    // =========================
    // EVENTS
    // =========================
    buscador?.addEventListener("change", aplicarFiltros);
    filterEstado?.addEventListener("change", aplicarFiltros);
    filterFecha?.addEventListener("input", aplicarFiltros);

    clearBtn?.addEventListener("click", () => {

        if (buscador) buscador.value = "";
        if (filterEstado) filterEstado.value = "";
        if (filterFecha) filterFecha.value = "";

        tbody.innerHTML = "";
        filasOriginales.forEach(row => tbody.appendChild(row));

        document.dispatchEvent(new Event("tableReady"));
    });

});