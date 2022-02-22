<template>
    <div class="modal-card" style="width: auto">
        <div class="modal-card-head">
            <span class="modal-card-title">表示するメニューを選択して、保存ボタンを押して下さい。</span>
        </div>
        <div class="modal-card-body tile menu-button-area">
            <div v-for="menu in menus" :key="menu[0]">
                <b-checkbox-button
                    class="menu-button"
                    v-model="selectedMenuList"
                    :native-value="menu[0]"
                    size="is-medium"
                >
                    {{ menu[1] }}
                </b-checkbox-button>
            </div>
        </div>
        <footer class="modal-card-foot button-area">
            <b-button size="is-large" @click="clear">選択解除</b-button>
            <b-button size="is-large" @click="$emit('close')">閉じる</b-button>
            <b-button size="is-large" type="is-secondary" @click="save">保存</b-button>
        </footer>
    </div>
</template>

<script>
import { invokeInfologUrl } from "@/assets/scripts/axiosRequest";

export default {
    name: "FilterModal",
    props: {
        openModal: {
            type: Boolean,
            default: false,
        },
    },
    data: () => ({
        menus: [],
        selectedMenuList: [],
        message: {},
    }),
    async created() {
        this.menus = JSON.parse(localStorage.getItem("menus"));
        this.selectedMenuList =
            localStorage.getItem("filterMenuList")?.split(",") || this.menus?.map((_) => _[0]) || [];
    },
    methods: {
        clear() {
            this.selectedMenuList = [];
        },
        save() {
            localStorage.setItem("filterMenuList", this.selectedMenuList);
            invokeInfologUrl(`フィルター情報変更のためリロード`);
            window.location.reload();
        },
    },
};
</script>

<style scoped>
.menu-button-area {
    flex-wrap: wrap;
    min-height: 60vh;
    align-content: flex-start;
    justify-content: space-around;
}
.menu-button {
    margin: 1em;
    min-width: 10em;
}

.button-area > button {
    width: 6em;
}
.button-area > button:nth-child(2) {
    margin-left: auto;
}
</style>
