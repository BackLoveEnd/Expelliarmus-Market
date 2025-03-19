import { createRouter, createWebHistory } from "vue-router";
import { done, start } from "@/nprogress.js";
import BaseApp from "@/shop/App.vue";
import ManagementApp from "@/management/App.vue";
import shopRoutes from "@/shop/routes.js";
import managementRoutes from "@/management/routes.js";
import { useAuthStore } from "@/stores/useAuthStore.js";
import ForgotPassword from "@/shop/views/Auth/Password/ForgotPassword.vue";
import Layout from "@/shop/views/Auth/Password/Layout.vue";
import ResetPassword from "@/shop/views/Auth/Password/ResetPassword.vue";

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
    meta: { guest: true },
  },
  {
    path: "/",
    component: BaseApp,
    children: shopRoutes,
  },
  {
    path: "/management",
    component: ManagementApp,
    children: managementRoutes,
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach(async (to, from, next) => {
  start();
  const auth = useAuthStore();

  if (to.meta.guest && auth.isAuthenticated) {
    return next({ name: "home" });
  }

  if (to.meta.requiresAuth) {
    return auth.fetchCurrentUser().then(() => {
      if (!auth.isAuthenticated) {
        return next({ name: "login" });
      }
      return next();
    });
  }

  next();
});

router.afterEach(() => {
  done();
});

export default router;
