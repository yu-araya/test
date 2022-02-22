import customAxios from "./wrapper/AxiosWrapper";
import { urls } from "./apiEndPoints";
import { messages } from "@/assets/scripts/constant";
import { openDialog } from "@/assets/scripts/wrapper/BuefyWrapper";

const errorHandlingTemplate = (serverErrorMessage, networkErrorMessage, otherErrorMessage) => (e) => {
    if (e.response) {
        // サーバーエラーメッセージ
        openDialog(serverErrorMessage, "再読み込み", () => window.location.reload());
    } else if (e.request) {
        // ネットワークエラーメッセージ
        openDialog(networkErrorMessage, "再読み込み", () => window.location.reload());
    } else {
        // その他エラーメッセージ
        openDialog(otherErrorMessage, "再読み込み", () => window.location.reload());
    }
};

const invokeWatchOrderUrl = async (requestAll) => {
    if (requestAll) {
        invokeInfologUrl(`リロード時watchOrderUrl実行`);
        return await customAxios
            .get(urls.watchOrderUrl(requestAll))
            .then((res) => {
                invokeInfologUrl(`リロード時watchOrderUrl完了`);
                return res;
            })
            .catch((e) => {
                invokeInfologUrl(`リロード時watchOrderUrl失敗`);
                errorHandlingTemplate(messages.error3, messages.error4, messages.error3)(e);
            });
    } else {
        return await customAxios.get(urls.watchOrderUrl(requestAll)).catch((e) => {
            invokeInfologUrl(`継続取得watchOrderUrl失敗`);
            throw e;
        });
    }
};

const involeGetMenusUrl = async () => {
    invokeInfologUrl(`getMenusUrl実行`);
    return await customAxios
        .get(urls.getMenusUrl())
        .then((res) => {
            invokeInfologUrl(`getMenusUrl完了`);
            return res;
        })
        .catch((e) => {
            invokeInfologUrl(`getMenusUrl失敗`);
            errorHandlingTemplate(messages.error3, messages.error4, messages.error3)(e);
        });
};

const involeGetVersionUrl = async () => {
    invokeInfologUrl(`getVersionUrl実行`);
    return await customAxios
        .get(urls.getVersionUrl("forkitchen"))
        .then((res) => {
            invokeInfologUrl(`getVersionUrl完了`);
            return res;
        })
        .catch((e) => {
            invokeInfologUrl(`watchOrderUrl完了`);
            errorHandlingTemplate(messages.error3, messages.error1, messages.error3)(e);
        });
};

// ロギングのAPIでユーザーに見えるべきではないので、エラーは握り潰す
const invokeInfologUrl = (message) => {
    const params = { message: `厨房向け画面：${message}` };
    customAxios.post(urls.infologUrl(), params).catch(() => {});
};

export { invokeWatchOrderUrl, involeGetMenusUrl, involeGetVersionUrl, invokeInfologUrl };
