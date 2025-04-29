/**
 * Funció per iniciar els botons de "Encendre", "Apagar", "Reiniciar" i "Eliminar" 
 * quan es selecciona una màquina virtual.
 */
function initializeBulkActions() {
    const seleccionar_todo_checkbox = document.getElementById('select-all');
    const checkbox_maquinas = document.querySelectorAll('.vm-select');
    const accion_botones = document.querySelectorAll('.bulk-actions button:not(.btn-success)');

    function actualizarEstadoBotones() {
        const checkbox_seleccionados = document.querySelectorAll('.vm-select:checked');
        accion_botones.forEach(button => {
            button.disabled = checkbox_seleccionados.length === 0;
        });
    }

    if (seleccionar_todo_checkbox) {
        seleccionar_todo_checkbox.addEventListener('change', function() {
            checkbox_maquinas.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            actualizarEstadoBotones();
        });
    }

    checkbox_maquinas.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const todos_seleccionados = Array.from(checkbox_maquinas).every(cb => cb.checked);
            const ninguno_seleccionado = Array.from(checkbox_maquinas).every(cb => !cb.checked);
            seleccionar_todo_checkbox.checked = todos_seleccionados;
            seleccionar_todo_checkbox.indeterminate = !todos_seleccionados && !ninguno_seleccionado;

            actualizarEstadoBotones();
        });
    });

    actualizarEstadoBotones();
}

// Inicializar todo al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    initializeBulkActions();
});