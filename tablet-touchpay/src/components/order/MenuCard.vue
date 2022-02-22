<template>
    <div class="menu-card">
        <button
            class="menu-card__button"
            :disabled="soldOut"
            @click="click"
            :data-cy="`menuButton${menuData.food_division}`"
        >
            <div class="menu-card__name">{{ menuData.food_division_name }}</div>
            <div class="menu-card__cost">
                <span v-if="soldOut">売り切れ</span>
                <span v-else>¥{{ menuData.food_cost | addComma }}</span>
            </div>
        </button>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Mixins } from "vue-property-decorator";
import { MenuData } from "@/store/types";
import MixinComponent from "@/assets/js/mixin";
import { messages } from "@/assets/js/constant";

@Component
export default class MenuCard extends Mixins(MixinComponent) {
    @Prop()
    public menuData!: MenuData;

    @Prop()
    public soldOut!: boolean;

    public click() {
        this.playSound("choice");
        if (this.countUp(this.menuData.food_division)) this.createMessage(messages["info2"]);
    }
}
</script>

<style lang="scss" scoped>
.menu-card {
    width: 100%;
    margin: 10px;

    &__button {
        align-items: center;
        border: 2px solid #363636;
        border-radius: 6px;
        height: 120px;
        width: 100%;
        font-size: 30px;
        background-color: white;
        display: flex;
        padding: 0px 10px;
        line-height: 2rem;
        color: #363636;
    }
    &__name {
        white-space: pre-line;
        margin-right: 10px;
    }
    &__cost {
        margin-left: auto;
    }
}
</style>
