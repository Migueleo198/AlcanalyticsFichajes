document.addEventListener("DOMContentLoaded", () => {

  if (fichajeActivo && fichajeActivo.estado === "abierto") {

    // Crear alerta
    const aviso = document.createElement("div");
    aviso.className = "alert alert-info d-flex justify-content-between align-items-center";

    aviso.innerHTML = `
      <div>
        Tienes un fichaje activo 🔵 
        <strong id="contadorHome" class="ms-2">00:00:00</strong>
      </div>
      <a href="${RUTA_URL}/Fichaje/index" class="btn btn-sm btn-primary">
        Ir a fichaje
      </a>
    `;

    document.querySelector(".content")?.prepend(aviso);

    // 🔥 INICIAR CONTADOR
    iniciarContadorHome(fichajeActivo.hora_entrada);
  }

});


// ========================
// CONTADOR HOME
// ========================
function iniciarContadorHome(horaInicio) {

  const contador = document.getElementById("contadorHome");
  if (!contador) return;

  setInterval(() => {

    const ahora = new Date();

    const [h, m, s] = horaInicio.split(":");

    const inicio = new Date();
    inicio.setHours(h, m, s, 0);

    const diff = Math.floor((ahora - inicio) / 1000);

    const horas = String(Math.floor(diff / 3600)).padStart(2, '0');
    const minutos = String(Math.floor((diff % 3600) / 60)).padStart(2, '0');
    const segundos = String(diff % 60).padStart(2, '0');

    contador.textContent = `${horas}:${minutos}:${segundos}`;

  }, 1000);
}