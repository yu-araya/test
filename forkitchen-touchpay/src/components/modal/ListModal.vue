<template>
    <div class="modal-card">
        <div class="modal-card-head">
            <div class="modal-card-title">一覧表示</div>
            <span class="description">フィルターされているメニューはグレーで表示されます。</span>
        </div>
        <div class="modal-card-body">
            <b-table
                :data="orderList"
                :paginated="true"
                per-page="10"
                :current-page.sync="currentPage"
                :pagination-simple="false"
                :pagination-rounded="false"
                sort-icon="arrow-up"
                sort-icon-size="is-small"
                aria-next-label="Next page"
                default-sort="id"
                default-sort-direction="asc"
                aria-previous-label="Previous page"
                aria-page-label="Page"
                aria-current-label="Current page"
                :row-class="isFiltered"
            >
                <b-table-column field="id" label="ID" width="40" sortable centered numeric v-slot="props">
                    {{ props.row.id }}
                </b-table-column>

                <b-table-column field="employeeName" label="社員名" sortable centered v-slot="props">
                    {{ props.row.employeeName }}
                </b-table-column>

                <b-table-column field="foodName" label="食事名" sortable centered v-slot="props">
                    {{ props.row.foodName }}
                </b-table-column>

                <b-table-column field="cardReceptTime" label="登録時間" sortable centered v-slot="props">
                    {{ new Date(props.row.cardReceptTime).toLocaleString() }}
                </b-table-column>

                <b-table-column field="isEated" label="提供済み" sortable centered v-slot="props">
                    <span v-if="props.row.isEated" class="tag is-secondary">
                        <img id="reload" class="icon" src="@/assets/img/check_circle.svg" />
                    </span>
                </b-table-column>

                <template #empty>
                    <div class="has-text-centered">No records</div>
                </template>
            </b-table>
        </div>
        <div class="modal-card-foot close-button">
            <b-button type="is-info" @click="$emit('close')">閉じる</b-button>
        </div>
    </div>
</template>

<script>
import { invokeWatchOrderUrl, invokeInfologUrl } from "@/assets/scripts/axiosRequest.js";
import { messages } from "@/assets/scripts/constant";

export default {
    name: "ListModal",
    props: {
        openModal: {
            type: Boolean,
            default: false,
        },
    },
    data: () => ({
        orderList: [],
        currentPage: 1,
        filterMenuList:
            localStorage.getItem("filterMenuList")?.split(",") ||
            JSON.parse(localStorage.getItem("menus"))?.map((_) => _[0]) ||
            [],
    }),
    async created() {
        invokeInfologUrl(`一覧画面表示開始`);
        const orderListResult = await invokeWatchOrderUrl(true);
        const hideList = localStorage.getItem("hideList")?.split(",") || [];
        if (orderListResult && orderListResult.data?.result && orderListResult.data?.orderList) {
            this.message = {};
            this.orderList = orderListResult.data.orderList.reduce(
                (prev, current) => [
                    ...prev,
                    {
                        ...current,
                        isEated: hideList.includes(String(current.id)),
                    },
                ],
                []
            );
        } else {
            this.message = messages.error3;
        }
        invokeInfologUrl(`一覧画面表示完了`);
    },
    methods: {
        isFiltered(row) {
            return this.filterMenuList.includes(row.foodDivision) || "filtered";
        },
    },
};
</script>

<style>
.description {
    font-size: 1em;
    margin-left: auto;
    margin-right: 50px;
}

.close-button {
    flex-direction: row-reverse;
}

tr.filtered {
    background-color: #aaaaaa;
}
</style>
