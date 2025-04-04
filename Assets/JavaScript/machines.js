document.addEventListener("DOMContentLoaded", function () {
    const apagarBtn = document.getElementById("btnApagar");
    const checkboxes = document.querySelectorAll(".vm-select");

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener("change", function () {
            const selectedVM = document.querySelector(".vm-select:checked");
            apagarBtn.disabled = !selectedVM;
        });
    });
});

document.getElementById("select-all").addEventListener("change", function () {
    const checkboxes = document.querySelectorAll(".vm-select");
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
        checkbox.dispatchEvent(new Event("change"));
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("formOculto");
    const vmsInput = document.getElementById("vmsInput");

    const getSelectedVMs = () => {
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
    };

    const checkboxes = document.querySelectorAll(".vm-select");
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener("change", function () {
            const selectedVMs = getSelectedVMs();
            vmsInput.value = JSON.stringify(selectedVMs);
        });
    });

    document.getElementById("btnApagar").addEventListener("click", function () {
        const vms = getSelectedVMs();
        vmsInput.value = JSON.stringify(vms);
        form.action = "../../Php/VM/apagar.php";
        form.submit();
    });

    document.getElementById("btnEncender").addEventListener("click", function () {
        const vms = getSelectedVMs();
        vmsInput.value = JSON.stringify(vms);
        form.action = "../../Php/VM/encender.php";
        form.submit();
    });

    document.getElementById("btnEliminar").addEventListener("click", function () {
        const vms = getSelectedVMs();
        const vmEncendida = vms.find(vm => vm.status === "running");
        if (vmEncendida) {
            alert("No se puede eliminar una VM encendida.");
            return;
        }

        vmsInput.value = JSON.stringify(vms);
        form.action = "../../Php/VM/eliminar.php";
        form.submit();
    });

    document.getElementById("btnReiniciar").addEventListener("click", function () {
        const vms = getSelectedVMs();
        vmsInput.value = JSON.stringify(vms);
        form.action = "../../Php/VM/reiniciar.php";
        form.submit();
    });

    document.getElementById("btnConsola").addEventListener("click", function () {
        const vms = getSelectedVMs();
        vmsInput.value = JSON.stringify(vms);
        form.action = "../../Php/VM/consola.php";
        form.submit();
    });
});

function actualizarDatosVM() {
    fetch("../../Php/VM/status.php")
        .then(response => response.json())  // Se convierte la respuesta en JSON
        .then(data => {
            console.log(data);  // Ahora logueamos los datos dentro de la promesa

            data.forEach(vm => {
                const cell = document.querySelector(`td[data-id="${vm.vmid}"]`);
                if (cell) {
                    const row = cell.closest("tr");

                    row.querySelector("td:nth-child(2)").textContent = vm.name;
                    row.querySelector("td:nth-child(3)").innerHTML = `<span class="status-indicator ${vm.status === 'running' ? 'active' : 'inactive'}"></span>${vm.status}`;
                    row.querySelector("td:nth-child(4)").textContent = vm.uptime;
                    row.querySelector("td:nth-child(5)").textContent = vm.cpu;
                    row.querySelector("td:nth-child(6)").textContent = `${vm.mem} / ${vm.maxmem}`;
                    row.querySelector("td:nth-child(7)").textContent = `${vm.disk} / ${vm.maxdisk}`;
                }
            });
        })
        .catch(error => console.error("Error al actualizar datos de VMs:", error));
}

setInterval(actualizarDatosVM, 3000);

document.getElementById('btnEditar').addEventListener('click', function () {
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

    // Seteamos valores en inputs
    document.getElementById('edit-nombre').value = nombre;
    document.getElementById('edit-cpu').value = isNaN(cpu) ? '' : cpu;
    document.getElementById('edit-ram').value = isNaN(ram) ? '' : ram;

    // Formulario: captura cambios
    document.getElementById('edit-form').onsubmit = function (e) {
        e.preventDefault();
        const nuevosDatos = {
            vmid: cells[1].dataset.id,
            node: cells[1].dataset.node,
            nombre: document.getElementById('edit-nombre').value,
            cpu: document.getElementById('edit-cpu').value,
            ram: document.getElementById('edit-ram').value,
            teclado: document.getElementById('edit-teclado').value,
        };
        console.log("Enviar datos editados:", nuevosDatos);
    };
});