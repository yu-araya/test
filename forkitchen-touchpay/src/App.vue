<template>
    <div id="app">
        <div class="header">
            <Header />
        </div>
        <div class="contents">
            <ForKitchen />
        </div>
    </div>
</template>

<script>
import Header from "./components/common/Header.vue";
import ForKitchen from "./components/ForKitchen.vue";
import { involeGetMenusUrl, invokeInfologUrl } from "@/assets/scripts/axiosRequest";
import { reloadTime, reloadTimeCheckDelay } from "./assets/scripts/constant";

export default {
    name: "App",
    components: {
        Header,
        ForKitchen,
    },
    async created() {
        await this.getMenuData();
        this.reloadTimer();
        invokeInfologUrl(`起動完了`);
    },
    methods: {
        async getMenuData() {
            invokeInfologUrl(`メニュー情報取得開始`);
            const getMenusResult = await involeGetMenusUrl();

            if (getMenusResult && getMenusResult.data.result) {
                localStorage.setItem("menus", JSON.stringify(Object.entries(getMenusResult.data.menus)));
            }
            invokeInfologUrl(`メニュー情報取得完了`);
        },
        reloadTimer() {
            setInterval(() => {
                invokeInfologUrl(`定時リロード時間確認処理実行`);
                const now = new Date();
                if (now.getHours() === reloadTime.hour) {
                    invokeInfologUrl(`定時リロード実行`);
                    localStorage.removeItem("hideList");
                    window.location.reload();
                }
                invokeInfologUrl(`定時リロードしない`);
            }, reloadTimeCheckDelay);
        },
    },
};
</script>

<style>
#app {
    font-family: Avenir, Helvetica, Arial, sans-serif;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    text-align: center;
    color: #2c3e50;
}

.header {
    height: 4.25rem;
}

.contents {
    height: 80vh;
}

#nav {
    padding: 30px;
}
</style>
