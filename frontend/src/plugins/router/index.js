// Composables
import { createRouter, createWebHistory } from "vue-router";
import PlatformRoute from "@modules/system/account/router/index.js";

const routes = PlatformRoute;

/** Scan and add modules route except platform */
const routeFiles = import.meta.glob("@modules/**/frontend/router/index.js", {
	eager: true,
});

Object.entries(routeFiles).forEach((file) => {
	if (!String(file[0]).includes("platform/frontend/router")) {
		routes.push(file[1].default);
	}
});

const router = createRouter({
	history: createWebHistory(import.meta.env.BASE_URL),
	routes,
});

router.beforeEach(async (to, from, next) => {
	if (to.meta.requiredAuth && !localStorage.getItem("auth")) {
		next({ name: "welcome-page" });
	} else if (to.name === "welcome-page" && localStorage.getItem("auth")) {
		next({ name: "account-dashboard" });
	} else {
		next();
	}
});

export default router;
