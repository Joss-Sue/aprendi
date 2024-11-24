document.addEventListener('DOMContentLoaded', function () {
    // Obtener el ID del curso desde la URL
    const urlParams = new URLSearchParams(window.location.search);
    const cursoId = urlParams.get('id');

    if (cursoId) {
        // Llamar a la API para obtener los detalles del curso
        fetch(`http://localhost/aprendi/api/cursoController.php?id=${cursoId}`)
            .then(response => response.json())
            .then(curso => {
                if (curso && curso.id) {
                    // Actualizar los elementos de la página con los datos del curso
                    document.querySelector('.course-header h1').textContent = curso.titulo;
                    document.querySelector('.course-header p:nth-of-type(1)').textContent = `${curso.descripcion}`;
                    document.querySelector('.course-header p:nth-of-type(2)').textContent = `Costo: $${curso.costo}`;

                    // Si hay niveles, obtenerlos y mostrarlos
                    cargarNiveles(cursoId);
                } else {
                    console.error("No se encontraron datos del curso.");
                }
            })
            .catch(error => {
                console.error("Error al obtener el curso:", error);
            });
    } else {
        console.error("ID de curso no especificado en la URL.");
    }
});
// Función para cargar niveles del curso con un desplegable
function cargarNiveles(cursoId) {
    fetch(`http://localhost/aprendi/api/nivelesController.php?curso_id=${cursoId}`)
        .then(response => response.json())
        .then(niveles => {
            const nivelesContainer = document.querySelector('.niveles');
            nivelesContainer.innerHTML = ''; 

            if (niveles && Array.isArray(niveles) && niveles.length > 0) {
                // Crear encabezado desplegable
                const headerElement = document.createElement('h3');
                headerElement.textContent = 'Niveles';
                headerElement.classList.add('niveles-header');
                headerElement.style.cursor = 'pointer';

                // Crear contenedor para la lista de niveles
                const listContainer = document.createElement('div');
                listContainer.classList.add('niveles-list');
                listContainer.style.display = 'none';

                const listElement = document.createElement('ul');
                listElement.classList.add('list-group');

                niveles.forEach((nivel, index) => {
                    const nivelElement = document.createElement('li');
                    nivelElement.classList.add('list-group-item');
                    
                    // Crear el texto del nivel
                    const nivelText = document.createElement('span');
                    nivelText.textContent = `Nivel ${index + 1}: ${nivel.descripcion}`;

                    // Crear el botón de compra

                    const compraButton = document.createElement('a');
                    // Agregar el texto y el botón al elemento del nivel
                    nivelElement.appendChild(nivelText);
                    nivelElement.appendChild(compraButton);
                    listElement.appendChild(nivelElement);
                });

                listContainer.appendChild(listElement);

                // Agregar encabezado y lista al contenedor principal
                nivelesContainer.appendChild(headerElement);
                nivelesContainer.appendChild(listContainer);

                // Función para desplegar/ocultar la lista de niveles
                headerElement.addEventListener('click', function () {
                    const isHidden = listContainer.style.display === 'none';
                    listContainer.style.display = isHidden ? 'block' : 'none';
                });
            } else {
                console.error("No se encontraron niveles para este curso.");
                nivelesContainer.innerHTML = '<p>No hay niveles disponibles.</p>';
            }
        })
        .catch(error => {
            console.error("Error al cargar niveles:", error);
        });
}


