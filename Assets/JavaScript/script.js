// Funciones para el configurador de servidores virtuales
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

function calculatePrice() {

    // Obtener valores actuales
    const vcpus = parseInt(document.getElementById('vcpus')?.value) || 0;
    const ram = parseInt(document.getElementById('ram')?.value) || 0;
    const storage = parseInt(document.getElementById('storage')?.value) || 0;
    const traffic = parseInt(document.getElementById('traffic')?.value) || 0;
    const ips = parseInt(document.getElementById('ips')?.value) || 0;

    // Precios base por unidad
    const pricePerVcpu = 10;
    const pricePerRam = 2;
    const pricePerStorage = 0.1;
    const pricePerTraffic = 5;
    const pricePerIp = 3;

    // Calcular precio total
    let totalPrice = (vcpus * pricePerVcpu) +
                     (ram * pricePerRam) +
                     (storage * pricePerStorage) +
                     (traffic * pricePerTraffic) +
                     (ips * pricePerIp);

    // Aplicar descuentos por volumen
    if (vcpus >= 8) totalPrice *= 0.9; // 10% descuento para 8+ vCPUs
    if (ram >= 16) totalPrice *= 0.9; // 10% descuento para 16+ GB RAM
    if (storage >= 200) totalPrice *= 0.9; // 10% descuento para 200+ GB

    // Redondear a 2 decimales
    totalPrice = Math.round(totalPrice * 100) / 100;

    // Actualizar el precio mostrado
    const priceElement = document.getElementById('total-price');
    if (priceElement) {
        console.log('Actualizando precio:', totalPrice); // Verifica que el precio calculado sea correcto
        priceElement.textContent = totalPrice.toFixed(2) + '€';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar el configurador
    const configurator = document.getElementById('vm-configurator');
    if (configurator) {
        // Calcular precio inicial
        calculatePrice();

        // Añadir event listeners para los inputs
        const inputs = ['vcpus', 'ram', 'storage', 'traffic', 'ips'];
        inputs.forEach(id => {
            const input = document.getElementById(id);
            if (input) {
                // Escuchar tanto el evento 'input' como 'change'
                input.addEventListener('input', calculatePrice);
                input.addEventListener('change', calculatePrice);
            }
        });

        // Manejar el envío del formulario
        configurator.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Recopilar datos del formulario
            const formData = new FormData(configurator);
            const config = Object.fromEntries(formData.entries());

            // Aquí iría la lógica para procesar la configuración
            console.log('Configuración del servidor:', config);
            
            // Mostrar mensaje de éxito
            alert('¡Servidor virtual configurado correctamente!');
        });
    }
});

document.addEventListener('DOMContentLoaded', function() {
    // Funcionalidad de selección múltiple
    const seleccionar_todo_checkbox = document.getElementById('select-all');
    const checkbox_maquinas = document.querySelectorAll('.vm-select');
    const accion_botones = document.querySelectorAll('.bulk-actions button:not(.btn-success)');

    // Función para actualizar el estado de los botones de acción múltiple
    function actualizarEstadoBotones() {
        const checkbox_seleccionados = document.querySelectorAll('.vm-select:checked');
        accion_botones.forEach(button => {
            button.disabled = checkbox_seleccionados.length === 0;
        });
    }

    // Manejar la selección de todas las máquinas
    seleccionar_todo_checkbox.addEventListener('change', function() {
        checkbox_maquinas.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        actualizarEstadoBotones();
    });

    // Manejar la selección individual de máquinas
    checkbox_maquinas.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            // Actualizar el estado del checkbox "Seleccionar todo"
            const todos_seleccionados = Array.from(checkbox_maquinas).every(cb => cb.checked);
            const ninguno_seleccionado = Array.from(checkbox_maquinas).every(cb => !cb.checked);
            seleccionar_todo_checkbox.checked = todos_seleccionados;
            seleccionar_todo_checkbox.indeterminate = !todos_seleccionados && !ninguno_seleccionado;
            
            actualizarEstadoBotones();
        });
    });

    // Inicializar el estado de los botones
    actualizarEstadoBotones();

    // Inicializar el configurador
    const configurator = document.getElementById('vm-configurator');
    if (configurator) {
        // Calcular precio inicial
        calculatePrice();

        // Añadir event listeners para los inputs
        const inputs = ['vcpus', 'ram', 'storage', 'traffic', 'ips'];
        inputs.forEach(id => {
            const input = document.getElementById(id);
            if (input) {
                input.addEventListener('change', calculatePrice);
            }
        });

        // Manejar el envío del formulario
        configurator.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Recopilar datos del formulario
            const formData = new FormData(configurator);
            const config = Object.fromEntries(formData.entries());

            // Aquí iría la lógica para procesar la configuración
            console.log('Configuración del servidor:', config);
            
            // Mostrar mensaje de éxito
            alert('¡Servidor virtual configurado correctamente!');
        });
    }
});