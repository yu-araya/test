import { Component, Vue } from "vue-property-decorator";
import { ReservationData } from "@/assets/scripts/types";
import { formatDateString } from "@/assets/scripts/date";
import axios from "axios";
import { messages, urls } from "@/assets/scripts/constant";
import { showAlertDialog } from "@/assets/scripts/alert-dialog";

type State = {
    reservationData: ReservationData[];
};

const state = Vue.observable<State>({
    reservationData: [],
});

@Component
export default class ReservationDataStore extends Vue {
    public get reservationData(): ReservationData[] {
        return state.reservationData;
    }

    /**
     * 社員の前後２ヶ月分の予約状況を取得する
     *
     * @param employeeId
     */
    public async loadReservationData(employeeId: string) {
        state.reservationData = [];
        await axios
            .post(urls.loadReservationDataUrl, { params: { employeeId: employeeId } })
            .then((res) => {
                const result = res.data.reservationData;
                result.forEach((data: ReservationData) => {
                    state.reservationData.push({
                        instrumentDivision: Number(data.instrumentDivision),
                        reservationDate: formatDateString(data.reservationDate, "YYYY/MM/DD"),
                        foodDivision: Number(data.foodDivision),
                        count: Number(data.count),
                    });
                });
            })
            .catch((err) => console.log(err));
    }

    /**
     * 事業所と日付に紐づく予約状況のデータを取得
     *
     * @param instumentDivision
     * @param reservationDate
     */
    public get oneDayReservationData() {
        return (instrumentDivision: number, reservationDate: string): ReservationData[] => {
            return state.reservationData.filter((data) => {
                return (
                    data.instrumentDivision === instrumentDivision &&
                    data.reservationDate === formatDateString(reservationDate, "YYYY/MM/DD")
                );
            });
        };
    }

    /**
     * 1日分の予約件数を取得
     *
     * @param instrumentDivision
     * @param reservationDate
     */
    public get oneDayReservationCount() {
        return (instrumentDivision: number, reservationDate: string): number => {
            const oneDayDatas = this.oneDayReservationData(instrumentDivision, reservationDate);
            return oneDayDatas?.reduce((result, data) => result + data.count, 0);
        };
    }

    /**
     * 事業所、予約日、食事区分に紐づく予約状況を取得
     *
     * @param instrumentDivision
     * @param reservationDate
     * @param foodDivision
     */
    public getRegistedDataOfFoodDivision(
        instrumentDivision: number,
        reservationDate: string,
        foodDivision: number
    ): ReservationData {
        const oneDayData = this.oneDayReservationData(instrumentDivision, reservationDate);
        return (
            oneDayData.find((data) => data.foodDivision === foodDivision) || {
                instrumentDivision: instrumentDivision,
                reservationDate: reservationDate,
                foodDivision: foodDivision,
                count: 0,
            }
        );
    }

    /**
     * 予約状況を登録
     *
     * @param reserveModalProps
     */
    public async registReserveData(reserveData: ReservationData[], employeeID: string) {
        const result = await this.registReserveDataWrapper(reserveData, employeeID);
        const msg = result ? messages.success1 : messages.error2;
        showAlertDialog(msg.msg, msg.type);
    }

    /**
     * 予約状況登録処理ラッパー
     *
     * @param reserveData
     * @param employeeID
     */
    private async registReserveDataWrapper(reserveData: ReservationData[], employeeID: string): Promise<boolean> {
        let allResult = true;
        for (const data of reserveData) {
            if (this.isCountChanged(data)) {
                const result = await this.runRegistReserveData(data, employeeID);
                allResult = allResult ? result : false;
            }
        }
        return allResult;
    }

    /**
     * 予約件数が変更されているかチェック
     *
     * @param data
     */
    private isCountChanged(data: ReservationData) {
        const registedData = this.getRegistedDataOfFoodDivision(
            data.instrumentDivision,
            data.reservationDate,
            data.foodDivision
        );
        return registedData.count !== data.count;
    }

    /**
     * 予約状況登録API呼び出し
     *
     * @param data
     */
    private async runRegistReserveData(data: ReservationData, employeeId: string): Promise<boolean> {
        let result = false;
        await axios
            .post(urls.registUrl, {
                employeeId,
                reservationDate: data.reservationDate,
                foodDivision: data.foodDivision,
                count: data.count,
            })
            .then((res) => {
                result = res.data.result;
            })
            .catch((err) => {
                console.log(err);
                result = false;
            });
        return result;
    }
}
