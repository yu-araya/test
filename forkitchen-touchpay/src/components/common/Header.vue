<template>
    <div>
        <b-navbar class="is-light" fixed-top>
            <template slot="brand">
                <b-navbar-item class="item">
                    <img id="logo" class="logo" src="@/assets/img/new-tpay-icon.png" alt="Agilecore_Logo" />
                </b-navbar-item>
            </template>
            <template slot="start">
                <b-navbar-item class="version">注文画面 {{ version }}</b-navbar-item>
            </template>

            <template slot="end">
                <b-navbar-item class="item" @click="openFilterModal">
                    <img id="reload" class="icon" src="@/assets/img/filter.svg" />
                </b-navbar-item>
                <b-navbar-item class="item" @click="openListModal">
                    <img id="reload" class="icon" src="@/assets/img/list.svg" />
                </b-navbar-item>
                <b-navbar-item class="item" @click="reload">
                    <img id="reload" class="icon" src="@/assets/img/refresh.svg" />
                </b-navbar-item>
            </template>
        </b-navbar>
    </div>
</template>

<script>
import ListModal from "@/components/modal/ListModal";
import FilterModal from "@/components/modal/FilterModal";
import { involeGetVersionUrl, invokeInfologUrl } from "@/assets/scripts/axiosRequest";
import { openModal } from "@/assets/scripts/wrapper/BuefyWrapper";

export default {
    name: "Header",
    data: () => ({ version: "", openList: false }),
    async created() {
        invokeInfologUrl(`バージョン取得開始`);
        const versionResult = await involeGetVersionUrl();
        if (versionResult && versionResult.status === 200) {
            const version = versionResult.data.version;
            if (localStorage.version !== version) {
                localStorage.version = version;
                invokeInfologUrl(`バージョンが古いためリロード実行`);
                window.location.reload();
            } else {
                this.version = version;
            }
        }
        invokeInfologUrl(`最新バージョンのためリロードなし`);
    },
    methods: {
        reload() {
            window.location.reload();
        },
        openFilterModal() {
            openModal(FilterModal, false);
        },
        openListModal() {
            openModal(ListModal, true);
        },
    },
};
</script>

<style scoped>
.item {
    width: 75px;
    justify-content: center;
}
.version {
    color: black !important;
}
.logo {
    transform: scale(1.5);
}
.icon {
    transform: scale(1.5);
}
.failed-log {
    margin: auto 10px;
}
</style>
