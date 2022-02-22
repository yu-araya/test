import Vue from "vue";
import VueRouter from "vue-router";
import Login from "@/views/Login.vue";
import Order from "@/views/Order.vue";

Vue.use(VueRouter);

const routes = [
    {
        path: "/Login",
        name: "Login",
        component: Login,
    },
    {
        path: "/Order",
        name: "Order",
        component: Order,
    },
];

const router = new VueRouter({
    base: process.env.BASE_URL,
    routes,
});

export default router;
