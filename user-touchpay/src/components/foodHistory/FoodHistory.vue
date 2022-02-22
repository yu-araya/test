<template>
    <div class="container tile is-ancester is-8 food-history">
        <div class="tile is-vertical is-parent">
            <div class="tile is-child is-4 food-history__monthpick">
                <b-datepicker
                    id="food-history-datepicker"
                    v-model="selectedMonth"
                    type="month"
                    :month-names="monthNames"
                    :min-date="minDate"
                    :max-date="maxDate"
                    placeholder="表示する年月を選択してください"
                    :mobile-native="false"
                ></b-datepicker>
            </div>
            <div class="tile is-child has-text-right" id="total-cost">
                合計金額 : {{ totalFoodCost.toLocaleString() }}円
            </div>
            <div class="tile is-child food-history__table">
                <b-table
                    id="food-history-table"
                    :data="foodHistoryTableData"
                    :paginated="true"
                    per-page="10"
                    default-sort-direction="asc"
                    sort-icon="chevron-up"
                    sort-icon-size="is-small"
                    default-sort="cardReceptTime"
                    :row-class="(row) => row.deleteFlg && 'food-history__table-deleted'"
                    class="w100"
                >
                    <template slot-scope="props">
                        <b-table-column
                            header-class="table-header"
                            field="cardReceptTime"
                            width="15%"
                            label="登録日"
                            sortable
                            centered
                        >
                            {{ receptDay(props.row.cardReceptTime) }} ({{ receptDate(props.row.cardReceptTime) }})
                        </b-table-column>
                        <b-table-column
                            header-class="table-header"
                            field="cardReceptTime"
                            width="15%"
                            label="登録時間"
                            sortable
                            centered
                        >
                            {{ receptTime(props.row.cardReceptTime) }}
                        </b-table-column>
                        <b-table-column
                            header-class="table-header"
                            field="instrumentName"
                            width="20%"
                            label="事業所"
                            sortable
                        >
                            {{ props.row.instrumentName }}
                        </b-table-column>
                        <b-table-column
                            header-class="table-header"
                            field="foodDivisionName"
                            width="35%"
                            label="食事名"
                            sortable
                        >
                            {{ props.row.foodDivisionName }}
                        </b-table-column>
                        <b-table-column
                            header-class="table-header"
                            field="foodCost"
                            width="15%"
                            label="金額"
                            sortable
                            centered
                        >
                            {{ props.row.foodCost }}円
                        </b-table-column>
                    </template>
                    <template slot="empty">
                        <section class="section food-history__table-empty" v-if="!isLoading">
                            <b-icon icon="emoticon-sad" size="is-large"></b-icon>
                            <p>データが存在しません。</p>
                        </section>
                    </template>
                </b-table>
                <b-loading :is-full-page="false" :active.sync="isLoading"> </b-loading>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Mixins } from "vue-property-decorator";
import StoreMixin from "@/store/StoreMixin";
import { formatDateToString, getDayOfWeek, severalYearsBeforeNow, lastDayOfMonth } from "@/assets/scripts/date";

@Component
export default class FoodHistory extends Mixins(StoreMixin) {
    private monthNames = ["1月", "2月", "3月", "4月", "5月", "6月", "7月", "8月", "9月", "10月", "11月", "12月"];

    private selectedMonth = new Date();

    private isLoading = false;

    private async mounted() {
        this.isLoading = true;
        await this.loadFoodHistoryData(this.userInfo.id);
        this.isLoading = false;
    }

    private get minDate() {
        return severalYearsBeforeNow(2);
    }

    private get maxDate() {
        return lastDayOfMonth();
    }

    private get receptDay() {
        return (date: Date) => {
            return formatDateToString(date, "DD");
        };
    }
    private get receptDate() {
        return (date: Date) => {
            return getDayOfWeek(date);
        };
    }
    private get receptTime() {
        return (date: Date) => {
            return formatDateToString(date, "HH:mm:ss");
        };
    }

    private get foodHistoryTableData() {
        return this.monthlyFoodHistoryData(this.selectedMonth);
    }

    private get totalFoodCost() {
        return this.monthlyFoodTotalCost(this.selectedMonth);
    }
}
</script>

<style lang="scss">
.food-history {
    justify-content: center;

    &__table {
        &-deleted {
            background-color: #aaa !important;
            td {
                border: 0 !important;
            }
        }
        &-empty {
            text-align: center;
        }
    }
}
</style>
