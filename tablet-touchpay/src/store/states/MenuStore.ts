import { Component, Vue } from "vue-property-decorator";
import { MenuData } from "@/store/types";
import { maxOrderNum } from "@/assets/js/constant";

type Store = {
    menuLists: MenuData[];
};

const state = Vue.observable<Store>({
    menuLists: [],
});

@Component
export default class EmployeeStore extends Vue {
    public get menuLists() {
        return state.menuLists;
    }

    public initMenuLists(menuArray: MenuData[]) {
        state.menuLists = [];
        menuArray.forEach((menuData: MenuData) => {
            state.menuLists.push({ ...menuData, count: 0, dispCard: false });
        });
    }

    /**
     * 注文数を加算
     * 最大件数に達していればtrueを返却
     *
     * @param division
     * @returns
     */
    public countUp(division: number): boolean {
        return state.menuLists.some((menuData) => {
            if (menuData.food_division === division) {
                if (menuData.count < maxOrderNum) {
                    menuData.dispCard = true;
                    menuData.count++;
                    return false;
                }
                return true;
            }
            return false;
        });
    }

    /**
     * 注文状況をクリア
     */
    public clearOrderData() {
        state.menuLists.forEach((menuData) => {
            menuData.count = 0;
            menuData.dispCard = false;
        });
    }

    /**
     * 注文があるかどうか
     *
     * @returns
     */
    public isOrdered(): boolean {
        return state.menuLists.some((menuData) => menuData.dispCard);
    }

    /**
     * 注文件数が0かどうか
     */
    public orderCountIsZero() {
        return !state.menuLists.some((menuData) => menuData.count > 0);
    }
}
