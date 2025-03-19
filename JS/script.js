document.addEventListener("DOMContentLoaded", () => {
    const vmData = [
        { name: "Máquina 1", status: "active", cpu: "25%", ram: "4GB / 8GB" },
        { name: "Máquina 2", status: "inactive", cpu: "0%", ram: "0GB / 16GB" },
    ];

    const tbody = document.querySelector(".vm-table tbody");

    vmData.forEach(vm => {
        const row = document.createElement("tr");

        row.innerHTML = `
            <td>${vm.name}</td>
            <td>
                <span class="status-indicator ${vm.status}"></span> 
                ${vm.status === "active" ? "Activa" : "Inactiva"}
            </td>
            <td>${vm.cpu}</td>
            <td>${vm.ram}</td>
            <td>
                <button class="btn btn-stop"><i class="fas fa-power-off"></i> Apagar</button>
                <button class="btn btn-start"><i class="fas fa-play"></i> Encender</button>
            </td>
        `;

        tbody.appendChild(row);
    });
});