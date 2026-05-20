/**
 * Validación de formularios — se activa automáticamente en cualquier
 * elemento <form data-validate> de la página.
 */
(function () {

  // ─── Mensajes de error ────────────────────────────────────────
  const MSG = {
    required    : 'Este campo es obligatorio',
    email       : 'Introduce un email válido',
    minlength   : n => `Mínimo ${n} caracteres`,
    maxlength   : n => `Máximo ${n} caracteres`,
    min         : n => `El valor mínimo es ${n}`,
    max         : n => `El valor máximo es ${n}`,
    match       : 'Las contraseñas no coinciden',
    dateAfter   : 'La fecha de fin debe ser posterior a la de inicio',
    dateFuture  : 'La fecha no puede ser anterior a hoy',
    pattern     : 'Formato inválido',
    select      : 'Selecciona una opción',
  };

  // ─── Valida un campo individual ───────────────────────────────
  function validarCampo(field) {
    if (field.disabled || field.type === 'hidden' || field.type === 'submit') return true;

    const val = field.value.trim();
    let error = '';

    // Obligatorio
    if (field.required && !val) {
      error = field.tagName === 'SELECT' ? MSG.select : MSG.required;

    // Email
    } else if (field.type === 'email' && val && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) {
      error = MSG.email;

    // Longitud mínima
    } else if (field.minLength > 0 && val && val.length < field.minLength) {
      error = MSG.minlength(field.minLength);

    // Longitud máxima
    } else if (field.maxLength > 0 && val && val.length > field.maxLength) {
      error = MSG.maxlength(field.maxLength);

    // Número mínimo
    } else if (field.type === 'number' && field.min !== '' && val !== '' && parseFloat(val) < parseFloat(field.min)) {
      error = MSG.min(field.min);

    // Número máximo
    } else if (field.type === 'number' && field.max !== '' && val !== '' && parseFloat(val) > parseFloat(field.max)) {
      error = MSG.max(field.max);

    // Patrón personalizado
    } else if (field.pattern && val && !new RegExp('^(?:' + field.pattern + ')$').test(val)) {
      error = field.dataset.errorMsg || MSG.pattern;

    // Confirmación de contraseña: data-match="id_del_campo_original"
    } else if (field.dataset.match) {
      const target = document.getElementById(field.dataset.match);
      if (target && val && val !== target.value) error = MSG.match;

    // Fecha no anterior a otra: data-date-after="id_campo_inicio"
    } else if (field.dataset.dateAfter) {
      const inicio = document.getElementById(field.dataset.dateAfter);
      if (inicio && val && inicio.value && val < inicio.value) error = MSG.dateAfter;

    // Fecha no pasada: data-date-future
    } else if ('dateFuture' in field.dataset && val) {
      const hoy = new Date().toISOString().split('T')[0];
      if (val < hoy) error = MSG.dateFuture;
    }

    aplicarEstado(field, error);
    return !error;
  }

  // ─── Aplica clases Bootstrap y muestra el mensaje ─────────────
  function aplicarEstado(field, error) {
    const hasValue = field.value.trim() !== '';
    field.classList.toggle('is-invalid', !!error);
    field.classList.toggle('is-valid',   !error && hasValue && !field.dataset.noValid);

    // Buscar o crear el <div class="invalid-feedback">
    let fb = field.parentElement?.querySelector('.invalid-feedback');
    if (!fb) {
      fb = document.createElement('div');
      fb.className = 'invalid-feedback';
      field.insertAdjacentElement('afterend', fb);
    }
    fb.textContent = error;
  }

  // ─── Valida todos los campos de un formulario ─────────────────
  function validarFormulario(form) {
    let valido = true;
    form.querySelectorAll('input, select, textarea').forEach(f => {
      if (!validarCampo(f)) valido = false;
    });
    return valido;
  }

  // ─── Limpia las clases de validación de un formulario ─────────
  function limpiarValidacion(form) {
    form.querySelectorAll('.is-invalid, .is-valid').forEach(f => {
      f.classList.remove('is-invalid', 'is-valid');
    });
    form.querySelectorAll('.invalid-feedback').forEach(fb => {
      fb.textContent = '';
    });
  }

  // ─── Exponer API global ───────────────────────────────────────
  window.Validacion = { validarCampo, validarFormulario, limpiarValidacion };

  // ─── Auto-inicialización para formularios con data-validate ───
  document.addEventListener('DOMContentLoaded', () => {

    document.querySelectorAll('form[data-validate]').forEach(form => {

      // Desactivar validación nativa del browser (usamos la nuestra)
      form.setAttribute('novalidate', '');

      // Validación al enviar
      form.addEventListener('submit', e => {
        if (!validarFormulario(form)) {
          e.preventDefault();
          e.stopPropagation();
          // Hacer scroll al primer error
          const firstError = form.querySelector('.is-invalid');
          firstError?.scrollIntoView({ behavior: 'smooth', block: 'center' });
          firstError?.focus();
        }
      });

      // Validación en tiempo real al perder el foco
      form.addEventListener('focusout', e => {
        if (e.target.matches('input, select, textarea')) {
          validarCampo(e.target);
        }
      }, true);

      // Re-validar mientras escribe si ya tenía error
      form.addEventListener('input', e => {
        if (e.target.matches('input, select, textarea')) {
          if (e.target.classList.contains('is-invalid')) {
            validarCampo(e.target);
          }
        }
      });

      // Limpiar al resetear / cerrar modal
      form.addEventListener('reset', () => limpiarValidacion(form));
    });

    // Limpiar al cerrar un modal Bootstrap
    document.addEventListener('hidden.bs.modal', e => {
      e.target.querySelectorAll('form[data-validate]').forEach(limpiarValidacion);
    });

  });

})();
