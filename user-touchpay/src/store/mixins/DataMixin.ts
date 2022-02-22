import { Component, Mixins } from "vue-property-decorator";
import FoodHistoryDataStore from "@/store/states/FoodHistoryDataStore";
import ReservationDataStore from "@/store/states/ReservationDataStore";

@Component
export default class DataMixin extends Mixins(FoodHistoryDataStore, ReservationDataStore) {}
