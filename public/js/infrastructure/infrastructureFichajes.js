let estado = "no_iniciado";
let horaInicio = null;
let intervalo = null;

let pausaInicio = null;
let tiempoPausado = 0;

const fichaje = window.fichajeActivo;
const descansos = window.descansos || [];
let enDescanso = window.enDescanso;

const reloj = document.getElementById("reloj");
const estadoUI = document.getElementById("estado");

const btnIniciar = document.getElementById("btnIniciar");
const btnPausar = document.getElementById("btnPausar"); // único botón
const btnFinalizar = document.getElementById("btnFinalizar");

document.addEventListener("DOMContentLoaded", () => {

  if (fichaje) {
    horaInicio = fichaje.hora_entrada;

    descansos.forEach(d => {

      if (d.hora_inicio && d.hora_fin) {
        const inicio = new Date();
        const fin = new Date();

        const [hi, mi, si] = d.hora_inicio.split(":");
        const [hf, mf, sf] = d.hora_fin.split(":");

        inicio.setHours(hi, mi, si);
        fin.setHours(hf, mf, sf);

        tiempoPausado += (fin - inicio);
      }

      // Detectar pausa activa
      if (d.hora_inicio && !d.hora_fin) {
        estado = "pausa";

        const [h, m, s] = d.hora_inicio.split(":");
        pausaInicio = new Date();
        pausaInicio.setHours(h, m, s);
      }

    });

    if (estado === "pausa") {
      enDescanso = true;
    } else {
      enDescanso = false;
    }

    if (enDescanso) {
      estado = "pausa";

      const ultima = descansos[descansos.length - 1];
      if (ultima && !ultima.hora_fin) {
        pausaInicio = new Date();
        const [h, m, s] = ultima.hora_inicio.split(":");
        pausaInicio.setHours(h, m, s);
      }

    } else {
      estado = "trabajando";
    }

    iniciarContador();
  }

  actualizarUI();
});

function iniciarContador() {

  clearInterval(intervalo);

  intervalo = setInterval(() => {

    if (!horaInicio) return;

    const ahora = new Date();

    const [h, m, s] = horaInicio.split(":");
    const inicio = new Date();
    inicio.setHours(h, m, s);

    let tiempo = ahora - inicio;

    tiempo -= tiempoPausado;

    if (estado === "pausa" && pausaInicio) {
      tiempo -= (ahora - pausaInicio);
    }

    const totalSeg = Math.floor(tiempo / 1000);

    const hFinal = String(Math.floor(totalSeg / 3600)).padStart(2, '0');
    const mFinal = String(Math.floor((totalSeg % 3600) / 60)).padStart(2, '0');
    const sFinal = String(totalSeg % 60).padStart(2, '0');

    reloj.textContent = `${hFinal}:${mFinal}:${sFinal}`;

  }, 1000);
}

function actualizarUI() {

  btnIniciar.disabled = false;
  btnPausar.disabled = true;
  btnFinalizar.disabled = true;

  // limpiar clases bootstrap dinámicas
  btnPausar.classList.remove("btn-warning", "btn-primary");

  if (!fichaje && estado === "no_iniciado") {
    estadoUI.textContent = "No iniciado";
  }

  if (estado === "trabajando") {
    estadoUI.textContent = "Trabajando";
    btnIniciar.disabled = true;
    btnPausar.disabled = false;
    btnFinalizar.disabled = false;

    btnPausar.textContent = "Pausar";
    btnPausar.classList.add("btn-warning"); // 🟡
  }

  if (estado === "pausa") {
    estadoUI.textContent = "En descanso";
    btnIniciar.disabled = true;
    btnPausar.disabled = false;
    btnFinalizar.disabled = false;

    btnPausar.textContent = "Reanudar";
    btnPausar.classList.add("btn-primary"); // 🔵
  }
}

function api(url, callback = null) {

  fetch(url, { method: 'POST' })
    .then(res => res.text())
    .then(text => {

      try {
        const data = JSON.parse(text);
        if (callback) callback(data);
      } catch (e) {
        console.error("NO ES JSON:", text);
      }

    })
    .catch(err => console.error(err));
}


// ======================
// EVENTOS BOTONES
// ======================

btnIniciar.onclick = () => {
  api(RUTA_URL + '/Fichaje/iniciar', () => location.reload());
};

btnPausar.onclick = () => {

  if (estado === "trabajando") {
    // PAUSAR
    pausaInicio = new Date();
    estado = "pausa";

    api(RUTA_URL + '/Fichaje/pausar');

  } else if (estado === "pausa") {
    // REANUDAR
    if (pausaInicio) {
      tiempoPausado += (new Date() - pausaInicio);
      pausaInicio = null;
    }

    estado = "trabajando";

    api(RUTA_URL + '/Fichaje/reanudar');
  }

  actualizarUI();
};

btnFinalizar.onclick = () => {
  api(RUTA_URL + '/Fichaje/finalizar', () => location.reload());
};