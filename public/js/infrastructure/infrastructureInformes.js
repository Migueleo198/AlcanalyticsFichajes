document.addEventListener("DOMContentLoaded", () => {

    const btnGenerar   = document.getElementById("btnGenerarInforme");
    const btnLimpiar   = document.getElementById("btnLimpiarFiltros");
    const btnSemanal   = document.getElementById("btnSemanal");
    const btnMensual   = document.getElementById("btnMensual");
    const inputDesde   = document.getElementById("desde");
    const inputHasta   = document.getElementById("hasta");
    const selectUsuarios = document.getElementById("usuario");
    const diasLabel    = document.getElementById("diasSeleccionados");
    const infoModo     = null; // reservado

    // ── Si no es admin, forzar selección propia ─────────────────
    if (USER_ROL !== "Administrador" && selectUsuarios) {
        selectUsuarios.multiple = false;
        selectUsuarios.size = 1;
        Array.from(selectUsuarios.options).forEach(opt => {
            opt.selected = opt.value === String(USER_ID);
        });
    }

    // ── Contador de días seleccionados ───────────────────────────
    function actualizarDias() {
        if (!diasLabel) return;
        const d = inputDesde?.value;
        const h = inputHasta?.value;
        if (!d || !h || d > h) {
            diasLabel.textContent = d && h && d > h ? '⚠ La fecha fin debe ser posterior al inicio' : '';
            diasLabel.className = 'text-danger mt-1 d-block small';
            return;
        }
        const diff = Math.round((new Date(h) - new Date(d)) / 86400000) + 1;
        diasLabel.textContent = `${diff} día${diff !== 1 ? 's' : ''} seleccionado${diff !== 1 ? 's' : ''}`;
        diasLabel.className = 'text-muted mt-1 d-block small';
    }
    inputDesde?.addEventListener('change', actualizarDias);
    inputHasta?.addEventListener('change', actualizarDias);

    // ── Atajos de rango ──────────────────────────────────────────
    btnSemanal?.addEventListener("click", () => {
        const hoy   = new Date();
        const lunes = new Date(hoy);
        lunes.setDate(hoy.getDate() - ((hoy.getDay() + 6) % 7)); // lunes de esta semana
        setRango(formatDate(lunes), formatDate(hoy));
    });

    btnMensual?.addEventListener("click", () => {
        const hoy       = new Date();
        const inicioMes = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
        setRango(formatDate(inicioMes), formatDate(hoy));
    });

    // ── Generar ──────────────────────────────────────────────────
    btnGenerar?.addEventListener("click", generarInforme);
    btnLimpiar?.addEventListener("click", limpiarFiltros);
});

function setRango(desde, hasta) {
    document.getElementById("desde").value = desde;
    document.getElementById("hasta").value = hasta;
    document.getElementById("desde").dispatchEvent(new Event('change'));
    document.getElementById("hasta").dispatchEvent(new Event('change'));
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
    const select = document.getElementById("usuario");
    if (!desde || !hasta) {
        mostrarError("Selecciona el rango de fechas antes de generar el informe.");
        return;
    }
    if (desde > hasta) {
        mostrarError("La fecha de inicio no puede ser posterior a la fecha fin.");
        return;
    }

    let usuarios = [];
    if (USER_ROL === "Administrador") {
        usuarios = Array.from(select.selectedOptions).map(o => o.value);
        // Sin selección → todos los empleados
        if (usuarios.length === 0) {
            usuarios = Array.from(select.options).map(o => o.value);
        }
    } else {
        usuarios = [String(USER_ID)];
    }

    const params = new URLSearchParams({ desde, hasta });
    usuarios.forEach(u => params.append('usuarios[]', u));

    const url = `${RUTA_URL}/Informes/generar?${params.toString()}`;
    window.open(url, "_blank");
}

function limpiarFiltros() {
    document.getElementById("desde").value = "";
    document.getElementById("hasta").value = "";
    const diasLabel = document.getElementById("diasSeleccionados");
    if (diasLabel) diasLabel.textContent = "";

    const select = document.getElementById("usuario");
    if (USER_ROL === "Administrador") {
        Array.from(select.options).forEach(opt => opt.selected = false);
    } else {
        Array.from(select.options).forEach(opt => {
            opt.selected = opt.value === String(USER_ID);
        });
    }
}

function mostrarError(msg) {
    // Reutiliza o crea un alert de error dentro del formulario
    const card = document.querySelector('.card-custom.p-4');
    let alerta = card?.querySelector('.informe-error');
    if (!alerta) {
        alerta = document.createElement('div');
        alerta.className = 'alert alert-warning alert-dismissible fade show informe-error mt-3';
        alerta.innerHTML = `<i class="bi bi-exclamation-triangle me-2"></i><span></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
        card?.appendChild(alerta);
    }
    alerta.querySelector('span').textContent = msg;
    alerta.classList.add('show');
}
