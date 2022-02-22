export const messages = {
    loading: {
        msg: "処理中です。しばらくお待ちください。",
        type: "is-default",
    },
    success1: {
        msg: "ご注文ありがとうございました。",
        type: "is-success",
    },
    success2: {
        msg: "受け付けました。",
        type: "is-success",
    },
    info1: {
        msg: "メニューを選択してください。",
        type: "is-info",
    },
    info2: {
        msg: "一度に注文できるのは1メニュー9個までです。",
        type: "is-info",
    },
    error1: {
        msg: "登録に失敗しました。お手数ですが管理者にお問い合わせください。",
        type: "is-danger",
    },
    error2: {
        msg: "このカードは使用できません。",
        type: "is-danger",
    },
    error3: {
        msg:
            "バージョン取得に失敗しました。<br>ネットワーク環境を確認してください。<br>改善されない場合は管理者様にお問い合わせください。",
        type: "is-danger",
    },
    error4: {
        msg: "QRコードの読み込みに失敗しました。お手数ですが再度読み込みを行うか、管理者にお問い合わせください。",
        type: "is-danger",
    },
};

export const maxOrderNum = 9;

export const reloadTime = {
    hour: 23,
    minute: 59,
    second: 59,
    millisecond: 0,
};

import { testMode } from "@/assets/property/property";
const touchPayUrl = (() => {
    if (!testMode) {
        const pathname = location.pathname.substr(0, location.pathname.indexOf("/tablet/"));
        return `${location.protocol}//${location.hostname}${pathname}`;
    } else {
        return "http://localhost:8081/TouchPay.standard";
    }
})();

export const urls = {
    getEmpUrl: `${touchPayUrl}/app/api/user?iccard_num=`,
    getOptionsUrl: `${touchPayUrl}/app/api/getOptions`,
    confirmEmployeeIdUrl: `${touchPayUrl}/app/api/confirmEmployeeId?employee_id=`,
    orderUrl: `${touchPayUrl}/app/api/foodhistory`,
    reloadUrl: `${touchPayUrl}/app/api/reload`,
    getVersionUrl: `${touchPayUrl}/app/api/getVersion`,
};
