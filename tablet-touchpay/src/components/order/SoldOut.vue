<template>
    <div class="soldout h100">
        <div class="tile is-ancester is-vertical">
            <span class="soldout__description">売り切れにするメニューをタップして、保存ボタンを押して下さい。</span>
            <div class="soldout__scroll">
                <b-tabs type="is-boxed" expanded>
                    <b-tab-item v-for="tab in tabs" :key="tab.tabNum" :label="tab.tabName">
                        <div class="menu-area__tabs is-ancester is-vertical">
                            <div class="tile is-horison" v-for="(menus, idx) in splitToCount(tab.tabNum)" :key="idx">
                                <div class="tile is-6" v-for="(data, idx) in menus" :key="idx">
                                    <b-checkbox-button
                                        class="soldout__button"
                                        v-model="localSoldoutList"
                                        :native-value="data.food_division"
                                        size="is-large"
                                        :data-cy="`soldOutFood${data.food_division}`"
                                    >
                                        <span class="soldout__button-label">{{ data.food_division_name }}</span>
                                    </b-checkbox-button>
                                </div>
                            </div>
                        </div>
                    </b-tab-item>
                </b-tabs>
            </div>
        </div>
        <footer class="soldout__footer">
            <b-button class="soldout__clear" size="is-large" type="is-light" @click="clear">選択解除</b-button>
            <b-button class="soldout__save" size="is-large" type="is-primary" data-cy="saveSoldOut" @click="save"
                >保存</b-button
            >
        </footer>
    </div>
</template>

<script lang="ts">
import { Component, Mixins, Prop, Emit } from "vue-property-decorator";
import MenuCard from "@/components/order/MenuCard.vue";
import StoreMixin from "@/store/StoreMixin";
import { MenuData } from "@/store/types";
import { SoldOutInfos } from "@/assets/js/indexedDB";

@Component({
    components: { MenuCard },
})
export default class SoldOut extends Mixins(StoreMixin) {
    @Prop()
    private isSoldOutModalActive!: boolean;

    private localSoldoutList: number[] = [];

    private soldOutInfos = new SoldOutInfos();

    @Emit()
    // eslint-disable-next-line
    public input(isSoldOutModalActive: boolean) {}

    private get localIsSoldOutModalActive(): boolean {
        return this.isSoldOutModalActive;
    }

    private set localIsSoldOutModalActive(isSoldOutModalActive: boolean) {
        this.input(isSoldOutModalActive);
    }

    private created() {
        this.localSoldoutList = this.soldoutList;
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

    private async clear() {
        this.localSoldoutList = [];
    }

    // storeとDBに売り切れ設定データを追加
    private async save() {
        await this.soldOutInfos.insertSoldoutData(this.localSoldoutList);
        this.initialize();
        this.input(false);
    }
}
</script>

<style lang="scss" scoped>
.soldout {
    background-color: white;
    height: 70vh;
    display: flex;
    flex-direction: column;

    &__description {
        height: 5vh;
        margin: 10px 0 0 20px;
        font-weight: bold;
        font-size: 20px;
        text-align: left;
    }

    &__scroll {
        height: 50vh;
        overflow: scroll;
    }

    &__horizontal {
        display: flex;
        padding: 0 10px;
    }

    &__button {
        width: 100%;
        margin: 10px 10px 0 10px;
    }

    &__footer {
        height: 10vh;
        justify-items: center;
        display: flex;
        align-items: center;
        flex-direction: row;
        padding: 20px;
        button {
            width: 20vw;
        }
        background-color: #cccccc;
    }

    &__save {
        margin-left: auto;
    }
}
</style>
