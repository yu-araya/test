import { Component, Mixins } from "vue-property-decorator";
import StoreMixin from "@/store/StoreMixin";
import { RequestData, MessageConst, MenuData, OrderData } from "@/store/types";
import { FoodHistoryInfos } from "@/assets/js/indexedDB";
import axios, { AxiosResponse } from "axios";
import { messages, urls } from "@/assets/js/constant";
import { showAlertDialog } from "@/assets/js/alert-dialog";
import { diffToNextEndOfDate } from "@/assets/js/date";

@Component({
    filters: {
        /**
         * カンマ編集
         * @param value
         */
        addComma(value: string) {
            if (!value) return 0;
            return value.toLocaleString();
        },
    },
})
export default class MixinComponent extends Mixins(StoreMixin) {
    public db: FoodHistoryInfos = new FoodHistoryInfos();
    public interval = 0;
    private sounds = new Map<string, HTMLAudioElement>();
    public reloadInterval = 0;

    constructor() {
        super();
        this.sounds.set("choice", new Audio(require(`@/assets/sounds/choice.mp3`)));
        this.sounds.set("error", new Audio(require(`@/assets/sounds/error.mp3`)));
        this.sounds.set("success", new Audio(require(`@/assets/sounds/success.mp3`)));
    }

    /**
     * アプリの現在のバージョンを取得する
     */
    public async getAppVersion() {
        const res = (await axios
            .get(`${urls.getVersionUrl}?contents=tablet`)
            // eslint-disable-next-line
            .catch((err) => console.log(err?.response?.data?.message, err))) as AxiosResponse;
        const version = res?.data?.version;
        if (version) {
            if (localStorage.version != version) window.location.reload();
            localStorage.setItem("version", version);
        } else {
            showAlertDialog(messages.error3);
        }
    }

    /**
     * 次の24時になったらリロードを実行する
     */
    public setNextTimer() {
        const diff = diffToNextEndOfDate();
        setTimeout(() => {
            this.reloadForUpdate();
        }, diff); // 次の24時まで待機
    }

    /**
     * バージョンが上がっていたらリロードする
     */
    private async reloadForUpdate() {
        const version = await this.getAppVersion();
        if (localStorage.version != version) window.location.reload();
        this.setNextTimer();
    }

    public async getEmployeeIdWhenCorrectCard(key: string) {
        try {
            const response = await axios.get(`${urls["getEmpUrl"]}${key}`);
            const employeeInfo = response.data["EmployeeInfo"];
            if (employeeInfo) {
                return employeeInfo["employee_id"];
            } else {
                return this.effectiveCardCheck(key);
            }
        } catch (err) {
            return this.effectiveCardCheck(key);
        }
    }

    public async getEmployeeIdWhenCorrectId(employeeId: string): Promise<boolean> {
        try {
            const response = await axios.get(`${urls["confirmEmployeeIdUrl"]}${employeeId}`);
            const result = response.data["result"];
            return result || this.effectiveIdCheck(employeeId);
        } catch (err) {
            return this.effectiveIdCheck(employeeId);
        }
    }

    public async regist(employeeId: string) {
        const requestData = await this.createRegistRequestData(employeeId);
        try {
            // 普通の喫食登録
            await this.registFoodInfo(requestData);
            this.clearOrderData();
            this.createMessage(messages["success1"]);
            this.playSound("success");
            this.setLoading(false);
        } catch (errMsg) {
            this.clearOrderData();
            await this.failedLogQueued(requestData);
            throw errMsg;
        }
    }

    private async createRegistRequestData(employeeId: string): Promise<RequestData> {
        const cardReceptDateTime = new Date()
        const menuLists = this.menuLists;
        const orderLists = await this.getOrderLists(menuLists);
        const result = {
            employeeId: employeeId,
            instrumentDivision: "1",
            order: orderLists,
            cardReceptDateTime: cardReceptDateTime,
        };
        return result;
    }

    private async getOrderLists(menuLists: MenuData[]): Promise<OrderData[]> {
        const result: OrderData[] = [];
        menuLists
            .filter((menuData) => {
                return menuData.count > 0;
            })
            .forEach((menuData) => {
                result.push({
                    foodDivision: menuData.food_division,
                    count: menuData.count,
                    foodCost: menuData.food_cost,
                });
            });
        return result;
    }

    private async failedLogQueued(requestData: RequestData) {
        this.db.insertRequestData(requestData);
        this.setRecordCount(await this.db.allLogsCount());
        this.sendLogScheduler();
    }


    public async registFoodInfo(requestData: RequestData) {
        try {
            await axios.post(urls["orderUrl"], requestData);
            // IndexedDbにため込んだデータを送信
            await this.sendlog();
        } catch (err) {
            throw messages["success2"];
        }
    }

    public async sendlog() {
        if (this.recordCount === 0) {
            return;
        }
        const queuedLog = await this.db.selectAllLogs();
        // 一旦全削除する
        await this.db.deleteAllLogs();
        const failedLog: RequestData[] = [];
        for (const request of queuedLog) {
            try {
                await this.registFoodInfo(request);
            } catch (err) {
                failedLog.push(request);
            }
        }
        // APIで登録に失敗したリクエストだけ再び保存
        await this.db.bulcInsertRequestData(failedLog);
        this.setRecordCount(await this.db.allLogsCount());
    }

    public sendLogScheduler() {
        clearInterval(this.interval);
        this.interval = setInterval(async () => {
            await this.sendlog();
            if (this.recordCount === 0) clearInterval(this.interval);
        }, 5 * 60 * 1000); // 5m（300000ms）
    }

    public playSound(fileName: string) {
        const player = this.sounds.get(fileName);
        player?.play();
    }

    public createMessage(message: MessageConst) {
        this.createMessageModal(message.msg, message.type);
        setTimeout(() => {
            this.closeModal();
        }, 3000);
    }
}
