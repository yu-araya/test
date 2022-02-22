import { Vue, Component, Mixins } from "vue-property-decorator";
import FoodDivisionStore from "@/store/states/FoodDivisionStore";
import InstrumentDivisionStore from "@/store/states/InstrumentDivisionStore";
import HolidayDataStore from "@/store/states/HolidayDataStore";
import DataMixin from "@/store/mixins/DataMixin";
import UserInfoStore from "@/store/states/UserInfoStore";
import { Options } from "@/assets/scripts/types";
import axios from "axios";
import { urls } from "@/assets/scripts/constant";

type State = {
    options: Options;
    displayDate: string;
};

const state = Vue.observable<State>({
    options: {} as Options,
    displayDate: "",
});

@Component
export default class StoreMixin extends Mixins(
    FoodDivisionStore,
    InstrumentDivisionStore,
    DataMixin,
    UserInfoStore,
    HolidayDataStore
) {
    public get options(): Options {
        return state.options;
    }

    public get displayDate(): string {
        return state.displayDate;
    }

    public setDisplayDate(date: string) {
        state.displayDate = date;
    }


    public async getOptions() {
        const result = await axios.get(urls.getOptionsUrl);
        state.options = {
            userReservationFlg: result?.data["userReservationFlg"],
            userFoodHistoryFlg: result?.data["userFoodHistoryFlg"],
            qrcodeFlg: result?.data["qrcodeFlg"],
        };
    }

    /**
     * 事業所と日付に紐づく予約状況のメニュー名を取得
     *
     * @param instrumentDivision
     * @param date
     */
    public get reservedMenuList() {
        return (instrumentDivision: number, date: string): string[] => {
            const oneDayDatas = this.oneDayReservationData(instrumentDivision, date);
            return (
                oneDayDatas?.map((data) => {
                    return this.menuName(data.foodDivision);
                }) || []
            );
        };
    }

    /**
     *
     */
    public async initialize() {
        await this.initInstruments();
        await this.initHolidays();
        await this.initMenus();
    }
}
