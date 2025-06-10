import {createRouter, createWebHistory} from "vue-router";
import {done, start} from "@/nprogress.js";
import BaseApp from "@/shop/App.vue";
import ManagementApp from "@/management/App.vue";
import shopRoutes from "@/shop/routes.js";
import managementRoutes from "@/management/routes.js";
import {useAuthStore} from "@/stores/useAuthStore.js";
import ForgotPassword from "@/shop/views/Auth/Password/ForgotPassword.vue";
import Layout from "@/shop/views/Auth/Password/Layout.vue";
import ResetPassword from "@/shop/views/Auth/Password/ResetPassword.vue";
import NotFound from "@/components/Default/NotFound.vue";
import ServerError from "@/components/Default/ServerError.vue";
import AuthManagement from "@/management/views/Manager/AuthManagement.vue";
import Maintenance from "@/components/Default/Maintenance.vue";

const routes = [
    {
        path: "/authentication/help",
        component: Layout,
        children: [
            {
                path: "forgot-password",
                name: "forgot-password",
                component: ForgotPassword,
            },
            {
                path: "reset-password/:token",
                name: "reset-password",
                component: ResetPassword,
                params: (route) => ({
                    token: route.query.token,
                    email: route.query.email,
                }),
            },
        ],
        meta: {guest: true},
    },
    {
        path: "/",
        component: BaseApp,
        children: shopRoutes,
    },
    {
        path: "/management/manager/auth",
        component: AuthManagement,
        name: "manager-login",
        meta: {
            guest: true
        },
        params: (route) => ({
            email: route.query.email
        })
    },
    {
        path: "/management",
        component: ManagementApp,
        children: managementRoutes,
        meta: {
            requiresManagerAuth: true,
        }
    },
    {
        path: "/500",
        component: ServerError,
    },
    {
        path: "/maintenance",
        component: Maintenance
    },
    {
        path: "/:any(.*)*",
        component: NotFound,
        name: "not-found",
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach(async (to, from, next) => {
    start();
    const auth = useAuthStore();

    if (to.meta.requiresManagerAuth) {
        return await auth.fetchCurrentUser().then(() => {
            if (!auth.isManager && !auth.isSuperManager) {
                return next({name: "home"});
            }
            return next();
        });
    }

    if (to.meta.guest && auth.isRegularUser) {
        return next({name: "home"});
    }

    if (to.meta.guest && (auth.isManager || auth.isSuperManager)) {
        return next({name: "manager-home"});
    }

    if (to.meta.requiresAuth) {
        return await auth.fetchCurrentUser().then(() => {
            if (!auth.isRegularUser) {
                return next({name: "login"});
            }
            return next();
        });
    }

    if (to.meta.onlySuperManager) {
        if (!auth.isSuperManager) {
            return next({name: "managers-home"});
        }
        return next();
    }

    next();
});

router.afterEach(() => {
    done();
});

export default router;
