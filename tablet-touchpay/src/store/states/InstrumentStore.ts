import { Component, Vue } from "vue-property-decorator";
import { InstrumentList } from "@/store/types";

type Store = {
    instrumentLists: InstrumentList[];
};

const state = Vue.observable<Store>({
    instrumentLists: [],
});

@Component
export default class InstrumentStore extends Vue {
    public get instrumentLists() {
        return state.instrumentLists;
    }

    public initInstrumentLists(instrumentLists: InstrumentList[]) {
        state.instrumentLists = [];
        state.instrumentLists = instrumentLists;
    }
}
