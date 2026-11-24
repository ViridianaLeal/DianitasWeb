

function escaparHTML(texto) {
    const elemento = document.createElement("div");
    elemento.textContent = texto ?? "";
    return elemento.innerHTML;
}


// ==========================================
// AGREGAR Pratillo
// ==========================================

window.agregarCategoria = async () => {

    const nombre = document.querySelector("#nombre").value.trim();

    if (nombre === "") {
        Swal.fire("Error", "Falta escribir el nombre de la categoría", "error");
        return;
    }

    
    const datos = new FormData();
    datos.append("nombre", nombre);

    try {

        const respuesta = await fetch("php/categorias/agregarCategoria.php", {
            method: "POST",
            body: datos
        });

        const resultado = await respuesta.json();

        if (resultado.success) {

            await Swal.fire("Éxito", resultado.mensaje, "success");

            document.querySelector("#formAgregarCa").reset();

            cargarCategorias();

        } else {

            Swal.fire("Error", resultado.mensaje, "error");

        }

    } catch (error) {

        console.error("Error al agregar:", error);

        Swal.fire(
            "Error",
            "No se pudo registrar la categoría. Revisa la respuesta PHP.",
            "error"
        );

    }
};


// ==========================================
// CARGAR TODAS LAS CATEGORIAS
// ==========================================

window.cargarCategorias = async () => {

    try {

        const respuesta = await fetch("php/categorias/cargarCategorias.php");

        const resultado = await respuesta.json();

        if (!resultado.success && !Array.isArray(resultado.data)) {
            throw new Error(resultado.mensaje || "Error al cargar categorías");
        }

        let registrosHTML = "";

        if (!resultado.data || resultado.data.length === 0) {

            registrosHTML = `
                <tr>
                    <td colspan="3">No hay categorías registradas.</td>
                </tr>
            `;

        } else {

            resultado.data.forEach(fila => {

                const id = Number(fila[0]);
                const nombre = escaparHTML(fila[1]);

                registrosHTML += `
                    <tr>
                        <td>${nombre}</td>

                        <td>
                            <button
                                type="button"
                                class="btn btn-editar"
                                data-bs-toggle="modal"
                                data-bs-target="#editarModal"
                                onclick="cargarCategoria(${id})">
                                Editar
                            </button>
                        </td>

                        <td>
                            <button
                                type="button"
                                class="btn btn-eliminar"
                                onclick="eliminarCategoria(${id})">
                                Eliminar
                            </button>
                        </td>
                    </tr>
                `;

            });

        }

        document.querySelector("#registros").innerHTML = registrosHTML;

    } catch (error) {

        console.error("Error al cargar categorías:", error);

        Swal.fire(
            "Error",
            "No se pudieron cargar las categorías.",
            "error"
        );

    }

};


// ==========================================
// ELIMINAR CATEGORIA
// ==========================================

window.eliminarCategoria = async (id_categoria) => {

    const confirmacion = await Swal.fire({

        title: "¿Eliminar categoría?",
        text: "Esta acción eliminará la categoría seleccionada.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar"

    });

    if (!confirmacion.isConfirmed) {
        return;
    }

    const datos = new FormData();
    datos.append("id_categoria", id_categoria);

    try {

        const respuesta = await fetch("php/categorias/eliminarCategoria.php", {
            method: "POST",
            body: datos
        });

        const resultado = await respuesta.json();

        if (resultado.success) {

            await Swal.fire("Éxito", resultado.mensaje, "success");

            cargarCategorias();

        } else {

            Swal.fire("Error", resultado.mensaje, "error");

        }

    } catch (error) {

        console.error("Error al eliminar:", error);

        Swal.fire("Error", "No se pudo eliminar la categoría.", "error");

    }

};


// ==========================================
// CARGAR UNA CATEGORIA PARA EDITAR
// ==========================================

window.cargarCategoria = async (id_categoria) => {

    const datos = new FormData();
    datos.append("id_categoria", id_categoria);

    try {

        const respuesta = await fetch("php/categorias/cargarCategoria.php", {
            method: "POST",
            body: datos
        });

        const resultado = await respuesta.json();

        if (!resultado.success || !resultado.id_categoria) {

            Swal.fire(
                "Error",
                resultado.mensaje || "No se encontró la categoría.",
                "error"
            );

            return;
        }

        document.querySelector("#id_categoria").value =
            resultado.id_categoria;

        document.querySelector("#enombre").value =
            resultado.nombre;

    } catch (error) {

        console.error("Error al consultar categoría:", error);

        Swal.fire("Error", "No se pudieron cargar los datos.", "error");

    }

};


// ==========================================
// EDITAR CATEGORIA
// ==========================================

window.editarCategoria = async () => {

    const id_categoria =
        document.querySelector("#id_categoria").value;

    const nombre =
        document.querySelector("#enombre").value.trim();

    if (id_categoria === "" || nombre === "") {

        Swal.fire(
            "Error",
            "Debes escribir el nombre de la categoría.",
            "error"
        );

        return;
    }

    const datos = new FormData();

    datos.append("id_categoria", id_categoria);
    datos.append("nombre", nombre);
    datos.append("activo", 1);

    try {

        const respuesta = await fetch("php/categorias/editarCategoria.php", {
            method: "POST",
            body: datos
        });

        const resultado = await respuesta.json();

        if (resultado.success) {

            bootstrap.Modal.getOrCreateInstance(
                document.querySelector("#editarModal")
            ).hide();

            document.querySelector("#formEditar").reset();

            await Swal.fire("Éxito", resultado.mensaje, "success");

            cargarCategorias();

        } else {

            Swal.fire("Error", resultado.mensaje, "error");

        }

    } catch (error) {

        console.error("Error al editar:", error);

        Swal.fire("Error", "No se pudo actualizar la categoría.", "error");

    }

};


// ==========================================
// INICIAR EL SISTEMA
// ==========================================

document.addEventListener("DOMContentLoaded", () => {

    cargarCategorias();

});
