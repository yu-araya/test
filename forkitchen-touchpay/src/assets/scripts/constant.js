export const messages = {
    loading: {
        msg: "処理中です。しばらくお待ちください。",
        type: "is-default",
    },
    info1: {
        msg: "注文が追加されました。",
        type: "is-secondary",
    },
    error1: {
        msg: "バージョンの取得に失敗しました。ネットワークを確認してください。",
        type: "is-danger",
    },
    error2: {
        msg: "データの取得に失敗しました。ネットワークを確認してください。",
        type: "is-danger",
    },
    error3: {
        msg: "サーバーでエラーが発生しました。管理者様にお問い合わせください。",
        type: "is-danger",
    },
    error4: {
        msg: "メニューの取得に失敗しました。ネットワークを確認してください。",
        type: "is-danger",
    },
};

export const reloadTime = {
    hour: 1,
    minute: 0,
    second: 0,
    millisecond: 0,
};

export const reloadTimeCheckDelay = 60 * 60 * 1000;

export const latestDisplayTime = 30 * 1000; // 30s
