

document.addEventListener('DOMContentLoaded', function () {

  const calendarEl = document.getElementById('calendar');

  const calendar = new FullCalendar.Calendar(calendarEl, {

    initialView: 'dayGridMonth',
    locale: 'es',

    selectable: true,

    // 📅 Selección de fechas
    select: function(info) {

      // 🔴 FullCalendar suma 1 día al end → lo corregimos
      let fechaFin = new Date(info.end);
      fechaFin.setDate(fechaFin.getDate() - 1);

      document.getElementById('fecha_inicio').value = info.startStr;
      document.getElementById('fecha_fin').value = fechaFin.toISOString().split('T')[0];

      let modal = new bootstrap.Modal(document.getElementById('vacacionesModal'));
      modal.show();
    },

    // 🔌 Eventos desde backend
    events: {
      url: RUTA_URL + '/vacaciones/getVacaciones',
      method: 'GET',
      failure: function() {
        alert('Error cargando vacaciones');
      }
    },

    // 🎨 Colores por estado
    eventDidMount: function(info) {
      const estado = info.event.extendedProps.estado;

      if (estado === 'aprobada') {
        info.el.style.backgroundColor = '#198754';
      } else if (estado === 'pendiente') {
        info.el.style.backgroundColor = '#ffc107';
      } else if (estado === 'rechazada') {
        info.el.style.backgroundColor = '#dc3545';
      }

      
      info.el.title = info.event.title + " (" + estado + ")";
    },

   
    eventClick: function(info) {
      alert(
        "Empleado: " + info.event.title +
        "\nEstado: " + info.event.extendedProps.estado +
        "\nInicio: " + info.event.startStr +
        "\nFin: " + info.event.endStr
      );
    }

  });

  calendar.render();

  // 🔘 Botón manual
  document.getElementById('btnNuevaVacacion').addEventListener('click', () => {
    let modal = new bootstrap.Modal(document.getElementById('vacacionesModal'));
    modal.show();
  });

  // 🔄 Recargar calendario al cerrar modal (cuando guardas)
  const modalEl = document.getElementById('vacacionesModal');
  modalEl.addEventListener('hidden.bs.modal', function () {
    calendar.refetchEvents();
  });

});