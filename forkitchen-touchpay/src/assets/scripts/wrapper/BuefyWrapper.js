import { DialogProgrammatic as Dialog, ModalProgrammatic as Modal } from "buefy";

const openDialog = (message, confirmText, onConfirm) => {
    Dialog.alert({
        message: message.msg,
        type: message.type,
        hasIcon: true,
        icon: "times-circle",
        iconPack: "fa",
        ariaRole: "alertdialog",
        ariaModal: true,
        confirmText,
        onConfirm,
    });
};

const openModal = (component, fullScreen) => {
    Modal.open({
        parent: this,
        component,
        fullScreen,
        trapFocus: true,
    });
};

export { openDialog, openModal };
