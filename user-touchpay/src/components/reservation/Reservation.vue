<template>
    <div class="container tile is-ancester reservation">
        <div class="tile is-vertical is-parent is-8">
            <div class="reservation__instrument">
                <span>事業所：</span>
                <b-select
                    id="instrument-select-box"
                    v-model="selectedInstrument"
                    placeholder="事業所を選択してください"
                >
                    <option v-for="option in instruments" :value="option.division" :key="option.division">
                        {{ option.name }}
                    </option>
                </b-select>
            </div>
            <div class="tile pdf-button-wrapper">
                <b-button class="pdf-button" v-if="ifExistPdfFile" @click="openPdf">献立表はこちら</b-button>
            </div>
            <div class="tile is-child" v-if="selectedInstrument" ref="reservation">
                <p id="calendar-description" class="has-text-left">予約したい日付を選択してください。</p>
                <reserve-calendar :instrumentDivision="selectedInstrument"></reserve-calendar>
                <b-loading :is-full-page="false" :active.sync="isLoading"> </b-loading>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Mixins } from "vue-property-decorator";
import ReserveCalendar from "@/components/items/ReserveCalendar.vue";
import StoreMixin from "@/store/StoreMixin";
import axios from "axios";
import { urls } from "@/assets/scripts/constant";

@Component({
    components: {
        ReserveCalendar,
    },
})
export default class Reservation extends Mixins(StoreMixin) {
    private selectedInstrument = 0;
    private isLoading = false;
    private pdfUrls: string[] = [];

    private async mounted() {
        this.isLoading = true;
        this.selectedInstrument = this.instruments[0].division;
        await this.loadReservationData(this.userInfo.id);
        await this.pdfDownload();
        this.isLoading = false;
    }

    private async pdfDownload() {
        const file = await axios.get(urls.getMenuPdfPathUrl).catch((err) => console.error(err));
        this.pdfUrls = file && file.data.pdfUrls;
    }

    private get ifExistPdfFile(): boolean {
        return this.pdfUrls?.some((url) => url.match(`.*${this.displayDate}.pdf`));
    }

    private openPdf() {
        const url = this.pdfUrls?.find((url) => url.match(`.*${this.displayDate}.pdf`));
        window.open(url, "_blank");
    }
}
</script>

<style lang="scss">
.reservation {
    justify-content: center;
    &__instrument {
        display: flex;
        margin-bottom: 10px;
        align-items: center;
    }
}
.pdf-button-wrapper {
    margin: 1em 0;

    .pdf-button {
        @media screen and (min-width: 640px) {
            width: 30%;
        }
        @media screen and (max-width: 640px) {
            width: 100%;
        }
    }
}
</style>
