// Ajustar el valor de un input
function adjustValue(inputId, adjustment) {
    const input = document.getElementById(inputId);
    if (input) {
        const currentValue = parseInt(input.value, 10);
        const minValue = parseInt(input.min, 10);
        const maxValue = parseInt(input.max, 10);

        let newValue = currentValue + adjustment;

        // Asegurarse de que el nuevo valor esté dentro del rango permitido
        if (newValue < minValue) {
            newValue = minValue;
        } else if (newValue > maxValue) {
            newValue = maxValue;
        }

        input.value = newValue;

        // Recalcular el precio en tiempo real
        calculatePrice();
    }
}

// Calcular el precio total
function calculatePrice() {
    // Obtener valores actuales
    const vcpus = parseInt(document.getElementById('vcpus')?.value) || 0;
    const ram = parseInt(document.getElementById('ram')?.value) || 0;
    const storage = parseInt(document.getElementById('storage')?.value) || 0;

    // Precios base por unidad
    const pricePerVcpu = 10;
    const pricePerRam = 2;
    const pricePerStorage = 0.1;

    // Calcular precio total
    let totalPrice = (vcpus * pricePerVcpu) +
                     (ram * pricePerRam) +
                     (storage * pricePerStorage);

    // Aplicar descuentos por volumen
    if (vcpus >= 8) totalPrice *= 0.9; // 10% descuento para 8+ vCPUs
    if (ram >= 16) totalPrice *= 0.9; // 10% descuento para 16+ GB RAM
    if (storage >= 200) totalPrice *= 0.9; // 10% descuento para 200+ GB

    // Redondear a 2 decimales
    totalPrice = Math.round(totalPrice * 100) / 100;

    // Actualizar el precio mostrado
    updatePriceDisplay(totalPrice);

    // Actualizar el campo oculto para enviar el precio por POST
    updateHiddenPrice(totalPrice);
}

// Actualizar el precio mostrado en la interfaz
function updatePriceDisplay(totalPrice) {
    const priceElement = document.getElementById('price');
    if (priceElement) {
        console.log('Actualizando precio:', totalPrice); // Verifica que el precio calculado sea correcto
        priceElement.textContent = totalPrice.toFixed(2) + '€';
    }
}

// Actualizar el campo oculto para enviar el precio por POST
function updateHiddenPrice(totalPrice) {
    const hiddenPriceInput = document.getElementById('hidden-price');
    if (hiddenPriceInput) {
        hiddenPriceInput.value = totalPrice.toFixed(2); // Actualiza el valor del campo oculto
    }
}

function initializeConfigurator() {
    const configurator = document.getElementById('vm-configurator');
    if (configurator) {
        // Calcular precio inicial
        calculatePrice();

        // Añadir event listeners para los inputs
        const inputs = ['vcpus', 'ram', 'storage'];
        inputs.forEach(id => {
            const input = document.getElementById(id);
            if (input) {
                // Escuchar tanto el evento 'input' como 'change'
                input.addEventListener('input', calculatePrice);
                input.addEventListener('change', calculatePrice);
            }
        });
    }
}

document.addEventListener('DOMContentLoaded', function() {
    initializeConfigurator();
});