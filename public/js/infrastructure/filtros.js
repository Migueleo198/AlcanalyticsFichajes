document.addEventListener("DOMContentLoaded", function () {

    const buscador = document.getElementById("buscadorTabla");

    if (!buscador) return;

    buscador.addEventListener("input", function () {
        const filtro = this.value.toLowerCase().trim();
        const tablas = document.querySelectorAll("table");

        tablas.forEach(tabla => {
            const filas = tabla.querySelectorAll("tbody tr");
            let hasVisibleRows = false;

            filas.forEach(fila => {
                const textoFila = fila.textContent.toLowerCase();

                const match = textoFila.includes(filtro);
                fila.style.display = match ? "" : "none";

                if (match) hasVisibleRows = true;
            });

            // Optional: show "no results" row
            let noResultsRow = tabla.querySelector(".no-results");

            if (!hasVisibleRows) {
                if (!noResultsRow) {
                    const tbody = tabla.querySelector("tbody");
                    noResultsRow = document.createElement("tr");
                    noResultsRow.className = "no-results";
                    noResultsRow.innerHTML = `
                        <td colspan="100%" class="text-center text-muted">
                            No se encontraron resultados
                        </td>
                    `;
                    tbody.appendChild(noResultsRow);
                }
            } else {
                if (noResultsRow) noResultsRow.remove();
            }
        });
    });

});