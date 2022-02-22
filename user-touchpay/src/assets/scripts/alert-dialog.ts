import { DialogProgrammatic as Dialog } from "buefy";

export const showAlertDialog = (message: string, type: string, onConfirm?: (value: string) => any) => {
    Dialog.alert({
        message,
        type,
        hasIcon: true,
        onConfirm,
    });
};

// eslint-disable-next-line
export const showConfirmDialog = (message: string, type: string, onConfirm: (value: string) => any) => {
    Dialog.confirm({
        message,
        type,
        hasIcon: true,
        onConfirm,
    });
};
