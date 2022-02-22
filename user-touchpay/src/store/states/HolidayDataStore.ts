import { Component, Vue } from "vue-property-decorator";
import { HolidayData } from "@/assets/scripts/types";
import axios from "axios";
import { urls } from "@/assets/scripts/constant";
import { formatDateString } from "@/assets/scripts/date";

type State = {
    holidays: HolidayData[];
};

const state = Vue.observable<State>({
    holidays: [],
});

@Component
export default class HolidayDataStore extends Vue {
    public get holidays(): HolidayData[] {
        return state.holidays;
    }

    /**
     * 休日設定の初期取得
     */
    public async initHolidays() {
        state.holidays = [];
        await axios
            .post(urls.loadHolidayDataUrl)
            .then((res) => {
                const result = res.data.holidays;
                result.forEach((data: HolidayData) => {
                    state.holidays.push({
                        instrumentDivision: Number(data.instrumentDivision),
                        holidayDate: data.holidayDate,
                    });
                });
            })
            .catch((err) => console.log(err));
    }

    /**
     * 事業所毎の休日設定を取得
     */
    public get instrumentsHolidays() {
        return (instrumentDivision: number) => {
            return state.holidays.filter((data) => data.instrumentDivision === instrumentDivision);
        };
    }

    /**
     * 休日設定されているかどうか
     */
    public isHoliday(instrumentDivision: number, reservationDate: string) {
        return this.instrumentsHolidays(instrumentDivision).some(
            (data) => data.holidayDate === formatDateString(reservationDate, "YYYY/MM/DD")
        );
    }
}
