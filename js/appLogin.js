
document.addEventListener("DOMContentLoaded", function () {

    const formulario = document.getElementById("formulario");
    const boton = document.getElementById("btnIniciar");

    formulario.addEventListener("submit", function (e) {

        e.preventDefault();

        // Obtener los datos del formulario
        let correo = document.getElementById("correo").value.trim();
        let contrasena = document.getElementById("contrasena").value;

        // Validar campos
        if (correo === "" || contrasena === "") {
            alert("Por favor, completa todos los campos.");
            return;
        }

        // Preparar los datos para enviarlos al PHP
        let datos = new FormData();

        datos.append("correo", correo);
        datos.append("contrasena", contrasena);

        // Desactivar botón mientras se valida
        boton.disabled = true;
        boton.textContent = "Validando...";

        // Enviar información al servidor
        fetch("php/usuarios/loginUsuario.php", {
            method: "POST",
            body: datos
        })

        .then(response => {

            if (!response.ok) {
                throw new Error("Error en el servidor: " + response.status);
            }

            return response.json();

        })

        .then(resultado => {

            if (resultado.success) {

                // Guardar información de la sesión
                localStorage.setItem("nombre", resultado.nombre);
                localStorage.setItem("puesto", resultado.puesto);

                alert(resultado.mensaje);

                // Redireccionar según el puesto
                switch (resultado.puesto) {

                    case "mesera":
                        window.location.href = "PrincipalDianitas.html";
                        break;

                    case "cocinera":
                        window.location.href = "CocinaDianitas.html";
                        break;

                    case "administrador":
                        window.location.href = "administrador.html";
                        break;

                    default:
                        alert("Puesto de empleado no válido.");
                        break;
                }

            } else {

                alert(resultado.mensaje);

            }

        })

        .catch(error => {

            console.error("Error al iniciar sesión:", error);

            alert("No se pudo conectar con el servidor. Verifica tu conexión y los archivos PHP.");

        })

        .finally(() => {

            boton.disabled = false;
            boton.textContent = "Iniciar sesión";

        });

    });

});
