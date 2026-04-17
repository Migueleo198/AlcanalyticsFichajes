document.addEventListener("DOMContentLoaded", () => {

    const btnGenerar = document.getElementById("btnGenerarInforme");
    const btnLimpiar = document.getElementById("btnLimpiarFiltros");
    const btnSemanal = document.getElementById("btnSemanal");
    const btnMensual = document.getElementById("btnMensual");
    const selectUsuarios = document.getElementById("usuario");

    if (USER_ROL !== "Administrador" && selectUsuarios) {
        const option = selectUsuarios.querySelector("option");
        selectUsuarios.multiple = false;
        selectUsuarios.size = 1;

        Array.from(selectUsuarios.options).forEach(opt => {
            opt.selected = opt.value === USER_ID;
        });
    }

    if (btnGenerar) {
        btnGenerar.addEventListener("click", generarInforme);
    }

    if (btnLimpiar) {
        btnLimpiar.addEventListener("click", limpiarFiltros);
    }

    if (btnSemanal) {
        btnSemanal.addEventListener("click", setSemanal);
    }

    if (btnMensual) {
        btnMensual.addEventListener("click", setMensual);
    }
});

function setRango(desde, hasta) {
    document.getElementById("desde").value = desde;
    document.getElementById("hasta").value = hasta;
}

function setSemanal() {
    const hoy = new Date();
    const hace7 = new Date();
    hace7.setDate(hoy.getDate() - 7);
    setRango(formatDate(hace7), formatDate(hoy));
}

function setMensual() {
    const hoy = new Date();
    const inicioMes = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
    setRango(formatDate(inicioMes), formatDate(hoy));
}

function formatDate(date) {
    const y = date.getFullYear();
    const m = String(date.getMonth() + 1).padStart(2, '0');
    const d = String(date.getDate()).padStart(2, '0');
    return `${y}-${m}-${d}`;
}

function generarInforme() {

    const desde = document.getElementById("desde").value;
    const hasta = document.getElementById("hasta").value;
    const selectUsuarios = document.getElementById("usuario");

    let usuarios = [];

    if (USER_ROL === "Administrador") {
        usuarios = Array.from(selectUsuarios.selectedOptions).map(o => o.value);
    } else {
        usuarios = [USER_ID];
    }

    if (!desde || !hasta) {
        alert("Selecciona fechas");
        return;
    }

    if (desde > hasta) {
        alert("Rango de fechas incorrecto");
        return;
    }

    let url = `${RUTA_URL}/Informes/generar?desde=${desde}&hasta=${hasta}`;

    usuarios.forEach(u => {
        url += `&usuarios[]=${u}`;
    });

    window.open(url, "_blank");
}

function limpiarFiltros() {
    document.getElementById("desde").value = "";
    document.getElementById("hasta").value = "";

    const select = document.getElementById("usuario");

    if (USER_ROL === "Administrador") {
        Array.from(select.options).forEach(opt => opt.selected = false);
    } else {
        Array.from(select.options).forEach(opt => opt.selected = opt.value === USER_ID);
    }
}