document.addEventListener("DOMContentLoaded", () => {

    const buscador = document.getElementById('buscadorTabla');
    const filterNombre = document.getElementById('filterNombre');
    const filterUsuario = document.getElementById('filterUsuario');
    const filterDni = document.getElementById('filterDni');
    const filterRol = document.getElementById('filterRol');
    const clearBtn = document.getElementById('clearFilters');

    function aplicarFiltros() {

        const { fullTable, setFilteredTable } = window.paginationData || {};

        if (!fullTable || !setFilteredTable) return;

        const texto = buscador?.value.toLowerCase().trim() || "";
        const nombre = filterNombre?.value.toLowerCase().trim() || "";
        const usuario = filterUsuario?.value.toLowerCase().trim() || "";
        const dni = filterDni?.value.toLowerCase().trim() || "";
        const rol = filterRol?.value.toLowerCase().trim() || "";

        const filtered = fullTable.filter(row => {

            const columns = row.querySelectorAll("td");
            if (!columns.length) return false;

            // Column mapping based on your table:
            // 0: ID
            // 1: Nombre
            // 2: Usuario
            // 3: DNI
            // 4: Teléfono
            // 5: Email
            // 6: Rol

            const nombreTxt = columns[1]?.innerText.toLowerCase() || "";
            const usuarioTxt = columns[2]?.innerText.toLowerCase() || "";
            const dniTxt = columns[3]?.innerText.toLowerCase() || "";
            const rolTxt = columns[6]?.innerText.toLowerCase() || "";

            const rowText = row.textContent.toLowerCase();

            return (
                (texto === "" || rowText.includes(texto)) &&
                (nombre === "" || nombreTxt.includes(nombre)) &&
                (usuario === "" || usuarioTxt.includes(usuario)) &&
                (dni === "" || dniTxt.includes(dni)) &&
                (rol === "" || rolTxt.includes(rol))
            );
        });

        setFilteredTable(filtered);
    }

    // =========================
    // EVENTS
    // =========================
    buscador?.addEventListener("input", aplicarFiltros);
    filterNombre?.addEventListener("input", aplicarFiltros);
    filterUsuario?.addEventListener("input", aplicarFiltros);
    filterDni?.addEventListener("input", aplicarFiltros);
    filterRol?.addEventListener("change", aplicarFiltros);

    // =========================
    // CLEAR FILTERS
    // =========================
    clearBtn?.addEventListener("click", () => {

        if (buscador) buscador.value = "";
        if (filterNombre) filterNombre.value = "";
        if (filterUsuario) filterUsuario.value = "";
        if (filterDni) filterDni.value = "";
        if (filterRol) filterRol.value = "";

        aplicarFiltros();
    });

});