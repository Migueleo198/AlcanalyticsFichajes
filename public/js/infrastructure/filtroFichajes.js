document.addEventListener('tableReady', () => {

    const TABLE = document.querySelector('table');
    const TBODY = TABLE.querySelector('tbody');
    const CONTAINER = TABLE.closest('.card');

    const buscador = document.getElementById('buscadorTabla');

    const filterEmpleado = document.getElementById('filterEmpleado');
    const filterEstado = document.getElementById('filterEstado');
    const filterFecha = document.getElementById('filterFecha');

    const clearBtn = document.getElementById('clearFilters');

    // =========================
    // DATA
    // =========================
    let fullTable = [];
    let filteredTable = [];

    function refreshFullTable() {
        fullTable = Array.from(TBODY.querySelectorAll('tr'));
        filteredTable = [...fullTable];
    }

    // =========================
    // PAGINATION
    // =========================
    let currentPage = 0;
    const rowsPerPage = 5;

    let paginationWrapper = document.getElementById('paginationWrapper');

    if (!paginationWrapper) {
        paginationWrapper = document.createElement('div');
        paginationWrapper.id = 'paginationWrapper';
        paginationWrapper.className = 'd-flex justify-content-center mt-3';

        paginationWrapper.innerHTML = `
            <nav>
                <ul class="pagination mb-0">
                    <li class="page-item" id="prevBtn">
                        <button class="page-link">&laquo;</button>
                    </li>

                    <li class="page-item disabled">
                        <span class="page-link" id="pageIndicator">1</span>
                    </li>

                    <li class="page-item" id="nextBtn">
                        <button class="page-link">&raquo;</button>
                    </li>
                </ul>
            </nav>
        `;

        CONTAINER.appendChild(paginationWrapper);
    }

    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const pageIndicator = document.getElementById('pageIndicator');

    // =========================
    // RENDER
    // =========================
    function renderPage(page) {

        const totalPages = Math.ceil(filteredTable.length / rowsPerPage) || 1;

        if (page < 0) page = 0;
        if (page >= totalPages) page = totalPages - 1;

        currentPage = page;

        TBODY.innerHTML = "";

        const start = page * rowsPerPage;
        const end = start + rowsPerPage;

        filteredTable.slice(start, end).forEach(row => {
            TBODY.appendChild(row);
        });

        pageIndicator.textContent = `${currentPage + 1} de ${totalPages}`;

        prevBtn.classList.toggle('disabled', currentPage === 0);
        nextBtn.classList.toggle('disabled', currentPage === totalPages - 1);
    }

    // =========================
    // FILTERS + SEARCH
    // =========================
    function applyFilters() {

        const texto = buscador?.value.toLowerCase() || "";
        const empleado = filterEmpleado?.value.toLowerCase() || "";
        const estado = filterEstado?.value.toLowerCase() || "";
        const fecha = filterFecha?.value || "";

        filteredTable = fullTable.filter(row => {

            const columnas = row.querySelectorAll("td");
            if (columnas.length === 0) return false;

            const empleadoTxt = columnas[1].innerText.toLowerCase();
            const fechaTxt = columnas[2].innerText;
            const entradaTxt = columnas[3].innerText.toLowerCase();
            const salidaTxt = columnas[4].innerText.toLowerCase();
            const estadoTxt = columnas[5].innerText.toLowerCase();

            // 🔎 buscador global
            const coincideBusqueda =
                row.textContent.toLowerCase().includes(texto);

            // 🎯 filtros
            const coincideEmpleado = empleado === "" || empleadoTxt.includes(empleado);
            const coincideEstado = estado === "" || estadoTxt.includes(estado);
            const coincideFecha = fecha === "" || fechaTxt.includes(fecha);

            return coincideBusqueda && coincideEmpleado && coincideEstado && coincideFecha;
        });

        currentPage = 0;
        renderPage(0);
    }

    // =========================
    // EVENTS
    // =========================
    buscador?.addEventListener('input', applyFilters);
    filterEmpleado?.addEventListener('input', applyFilters);
    filterEstado?.addEventListener('change', applyFilters);
    filterFecha?.addEventListener('change', applyFilters);

    clearBtn?.addEventListener('click', () => {

        if (buscador) buscador.value = "";
        if (filterEmpleado) filterEmpleado.value = "";
        if (filterEstado) filterEstado.value = "";
        if (filterFecha) filterFecha.value = "";

        filteredTable = [...fullTable];
        currentPage = 0;
        renderPage(0);
    });

    prevBtn.addEventListener('click', () => {
        if (currentPage > 0) renderPage(currentPage - 1);
    });

    nextBtn.addEventListener('click', () => {
        const totalPages = Math.ceil(filteredTable.length / rowsPerPage);
        if (currentPage < totalPages - 1) renderPage(currentPage + 1);
    });

    // =========================
    // INIT
    // =========================
    refreshFullTable();
    renderPage(0);

});