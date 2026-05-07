document.addEventListener("DOMContentLoaded", () => {

    const form = document.getElementById("formResetPassword");

    if (!form) {
        console.error("Formulario no encontrado");
        return;
    }

    form.addEventListener("submit", async (e) => {

        e.preventDefault();

        const formData = new FormData(form);

        const pass = formData.get("nueva_password");
        const confirm = formData.get("confirma_password");

        // Validate passwords match
        if (pass !== confirm) {

            alert("Las contraseñas no coinciden");

            return;
        }

        // Validate minimum length
        if (pass.length < 4) {

            alert("La contraseña debe tener al menos 4 caracteres");

            return;
        }

        try {

            const response = await fetch(
                "/login/guardarNuevaPassword",
                {
                    method: "POST",
                    body: formData
                }
            );

            // IMPORTANT:
            // Read raw response first for debugging
            const rawText = await response.text();

            console.log("RAW RESPONSE:");
            console.log(rawText);

            let data;

            try {

                data = JSON.parse(rawText);

            } catch (jsonError) {

                console.error("JSON ERROR:", jsonError);

                alert(
                    "El servidor devolvió una respuesta inválida.\n\n" +
                    "Mira la consola del navegador."
                );

                return;
            }

            if (data.success) {

                alert("Contraseña actualizada correctamente");

                window.location.href = "/login";

            } else {

                alert(
                    data.message ||
                    "Error al actualizar contraseña"
                );
            }

        } catch (error) {

            console.error("FETCH ERROR:", error);

            alert("Error de conexión");
        }
    });

});