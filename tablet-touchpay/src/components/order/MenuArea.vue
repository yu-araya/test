<template>
    <div class="menu-area">
        <section>
            <p class="menu-area__discription">1.注文するメニューをタッチしてください</p>
            <b-tabs type="is-toggle" expanded>
                <b-tab-item v-for="tab in tabs" :key="tab.tabNum" :label="tab.tabName">
                    <div class="menu-area__tabs is-ancester is-vertical">
                        <div class="tile is-horison" v-for="(menus, idx) in splitToCount(tab.tabNum)" :key="idx">
                            <div class="tile is-6" v-for="(data, idx) in menus" :key="idx">
                                <MenuCard :menuData="data" :soldOut="isSoldOut(data.food_division)"></MenuCard>
                            </div>
                        </div>
                    </div>
                </b-tab-item>
            </b-tabs>
        </section>
    </div>
</template>

<script lang="ts">
import { Component, Mixins } from "vue-property-decorator";
import MenuCard from "@/components/order/MenuCard.vue";
import StoreMixin from "@/store/StoreMixin";
import { MenuData } from "@/store/types";

@Component({
    components: {
        MenuCard,
    },
})
export default class MenuArea extends Mixins(StoreMixin) {
    created() {
        this.initialize();
    }

    // タブリスト取得(メニューがない場合は表示しない)
    get tabs() {
        const tabLists = this.tabLists;
        const menuLists = this.menuLists;
        return tabLists.filter((tab) => {
            return menuLists.some((menu) => menu.category === tab.tabNum);
        });
    }

    // 2個ずつの配列にする
    get splitToCount() {
        return (tabNum: string) => {
            const result: [MenuData[]] = [[]];
            let index = 0;
            this.menuLists
                .filter((menuData) => {
                    return menuData.category === tabNum;
                })
                .forEach((menuData) => {
                    if (result[index].length === 2) {
                        ++index;
                        result.push([]);
                    }
                    result[index].push(menuData);
                });
            return result;
        };
    }

    get isSoldOut() {
        return ($foodDivision: number) => {
            return this.soldoutList?.includes($foodDivision);
        };
    }
}
</script>

<style lang="scss">
.menu-area {
    nav.tabs::-webkit-scrollbar {
        display: none;
    }
    &__discription {
        text-align: left;
        margin: 10px;
        font-weight: bold;
        font-size: 20px;
    }
    &__tabs {
        height: 55vh;
        overflow: scroll;
    }
}
</style>
