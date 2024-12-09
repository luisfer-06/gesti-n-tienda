document.addEventListener('DOMContentLoaded', function () {
    // Manejador del formulario de registro de producto
    const form = document.getElementById('productForm');

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(form);

        // Obtener el código de administrador del formulario
        const adminCode = document.getElementById('adminCode').value;

        // Validar que el código de administrador no esté vacío
        if (!adminCode) {
            alert('Debes ingresar el código de administrador.');
            return;
        }

        fetch('../sql/register_product.php', {
            method: 'POST',
            body: formData,
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    alert('Producto registrado exitosamente');
                    form.reset();
                } else {
                    alert('Error al registrar el producto: ' + data.message);
                }
            })
            .catch((error) => {
                console.error('Error:', error);
                alert('Ocurrió un error al procesar la solicitud');
            });
    });

    // Manejadores para el modal de actualización
    const updateModal = document.getElementById('updateModal');
    const updateDataLink = document.getElementById('updateDataLink');
    const closeModal = document.getElementById('closeModal');
    const updateForm = document.getElementById('updateForm');

    // Mostrar el modal al hacer clic en "Actualizar datos"
    updateDataLink.addEventListener('click', (e) => {
        e.preventDefault();
        updateModal.classList.remove('hidden');
    });

    // Cerrar el modal
    closeModal.addEventListener('click', () => {
        updateModal.classList.add('hidden');
    });

    // Enviar datos del formulario de actualización
    updateForm.addEventListener('submit', (e) => {
        e.preventDefault();

        const updateCode = document.getElementById('updateCode').value;
        const updateQuantity = parseInt(
            document.getElementById('updateQuantity').value
        );

        // Validar que los campos no estén vacíos
        if (!updateCode || !updateQuantity) {
            alert('Debes ingresar todos los campos.');
            return;
        }

        // Realizar solicitud a la base de datos mediante Fetch
        fetch('../sql/update_product.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                code: updateCode,
                quantity: updateQuantity,
            }),
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    alert('Producto actualizado correctamente');
                    updateModal.classList.add('hidden');
                    updateForm.reset();
                } else {
                    alert('Error al actualizar el producto: ' + data.error);
                }
            })
            .catch((error) => {
                console.error('Error:', error);
                alert('Error al conectar con el servidor');
            });
    });
});
