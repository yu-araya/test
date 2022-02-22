import { Component, Vue } from "vue-property-decorator";
import { InstrumentDivision } from "@/assets/scripts/types";
import axios from "axios";
import { urls } from "@/assets/scripts/constant";

type State = {
    instruments: InstrumentDivision[];
};

const state = Vue.observable<State>({
    instruments: [],
});

@Component
export default class InstrumentDivisionStore extends Vue {
    public get instruments(): InstrumentDivision[] {
        return state.instruments;
    }

    public async initInstruments() {
        state.instruments = [];
        await axios
            .post(urls.loadInstrumentUrl)
            .then((res) => {
                const result = res.data.instrumentList;
                result.forEach((data: InstrumentDivision) => {
                    state.instruments.push({
                        division: Number(data.division),
                        name: data.name,
                    });
                });
            })
            .catch((err) => console.log(err));
    }

    /**
     * 事業所名を取得
     *
     * @param instrumentDivision
     */
    public get instrumentName() {
        return (instrumentDivision: number): string => {
            return (
                state.instruments.find((element) => {
                    return element.division === instrumentDivision;
                })?.name || ""
            );
        };
    }
}
