document.addEventListener("DOMContentLoaded", function () {
    const config = {
        apagarBtnId: "btnApagar",
        encenderBtnId: "btnEncender",
        reiniciarBtnId: "btnReiniciar",
        consolaBtnId: "btnConsola",
        eliminarBtnId: "btnEliminar",
        selectAllId: "select-all",
        checkboxSelector: ".vm-select",
        formId: "formOculto",
        vmsInputId: "vmsInput",
        nameCellSelector: "td:nth-child(2)",
        statusCellSelector: "td:nth-child(3)",
        uptimeCellSelector: "td:nth-child(4)",
        cpuCellSelector: "td:nth-child(5)",
        memoryCellSelector: "td:nth-child(6)",
        diskCellSelector: "td:nth-child(7)",
        statusUrl: "../../Php/VM/status.php",
        editBtnId: "btnEditar",
        editRowId: "edit-row",
        editFormId: "edit-form",
        saveBtnId: "btnGuardar",
        editNombreId: "vmid",
        editCpuId: "vcpus",
        editRamId: "ram",
        editTecladoId: "edit-teclado",
        editActionUrl: "../../Php/VM/editar.php",
        actions: {
            apagar: "../../Php/VM/apagar.php",
            encender: "../../Php/VM/encender.php",
            reiniciar: "../../Php/VM/reiniciar.php",
            consola: "../../Php/VM/consola.php",
            eliminar: "../../Php/VM/eliminar.php",
        }
    };

    initCheckboxEvents(config);
    initActionButtons(config);
    editarFila(config);
    setInterval(() => actualizarDatosVM(config), 3000);
});