<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forma de Pago</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../styles/index.css">
</head>
<body>
    <!-- Contenedor del Menú -->
    <div id="menu-container"></div>

    <!-- Título -->
    <div class="container mt-5">
        <h1 class="text-center">Seleccionar Forma de Pago</h1>
    </div>

    <!-- Formulario de Pago -->
    <div class="container mt-4">
        <form action="procesar_pago.php" method="POST">
            <!-- Opciones de Pago -->
            <div class="mb-4">
                <h5>Elige tu método de pago:</h5>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="paymentMethod" id="creditCard" value="creditCard" required>
                    <label class="form-check-label" for="creditCard">
                        Tarjeta de Crédito/Débito
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="paymentMethod" id="paypal" value="paypal">
                    <label class="form-check-label" for="paypal">
                        PayPal
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="paymentMethod" id="bankTransfer" value="bankTransfer">
                    <label class="form-check-label" for="bankTransfer">
                        Transferencia Bancaria
                    </label>
                </div>
            </div>

            <!-- Detalles de Tarjeta de Crédito -->
            <div id="creditCardInfo" class="payment-details">
                <h5>Detalles de Tarjeta de Crédito</h5>
                <div class="mb-3">
                    <label for="cardNumber" class="form-label">Número de Tarjeta</label>
                    <input type="text" class="form-control" id="cardNumber" name="cardNumber" placeholder="1234 5678 9012 3456">
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="expiryDate" class="form-label">Fecha de Expiración</label>
                        <input type="text" class="form-control" id="expiryDate" name="expiryDate" placeholder="MM/AA">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="cvv" class="form-label">CVV</label>
                        <input type="text" class="form-control" id="cvv" name="cvv" placeholder="123">
                    </div>
                </div>
            </div>

            <!-- Información de PayPal -->
            <div id="paypalInfo" class="payment-details d-none">
                <h5>Pago con PayPal</h5>
                <p>Serás redirigido a PayPal para completar tu compra de forma segura.</p>
            </div>

            <!-- Información de Transferencia Bancaria -->
            <div id="bankTransferInfo" class="payment-details d-none">
                <h5>Pago con Transferencia Bancaria</h5>
                <p>Recibirás la información bancaria en tu correo para completar la transferencia.</p>
            </div>

            <!-- Botón de Enviar -->
            <div class="mt-4">
                <button type="submit" class="btn btn-green">Completar Pago</button>
            </div>
        </form>
    </div>

    <!-- Contenedor del Footer -->
    <div id="footer-container"></div>

    <!-- Incluir el menú y el footer con JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetch('../partials/menu.php')
                .then(response => response.text())
                .then(data => {
                    document.getElementById('menu-container').innerHTML = data;
                });

            fetch('../partials/footer.php')
                .then(response => response.text())
                .then(data => {
                    document.getElementById('footer-container').innerHTML = data;
                });

            // Funcionalidad para mostrar/ocultar buscador avanzado
            document.getElementById('advancedSearchToggle').addEventListener('click', function() {
                const advancedSearch = document.getElementById('advancedSearch');
                advancedSearch.style.display = (advancedSearch.style.display === 'none' || advancedSearch.style.display === '') ? 'block' : 'none';
            });


            // Mostrar campos según el método de pago seleccionado
            const paymentMethodInputs = document.querySelectorAll('input[name="paymentMethod"]');
            const creditCardInfo = document.getElementById('creditCardInfo');
            const paypalInfo = document.getElementById('paypalInfo');
            const bankTransferInfo = document.getElementById('bankTransferInfo');

            paymentMethodInputs.forEach(input => {
                input.addEventListener('change', function() {
                    creditCardInfo.classList.add('d-none');
                    paypalInfo.classList.add('d-none');
                    bankTransferInfo.classList.add('d-none');

                    if (this.value === 'creditCard') {
                        creditCardInfo.classList.remove('d-none');
                    } else if (this.value === 'paypal') {
                        paypalInfo.classList.remove('d-none');
                    } else if (this.value === 'bankTransfer') {
                        bankTransferInfo.classList.remove('d-none');
                    }
                });
            });
        });
    </script>
</body>
</html>
