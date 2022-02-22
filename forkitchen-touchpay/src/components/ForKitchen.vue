<template>
    <div class="forkitchen">
        <div class="sortarea">
            <div class="sortinfo" @click="switchSortType" v-if="isAsc">
                <img id="reload" src="@/assets/img/sort_ascending.svg" />
                登録が古い順に表示中
            </div>
            <div class="sortinfo" @click="switchSortType" v-else>
                <img id="reload" src="@/assets/img/sort_descending.svg" />
                登録が新しい順に表示中
            </div>
        </div>
        <div class="scroll">
            <b-message
                v-if="message.msg"
                title="message"
                :type="this.message.type"
                aria-close-label="Close message"
                class="alert-message"
            >
                {{ this.message.msg }}
            </b-message>
            <div class="menuarea">
                <MenuCard class="menucard" v-for="order in showOrderList" :key="order.id" v-bind="order"></MenuCard>
            </div>
        </div>
    </div>
</template>

<script>
import MenuCard from "./common/MenuCard.vue";
import { invokeWatchOrderUrl, invokeInfologUrl } from "../assets/scripts/axiosRequest.js";
import { messages } from "../assets/scripts/constant";
export default {
    name: "ForKitchen",
    components: {
        MenuCard,
    },
    data: () => ({
        orderList: [],
        message: {},
        interval: 0,
        hideList: localStorage.getItem("hideList")?.split(",") || [],
        filterMenuList:
            localStorage.getItem("filterMenuList")?.split(",") ||
            JSON.parse(localStorage.getItem("menus"))?.map((_) => _[0]) ||
            [],
        isAsc: localStorage.menuSortIsAsc === "true",
    }),
    async created() {
        this.loadOrderList();
        this.startWatchOrder();
    },
    methods: {
        async loadOrderList() {
            invokeInfologUrl(`初回注文データ取得開始`);
            const orderList = await invokeWatchOrderUrl(true);
            if (orderList) {
                if (orderList.data?.result) {
                    this.message = {};
                    this.orderList = orderList.data.orderList;
                } else if (orderList) {
                    this.message = messages.error3;
                }
            }
            invokeInfologUrl(`初回注文データ取得完了`);
        },
        async startWatchOrder() {
            this.interval = setInterval(this.loadLatestList, 1000);
        },
        async loadLatestList() {
            const orderList = await invokeWatchOrderUrl(false).catch((e) => {
                if (e.response) {
                    this.message = messages.error3;
                } else if (e.request) {
                    this.message = messages.error2;
                } else {
                    this.message = messages.error3;
                }
            });
            if (orderList) {
                if (orderList.data?.result) {
                    this.message = {};
                    let pushed = false;
                    orderList.data.orderList?.forEach((data) => {
                        if (!this.orderList) this.orderList = [];
                        if (!this.orderList.some((order) => order.id === data.id)) {
                            invokeInfologUrl(`新規注文データ取得`);
                            this.orderList.push(data);
                            if (this.filterMenuList.includes(data.foodDivision)) {
                                pushed = true;
                            }
                        }
                    });
                    if (pushed) {
                        this.$buefy.toast.open({
                            message: messages.info1.msg,
                            type: messages.info1.type,
                            position: "is-bottom-left",
                            duration: 1000,
                        });
                    }
                } else {
                    this.message = messages.error3;
                }
            }
        },
        switchSortType() {
            this.isAsc = !this.isAsc;
            localStorage.setItem("menuSortIsAsc", this.isAsc);
            invokeInfologUrl(`ソート切り替わり ${this.isAsc ? "昇順" : "降順"}`);
        },
    },
    computed: {
        showOrderList() {
            return (
                this.orderList &&
                this.orderList
                    .filter(
                        (_) => !this.hideList.includes(String(_.id)) && this.filterMenuList.includes(_.foodDivision)
                    )
                    .sort((a, b) => (this.isAsc ? a.id - b.id : b.id - a.id))
            );
        },
    },
};
</script>

<style scoped>
.alert-message {
    margin: 0 5em;
}

.sortarea {
    display: flex;
    justify-content: flex-end;
}

.sortinfo {
    display: flex;
    margin-right: 2em;
    margin-bottom: 1em;
}

.menuarea {
    display: flex;
    margin: 0 20px 10px 20px;
    flex-wrap: wrap;
}

.menucard {
    width: 48%;
    flex-grow: 0;
}

.menucard:last-child {
    margin-bottom: 1.5rem;
}
</style>
