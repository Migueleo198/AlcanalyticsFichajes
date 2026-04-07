document.addEventListener("DOMContentLoaded", () => {

    const btnGenerar = document.getElementById("btnGenerarInforme");
    const btnLimpiar = document.getElementById("btnLimpiarFiltros");

    if (btnGenerar) {
        btnGenerar.addEventListener("click", generarInforme);
    }

    if (btnLimpiar) {
        btnLimpiar.addEventListener("click", limpiarFiltros);
    }

});

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

function limpiarFiltros() {
    document.getElementById("desde").value = "";
    document.getElementById("hasta").value = "";
    document.getElementById("usuario").value = "";
}