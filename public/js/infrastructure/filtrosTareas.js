document.addEventListener("DOMContentLoaded", () => {

    const buscador = document.getElementById('buscadorTabla');
    const filterTitulo = document.getElementById('filterTitulo');
    const filterUsuario = document.getElementById('filterUsuario');
    const filterEstado = document.getElementById('filterEstado');
    const filterTipo = document.getElementById('filterTipo');
    const filterFecha = document.getElementById('filterFecha');
    const clearBtn = document.getElementById('clearFilters');

    function aplicarFiltros() {

        const { fullTable, setFilteredTable } = window.paginationData || {};

        if (!fullTable || !setFilteredTable) return;

        const texto = buscador?.value.toLowerCase().trim() || "";
        const titulo = filterTitulo?.value.toLowerCase().trim() || "";
        const usuario = filterUsuario?.value.toLowerCase().trim() || "";
        const estado = filterEstado?.value.toLowerCase().trim() || "";
        const tipo = filterTipo?.value.toLowerCase().trim() || "";
        const fecha = filterFecha?.value || "";

        const filtered = fullTable.filter(row => {

            const columns = row.querySelectorAll("td");
            if (!columns.length) return false;

            // Column mapping based on your table:
            // 0: #
            // 1: id_fichaje
            // 2: usuario
            // 3: titulo
            // 4: descripcion
            // 5: hora_inicio
            // 6: hora_fin
            // 7: tiempo_total
            // 8: estado
            // 9: fecha
            // 10: tipo
            // 11: acciones

            const usuarioTxt = columns[2]?.innerText.toLowerCase() || "";
            const tituloTxt = columns[3]?.innerText.toLowerCase() || "";
            const estadoTxt = columns[8]?.innerText.toLowerCase() || "";
            const fechaTxt = columns[9]?.innerText || "";
            const tipoTxt = columns[10]?.innerText.toLowerCase() || "";

            const rowText = row.textContent.toLowerCase();

            return (
                (texto === "" || rowText.includes(texto)) &&
                (titulo === "" || tituloTxt.includes(titulo)) &&
                (usuario === "" || usuarioTxt.includes(usuario)) &&
                (estado === "" || estadoTxt.includes(estado)) &&
                (tipo === "" || tipoTxt.includes(tipo)) &&
                (fecha === "" || fechaTxt.includes(fecha))
            );
        });

        setFilteredTable(filtered);
    }

    // =========================
    // EVENTS
    // =========================
    buscador?.addEventListener("input", aplicarFiltros);
    filterTitulo?.addEventListener("input", aplicarFiltros);
    filterUsuario?.addEventListener("input", aplicarFiltros);
    filterEstado?.addEventListener("change", aplicarFiltros);
    filterTipo?.addEventListener("input", aplicarFiltros);
    filterFecha?.addEventListener("change", aplicarFiltros);

    // =========================
    // CLEAR FILTERS
    // =========================
    clearBtn?.addEventListener("click", () => {

        if (buscador) buscador.value = "";
        if (filterTitulo) filterTitulo.value = "";
        if (filterUsuario) filterUsuario.value = "";
        if (filterEstado) filterEstado.value = "";
        if (filterTipo) filterTipo.value = "";
        if (filterFecha) filterFecha.value = "";

        aplicarFiltros();
    });

});