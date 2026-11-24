
// ============================================
// DIANITA'S - CRUD DE EMPLEADOS
// ============================================


// ============================================
// AGREGAR EMPLEADO
// ============================================

const agregarEmpleado = async () => {

    const formulario = document.querySelector("#formAgregar");

    if (!formulario.checkValidity()) {
        formulario.reportValidity();
        return;
    }

    const datos = new FormData(formulario);

    // COMPROBAR LOS DATOS ENVIADOS
    console.log("DATOS DEL FORMULARIO:");

    for (const [campo, valor] of datos.entries()) {
        console.log(campo + ": " + valor);
    }

    console.log("PUESTO ENVIADO:", datos.get("puesto"));

    try {

        const respuesta = await fetch(
            "php/empleados/agregarEmpleado.php",
            {
                method: "POST",
                body: datos
            }
        );

        const resultado = await respuesta.json();

        console.log("RESPUESTA DE PHP:", resultado);

        if (resultado.success === true) {

            Swal.fire({
                icon: "success",
                title: "¡REGISTRO EXITOSO!",
                text: resultado.mensaje,
                footer: "DIANITA'S"
            });

            formulario.reset();

            const modal = bootstrap.Modal.getOrCreateInstance(
                document.querySelector("#agregarModal")
            );

            modal.hide();

            cargarEmpleados();

        } else {

            Swal.fire({
                icon: "error",
                title: "ERROR",
                text: resultado.mensaje,
                footer: "DIANITA'S"
            });

        }

    } catch (error) {

        console.error("Error al agregar empleado:", error);

        Swal.fire({
            icon: "error",
            title: "ERROR",
            text: "NO SE PUDO CONECTAR CON EL SERVIDOR",
            footer: "DIANITA'S"
        });

    }

};


// ============================================
// CARGAR TODOS LOS EMPLEADOS
// ============================================

const cargarEmpleados = async () => {

    try {

        const respuesta = await fetch(
            "php/empleados/cargarEmpleados.php"
        );

        if (!respuesta.ok) {
            throw new Error("Error HTTP: " + respuesta.status);
        }

        const resultado = await respuesta.json();

        const registros = document.querySelector("#registros");

        registros.innerHTML = "";

        if (resultado.error) {
            throw new Error(resultado.error);
        }

        if (resultado.data.length > 0) {

            resultado.data.forEach((fila) => {

                registros.innerHTML += `
                    <tr>
                        <td>${escaparHTML(fila[0])}</td>
                        <td>${escaparHTML(fila[1])}</td>
                        <td>${escaparHTML(fila[2])}</td>
                        <td>${escaparHTML(fila[3])}</td>
                        <td>${escaparHTML(fila[4])}</td>
                        <td>${escaparHTML(fila[5])}</td>

                        <td>
                            <div class="botones">

                                <button
                                    type="button"
                                    class="btn btn-warning btn-sm"
                                    onclick="cargarEmpleado(${Number(fila[0])})"
                                >
                                    Editar
                                </button>

                                <button
                                    type="button"
                                    class="btn btn-danger btn-sm"
                                    onclick="eliminarEmpleado(${Number(fila[0])})"
                                >
                                    Eliminar
                                </button>

                            </div>
                        </td>
                    </tr>
                `;

            });

        } else {

            registros.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center">
                        NO HAY EMPLEADOS REGISTRADOS
                    </td>
                </tr>
            `;

        }

    } catch (error) {

        console.error("Error al cargar empleados:", error);

        Swal.fire({
            icon: "error",
            title: "ERROR",
            text: "NO SE PUDIERON CARGAR LOS EMPLEADOS",
            footer: "DIANITA'S"
        });

    }

};


// ============================================
// PROTEGER TEXTO AL MOSTRARLO EN HTML
// ============================================

const escaparHTML = (texto) => {

    return String(texto ?? "").replace(/[&<>"']/g, (caracter) => {

        const caracteres = {
            "&": "&amp;",
            "<": "&lt;",
            ">": "&gt;",
            '"': "&quot;",
            "'": "&#039;"
        };

        return caracteres[caracter];

    });

};


// ============================================
// ELIMINAR EMPLEADO
// ============================================

const eliminarEmpleado = async (id) => {

    const confirmacion = await Swal.fire({

        title: "¿ELIMINAR EMPLEADO?",
        text: "También se eliminará su cuenta de acceso.",
        icon: "warning",

        showCancelButton: true,

        confirmButtonColor: "#d63384",
        cancelButtonColor: "#6c757d",

        confirmButtonText: "SÍ, ELIMINAR",
        cancelButtonText: "CANCELAR"

    });

    if (!confirmacion.isConfirmed) {
        return;
    }

    const datos = new FormData();

    datos.append("id_empleado", id);

    try {

        const respuesta = await fetch(
            "php/empleados/eliminarEmpleado.php",
            {
                method: "POST",
                body: datos
            }
        );

        if (!respuesta.ok) {
            throw new Error("Error HTTP: " + respuesta.status);
        }

        const resultado = await respuesta.json();

        if (resultado.success === true) {

            Swal.fire({
                icon: "success",
                title: "ELIMINADO",
                text: resultado.mensaje,
                footer: "DIANITA'S"
            });

            // Actualizar tabla
            cargarEmpleados();

        } else {

            Swal.fire({
                icon: "error",
                title: "ERROR",
                text: resultado.mensaje,
                footer: "DIANITA'S"
            });

        }

    } catch (error) {

        console.error("Error al eliminar empleado:", error);

        Swal.fire({
            icon: "error",
            title: "ERROR",
            text: "NO SE PUDO ELIMINAR EL EMPLEADO",
            footer: "DIANITA'S"
        });

    }

};


// ============================================
// CARGAR DATOS DE UN EMPLEADO PARA EDITAR
// ============================================

const cargarEmpleado = async (id) => {

    const datos = new FormData();

    datos.append("id_empleado", id);

    try {

        const respuesta = await fetch(
            "php/empleados/cargarEmpleado.php",
            {
                method: "POST",
                body: datos
            }
        );

        if (!respuesta.ok) {
            throw new Error("Error HTTP: " + respuesta.status);
        }

        const resultado = await respuesta.json();

        if (resultado.success === true) {

            // Colocar datos en el formulario
            document.querySelector("#eid_empleado").value =
                resultado.id_empleado;

            document.querySelector("#enombre").value =
                resultado.nombre;

            document.querySelector("#eapellidos").value =
                resultado.apellidos;

            document.querySelector("#epuesto").value =
                resultado.puesto.toLowerCase().trim();

            document.querySelector("#etelefono").value =
                resultado.telefono;

            document.querySelector("#ecorreo").value =
                resultado.correo;

            // Abrir modal de edición
            const modal = bootstrap.Modal.getOrCreateInstance(
                document.querySelector("#editarModal")
            );

            modal.show();

        } else {

            Swal.fire({
                icon: "error",
                title: "ERROR",
                text: resultado.mensaje,
                footer: "DIANITA'S"
            });

        }

    } catch (error) {

        console.error("Error al buscar empleado:", error);

        Swal.fire({
            icon: "error",
            title: "ERROR",
            text: "NO SE PUDO CARGAR LA INFORMACIÓN DEL EMPLEADO",
            footer: "DIANITA'S"
        });

    }

};


// ============================================
// EDITAR EMPLEADO
// ============================================

const editarEmpleado = async () => {

    const formulario = document.querySelector("#formEditar");

    // Validar formulario
    if (!formulario.checkValidity()) {
        formulario.reportValidity();
        return;
    }

    const datos = new FormData(formulario);

    try {

        const respuesta = await fetch(
            "php/empleados/editarEmpleado.php",
            {
                method: "POST",
                body: datos
            }
        );

        if (!respuesta.ok) {
            throw new Error("Error HTTP: " + respuesta.status);
        }

        const resultado = await respuesta.json();

        if (resultado.success === true) {

            Swal.fire({
                icon: "success",
                title: "¡ACTUALIZACIÓN EXITOSA!",
                text: resultado.mensaje,
                footer: "DIANITA'S"
            });

            // Cerrar modal
            const modal = bootstrap.Modal.getOrCreateInstance(
                document.querySelector("#editarModal")
            );

            modal.hide();

            // Actualizar tabla
            cargarEmpleados();

        } else {

            Swal.fire({
                icon: "error",
                title: "ERROR",
                text: resultado.mensaje,
                footer: "DIANITA'S"
            });

        }

    } catch (error) {

        console.error("Error al editar empleado:", error);

        Swal.fire({
            icon: "error",
            title: "ERROR",
            text: "NO SE PUDO ACTUALIZAR EL EMPLEADO",
            footer: "DIANITA'S"
        });

    }

};


// ============================================
// INICIAR CARGA DE EMPLEADOS
// ============================================

document.addEventListener("DOMContentLoaded", () => {

    cargarEmpleados();

});
