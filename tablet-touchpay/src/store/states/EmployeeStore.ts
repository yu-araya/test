import { Component, Vue } from "vue-property-decorator";
import { EmployeeData } from "@/store/types";

type Store = {
    employeeLists: EmployeeData[];
};

const state = Vue.observable<Store>({
    employeeLists: [],
});

@Component
export default class EmployeeStore extends Vue {
    public get employeeLists() {
        return state.employeeLists;
    }

    public initEmployeeLists(employeeLists: EmployeeData[]) {
        state.employeeLists = [];
        state.employeeLists = employeeLists;
    }

    /**
     * タッチされたカードが登録されているか
     * されていたら社員ID返却
     *
     * @param icCardNum
     * @returns
     */
    public effectiveCardCheck(icCardNum: string): string {
        return (
            state.employeeLists.find((employeeData) => {
                return employeeData.ic_card_number === icCardNum || employeeData.ic_card_number2 === icCardNum;
            })?.employee_id || ""
        );
    }

    /**
     * QRコードで読み込んだ社員コードが登録されているか確認
     *
     * @param employeeId
     */
     public effectiveIdCheck(employeeId: string): boolean {
        return state.employeeLists.some((employeeData) => {
            return employeeData.employee_id === employeeId;
        });
    }

}
