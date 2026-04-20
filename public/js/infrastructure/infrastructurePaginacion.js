document.addEventListener('tableReady', () => {

    const TABLE = document.querySelector('table');
    if (!TABLE) return;

    const TBODY = TABLE.querySelector('tbody');
    const CONTAINER = TABLE.closest('.card');

    const buscador = document.getElementById('buscadorTabla');

    const rowsPerPage = 10;

    let fullTable = [];
    let filteredTable = [];
    let currentPage = 0;
    let totalPages = 1;

    function snapshot() {
        return Array.from(TBODY.querySelectorAll('tr')).map(r => r.cloneNode(true));
    }

    // =========================
    // CREATE PAGINATION IF MISSING
    // =========================
    function ensurePaginationUI() {

        let wrapper = document.getElementById('paginationWrapper');

        if (!wrapper) {
            wrapper = document.createElement('div');
            wrapper.id = 'paginationWrapper';
            wrapper.className = 'd-flex justify-content-center mt-3';

            wrapper.innerHTML = `
                <nav>
                    <ul class="pagination mb-0">

                        <li class="page-item" id="prevBtn">
                            <button class="page-link">&laquo;</button>
                        </li>

                        <li class="page-item disabled">
                            <span class="page-link" id="pageIndicator">1 / 1</span>
                        </li>

                        <li class="page-item" id="nextBtn">
                            <button class="page-link">&raquo;</button>
                        </li>

                    </ul>
                </nav>
            `;

            CONTAINER.appendChild(wrapper);
        }

        return wrapper;
    }

    // =========================
    // CORE STATE UPDATE
    // =========================
    function recalc() {
        totalPages = Math.max(1, Math.ceil(filteredTable.length / rowsPerPage));

        if (currentPage >= totalPages) currentPage = totalPages - 1;
        if (currentPage < 0) currentPage = 0;
    }

    function render() {

        recalc();

        TBODY.innerHTML = "";

        const start = currentPage * rowsPerPage;
        const end = start + rowsPerPage;

        filteredTable
            .slice(start, end)
            .forEach(row => TBODY.appendChild(row.cloneNode(true)));

        const indicator = document.getElementById('pageIndicator');
        if (indicator) {
            indicator.textContent = `${currentPage + 1} de ${totalPages}`;
        }

        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');

        if (prevBtn) prevBtn.classList.toggle('disabled', currentPage === 0);
        if (nextBtn) nextBtn.classList.toggle('disabled', currentPage >= totalPages - 1);
    }

    // =========================
    // FILTER
    // =========================
    function applyFilter() {

        const value = (buscador?.value || "").toLowerCase().trim();

        fullTable = snapshot();

        filteredTable = fullTable.filter(row =>
            row.textContent.toLowerCase().includes(value)
        );

        currentPage = 0;

        render();
    }

    // =========================
    // INIT
    // =========================
    function init() {

        ensurePaginationUI();

        fullTable = snapshot();
        filteredTable = [...fullTable];

        attachEvents();
        render();
    }

    // =========================
    // EVENTS (SAFE BINDING)
    // =========================
    function attachEvents() {

        buscador?.addEventListener('input', applyFilter);

        document.addEventListener('click', (e) => {

            if (e.target.closest('#nextBtn')) {
                recalc();
                if (currentPage < totalPages - 1) {
                    currentPage++;
                    render();
                }
            }

            if (e.target.closest('#prevBtn')) {
                recalc();
                if (currentPage > 0) {
                    currentPage--;
                    render();
                }
            }
        });
    }

    init();
});