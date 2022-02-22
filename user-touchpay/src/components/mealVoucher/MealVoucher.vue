<template>
    <div class="container meal-voucher">
        <div class="meal-voucher__link">
            <b-button type="is-primary" tag="router-link" :to="{ name: 'qrcode' }" size="is-large" expanded>
                QR画面に戻る
            </b-button>
        </div>
        <div class="card voucher" v-for="(data, idx) in dailyOrderData" :key="idx">
            <div class="voucher__contents">
                <div class="voucher__name">{{ data.foodDivisionName }}</div>
                <div class="voucher__cost">{{ costFormat(data.foodCost) }}円</div>
            </div>
            <div class="voucher__date">{{ data.orderDate }}</div>
        </div>
    </div>
</template>

<script lang="ts">
import Vue from "vue";
import axios from "axios";
import { urls } from "@/assets/scripts/constant";

type DailyOrderDataType = {
    foodDivisionName: string;
    foodCost: string;
    orderDate: string;
};

type DataType = {
    dailyOrderData: DailyOrderDataType[];
};

export default Vue.extend({
    name: "MealVoucher",
    data(): DataType {
        return {
            dailyOrderData: [],
        };
    },
    async created() {
        const result = await axios.get(`${urls["loadDailyOrderDataUrl"]}?employeeId=${localStorage.userId}`);
        this.dailyOrderData = result.data.dailyOrderList as DailyOrderDataType[];
    },
    computed: {
        costFormat(): Function {
            return (foodCost: number): string => {
                return String(foodCost).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
            };
        },
    },
});
</script>

<style lang="scss">
.meal-voucher {
    @media screen and (min-width: 1024px) {
        width: 40% !important;
    }
    display: flex;
    flex-direction: column;
    padding: 0 1em;

    &__link {
        margin-bottom: 1em;
    }
}

.voucher {
    height: 7em;
    margin-bottom: 1em;
    padding: 0.5em 1em;
    display: flex;
    flex-direction: column;

    &__contents {
        height: 100%;
        display: flex;
        align-items: center;
        font-size: 1.5em;
        white-space: pre-line;
        padding: 0 0.5em;
    }

    &__cost {
        margin-left: auto;
    }

    &__date {
        text-align: end;
    }
}
</style>
