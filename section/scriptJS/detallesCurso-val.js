function verificarRegistro(cursoId, estudianteId) {
    const url = `http://localhost/aprendi/api/inscripcionesController.php?id=${estudianteId}`;

    return fetch(url)
        .then(response => {
            if (!response.ok) {
                throw new Error('Error al verificar la inscripción');
            }
            return response.json();
        })
        .then(data => {
            // Verificar si hay alguna inscripción para el curso especificado
            return data.some(inscripcion => inscripcion.curso_id == cursoId);
        })
        .catch(error => {
            console.error("Error al verificar el registro:", error);
            return false; // Si hay error, asumimos que no está inscrito
        });
}
document.addEventListener('DOMContentLoaded', function () {
    const cursoId = new URLSearchParams(window.location.search).get('id'); // Obtener curso ID de la URL
    const comprarButton = document.querySelector('#comprarButton'); // Botón de compra
    const comprarButtonh = document.querySelector('#comprarhbtn'); // Botón de compra
    if (cursoId && estudianteId) {
        verificarRegistro(cursoId, estudianteId)
            .then(existe => {
                if (existe) {
                    if (comprarButton) comprarButton.style.display = 'none'; // Ocultar botón
                    comprarButtonh.style.display = 'none';
                } else {
                    if (comprarButton) comprarButton.style.display = 'block'; // Mostrar botón
                    comprarButtonh.style.display = 'block';
                }
            })
            .catch(error => {
                console.error("Error al verificar el registro:", error);
                if (comprarButton) comprarButton.style.display = 'none'; // Por seguridad, oculta el botón
            });
            
    } else {
        console.error("No se pudo obtener el cursoId o estudianteId.");
    }

    if (cursoId) {
        // Llamar a la API para obtener los detalles del curso
        fetch(`http://localhost/aprendi/api/cursoController.php?id=${cursoId}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            },
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Error en la API: ${response.status} - ${response.statusText}`);
                }
                return response.json();
            })
            .then(curso => {
                if (curso && curso.id) {
                    // Actualizar los elementos de la página con los datos del curso
                    document.querySelector('.course-header h1').textContent = curso.titulo;
                    document.querySelector('.course-header p:nth-of-type(1)').textContent = `${curso.descripcion}`;
                    document.querySelector('.course-header p:nth-of-type(2)').textContent = `Costo: $${curso.costo}`;

                    // Reemplazar el contenido del video por la imagen del curso
                    const videoSection = document.querySelector('.video-section');
                    videoSection.innerHTML = `
                        <div class="video-container">
                            <img src="${curso.imagen}" alt="Imagen del curso" class="img-fluid rounded" style="width: 100%; height: 300px; object-fit: cover;">
                        </div>
                    `;

                    // Si hay niveles, obtenerlos y mostrarlos
                    cargarNiveles(cursoId);
                } else {
                    console.error("No se encontraron datos del curso.");
                    alert("No se pudo cargar el curso. Verifica si está activo o si el ID es correcto.");
                }
            })
            .catch(error => {
                console.error("Error al obtener el curso:", error);
                alert("Error al obtener el curso. Intenta nuevamente.");
            });
    } else {
        console.error("ID de curso no especificado en la URL.");
    }
});

function cargarNiveles(cursoId) {
    fetch(`http://localhost/aprendi/api/nivelesController.php?curso_id=${cursoId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('No se pudo cargar los niveles.');
            }
            return response.json();
        })
        .then(niveles => {
            console.log("Niveles recibidos:", niveles);

            const nivelesContainer = document.querySelector('.niveles');
            nivelesContainer.innerHTML = '';

            // Validar que la respuesta sea un array
            if (!Array.isArray(niveles)) {
                niveles = [niveles]; // Convertir en array si es un objeto único
            }

            if (niveles.length > 0) {
                const headerElement = document.createElement('h3');
                headerElement.textContent = 'Niveles del curso';
                headerElement.classList.add('niveles-header');
                headerElement.style.cursor = 'pointer';

                const listContainer = document.createElement('div');
                listContainer.classList.add('niveles-list');
                listContainer.style.display = 'none';

                const listElement = document.createElement('ul');
                listElement.classList.add('list-group');

                niveles.forEach((nivel) => {
                    const nivelElement = document.createElement('li');
                    nivelElement.classList.add('list-group-item');

                    const nivelText = document.createElement('span');
                    nivelText.textContent = `Nivel ${nivel.nivel}: ${nivel.descripcion || 'Sin descripción'}`;

                    nivelElement.appendChild(nivelText);
                    listElement.appendChild(nivelElement);
                });

                listContainer.appendChild(listElement);

                nivelesContainer.appendChild(headerElement);
                nivelesContainer.appendChild(listContainer);

                headerElement.addEventListener('click', () => {
                    const isHidden = listContainer.style.display === 'none';
                    listContainer.style.display = isHidden ? 'block' : 'none';
                });
            } else {
                nivelesContainer.innerHTML = '<p>No hay niveles disponibles.</p>';
            }
        })
        .catch(error => {
            console.error("Error al cargar niveles:", error);
            const nivelesContainer = document.querySelector('.niveles');
            nivelesContainer.innerHTML = '<p>Error al cargar los niveles.</p>';
        });
}
