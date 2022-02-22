import { Component, Vue } from "vue-property-decorator";
import { FoodDivision } from "@/assets/scripts/types";
import axios from "axios";
import { urls } from "@/assets/scripts/constant";

type State = {
    menus: FoodDivision[];
};

const state = Vue.observable<State>({
    menus: [],
});

@Component
export default class FoodDivisionStore extends Vue {
    public get menus(): FoodDivision[] {
        return state.menus;
    }

    public async initMenus() {
        state.menus = [];
        await axios
            .post(urls.loadFoodDivisionUrl)
            .then((res) => {
                const result = res.data.foodDivisionList;
                result.forEach((data: FoodDivision) => {
                    state.menus.push({
                        instrumentDivision: Number(data.instrumentDivision),
                        division: Number(data.division),
                        name: data.name,
                    });
                });
            })
            .catch((err) => console.log(err));
    }

    /**
     * 食事名を取得
     *
     * @param foodDivision
     */
    public get menuName() {
        return (foodDivision: number): string => {
            return state.menus.find((menu) => menu.division === foodDivision)?.name || "";
        };
    }

    /**
     * 事業所に紐づくメニューを取得
     *
     * @param instrumentDivision
     */
    public get instrumentMenuList() {
        return (instrumentDivision: number): FoodDivision[] => {
            return state.menus.filter((menu) => menu.instrumentDivision === instrumentDivision);
        };
    }
}
