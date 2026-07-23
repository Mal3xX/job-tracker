import { createRouter, createWebHistory } from "vue-router";
import { useAuthStore } from "@/stores/useAuthStore";

const routes = [
  {
    path: "/login",
    name: "login",
    component: () => import("@/views/auth/LoginView.vue"),
    meta: { guest: true },
  },
  {
    path: "/register",
    name: "register",
    component: () => import("@/views/auth/RegisterView.vue"),
    meta: { guest: true },
  },
  {
    path: "/",
    component: () => import("@/views/layouts/AppLayout.vue"),
    meta: { requiresAuth: true },
    children: [
      {
        path: "",
        name: "dashboard",
        component: () => import("@/views/dashboard/DashboardView.vue"),
      },
      {
        path: "settings",
        name: "settings",
        component: () => import("@/views/settings/SettingsView.vue"),
      },
      {
        path: "companies",
        name: "companies",
        component: () => import("@/views/companies/CompanyListView.vue"),
      },
      {
        path: "companies/:id",
        name: "company-detail",
        component: () => import("@/views/companies/CompanyDetailView.vue"),
      },
      {
        path: "applications",
        name: "applications",
        component: () => import("@/views/applications/ApplicationListView.vue"),
      },
      {
        path: "applications/:id",
        name: "application-detail",
        component: () =>
          import("@/views/applications/ApplicationDetailView.vue"),
      },
      {
        path: "admin/users",
        name: "admin-users",
        component: () => import("@/views/admin/AdminUsersListView.vue"),
      },
      {
        path: "admin/users/:id",
        name: "admin-user-detail",
        component: () => import("@/views/admin/AdminUserDetailView.vue"),
      },
    ],
  },
  {
    path: "/:pathMatch(.*)*",
    name: "not-found",
    redirect: { name: "login" },
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach(async (to, _from) => {
  const authStore = useAuthStore();
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    try {
      await authStore.fetchUser();
    } catch {
      return { name: "login" };
    }
  }
  if (to.meta.guest && authStore.isAuthenticated) {
    return { name: "dashboard" };
  }
});

export default router;
