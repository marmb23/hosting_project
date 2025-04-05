function initCheckboxEvents() {
    const apagarBtn = document.getElementById("btnApagar");
    const checkboxes = document.querySelectorAll(".vm-select");

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener("change", function () {
            const selectedVM = document.querySelector(".vm-select:checked");
            apagarBtn.disabled = !selectedVM;
        });
    });

    document.getElementById("select-all").addEventListener("change", function () {
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
            checkbox.dispatchEvent(new Event("change"));
        });
    });
}

function getSelectedVMs() {
    const selectedCheckboxes = document.querySelectorAll(".vm-select:checked");
    return Array.from(selectedCheckboxes).map(cb => {
        const row = cb.closest("tr");
        const nameCell = row.querySelector("td:nth-child(2)");
        const isRunning = row.querySelector("td:nth-child(3) span").classList.contains("active");
        return {
            vmid: nameCell.getAttribute("data-id"),
            node: nameCell.getAttribute("data-node"),
            status: isRunning ? "running" : "stopped"
        };
    });
}

function initActionButtons() {
    const form = document.getElementById("formOculto");
    const vmsInput = document.getElementById("vmsInput");

    const configureButton = (buttonId, actionUrl, validateFn = null) => {
        document.getElementById(buttonId).addEventListener("click", function () {
            const vms = getSelectedVMs();
            if (validateFn && !validateFn(vms)) return;

            vmsInput.value = JSON.stringify(vms);
            form.action = actionUrl;
            form.submit();
        });
    };

    configureButton("btnApagar", "../../Php/LXC/apagar.php");
    configureButton("btnEncender", "../../Php/LXC/encender.php");
    configureButton("btnReiniciar", "../../Php/LXC/reiniciar.php");
    configureButton("btnConsola", "../../Php/LXC/consola.php");
    configureButton("btnEliminar", "../../Php/LXC/eliminar.php", vms => {
        const vmEncendida = vms.find(vm => vm.status === "running");
        if (vmEncendida) {
            alert("No se puede eliminar un contenedor encendido.");
            return false;
        }
        return true;
    });
}

function actualizarDatosContainer() {
    fetch("../../Php/LXC/status.php")
        .then(response => response.json())
        .then(data => {
            console.log(data);
            data.forEach(vm => {
                const cell = document.querySelector(`td[data-id="${vm.vmid}"]`);
                if (cell) {
                    const row = cell.closest("tr");

                    // Excluir la fila de edición (edit-row) de las actualizaciones
                    if (row.id !== 'edit-row') {
                        row.querySelector("td:nth-child(2)").textContent = vm.name;
                        row.querySelector("td:nth-child(3)").innerHTML = `<span class="status-indicator ${vm.status === 'running' ? 'active' : 'inactive'}"></span>${vm.status}`;
                        row.querySelector("td:nth-child(4)").textContent = vm.uptime;
                        row.querySelector("td:nth-child(5)").textContent = vm.cpu;
                        row.querySelector("td:nth-child(6)").textContent = `${vm.mem} / ${vm.maxmem}`;
                        row.querySelector("td:nth-child(7)").textContent = `${vm.disk} / ${vm.maxdisk}`;
                    }
                }
            });
        })
        .catch(error => console.error("Error al actualizar datos de VMs:", error));
}

function initEditButton() {
    document.getElementById('btnEditar').addEventListener('click', function () {
        const checked = document.querySelector('.vm-select:checked');
        if (!checked) return;

        const row = checked.closest('tr');
        const editRow = document.getElementById('edit-row');
        const formEditar = document.getElementById('formEditar');

        // Mostrar la fila de edición y el formulario con transición
        row.insertAdjacentElement('afterend', editRow);
        toggleVisibility(editRow, true);
        toggleVisibility(formEditar, true);

        // Obtener datos de la fila seleccionada
        const cells = row.querySelectorAll('td');
        const nombre = cells[1].innerText.trim();
        const cpu = parseFloat(cells[4].innerText);
        const ram = parseFloat(cells[5].innerText);


        // Configurar el botón de guardar
        const guardarBtn = document.getElementById('btnGuardar');
        guardarBtn.replaceWith(guardarBtn.cloneNode(true)); // Elimina eventos previos
        document.getElementById('btnGuardar').addEventListener('click', function (e) {
            e.preventDefault();

            const nuevosDatos = {
                vmid: cells[1].dataset.id,
                node: cells[1].dataset.node,
                cpu: document.getElementById('edit-cpu').value,
                ram: document.getElementById('edit-ram').value,
                swap: document.getElementById('edit-swap').value,
            };

            const form = document.getElementById('edit-form');
            let jsonInput = document.getElementById('json-data');
            if (!jsonInput) {
                jsonInput = document.createElement('input');
                jsonInput.type = 'hidden';
                jsonInput.name = 'json_data';
                jsonInput.id = 'json-data';
                form.appendChild(jsonInput);
            }
            jsonInput.value = JSON.stringify(nuevosDatos);

            form.action = '../../Php/LXC/editar.php';
            form.submit();
        });

        // Ocultar la fila de edición y el formulario si se deselecciona el checkbox
        checked.addEventListener('change', function () {
            if (!this.checked) {
                toggleVisibility(editRow, false);
                toggleVisibility(formEditar, false);
            }
        });
    });
}

// Función reutilizable para manejar transiciones
function toggleVisibility(element, show) {
    if (show) {
        element.classList.remove('hidden');
        element.classList.add('visible');
        element.style.display = ''; // Asegúrate de que sea visible
    } else {
        element.classList.remove('visible');
        element.classList.add('hidden');
        element.addEventListener('transitionend', function () {
            element.style.display = 'none'; // Oculta completamente después de la transición
        }, { once: true }); // Asegura que el evento se ejecute solo una vez
    }
}

document.addEventListener("DOMContentLoaded", function () {
    initCheckboxEvents();
    initActionButtons();
    initEditButton();
    setInterval(actualizarDatosContainer, 3000);
});