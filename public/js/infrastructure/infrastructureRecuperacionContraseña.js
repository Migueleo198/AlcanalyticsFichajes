document.addEventListener("DOMContentLoaded", () => {

    const form = document.querySelector("form");
    const input = document.getElementById("usuario_email");
    const button = form.querySelector("button");

    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        const email = input.value.trim();

        if (!email) {
            alert("Introduce un correo o usuario");
            return;
        }

        // UI loading
        button.disabled = true;
        button.textContent = "Enviando...";

        try {
            const formData = new FormData();
            formData.append("usuario_email", email);

            const response = await fetch("/login/enviarRecuperacion", {
                method: "POST",
                body: formData
            });

            const data = await response.json();

            console.log("Respuesta servidor:", data);

            if (data.success) {

                alert(data.message || "Si el correo existe, recibirás instrucciones");

                input.value = "";

            } else {

                alert(data.message || "Error al enviar el correo");
            }

        } catch (error) {

            console.error("Error fetch:", error);

            alert("Error de conexión con el servidor");

        } finally {

            button.disabled = false;
            button.textContent = "Enviar Instrucciones";
        }
    });

});