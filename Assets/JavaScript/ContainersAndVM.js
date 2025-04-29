/**
 * Inicialitza els esdeveniments dels checkbox per habilitar o deshabilitar botons segons la selecció.
 * @param {Object} config - Configuració dels selectors i IDs necessaris.
 * @param {string} config.apagarBtnId - L'ID del botó d'apagar.
 * @param {string} config.checkboxSelector - El selector dels checkbox.
 * @param {string} config.selectAllId - L'ID del checkbox per seleccionar tots.
 */
function initCheckboxEvents(config) {
    const apagarBtn = document.getElementById(config.apagarBtnId);
    const checkboxes = document.querySelectorAll(config.checkboxSelector);

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener("change", function () {
            const selectedVM = document.querySelector(`${config.checkboxSelector}:checked`);
            apagarBtn.disabled = !selectedVM;
        });
    });

    document.getElementById(config.selectAllId).addEventListener("change", function () {
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
            checkbox.dispatchEvent(new Event("change"));
        });
    });
}

/**
 * Obté les màquines virtuals seleccionades a la taula.
 * @param {Object} config - Configuració dels selectors necessaris.
 * @param {string} config.checkboxSelector - El selector dels checkbox.
 * @param {string} config.nameCellSelector - El selector de la cel·la amb el nom de la màquina.
 * @param {string} config.statusCellSelector - El selector de la cel·la amb l'estat de la màquina.
 * @returns {Array<Object>} - Una llista d'objectes amb informació de les màquines seleccionades.
 */
function getSelectedVMs(config) {
    const selectedCheckboxes = document.querySelectorAll(`${config.checkboxSelector}:checked`);
    return Array.from(selectedCheckboxes).map(cb => {
        const row = cb.closest("tr");
        const nameCell = row.querySelector(config.nameCellSelector);
        const isRunning = row.querySelector(config.statusCellSelector).classList.contains("active");
        return {
            vmid: nameCell.getAttribute("data-id"),
            node: nameCell.getAttribute("data-node"),
            status: isRunning ? "running" : "stopped"
        };
    });
}

/**
 * Inicialitza els botons d'acció per executar operacions sobre les màquines seleccionades.
 * @param {Object} config - Configuració dels botons i accions.
 * @param {string} config.formId - L'ID del formulari.
 * @param {string} config.vmsInputId - L'ID del camp ocult per enviar les màquines seleccionades.
 * @param {Object} config.actions - Les URLs de les accions (apagar, encendre, reiniciar, etc.).
 * @param {string} config.editActionUrl - La URL per editar màquines.
 */
function initActionButtons(config) {
    const form = document.getElementById(config.formId);
    const vmsInput = document.getElementById(config.vmsInputId);

    const configureButton = (buttonId, actionUrl, validateFn = null) => {
        document.getElementById(buttonId).addEventListener("click", function () {
            const vms = getSelectedVMs(config);
            if (validateFn && !validateFn(vms)) return;

            vmsInput.value = JSON.stringify(vms);
            form.action = actionUrl;
            form.submit();
        });
    };

    configureButton(config.apagarBtnId, config.actions.apagar);
    configureButton(config.encenderBtnId, config.actions.encender);
    configureButton(config.reiniciarBtnId, config.actions.reiniciar);
    configureButton(config.saveBtnId, config.editActionUrl);
    configureButton(config.consolaBtnId, config.actions.consola);
    configureButton(config.eliminarBtnId, config.actions.eliminar, vms => {
        const vmEncendida = vms.find(vm => vm.status === "running");
        if (vmEncendida) {
            alert("No se puede eliminar una VM o contenedor encendido.");
            return false;
        }
        return true;
    });
}

/**
 * Actualitza les dades dels contenidors (LXC) a la taula amb informació actualitzada del servidor.
 */
function actualizarDatosLXC() {
    fetch("../../Php/LXC/status.php")
        .then(response => response.json())
        .then(data => {
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
        .catch(error => console.error("Error al actualizar datos:", error));
}

/**
 * Actualitza les dades de les màquines virtuals (VM) a la taula amb informació actualitzada del servidor.
 */
function actualizarDatosVM() {
    fetch("../../Php/VM/status.php")
        .then(response => response.json())
        .then(data => {
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
        .catch(error => console.error("Error al actualizar datos:", error));
}

/**
 * Mostra la fila d'edició sota la màquina seleccionada i omple els inputs amb les dades actuals.
 */
function editarFila() {
    const checked = document.querySelector('.vm-select:checked');

    if (!checked) return;

    const row = checked.closest('tr');
    const editRow = document.getElementById('edit-row');
    
    // Insertamos justo debajo
    row.insertAdjacentElement('afterend', editRow);
    editRow.style.display = 'table-row';

    // Obtenemos los datos actuales desde la fila
    const cells = row.querySelectorAll('td');
    const nombre = cells[1].innerText.trim();
    const cpu = parseFloat(cells[4].innerText);
    const ram = parseFloat(cells[5].innerText);

    const vmid = row.querySelector('td[data-id]').getAttribute('data-id');
    const node = row.querySelector('td[data-id]').getAttribute('data-node');
    document.getElementById('edit-vmid').value = vmid;
    document.getElementById('edit-node').value = node;

    // Seteamos valores en inputs
    document.getElementById('edit-nombre').value = nombre;
    document.getElementById('edit-cpu').value = isNaN(cpu) ? '' : cpu;
    document.getElementById('edit-ram').value = isNaN(ram) ? '' : ram;
}

// Asignar la función al botón
document.getElementById('btnEditar').addEventListener('click', editarFila);

/**
 * Mostra o amaga un element HTML canviant les seves classes de visibilitat.
 * @param {HTMLElement} element - L'element HTML a modificar.
 * @param {boolean} show - Indica si l'element s'ha de mostrar (true) o amagar (false).
 */
function toggleVisibility(element, show) {
    if (show) {
        element.classList.remove('hidden');
        element.classList.add('visible');
    } else {
        element.classList.remove('visible');
        element.classList.add('hidden');
    }
}

/**
 * Envia un formulari en format JSON al servidor quan es fa clic en un botó específic.
 * @param {string} btnId - L'ID del botó que activa l'enviament del formulari.
 */
function enviarFormularioJson(btnId) {
    const btn = document.getElementById();

    if (!btn) {
        console.error(`No se encontró un botón con id: ${btnId}`);
        return;
    }

    btn.addEventListener('click', function(event) {
        event.preventDefault(); // Evita el envío tradicional

        const form = btn.closest('form');
        if (!form) {
            console.error(`No se encontró un formulario asociado al botón ${btnId}.`);
            return;
        }

        const formData = new FormData(form);
        const jsonData = {};

        formData.forEach((value, key) => {
            jsonData[key] = value;
        });

        fetch(form.action, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(jsonData)
        })
        .then(response => response.json())
        .then(data => {
            console.log("Respuesta del servidor:", data);
            // Aquí puedes poner mensajes de éxito, redireccionar, etc.
        })
        .catch(error => {
            console.error("Error al enviar el formulario:", error);
        });
    });
}