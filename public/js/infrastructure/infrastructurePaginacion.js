document.addEventListener('tableReady', () => {

    const TABLE = document.querySelector('table');
    const TBODY = TABLE.querySelector('tbody');
    const CONTAINER = TABLE.closest('.card');

    const buscador = document.getElementById('buscadorTabla');

    // Master data (ALL rows)
    const fullTable = Array.from(TBODY.querySelectorAll('tr'));

    let filteredTable = [...fullTable];

    let currentPage = 0;
    const rowsPerPage = 10;

    // =========================
    // EXPOSE DATA GLOBALLY (SAFE)
    // =========================
    window.paginationData = {
        get fullTable() {
            return fullTable;
        },
        get filteredTable() {
            return filteredTable;
        },
        setFilteredTable(newFiltered) {
            if (!Array.isArray(newFiltered)) return;
            filteredTable = newFiltered;
            currentPage = 0;
            renderPage(0);
        }
    };

    // =========================
    // PAGINATION UI
    // =========================
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
    // FILTER FUNCTION (UNCHANGED)
    // =========================
    function applyFilter() {
        const filtro = buscador.value.toLowerCase().trim();

        filteredTable = fullTable.filter(row => {
            return row.textContent.toLowerCase().includes(filtro);
        });

        currentPage = 0;
        renderPage(0);
    }

    // =========================
    // EVENTS
    // =========================
    buscador.addEventListener('input', applyFilter);

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
    renderPage(0);

});