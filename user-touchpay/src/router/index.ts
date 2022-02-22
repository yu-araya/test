import Vue from "vue";
import VueRouter, { RouteConfig } from "vue-router";
import Login from "@/views/Login.vue";
import Main from "@/views/Main.vue";
import NotFound from "@/views/NotFound.vue";
import UserQrcode from "@/components/qrcode/UserQrcode.vue";
import FoodHistory from "@/components/foodHistory/FoodHistory.vue";
import Reservation from "@/components/reservation/Reservation.vue";
import ChangePassword from "@/components/password/ChangePassword.vue";
import MealVoucher from "@/components/mealVoucher/MealVoucher.vue";
import { auth } from "@/store/states/UserInfoStore";
import { showAlertDialog } from "@/assets/scripts/alert-dialog";
import { messages } from "@/assets/scripts/constant";

Vue.use(VueRouter);

const routes: Array<RouteConfig> = [
    {
        path: "/login",
        name: "login",
        component: Login,
        // eslint-disable-next-line
        beforeEnter: (to, from, next) => {
            if (auth.loggedIn) auth.logout();
            next();
        },
    },
    {
        path: "/:userId",
        name: "Main",
        component: Main,
        children: [
            {
                path: "qrcode",
                name: "qrcode",
                component: UserQrcode,
                meta: { requireAuth: true },
            },
            {
                path: "foodHistory",
                name: "foodHistory",
                component: FoodHistory,
                meta: {
                    requireAuth: true,
                    foodHistoryOption: true,
                },
            },
            {
                path: "voucher",
                name: "voucher",
                component: MealVoucher,
                meta: {
                    requireAuth: true,
                },
            },
            {
                path: "reservation",
                name: "reservation",
                component: Reservation,
                meta: {
                    requireAuth: true,
                    reservationOption: true,
                },
            },
            {
                path: "password",
                name: "password",
                component: ChangePassword,
                meta: {
                    requireAuth: true,
                },
            },
        ],
    },
    { path: "/*", component: NotFound },
];

const router = new VueRouter({
    base: process.env.BASE_URL,
    routes,
});

router.beforeEach((to, from, next) => {
    if (to.matched.some((record) => record.meta.requireAuth) && !auth.loggedIn) {
        showAlertDialog(messages.error4.msg, messages.error4.type);
        next({ name: "login" });
    } else {
        next();
    }
});

export default router;
