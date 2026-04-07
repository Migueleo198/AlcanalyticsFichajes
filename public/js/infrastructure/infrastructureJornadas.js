let fullTable = [];
let currentPage = 0;
const rowsPerPage = 5;

document.addEventListener('tableReady', () => {

    const TABLE = document.querySelector('table');
    const TBODY = TABLE.querySelector('tbody');

    // 🔥 IMPORTANT: reset state
    fullTable = Array.from(TBODY.querySelectorAll('tr'));
    currentPage = 0;

    setupPaginationUI(TABLE);
    renderPage();
});

function renderPage() {

    const TABLE = document.querySelector('table');
    const TBODY = TABLE.querySelector('tbody');

    const totalPages = Math.ceil(fullTable.length / rowsPerPage);

    TBODY.innerHTML = "";

    const start = currentPage * rowsPerPage;
    const end = start + rowsPerPage;

    fullTable.slice(start, end).forEach(row => {
        TBODY.appendChild(row);
    });

    updateUI(totalPages);
}

function setupPaginationUI(TABLE) {

    let wrapper = document.getElementById('paginationWrapper');

    if (wrapper) return; // ✅ prevent duplicates

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
                    <span class="page-link" id="pageIndicator">1</span>
                </li>

                <li class="page-item" id="nextBtn">
                    <button class="page-link">&raquo;</button>
                </li>
            </ul>
        </nav>
    `;

    TABLE.closest('.card').appendChild(wrapper);

    document.getElementById('prevBtn').addEventListener('click', () => {
        if (currentPage > 0) {
            currentPage--;
            renderPage();
        }
    });

    document.getElementById('nextBtn').addEventListener('click', () => {
        const totalPages = Math.ceil(fullTable.length / rowsPerPage);
        if (currentPage < totalPages - 1) {
            currentPage++;
            renderPage();
        }
    });
}

function updateUI(totalPages) {
    document.getElementById('pageIndicator').textContent =
        `${currentPage + 1} / ${totalPages}`;

    document.getElementById('prevBtn')
        .classList.toggle('disabled', currentPage === 0);

    document.getElementById('nextBtn')
        .classList.toggle('disabled', currentPage === totalPages - 1);
}