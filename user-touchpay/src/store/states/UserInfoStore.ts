import { Component, Vue } from "vue-property-decorator";
import { UserInfo } from "@/assets/scripts/types";
import axios from "axios";
import { messages, urls } from "@/assets/scripts/constant";
import { showAlertDialog } from "@/assets/scripts/alert-dialog";

type State = {
    userInfo: UserInfo;
};

const state = Vue.observable<State>({
    userInfo: {} as UserInfo,
});

export const auth = {
    loggedIn: false,
    login: () => {
        auth.loggedIn = true;
    },
    logout: () => {
        localStorage.removeItem("userId");
        state.userInfo = {} as UserInfo;
        auth.loggedIn = false;
    },
};

@Component
export default class UserInfoStore extends Vue {
    public get userInfo(): UserInfo {
        return state.userInfo;
    }

    public get userId(): string {
        return localStorage.userId;
    }

    public async loginConfirm(employeeId: string, password: string): Promise<string> {
        return await axios
            .post(urls.loginUrl, {
                params: {
                    employeeId,
                    password,
                },
            })
            .then((res) => {
                if (res.data) {
                    localStorage.userId = res.data.id;
                    auth.login();
                    state.userInfo = res.data;
                    return "success";
                } else {
                    return "failed";
                }
            })
            .catch((err) => {
                console.log(err);
                return "error";
            });
    }

    /**
     * パスワードを変更する
     *
     * @param password
     */
    public async changePassword(password: string) {
        await axios
            .post(urls.passChangeUrl, { employeeId: state.userInfo.id, password })
            .then(() => {
                showAlertDialog(messages.success2.msg, messages.success2.type);
            })
            .catch((err) => {
                console.log(err);
                showAlertDialog(messages.error3.msg, messages.error3.type);
            });
    }
}
