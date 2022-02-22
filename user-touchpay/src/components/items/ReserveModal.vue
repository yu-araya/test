<template>
    <div id="reserve-modal" class="container tile is-ancester is-vertical modal-card reserve-modal">
        <header id="reserve-modal-description" class="modal-card-head reserve-modal__head">
            <div class="reserve-modal__head-date">{{ dateTitle }}の予約を登録します</div>
            <div class="reserve-modal__head-desc">予約したいメニューの個数を選択してください。</div>
        </header>
        <section class="modal-card-body reserve-modal__body">
            <div
                class="tile reserve-modal__body-menu"
                v-for="(menu, idx) in instrumentMenuList(instrumentDivision)"
                :key="idx"
            >
                <div
                    :id="`reserve-modal-card${menu.division}`"
                    class="container tile is-ancester box reserve-menu-card"
                >
                    <div class="tile reserve-menu-card__name">{{ menu.name }}</div>
                    <div class="tile reserve-menu-card__count">
                        <b-numberinput
                            :id="`reserve-modal-card${menu.division}-counter`"
                            v-model="menuReservationData(menu.division).count"
                            min="0"
                        ></b-numberinput>
                    </div>
                </div>
            </div>
        </section>
        <footer class="modal-card-foot reserve-modal__foot">
            <b-button id="reserve-modal-cancel" @click="cancel($parent)" label="キャンセル" expanded> </b-button>
            <b-button id="reserve-modal-clear" @click="clear" label="クリア" expanded> </b-button>
            <b-button id="reserve-modal-save" type="is-primary" @click="save($parent)" label="保存" expanded>
            </b-button>
        </footer>
        <b-loading :is-full-page="true" :active.sync="isLoading" :can-cancel="false"> </b-loading>
    </div>
</template>

<script lang="ts">
import { Component, Mixins, Prop } from "vue-property-decorator";
import StoreMixins from "@/store/StoreMixin";
import { formatDateString } from "@/assets/scripts/date";
import { ReservationData } from "@/assets/scripts/types";
import { messages } from "@/assets/scripts/constant";
import { showConfirmDialog } from "@/assets/scripts/alert-dialog";

@Component
export default class ReserveModal extends Mixins(StoreMixins) {
    @Prop()
    private instrumentDivision!: number;

    @Prop()
    private reservationDate!: string;

    private reservationDatas: ReservationData[] = [];

    private isLoading = false;

    private created() {
        this.createEmptyMenuReserveData();
    }

    /**
     * 予約データを複製
     */
    private createEmptyMenuReserveData() {
        const menuList = this.instrumentMenuList(this.instrumentDivision);
        const data = this.oneDayReservationData(this.instrumentDivision, this.reservationDate);
        menuList.forEach((menu) => {
            const reservedCount = data.find((data) => data.foodDivision === menu.division)?.count;
            this.reservationDatas.push({
                instrumentDivision: this.instrumentDivision,
                reservationDate: this.reservationDate,
                foodDivision: menu.division,
                count: reservedCount || 0,
            });
        });
    }

    private get dateTitle() {
        return formatDateString(this.reservationDate, "YYYY年M月D日");
    }

    private get menuReservationData() {
        return (division: number): ReservationData => {
            return this.reservationDatas.find((data) => data.foodDivision === division) || ({} as ReservationData);
        };
    }

    // eslint-disable-next-line
    private cancel(parent: any) {
        parent.close();
    }

    private clear() {
        this.reservationDatas.forEach((data) => (data.count = 0));
    }

    // eslint-disable-next-line
    private async save(parent: any) {
        await showConfirmDialog(messages.confirm1.msg, messages.confirm1.type, async () => {
            this.isLoading = true;
            await this.registReserveData(this.reservationDatas, this.userInfo.id);
            this.loadReservationData(this.userInfo.id);
            this.isLoading = false;
            parent.close();
        });
    }
}
</script>

<style lang="scss">
@import "@/assets/style/global.scss";
.reserve-modal {
    &__head {
        display: flex;
        flex-direction: column;
        &-date {
            font-size: 20px;
            font-weight: bold;
            @media screen and (max-width: 640px) {
                font-size: 16px;
            }
        }
        &-desc {
            @media screen and (max-width: 640px) {
                font-size: 14px;
            }
        }
        background-color: $THEME_COLOR !important;
        color: white;
    }

    &__body {
        overflow: scroll;
        -webkit-overflow-scrolling: auto;
        @media screen and (max-width: 640px) {
            height: 50vh;
        }
        &-menu {
            margin: 10px 0;
        }
        background-color: $BACKGROUND_COLOR !important;
    }

    .reserve-menu-card {
        font-size: 20px;
        font-weight: bold;
        font-family: "ヒラギノ丸ゴ ProN W4";

        &__name {
            align-items: center;
        }
    }
}
</style>
