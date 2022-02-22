<template>
    <div class="forkitchen container box" :class="{ 'latest-card': isLatest, hiddened: isHidden }">
        <div class="flex menu-header">
            <div class="orderId">{{ id }}</div>
            <div class="employee-name">{{ employeeName }}</div>
        </div>
        <div class="hidden-button" @click="hideCard">✖️</div>
        <div class="food-name">{{ foodName }}</div>
        <div class="card-recept-time">{{ formattedCardReceptTime }}</div>
    </div>
</template>

<script>
import { latestDisplayTime } from "../../assets/scripts/constant";
import { formatDateString } from "../../assets/scripts/wrapper/DayJsWrapper";

export default {
    name: "MenuCard",
    props: {
        id: Number,
        foodName: String,
        employeeName: String,
        cardReceptTime: String,
    },
    data: () => ({
        isHidden: false,
        isLatest: false,
    }),
    created() {
        this.isLatest = true;
        setTimeout(() => {
            this.isLatest = false;
        }, latestDisplayTime);
    },
    computed: {
        formattedCardReceptTime() {
            return formatDateString(this.cardReceptTime, "M月D日 H時m分s秒");
        },
    },
    methods: {
        hideCard() {
            this.isHidden = !this.isHidden;
            if (this.isHidden) {
                const hideList = localStorage.getItem("hideList");
                const newHideList = `${this.id}${hideList ? "," + hideList : ""}`;
                localStorage.setItem("hideList", newHideList);
            }
        },
    },
};
</script>

<style scoped>
.forkitchen {
    border: 1px solid #aaaaaa;
}

.menu-header {
    font-size: 1em;
}

.orderId {
    text-align: left;
    margin-right: 1em;
}

.employee-name {
    text-align: left;
}

.hidden-button {
    position: absolute;
    top: 0;
    right: 10px;
    margin-left: auto;
    font-size: 2em;
}

.food-name {
    font-size: 2.5em;
    white-space: pre-line;
    text-align: center;
    flex-grow: 1;
}

.card-recept-time {
    text-align: right;
    font-size: 1.3em;
    align-self: flex-end;
}

.hiddened {
    display: none;
}

.latest-card {
    background-color: #e6e6fa;
}
</style>
