document.addEventListener('DOMContentLoaded', () => {

    const buscador = document.getElementById('buscadorTabla');
    const filterEmpleado = document.getElementById('filterEmpleado');
    const filterEstado = document.getElementById('filterEstado');
    const filterFecha = document.getElementById('filterFecha');
    const clearBtn = document.getElementById('clearFilters');

    function aplicarFiltros() {

        const { fullTable, setFilteredTable } = window.paginationData || {};

        if (!fullTable || !setFilteredTable) return;

        const texto = buscador?.value.toLowerCase().trim() || "";
        const empleado = filterEmpleado?.value.toLowerCase().trim() || "";
        const estado = filterEstado?.value.toLowerCase().trim() || "";
        const fecha = filterFecha?.value || "";

        const filtered = fullTable.filter(row => {

            const columns = row.querySelectorAll("td");
            if (!columns.length) return false;

            const empleadoTxt = columns[1]?.innerText.toLowerCase() || "";
            const fechaTxt = columns[2]?.innerText || "";
            const estadoTxt = columns[5]?.innerText.toLowerCase() || "";

            const rowText = row.textContent.toLowerCase();

            return (
                (texto === "" || rowText.includes(texto)) &&
                (empleado === "" || empleadoTxt.includes(empleado)) &&
                (estado === "" || estadoTxt.includes(estado)) &&
                (fecha === "" || fechaTxt.includes(fecha))
            );
        });

        setFilteredTable(filtered);
    }

    // =========================
    // EVENTS
    // =========================
    buscador?.addEventListener("input", aplicarFiltros);
    filterEmpleado?.addEventListener("input", aplicarFiltros);
    filterEstado?.addEventListener("change", aplicarFiltros);
    filterFecha?.addEventListener("change", aplicarFiltros);

    // =========================
    // CLEAR FILTERS
    // =========================
    clearBtn?.addEventListener("click", () => {

        if (buscador) buscador.value = "";
        if (filterEmpleado) filterEmpleado.value = "";
        if (filterEstado) filterEstado.value = "";
        if (filterFecha) filterFecha.value = "";

        aplicarFiltros();
    });

});