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

function actualizarDatos(config) {
    fetch(config.statusUrl)
        .then(response => response.json())
        .then(data => {
            console.log(data);
            data.forEach(vm => {
                const cell = document.querySelector(`td[data-id="${vm.vmid}"]`);
                if (cell) {
                    const row = cell.closest("tr");

                    // Excluir la fila de edición (edit-row) de las actualizaciones
                    if (row.id !== 'edit-row') {
                        // Actualizar nombre
                        row.querySelector(config.nameCellSelector).textContent = vm.name;
                        row.querySelector(config.statusCellSelector).innerHTML = `<span class="status-indicator ${vm.status === 'running' ? 'active' : 'inactive'}"></span>${vm.status}`;
                        row.querySelector(config.uptimeCellSelector).textContent = vm.uptime;
                        row.querySelector(config.cpuCellSelector).textContent = vm.cpu;
                        row.querySelector(config.memoryCellSelector).textContent = `${vm.mem} / ${vm.maxmem}`;
                        row.querySelector(config.diskCellSelector).textContent = `${vm.disk} / ${vm.maxdisk}`;
                    }
                }
            });
        })
        .catch(error => console.error("Error al actualizar datos:", error));
}

function initEditButton(config) {
    document.getElementById(config.editBtnId).addEventListener('click', function () {
        const checked = document.querySelector(`${config.checkboxSelector}:checked`);
        if (!checked) return;

        const row = checked.closest('tr');
        const editRow = document.getElementById(config.editRowId);
        const formEditar = document.getElementById(config.editFormId);

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
        const guardarBtn = document.getElementById(config.saveBtnId);
        guardarBtn.replaceWith(guardarBtn.cloneNode(true)); // Elimina eventos previos
        document.getElementById(config.saveBtnId).addEventListener('click', function (e) {
            e.preventDefault();

            const nuevosDatos = {
                vmid: cells[1].dataset.id,
                node: cells[1].dataset.node,
                cpu: document.getElementById(config.editCpuId).value,
                ram: document.getElementById(config.editRamId).value,
                swap: document.getElementById(config.editSwapId).value,
            };

            const form = document.getElementById(config.editFormId);
            let jsonInput = document.getElementById('json-data');
            if (!jsonInput) {
                jsonInput = document.createElement('input');
                jsonInput.type = 'hidden';
                jsonInput.name = 'json_data';
                jsonInput.id = 'json-data';
                form.appendChild(jsonInput);
            }
            jsonInput.value = JSON.stringify(nuevosDatos);

            form.action = config.editActionUrl;
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