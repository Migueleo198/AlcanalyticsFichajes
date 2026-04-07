document.addEventListener("DOMContentLoaded", () => {

    const buscador = document.getElementById('buscadorTabla');
    const filterEmpleado = document.getElementById('filterEmpleado');
    const filterMotivo = document.getElementById('filterMotivo');
    const filterEstado = document.getElementById('filterEstado');
    const filterFechaInicio = document.getElementById('filterFechaInicio');
    const clearBtn = document.getElementById('clearFilters');

    function aplicarFiltros() {
        console.log(window.paginationData);
        const { fullTable, setFilteredTable } = window.paginationData || {};
        if (!fullTable || !setFilteredTable) return;

        const texto = buscador?.value.toLowerCase().trim() || "";
        const empleado = filterEmpleado?.value.toLowerCase().trim() || "";
        const motivo = filterMotivo?.value.toLowerCase().trim() || "";
        const estado = filterEstado?.value.toLowerCase().trim() || "";
        const fechaInicio = filterFechaInicio?.value || "";

        const filtered = fullTable.filter(row => {

            const columns = row.querySelectorAll("td");
            if (!columns.length) return false;

            // Column mapping:
            // 0: Empleado
            // 1: Motivo
            // 2: Fecha inicio
            // 3: Fecha fin
            // 4: Estado

            const empleadoTxt = columns[0]?.innerText.toLowerCase() || "";
            const motivoTxt = columns[1]?.innerText.toLowerCase() || "";
            const fechaInicioTxt = columns[2]?.innerText || "";
            const estadoTxt = columns[4]?.innerText.toLowerCase() || "";

            const rowText = row.textContent.toLowerCase();

            return (
                (texto === "" || rowText.includes(texto)) &&
                (empleado === "" || empleadoTxt.includes(empleado)) &&
                (motivo === "" || motivoTxt.includes(motivo)) &&
                (estado === "" || estadoTxt.includes(estado)) &&
                (fechaInicio === "" || fechaInicioTxt.includes(fechaInicio))
            );
        });

        setFilteredTable(filtered);
    }

    // =========================
    // EVENTS
    // =========================
    buscador?.addEventListener("input", aplicarFiltros);
    filterEmpleado?.addEventListener("input", aplicarFiltros);
    filterMotivo?.addEventListener("input", aplicarFiltros);
    filterEstado?.addEventListener("change", aplicarFiltros);
    filterFechaInicio?.addEventListener("change", aplicarFiltros);

    // =========================
    // CLEAR FILTERS
    // =========================
    clearBtn?.addEventListener("click", () => {

        if (buscador) buscador.value = "";
        if (filterEmpleado) filterEmpleado.value = "";
        if (filterMotivo) filterMotivo.value = "";
        if (filterEstado) filterEstado.value = "";
        if (filterFechaInicio) filterFechaInicio.value = "";

        aplicarFiltros();
    });

});