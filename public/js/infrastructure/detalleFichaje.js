document.addEventListener("DOMContentLoaded", () => {

  const filas = document.querySelectorAll("tbody tr");

  filas.forEach(fila => {

    const inicio = fila.querySelector("td:nth-child(1)").textContent.trim();
    const finTd = fila.querySelector(".hora-fin");
    const duracionTd = fila.querySelector(".duracion");

    // ======================
    // SI ESTÁ EN CURSO
    // ======================
    if (finTd.textContent.trim() === "En curso") {

      const [h, m, s] = inicio.split(":");

      const inicioDate = new Date();
      inicioDate.setHours(h, m, s);

      setInterval(() => {

        const ahora = new Date();
        const diff = ahora - inicioDate;

        const minutos = Math.floor(diff / 60000);

        duracionTd.textContent = minutos + " min";

      }, 60000); // cada minuto

    }

  });

});