
document.addEventListener("DOMContentLoaded", () => {

    const conocidos = new Set();

    let audio = null;
    let consultando = false;

    // Crear panel de notificaciones
    const panel = document.createElement("div");

    panel.innerHTML = `
        <button id="btnNotificaciones">
            🔔 Pedidos listos
            <span id="contadorNotificaciones">0</span>
        </button>

        <div id="panelNotificaciones" hidden>
            <h3>Pedidos listos para entregar</h3>
            <div id="listaNotificaciones"></div>
        </div>
    `;

    document.body.appendChild(panel);

    const estilos = document.createElement("style");

    estilos.textContent = `
        #btnNotificaciones {
            position: fixed;
            top: 15px;
            right: 15px;
            background: #c52d62;
            color: white;
            border: 2px solid white;
            padding: 12px 18px;
            border-radius: 25px;
            cursor: pointer;
            font-weight: bold;
            z-index: 9999;
            box-shadow: 0 3px 10px #0003;
        }

        #contadorNotificaciones {
            background: #dc2626;
            color: white;
            padding: 4px 8px;
            border-radius: 50%;
            margin-left: 5px;
        }

        #panelNotificaciones {
            position: fixed;
            top: 75px;
            right: 15px;
            width: min(340px, 90vw);
            max-height: 70vh;
            overflow-y: auto;
            background: white;
            color: #333;
            padding: 18px;
            border-radius: 15px;
            box-shadow: 0 5px 20px #0004;
            z-index: 9999;
        }

        #panelNotificaciones h3 {
            color: #c52d62;
            margin-bottom: 15px;
        }

        .pedido-listo {
            background: #f0f7e8;
            border-left: 5px solid #688430;
            padding: 12px;
            margin-bottom: 12px;
            border-radius: 8px;
        }

        .pedido-listo button {
            background: #688430;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            margin-top: 10px;
        }
    `;

    document.head.appendChild(estilos);

    const boton = document.getElementById(
        "btnNotificaciones"
    );

    const ventana = document.getElementById(
        "panelNotificaciones"
    );

    // Activar sonido mediante interacción del usuario
    boton.addEventListener("click", async () => {

        ventana.hidden = !ventana.hidden;

        try {
            audio = audio || new AudioContext();
            await audio.resume();
        } catch (error) {
            console.error(error);
        }
    });

    // Sonido de notificación
    function reproducirSonido() {

        if (!audio || audio.state !== "running") {
            return;
        }

        const tono = audio.createOscillator();
        const volumen = audio.createGain();

        tono.type = "sine";
        tono.frequency.value = 880;

        volumen.gain.value = 0.15;

        tono.connect(volumen);
        volumen.connect(audio.destination);

        tono.start();
        tono.stop(audio.currentTime + 0.4);
    }

    // Consultar pedidos terminados
    async function cargarNotificaciones() {

        if (consultando) return;

        consultando = true;

        try {

            const respuesta = await fetch(
                "php/pedidos/obtenerNotificaciones.php",
                { cache: "no-store" }
            );

            if (!respuesta.ok) {
                throw new Error("Error de conexión");
            }

            const pedidos = await respuesta.json();

            if (!Array.isArray(pedidos)) {
                throw new Error("Respuesta incorrecta");
            }

            document.getElementById(
                "contadorNotificaciones"
            ).textContent = pedidos.length;

            const lista = document.getElementById(
                "listaNotificaciones"
            );

            lista.replaceChildren();

            if (pedidos.length === 0) {
                lista.textContent =
                    "No hay pedidos listos.";
            }

            let nuevoPedido = false;

            pedidos.forEach(pedido => {

                const id = String(pedido.id_pedido);

                if (!conocidos.has(id)) {

                    conocidos.add(id);
                    nuevoPedido = true;
                }

                const tarjeta =
                    document.createElement("div");

                tarjeta.className = "pedido-listo";

                const titulo =
                    document.createElement("strong");

                titulo.textContent = "¡PEDIDO LISTO!";

                const mesa =
                    document.createElement("p");

                mesa.textContent = pedido.mesa;

                const mensaje =
                    document.createElement("p");

                mensaje.textContent =
                    "Pedido #" + id +
                    " listo para recoger en cocina.";

                const entregar =
                    document.createElement("button");

                entregar.textContent =
                    "Marcar como entregado";

                entregar.onclick = async () => {

                    entregar.disabled = true;

                    try {

                        const respuesta = await fetch(
                            "php/pedidos/entregarPedido.php",
                            {
                                method: "POST",
                                headers: {
                                    "Content-Type":
                                        "application/json"
                                },
                                body: JSON.stringify({
                                    id_pedido: Number(id)
                                })
                            }
                        );

                        const resultado =
                            await respuesta.json();

                        if (!resultado.success) {
                            throw new Error(
                                "No se pudo confirmar"
                            );
                        }

                        await cargarNotificaciones();

                    } catch (error) {

                        alert(
                            "No se pudo confirmar la entrega."
                        );

                    } finally {
                        entregar.disabled = false;
                    }
                };

                tarjeta.append(
                    titulo,
                    mesa,
                    mensaje,
                    entregar
                );

                lista.appendChild(tarjeta);
            });

            if (nuevoPedido) {

                ventana.hidden = false;

                reproducirSonido();
            }

        } catch (error) {

            console.error(
                "Error en notificaciones:",
                error
            );

        } finally {

            consultando = false;
        }
    }

    // Primera consulta
    cargarNotificaciones();

    // Actualizar cada 3 segundos
    setInterval(cargarNotificaciones, 3000);

});
