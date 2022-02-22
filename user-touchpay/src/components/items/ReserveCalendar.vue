<template>
    <div class="calendar">
        <v-calendar id="reservation-calendar" is-expanded :min-date="startMonth" :max-date="endMonth" locale="ja">
            <template slot="header-title" slot-scope="props"> {{ yearMonthLabel(props) }} </template>
            <template slot="day-content" slot-scope="props">
                <div
                    :id="`calendar-${props.day.id}`"
                    class="cell-container"
                    :class="{ holiday: holiday(props.day.date), pastDate: pastDate(props.day.date) }"
                >
                    <div class="cell-header">
                        <div class="cell-header__date">
                            {{ props.day.day }}
                        </div>
                    </div>
                    <div
                        :id="`calendar-cell-${props.day.id}`"
                        class="cell-content"
                        @click="openReservationModal(props.day.date)"
                    >
                        <b-tag
                            :id="`calendar-cell-tag-${props.day.id}`"
                            class="cell-content__tag"
                            v-if="oneDayReservationCount(instrumentDivision, props.day.date) !== 0"
                            type="is-info"
                            expanded
                        >
                            <span class="cell-content__tag--disable">予約あり</span>
                        </b-tag>
                    </div>
                </div>
            </template>
        </v-calendar>
        <b-modal :active.sync="isReserveModalActive" has-modal-card trap-focus :destroy-on-hide="true">
            <ReserveModal :instrumentDivision="instrumentDivision" :reservationDate="reservationDate"></ReserveModal>
        </b-modal>
    </div>
</template>

<script lang="ts">
import { Component, Mixins, Prop } from "vue-property-decorator";
import StoreMixin from "@/store/StoreMixin";
import ReserveModal from "@/components/items/ReserveModal.vue";
import { isPastDate, severalMonthsBeforeNow, severalMonthsAfterNow } from "@/assets/scripts/date";

@Component({
    components: { ReserveModal },
})
export default class ReserveCalendar extends Mixins(StoreMixin) {
    @Prop()
    private instrumentDivision!: number;

    private reservationDate = "";

    private isReserveModalActive = false;

    private startMonth: Date = new Date();

    private endMonth: Date = new Date();

    private openReservationModal(reservationDate: string) {
        const pastDate = isPastDate(reservationDate);
        const holiday = this.isHoliday(this.instrumentDivision, reservationDate);
        if (!(pastDate || holiday)) {
            this.reservationDate = reservationDate;
            this.isReserveModalActive = true;
        }
    }

    private created() {
        this.startMonth = severalMonthsBeforeNow(2);
        this.endMonth = severalMonthsAfterNow(2);
    }

    private get holiday() {
        return (reservationDate: string): boolean => {
            return this.isHoliday(this.instrumentDivision, reservationDate);
        };
    }

    private get pastDate() {
        return (reservationDate: string): boolean => {
            return isPastDate(reservationDate)
        };
    }

        /**
     * カレンダーのタイトルの年月をYYYY年MM月に変換
     * 献立表で使うために現在の年月YYYYMMをstoreに保持
     */
    private get yearMonthLabel() {
        // eslint-disable-next-line
        return (props: any): string => {
            this.setDisplayDate(`${props.yearLabel}${props.month.toString().padStart(2, "0")}`);
            return `${props.yearLabel}年${props.monthLabel}`;
        };
    }
}
</script>

<style lang="scss">
.calendar {
    .vc-weekday,
    .vc-day {
        border: 1px solid #cbd5e0;
        border-collapse: collapse;
    }
    .holiday {
        background-color: #ffcccc;
        color: #ff3a3a;
    }
    
    .pastDate {
        background-color: #aaaaaa;
    }

    .cell {
        &-container {
            padding: 5px;
        }

        &-header {
            text-align: left;
            font-weight: bold;
            font-size: 20px;
        }

        &-content {
            height: 5vh;

            &__tag {
                @media screen and (max-width: 640px) {
                    width: 2em;
                    border-radius: 50%;
                }
                &--disable {
                    @media screen and (max-width: 640px) {
                        visibility: hidden;
                    }
                }
            }
        }
    }
}
</style>
