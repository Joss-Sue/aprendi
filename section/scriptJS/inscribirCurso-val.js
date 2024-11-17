document.addEventListener('DOMContentLoaded', function () {
    const compraButton = document.querySelector('.btn-green');

    if (compraButton) {
        // Obtener el curso_id desde la URL
        const urlParams = new URLSearchParams(window.location.search);
        const cursoId = urlParams.get('id');

        if (!cursoId) {
            console.error("No se encontró el ID del curso.");
            return;
        }

        // Al hacer clic en el botón de compra
        compraButton.addEventListener('click', function (event) {
            event.preventDefault();
            // Redirigir al usuario a la página de pago con el curso_id
            window.location.href = `../partials/FormaPago.php?id=${cursoId}`;
        });
    }
});
