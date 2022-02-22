<template>
    <div class="order-card">
        <div class="tile is-ancester h100">
            <div class="tile is-7 order-card__menu">{{ menuData.food_division_name }}</div>
            <div class="tile is-5 order-card__count">
                <b-field>
                    <b-numberinput
                        v-model="menuData.count"
                        min="0"
                        :max="maxOrderNum"
                        type="is-light"
                        size="is-small"
                        :editable="edit"
                        :data-cy="`orderCount${menuData.food_division}`"
                    ></b-numberinput>
                </b-field>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue, Prop } from "vue-property-decorator";
import { MenuData } from "@/store/types";
import { maxOrderNum } from "@/assets/js/constant";

@Component
export default class OrderCard extends Vue {
    @Prop()
    public menuData!: MenuData;

    private edit = false;

    private maxOrderNum = 0;

    created() {
        this.maxOrderNum = maxOrderNum;
    }
}
</script>

<style lang="scss" scoped>
.order-card {
    border: solid 1px #dbdbdb;
    height: 50px;
    border-radius: 6px;
    padding: 5px;
    margin-bottom: 10px;
    .is-ancester {
        align-items: center;
    }
}
</style>
