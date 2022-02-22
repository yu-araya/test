import { Component, Vue, Mixins } from "vue-property-decorator";
import EmployeeStore from "@/store/states/EmployeeStore";
import InstrumentStore from "@/store/states/InstrumentStore";
import MenuStore from "@/store/states/MenuStore";
import SoldOutStore from "@/store/states/SoldOutStore";
import TabListStore from "@/store/states/TabListStore";
import axios from "axios";
import { urls } from "@/assets/js/constant";
import { SoldOutInfos } from "@/assets/js/indexedDB";
import { MessageModalData } from "@/store/types";

const soldOutInfos = new SoldOutInfos();

type Store = {
    recordCount: number;
    messageModal: MessageModalData;
    isLoading: boolean;
    useQrcode: boolean;
    useQrcodeReaderDevice: boolean;
};

export const state = Vue.observable<Store>({
    recordCount: 0,
    messageModal: {
        isMessageModalActive: false,
        message: "",
        messageType: "",
    },
    isLoading: false,
    useQrcode: false,
    useQrcodeReaderDevice: false,
});

@Component
export default class StoreMixin extends Mixins(EmployeeStore, InstrumentStore, SoldOutStore, TabListStore, MenuStore) {
    public get recordCount() {
        return state.recordCount;
    }

    public get messageModal() {
        return state.messageModal;
    }

    public get isLoading() {
        return state.isLoading;
    }

    public setLoading(value: boolean) {
        state.isLoading = value;
    }
    
    public get useQrcode() {
        return state.useQrcode;
    }

    public get useQrcodeReaderDevice() {
        return state.useQrcodeReaderDevice;
    }

    public async getOption() {
        await axios.get(urls["getOptionsUrl"]).then((response) => {
            state.useQrcode = Boolean(response?.data["qrcodeFlg"]);
            state.useQrcodeReaderDevice = Boolean(response?.data["qrcodeDevice"]);
        });
    }

    /**
     * タブリスト、事業所リスト、社員リスト、メニューリスト、売り切れ設定リストを初期化
     */
    public async initialize() {
        await axios.get(urls["reloadUrl"]).then((response) => {
            this.initTabLists(response.data["Tab"]);
            this.initInstrumentLists(response.data["InstrumentDivision"]);
            this.initEmployeeLists(response.data["EmployeeInfo"]);
            this.initMenuLists(response.data["Menu"]);
        });
        this.initSoldoutList(await soldOutInfos.selectSoldouts());
    }

    /**
     *  indexedDB件数を取得
     *
     * @param count
     */
    public setRecordCount(count: number) {
        state.recordCount = count;
    }

    /**
     * メッセージを作成、モーダルを表示
     *
     * @param message
     * @param type { is-success, is-danger, is-info}
     */
    public createMessageModal(message: string, type: string) {
        state.messageModal = {
            isMessageModalActive: true,
            message: message,
            messageType: type,
        };
    }

    /**
     * モーダルを閉じる
     */
    public closeModal() {
        state.messageModal = {
            isMessageModalActive: false,
            message: "",
            messageType: "",
        };
    }

    public async decodeUserId(qrDecodeString: string) {
        console.log("qrDecodeString:" + qrDecodeString);
        let base64DecodedString = "";
        try {
            base64DecodedString = atob(qrDecodeString);
        } catch (error) {
            console.log(error);
        }
        console.log("base64DecodedString:" + base64DecodedString);
        return base64DecodedString.replace("userId", "");
    }
}
