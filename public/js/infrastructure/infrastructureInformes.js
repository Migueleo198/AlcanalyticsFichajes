document.addEventListener("DOMContentLoaded", () => {

    console.log("📊 infrastructureInformes cargado");

    const btnGenerar = document.getElementById("btnGenerarInforme");
    const btnLimpiar = document.getElementById("btnLimpiarFiltros");
    const btnSemanal = document.getElementById("btnSemanal");
    const btnMensual = document.getElementById("btnMensual");

    // =========================
    // EVENTOS
    // =========================

    if (btnGenerar) {
        btnGenerar.addEventListener("click", generarInforme);
    }

    if (btnLimpiar) {
        btnLimpiar.addEventListener("click", limpiarFiltros);
    }

    if (btnSemanal) {
        btnSemanal.addEventListener("click", () => {
            setSemanal();
        });
    }

    if (btnMensual) {
        btnMensual.addEventListener("click", () => {
            setMensual();
        });
    }

});

// =========================
// UTILIDADES FECHAS
// =========================

function setRango(desde, hasta) {
    document.getElementById("desde").value = desde;
    document.getElementById("hasta").value = hasta;
}

function setSemanal() {
    const hoy = new Date();
    const hace7 = new Date();

    hace7.setDate(hoy.getDate() - 7);

    setRango(
        formatDate(hace7),
        formatDate(hoy)
    );
}

function setMensual() {
    const hoy = new Date();
    const inicioMes = new Date(hoy.getFullYear(), hoy.getMonth(), 1);

    setRango(
        formatDate(inicioMes),
        formatDate(hoy)
    );
}

function formatDate(date) {
    return date.toISOString().split("T")[0];
}

// =========================
// GENERAR INFORME
// =========================

function generarInforme() {

    const desde = document.getElementById("desde").value;
    const hasta = document.getElementById("hasta").value;
    const usuario = document.getElementById("usuario").value;

    if (!desde || !hasta) {
        alert("Selecciona fechas");
        return;
    }

    let url = `${RUTA_URL}/Informes/generar?desde=${desde}&hasta=${hasta}`;

    if (usuario) {
        url += `&usuario=${usuario}`;
    }

    window.open(url, "_blank");
}

// =========================
// LIMPIAR FILTROS
// =========================

function limpiarFiltros() {
    document.getElementById("desde").value = "";
    document.getElementById("hasta").value = "";
    document.getElementById("usuario").value = "";
}