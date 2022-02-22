export const messages = {
    success1: {
        msg: "予約を登録しました。",
        type: "is-success",
    },
    success2: {
        msg: "パスワードを変更しました。",
        type: "is-success",
    },
    confirm1: {
        msg: "予約を登録します。よろしいですか？",
        type: "is-info",
    },
    confirm2: {
        msg: "パスワードを変更します。よろしいですか？",
        type: "is-info",
    },
    error1: {
        msg: "有効な社員データが存在しません。",
        type: "is-danger",
    },
    error2: {
        msg: "予約の登録に失敗しました。管理者にお問い合わせください。",
        type: "is-danger",
    },
    error3: {
        msg: "パスワードの変更に失敗しました。管理者にお問い合わせください。",
        type: "is-danger",
    },
    error4: {
        msg: "画面の再読み込みはできません。ログインし直してください。",
        type: "is-danger",
    },
    error5: {
        msg: "サーバーとの接続に失敗しました。管理者にお問い合わせください。",
        type: "is-danger",
    },
    error6: {
        msg:
            "設定データの取得に失敗しました。ネットワークを確認してOKボタンを押してください。直らない場合は管理者にお問い合わせください。",
        type: "is-danger",
    },
};

import { testMode } from "@/assets/property/property";
const touchPayUrl = (() => {
    if (!testMode) {
        const pathname = location.pathname.substr(0, location.pathname.indexOf("/user/"));
        return `${location.protocol}//${location.hostname}${pathname}`;
    } else {
        return "http://localhost:8081/TouchPay.standard";
    }
})();
export const urls = {
    getOptionsUrl: `${touchPayUrl}/app/api/getOptions`,
    loginUrl: `${touchPayUrl}/app/api/login`,
    loadInstrumentUrl: `${touchPayUrl}/app/api/loadInstrument`,
    loadFoodDivisionUrl: `${touchPayUrl}/app/api/loadFoodDivision`,
    loadFoodHistoryDataUrl: `${touchPayUrl}/app/api/loadFoodHistory`,
    loadDailyOrderDataUrl: `${touchPayUrl}/app/api/loadDailyOrderData`,
    loadReservationDataUrl: `${touchPayUrl}/app/api/loadReservationData`,
    loadHolidayDataUrl: `${touchPayUrl}/app/api/loadHolidayData`,
    registUrl: `${touchPayUrl}/app/api/registReservation`,
    passChangeUrl: `${touchPayUrl}/app/api/passwordChange`,
    getMenuPdfPathUrl: `${touchPayUrl}/app/api/getMenuPdfPath`,
};
