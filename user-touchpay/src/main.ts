import Vue from "vue";
import App from "./App.vue";
import router from "./router";
import Buefy from "buefy";
import "normalize.css";
import "buefy/dist/buefy.css";
import VCalendar from "v-calendar";
import { ValidationProvider, ValidationObserver } from "vee-validate";
import "@/assets/plugins/vee-validate";

Vue.use(Buefy);
Vue.use(VCalendar);
Vue.config.productionTip = false;
Vue.component("ValidationProvider", ValidationProvider);
Vue.component("ValidationObserver", ValidationObserver);

new Vue({
    router,
    render: (h) => h(App),
}).$mount("#app");
