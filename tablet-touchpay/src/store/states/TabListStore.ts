import { Component, Vue } from "vue-property-decorator";
import { TabList } from "@/store/types";

type Store = {
    tabLists: TabList[];
};

const state = Vue.observable<Store>({
    tabLists: [],
});

@Component
export default class TabListStore extends Vue {
    public get tabLists() {
        return state.tabLists;
    }

    public initTabLists(tabLists: TabList[]) {
        state.tabLists = [];
        state.tabLists = tabLists;
    }
}
