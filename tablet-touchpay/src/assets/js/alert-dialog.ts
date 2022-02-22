import { DialogProgrammatic as Dialog } from "buefy";
import { MessageConst } from "@/store/types";

export const showAlertDialog = (message: MessageConst) => {
    Dialog.alert({
        message: message.msg,
        type: message.type,
        hasIcon: true,
        size: "is-large",
    });
};

// eslint-disable-next-line
export const showConfirmDialog = (message: MessageConst, onConfirm: (value: string) => any) => {
    Dialog.confirm({
        message: message.msg,
        type: message.type,
        hasIcon: true,
        onConfirm,
    });
};
