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
        statusUrl: "../../Php/LXC/status.php",
        editBtnId: "btnEditar",
        editRowId: "edit-row",
        editFormId: "edit-form",
        saveBtnId: "btnGuardar",
        editCpuId: "edit-cpu",
        editRamId: "edit-ram",
        editSwapId: "edit-swap",
        editActionUrl: "../../Php/LXC/editar.php",
        actions: {
            apagar: "../../Php/LXC/apagar.php",
            encender: "../../Php/LXC/encender.php",
            reiniciar: "../../Php/LXC/reiniciar.php",
            consola: "../../Php/LXC/consola.php",
            eliminar: "../../Php/LXC/eliminar.php"
        }
    };

    initCheckboxEvents(config);
    initActionButtons(config);
    //initEditButton(config);
    setInterval(() => actualizarDatosLXC(), 3000);
});