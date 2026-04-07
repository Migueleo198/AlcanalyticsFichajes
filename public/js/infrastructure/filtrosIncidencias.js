document.addEventListener("DOMContentLoaded", () => {

    const buscador = document.getElementById('buscadorTabla');
    const filterIdFichaje = document.getElementById('filterIdFichaje');
    const filterMensaje = document.getElementById('filterMensaje');
    const filterEstado = document.getElementById('filterEstado');
    const filterFecha = document.getElementById('filterFecha');
    const clearBtn = document.getElementById('clearFilters');

    function aplicarFiltros() {

        const { fullTable, setFilteredTable } = window.paginationData || {};
        if (!fullTable || !setFilteredTable) return;

        const texto = buscador?.value.toLowerCase().trim() || "";
        const idFichaje = filterIdFichaje?.value.toLowerCase().trim() || "";
        const mensaje = filterMensaje?.value.toLowerCase().trim() || "";
        const estado = filterEstado?.value.toLowerCase().trim() || "";
        const fecha = filterFecha?.value || "";

        const filtered = fullTable.filter(row => {

            const columns = row.querySelectorAll("td");
            if (!columns.length) return false;

            // Column mapping:
            // 0: #
            // 1: id_fichaje
            // 2: mensaje
            // 3: respuesta
            // 4: estado
            // 5: fecha

            const idTxt = columns[1]?.innerText.toLowerCase() || "";
            const mensajeTxt = columns[2]?.innerText.toLowerCase() || "";
            const estadoTxt = columns[4]?.innerText.toLowerCase() || "";
            const fechaTxt = columns[5]?.innerText || "";

            const rowText = row.textContent.toLowerCase();

            return (
                (texto === "" || rowText.includes(texto)) &&
                (idFichaje === "" || idTxt.includes(idFichaje)) &&
                (mensaje === "" || mensajeTxt.includes(mensaje)) &&
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
    filterIdFichaje?.addEventListener("input", aplicarFiltros);
    filterMensaje?.addEventListener("input", aplicarFiltros);
    filterEstado?.addEventListener("change", aplicarFiltros);
    filterFecha?.addEventListener("change", aplicarFiltros);

    // =========================
    // CLEAR FILTERS
    // =========================
    clearBtn?.addEventListener("click", () => {

        if (buscador) buscador.value = "";
        if (filterIdFichaje) filterIdFichaje.value = "";
        if (filterMensaje) filterMensaje.value = "";
        if (filterEstado) filterEstado.value = "";
        if (filterFecha) filterFecha.value = "";

        aplicarFiltros();
    });

});