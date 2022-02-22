import Vue from "vue";
import App from "./App.vue";
import "./registerServiceWorker";
import Buefy from "buefy";
import "@/assets/styles/buefy.scss";
import "./assets/styles/global.css";

Vue.config.productionTip = false;
Vue.use(Buefy);

new Vue({
    render: (h) => h(App),
}).$mount("#app");
