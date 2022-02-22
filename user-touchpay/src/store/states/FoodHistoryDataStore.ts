import { Component, Vue } from "vue-property-decorator";
import { FoodHistoryData } from "@/assets/scripts/types";
import axios from "axios";
import { urls } from "@/assets/scripts/constant";
import { isMonthly } from "@/assets/scripts/date";

type State = {
    foodHistoryData: FoodHistoryData[];
};

const state = Vue.observable<State>({
    foodHistoryData: [],
});

@Component
export default class FoodHistoryDataStore extends Vue {
    public get foodHistoryData(): FoodHistoryData[] {
        return state.foodHistoryData;
    }

    /**
     * １ヶ月分の喫食データを取得
     *
     * @param month YYYY/MM
     */
    public get monthlyFoodHistoryData() {
        return (month: Date): FoodHistoryData[] => {
            return state.foodHistoryData.filter((data) => {
                return isMonthly(data.cardReceptTime, month);
            });
        };
    }

    /**
     * １ヶ月分の喫食データを取得
     *
     * @param month YYYY/MM
     */
    public get monthlyFoodTotalCost() {
        return (month: Date): number => {
            return this.monthlyFoodHistoryData(month).reduce((accumulator, current) => {
                return current.deleteFlg ? accumulator : accumulator + current.foodCost;
            }, 0);
        };
    }

    /**
     * 個人の喫食状況を取得します
     * 初期表示時のみ現在日付を取得
     *
     * @param employeeId
     */
    public async loadFoodHistoryData(employeeId: string): Promise<void> {
        state.foodHistoryData = [];
        await axios.get(`${urls.loadFoodHistoryDataUrl}?employeeId=${employeeId}`).then((res) => {
            const result = res.data.foodHistoryDatas;
            // eslint-disable-next-line
            result?.forEach((data: FoodHistoryData) => {
                state.foodHistoryData.push({
                    instrumentDivision: Number(data.instrumentDivision),
                    instrumentName: data.instrumentName,
                    foodDivision: Number(data.foodDivision),
                    foodDivisionName: data.foodDivisionName,
                    foodCost: Number(data.foodCost),
                    cardReceptTime: new Date(data.cardReceptTime),
                    deleteFlg: data.deleteFlg,
                });
            });
        });
    }
}
