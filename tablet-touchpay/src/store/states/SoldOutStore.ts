import { Component, Vue } from "vue-property-decorator";

type Store = {
    soldoutList: number[];
};

const state = Vue.observable<Store>({
    soldoutList: [],
});

@Component
export default class SoldOutStore extends Vue {
    public get soldoutList(): number[] {
        return state.soldoutList || [];
    }

    public initSoldoutList(soldoutList: number[]) {
        state.soldoutList = [];
        state.soldoutList = soldoutList;
    }
}
