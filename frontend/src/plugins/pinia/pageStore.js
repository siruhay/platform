import { defineStore } from "pinia";

// if (!sessionStorage.tabCreateTimestamp) {
//     sessionStorage.tabCreateTimestamp = Date.now();
// }

export const usePageStore = defineStore("pageStore", {
    state: () => ({
        email: "monoland@dev",
        password: "P@ssw0rd",
        password_confirmation: "P@ssw0rd",
        forgotPassword: false,
        challengedPassword: false,
        resetFeature: false,
        twoFactor: null,
        twoFactorCode: null,
        twoFactorMode: "code",
        twoModeCode: true,

        auth: null,
        activityLog: false,
        accountBase: {},
        appsMenus: [],

        beforePost: undefined,

        checksum: null,
        combos: {},
        currentFile: {},

        dialogFile: false,
        dockMenus: [],
        domain: "backend",

        filters: {},
        formState: null,
        formStateLast: null,

        geoCoords: {},
        geoIsDenied: false,
        geoInitialized: false,
        geoSupport: false,

        headers: [],
        helpState: false,
        highlight: "deep-orange",

        icon: null,
        initialized: false,
        itemsPerPage: 10,
        isMobile: false,
        impersonated: false,

        key: null,

        loading: false,
        logs: [],

        meta: {},
        modules: {
            administrator: [],
            personal: [],
        },

        module: {},
        moduleName: null,
        moduleKey: null,
        moduleType: null,

        navigationState: true,

        overlay: false,

        params: {},
        paramsCache: {},
        paramsOld: {},

        page: {},
        pageKey: null,
        pageName: null,
        pagePath: null,

        parent: {},
        parentKey: null,
        parentName: null,

        railMode: false,
        record: {},
        recordBase: {},
        records: [],
        routePrefix: false,

        search: null,
        selected: [],
        sideMenus: [],
        sidehelpState: false,
        sidenavState: false,
        snackbar: {
            color: "red",
            state: false,
            text: null,
        },
        softdelete: false,
        statuses: {},

        title: null,
        theme: "teal",
        totalRecords: 0,
        trashed: null,

        usesync: false,
        usetrash: false,
    }),

    getters: {
        buildPath: (state) => {
            let pagePath =
                state.pagePath ??
                `${state.module.prefix ? state.module.prefix + "/" : ""}api/${
                    state.page.path
                }`;

            Object.keys(state.$route.params).forEach((param) => {
                pagePath = pagePath.replace(
                    `:${param}`,
                    state.$route.params[param]
                );
            });

            return pagePath;
        },

        hasSelected: (state) =>
            Array.isArray(state.selected) && state.selected.length > 0,
    },

    actions: {
        authenticate() {
            this.$http(`account/login`, {
                method: "POST",
                params: {
                    email: this.email,
                    password: this.password,
                },
            }).then((response) => {
                this.twoFactor = response.two_factor;
                this.$storage.setItem("twoFactor", this.twoFactor);
                this.email = null;
                this.password = null;

                if (!this.twoFactor) {
                    this.getUserData();
                }
            });
        },

        clearFilters() {
            this.search = null;
            this.paramsCache.findBy = null;

            this.filters = {};
            this.paramsCache.filters = null;
        },

        challenge() {
            let params = this.twoModeCode
                ? { code: this.twoFactorCode }
                : { recovery_code: this.twoFactorCode };

            this.$http(`account/login-challenge`, {
                method: "POST",
                params: params,
            }).then(() => {
                this.getUserData();
            });
        },

        finduser() {
            this.$http(`account/login-finder`, {
                method: "POST",
                params: {
                    email: this.email,
                },
            }).then(() => {
                this.resetFeature = true;
            });
        },

        initAccount() {
            this.module = {};
            this.dockMenus = [];
            this.sideMenus = [];
        },

        initApplication() {
            this.auth = this.$storage.getItem("auth");
            this.checksum = this.$storage.getItem("checksum");
            this.modules = this.$storage.getItem("modules");

            if (this.modules && "account" in this.modules) {
                this.accountBase = this.modules.account[0];
                this.appsMenus = this.accountBase.pages.reduce(
                    (carry, page) => {
                        if (page.side === true) {
                            carry.push(page);
                        }

                        return carry;
                    },
                    []
                );
            }

            if (this.auth) {
                this.theme = "theme" in this.auth ? this.auth.theme : "teal";
                this.highlight =
                    "highlight" in this.auth
                        ? this.auth.highlight
                        : "deep-orange";
            }
        },

        initGeoLocation() {
            if ("geolocation" in navigator) {
                this.geoSupport = true;

                // setInterval(() => {
                navigator.geolocation.getCurrentPosition(
                    (pos) => {
                        const { accuracy, altitude, latitude, longitude } =
                            pos.coords;

                        this.geoCoords = {
                            accuracy,
                            altitude,
                            latitude,
                            longitude,
                        };

                        this.$http(`account/api/user-geodata`, {
                            method: "POST",
                            params: {
                                coords: this.geoCoords,
                            },
                        }).then(() => {
                            this.geoInitialized = true;
                        });
                    },
                    (err) => {
                        this.geoIsDenied = err.code === 1;
                        this.geoInitialized = false;
                    }
                );
                // }, 1000 * 30);
            }
        },

        initModule({ mobile }) {
            this.isMobile = mobile;

            if (this.moduleName === "account") {
                this.module = JSON.parse(JSON.stringify(this.accountBase));
            } else {
                this.module = this.modules[this.moduleType].find(
                    (module) => module.slug === this.moduleName
                );
            }

            if (this.module) {
                this.dockMenus = this.module.pages.reduce((carry, page) => {
                    if (page.dock === true) {
                        carry.push(page);
                    }

                    return carry;
                }, []);

                this.sideMenus = this.module.pages.reduce((carry, page) => {
                    if (page.side === true) {
                        carry.push(page);
                    }

                    return carry;
                }, []);
            }
        },

        initPage(callback) {
            this.combos = {};
            this.filters = {};
            this.headers = [];
            this.icon = null;
            this.key = null;
            this.logs = [];
            this.recordBase = {};
            this.statuses = {};
            this.title = null;
            this.trashed = null;
            this.record = {};
            this.records = [];
            this.totalRecords = 0;
            this.selected = [];
            this.initialized = false;

            /** initial meta and params if mobile */
            if (this.isMobile && Object.keys(this.meta).length === 0) {
                this.meta = {
                    current_page: 1,
                    last_page: 1,
                };

                this.params = {
                    page: 1,
                    itemsPerPage: 10,
                    sortBy: [],
                    groupBy: [],
                };
            }

            /** jika page tidak sama dengan yang sedang di buka */
            if (
                this.page &&
                Object.keys(this.page).length > 0 &&
                this.page.slug !== this.pageName
            ) {
                this.$storage.removeItem("recordBase");

                if (this.isMobile) {
                    this.meta = {
                        current_page: 1,
                        last_page: 1,
                    };

                    this.params = {
                        page: 1,
                        itemsPerPage: 10,
                        sortBy: [],
                        groupBy: [],
                    };
                }

                this.paramsOld = {};
            }

            this.page = this.module.pages.find(
                (page) => page.slug === this.pageName
            );

            if (typeof callback === "function") {
                callback();
            }
        },

        jsonReplacer(match, pIndent, pKey, pVal, pEnd) {
            let key = "<span class=json-key>";
            let val = "<span class=json-value>";
            let str = "<span class=json-string>";
            let r = pIndent || "";

            if (pKey) r = r + key + pKey.replace(/[": ]/g, "") + "</span>: ";
            if (pVal) r = r + (pVal[0] == '"' ? str : val) + pVal + "</span>";
            return r + (pEnd || "");
        },

        jsonPrettyPrint(obj) {
            let jsonLine = /^( *)("[\w]+": )?("[^"]*"|[\w.+-]*)?([,[{])?$/gm;

            return JSON.stringify(obj, null, 3)
                .replace(/&/g, "&amp;")
                .replace(/\\"/g, "&quot;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(jsonLine, this.jsonReplacer);
        },

        getCreateData() {
            this.combos = this.$storage.getItem("combos");
            this.recordBase = this.$storage.getItem("recordBase");
            this.record = JSON.parse(JSON.stringify(this.recordBase));

            // if (Object.keys(this.record).length <= 0) {
            //     this.recordBase = this.$storage.getItem("recordBase");
            //     this.record = JSON.parse(JSON.stringify(this.recordBase));
            // }
        },

        getDashboard(callback) {
            if (!this.page || (this.page && this.page.slug !== this.pageName)) {
                this.initPage();
            }

            this.sidenavState = false;

            this.$http(
                `${
                    this.module.prefix ? this.module.prefix + "/" : ""
                }api/dashboard`
            ).then(({ record }) => {
                this.record = record;

                if (typeof callback === "function") {
                    callback(record);
                }
            });
        },

        getPageData(dataFromStore) {
            if (!this.page || (this.page && this.page.slug !== this.pageName)) {
                this.initPage();
            }

            if (dataFromStore && Object.keys(this.record).length > 0) {
                return;
            } else {
                let pagePath = null;

                if (!this.$route.params[this.pageKey]) {
                    pagePath = this.buildPath;
                } else {
                    pagePath = `${this.buildPath}/${
                        this.$route.params[this.pageKey]
                    }`;
                }

                if (this.routePrefix) {
                    pagePath = pagePath + this.routePrefix;
                }

                this.$http(pagePath, {
                    method: "GET",
                    params: {
                        activities: this.activityLog ? true : null,
                    },
                })
                    .then((response) => {
                        this.mapResponseData(response);
                    })
                    .catch(() => this.mapPageDataError());
            }
        },

        getPageDatas(params) {
            if (!this.page || (this.page && this.page.slug !== this.pageName)) {
                this.initPage();
            }

            if (Object.keys(params).length === 0) {
                return;
            }

            const defaultParams = this.mapDefaultParams(params);

            if (!defaultParams) {
                return;
            }

            let pagePath = this.buildPath;

            this.$http(pagePath, {
                method: "GET",
                params: defaultParams,
            })
                .then((response) => {
                    this.mapResponseData(response);
                })
                .catch(() => this.mapPageDatasError());
        },

        getSanctumCSRF({ mobile }) {
            this.isMobile = mobile;
            this.$http(`sanctum/csrf-cookie`);
        },

        getUserData() {
            this.$http(`account/api/user-data`, {
                params: {
                    mobile: true,
                },
            }).then((response) => {
                this.auth = response.auth;
                this.checksum = response.checksum;
                this.modules = response.modules;
                this.theme = "theme" in this.auth ? this.auth.theme : "teal";
                this.highlight =
                    "highlight" in this.auth
                        ? this.auth.highlight
                        : "deep-orange";

                if (response.modules && "account" in response.modules) {
                    this.accountBase = response.modules.account[0];
                    this.appsMenus = this.accountBase.pages.reduce(
                        (carry, page) => {
                            if (page.side === true) {
                                carry.push(page);
                            }

                            return carry;
                        },
                        []
                    );
                }

                this.$storage.setItem("auth", response.auth);
                this.$storage.setItem("checksum", response.checksum);
                this.$storage.setItem("modules", response.modules);
                this.$storage.setItem("token", response.token);

                this.redirectUserToDashboard();
            });
        },

        getUserDashboard() {
            this.$http(`account/api/dashboard`).then((response) => {
                this.mapResponseData(response);
            });
        },

        getUserModules() {
            if (!this.page || (this.page && this.page.slug !== this.pageName)) {
                this.initPage();
            }

            this.$http(`account/api/user-modules`).then((response) => {
                this.mapUserModule(response);
            });
        },

        mapUserModule(response) {
            if ("auth" in response) {
                this.auth = response.auth;
                this.$storage.setItem("auth", response.auth);

                this.theme = "theme" in this.auth ? this.auth.theme : "teal";
            }

            if ("checksum" in response && response.checksum !== this.checksum) {
                this.checksum = response.checksum;
                this.$storage.setItem("checksum", response.checksum);

                this.modules = response.modules;
                this.$storage.setItem("modules", response.modules);
            }

            this.impersonated = false;

            if ("impersonated" in response) {
                this.impersonated = true;
            }
        },

        mapResponseData(response) {
            if ("data" in response) {
                const { data, meta, setups } = response;

                /** map meta */
                if (meta) {
                    this.meta = meta;
                    this.totalRecords = meta.total;
                }

                /** map setups */
                if (setups) {
                    const {
                        combos,
                        filters,
                        headers,
                        icon,
                        key,
                        parent,
                        recordBase,
                        usetrash,
                        statuses,
                        title,
                        trashed,
                    } = setups;

                    this.combos =
                        combos && Object.keys(combos).length > 0 ? combos : {};

                    this.filters =
                        filters && Object.keys(filters).length > 0
                            ? filters
                            : {};

                    this.headers =
                        headers && Array.isArray(headers) ? headers : [];

                    this.icon = icon;
                    this.key = key;

                    this.parent =
                        parent && Object.keys(parent).length > 0 ? parent : {};

                    this.logs = [];
                    this.recordBase =
                        recordBase && Object.keys(recordBase).length > 0
                            ? recordBase
                            : {};
                    this.record = JSON.parse(JSON.stringify(this.recordBase));
                    this.usetrash = usetrash ?? false;
                    this.statuses =
                        statuses && Object.keys(statuses).length > 0
                            ? statuses
                            : {};
                    this.title = title;
                    this.trashed = trashed;

                    this.initialized = true;

                    this.$storage.setItem("combos", this.combos);
                    this.$storage.setItem("recordBase", this.recordBase);
                }

                /** map record */
                if (this.isMobile) {
                    if (
                        Object.keys(this.paramsCache).length > 0 &&
                        this.paramsCache.trashed === this.paramsOld.trashed &&
                        this.paramsCache.findBy === this.paramsOld.findBy &&
                        this.paramsCache.filters === this.paramsOld.filters
                    ) {
                        data.forEach((record) => {
                            if (
                                !this.records.find(
                                    (rc) => rc[this.key] === record[this.key]
                                )
                            ) {
                                this.records.push(record);
                            }
                        });
                    } else {
                        this.records = data ?? [];
                    }

                    this.paramsCache = JSON.parse(
                        JSON.stringify(this.paramsOld)
                    );
                } else {
                    this.records = data ?? [];
                }
            } else {
                const { record, setups } = response;

                this.record = record;

                if (setups) {
                    const {
                        combos,
                        icon,
                        key,
                        parent,
                        logs,
                        softdelete,
                        statuses,
                        title,
                    } = setups;

                    this.combos =
                        combos && Object.keys(combos).length > 0 ? combos : {};
                    this.icon = icon;
                    this.key = key;
                    this.parent =
                        parent && Object.keys(parent).length > 0 ? parent : {};
                    this.logs = logs && Array.isArray(logs) ? logs : [];
                    this.softdelete = softdelete ?? false;
                    this.statuses =
                        statuses && Object.keys(statuses).length > 0
                            ? statuses
                            : {};
                    this.title = title;
                }
            }
        },

        mapPageDataError() {
            this.record = {};
            this.title = "Untitled";
        },

        mapPageDatasError() {
            this.combos = {};
            this.filters = {};
            this.headers = [];
            this.icon = null;
            this.key = null;
            this.logs = [];
            this.recordBase = {};
            this.record = {};
            this.statuses = {};
            this.title = "Untitled";
            this.trashed = false;
            this.initialized = false;
        },

        mapDefaultParams(params) {
            let filters = "";

            /**
             * remap filters to string
             */
            Object.keys(this.filters).forEach((key, idx) => {
                if (idx > 0 && filters !== "") {
                    filters = filters + "+";
                }

                if (this.filters[key].used) {
                    filters =
                        filters +
                        key +
                        ";" +
                        this.filters[key].operator +
                        ";" +
                        this.filters[key].value;
                }
            });

            const defaultParams = Object.assign(
                {
                    initialized:
                        (params &&
                            Object.keys(params).length > 0 &&
                            params.page !== 1) ||
                        this.initialized
                            ? true
                            : null,
                    filters: filters !== "" ? filters : null,
                    findBy:
                        this.search && this.search !== "" ? this.search : null,
                    trashed:
                        this.trashed && this.trashed !== "false" ? true : null,
                },
                params
            );

            /**
             * remap sortBy from array to string
             */
            if (
                Array.isArray(defaultParams.sortBy) &&
                defaultParams.sortBy.length > 0
            ) {
                let sortBy = null;

                defaultParams.sortBy.forEach((sort) => {
                    sortBy = sort.key + ":" + sort.order;
                });

                defaultParams.sortBy = sortBy;
            } else {
                defaultParams.sortBy = null;
            }

            /**
             * remap groupBy from array to string
             */
            if (
                Array.isArray(defaultParams.groupBy) &&
                defaultParams.groupBy.length > 0
            ) {
                let groupBy = null;

                defaultParams.groupBy.forEach((sort, idx) => {
                    if (idx > 0) {
                        groupBy = groupBy + "+";
                    }

                    groupBy = sort.key + ":" + sort.order;
                });

                defaultParams.groupBy = groupBy;
            } else {
                defaultParams.groupBy = null;
            }

            let resultParams = JSON.parse(JSON.stringify(defaultParams));

            if ("initialized" in resultParams) {
                delete resultParams.initialized;
            }

            if (Object.keys(this.paramsOld).length === 0) {
                this.paramsOld = JSON.parse(JSON.stringify(resultParams));
            } else {
                if (
                    JSON.stringify(resultParams) ===
                    JSON.stringify(this.paramsOld)
                ) {
                    return null;
                } else {
                    this.paramsOld = JSON.parse(JSON.stringify(resultParams));
                }
            }

            return defaultParams;
        },

        openFormCreate() {
            this.formStateLast = JSON.parse(JSON.stringify(this.formState));
            this.formState = "create";

            // if (this.sidenavState) {
            //     this.sidenavState = false;
            setTimeout(() => {
                this.$router.push({ name: this.page.slug + "-create" });
            }, 300);
            // } else {
            //     this.$router.push({ name: this.page.slug + "-create" });
            // }
        },

        openFormData() {
            this.formStateLast = JSON.parse(JSON.stringify(this.formState));
            this.record = JSON.parse(JSON.stringify(this.recordBase));
            // this.helpState = false;

            if (this.formState === "create") {
                this.search = null;
            } else {
                this.setSelected();
            }

            this.$router.push({ name: this.page.slug });
            this.formState = null;
        },

        openFormRoute(routeName) {
            if (!this.$router.hasRoute(routeName)) {
                this.snackbar.color = "deep-orange";
                this.snackbar.text = "route: " + routeName + " does`nt exists.";
                this.snackbar.state = true;
                return;
            }

            const params = {};

            const routeTarget = this.$router
                .getRoutes()
                .find((rt) => rt.name === routeName);

            if (routeTarget.path.includes(`:${this.pageKey}`)) {
                params[this.pageKey] = this.record[this.key];
            }

            this.$router.push({
                name: routeName,
                params: { ...params, ...this.$route.params },
            });
        },

        openFormEdit() {
            this.formStateLast = JSON.parse(JSON.stringify(this.formState));

            this.formState = "edit";

            this.openFormRoute(this.page.slug + "-edit");
        },

        openFormShow() {
            this.formStateLast = JSON.parse(JSON.stringify(this.formState));

            this.formState = "show";

            this.openFormRoute(this.page.slug + "-show");
        },

        postFormCreate() {
            if (this.beforePost) {
                this.beforePost(this.record);
            }

            const pagePath = this.buildPath;

            this.$http(pagePath, {
                method: "POST",
                params: this.record,
            }).then((record) => {
                this.records.push(record);

                this.openFormData();

                this.snackbar.color = "green";
                this.snackbar.text = `tambah data ${this.pageKey} berhasil`;
                this.snackbar.state = true;
            });
        },

        postFormDelete(callback) {
            if (this.beforePost) {
                this.beforePost(this.record);
            }

            if (typeof callback === "function") {
                callback();
            }

            const pagePath = `${this.buildPath}/${
                this.$route.params[this.pageKey]
            }`;

            this.$http(pagePath, {
                method: "DELETE",
                params: this.record,
            }).then((record) => {
                let index = this.records.findIndex(
                    (rc) => rc[this.key] === record[this.key]
                );

                if (index !== -1) {
                    this.records.splice(index, 1);
                }

                this.openFormData();

                this.snackbar.color = "green";
                this.snackbar.text = `hapus data ${this.pageKey} berhasil`;
                this.snackbar.state = true;
            });
        },

        postFormEdit() {
            if (this.beforePost) {
                this.beforePost(this.record);
            }

            const pagePath = `${this.buildPath}/${
                this.$route.params[this.pageKey]
            }`;

            this.$http(pagePath, {
                method: "PUT",
                params: this.record,
            }).then((record) => {
                let index = this.records.findIndex(
                    (rc) => rc[this.key] === record[this.key]
                );

                if (index !== -1) {
                    Object.keys(record).forEach((key) => {
                        this.records[index][key] = record[key];
                    });
                }

                this.openFormData();

                this.snackbar.color = "green";
                this.snackbar.text = `update data ${this.pageKey} berhasil`;
                this.snackbar.state = true;
            });
        },

        postFormForceDelete(callback) {
            if (this.beforePost) {
                this.beforePost(this.record);
            }

            if (typeof callback === "function") {
                callback();
            }

            const pagePath = `${this.buildPath}/${
                this.$route.params[this.pageKey]
            }/force`;

            this.$http(pagePath, {
                method: "DELETE",
                params: this.record,
            }).then((record) => {
                let index = this.records.findIndex(
                    (rc) => rc[this.key] === record[this.key]
                );

                if (index !== -1) {
                    this.records.splice(index, 1);
                }

                this.openFormData();

                this.snackbar.color = "green";
                this.snackbar.text = `permanent hapus data ${this.pageKey} berhasil`;
                this.snackbar.state = true;
            });
        },

        postFormRestore(callback) {
            if (this.beforePost) {
                this.beforePost(this.record);
            }

            if (typeof callback === "function") {
                callback();
            }

            const pagePath = `${this.buildPath}/${
                this.$route.params[this.pageKey]
            }/restore`;

            this.$http(pagePath, {
                method: "PUT",
                params: this.record,
            }).then((record) => {
                let index = this.records.findIndex(
                    (rc) => rc[this.key] === record[this.key]
                );

                if (index !== -1) {
                    this.records.splice(index, 1);
                }

                this.openFormData();

                this.snackbar.color = "green";
                this.snackbar.text = `permanent hapus data ${this.pageKey} berhasil`;
                this.snackbar.state = true;
            });
        },

        redirectUserToDashboard: function () {
            this.$router.push({ name: "account-dashboard" });
        },

        resetpass(callback) {
            if (typeof callback === "function") {
                callback();
            }

            let params = this.twoModeCode
                ? { code: this.twoFactorCode }
                : { recovery_code: this.twoFactorCode };

            this.$http(`account/reset-password`, {
                method: "POST",
                params: {
                    ...params,
                    email: this.email,
                    password: this.password,
                    password_confirmation: this.password_confirmation,
                },
            }).then(() => {
                //
            });
        },

        setHelpState(state) {
            this.helpState = state;
        },

        setSidenavState(state) {
            this.sidehelpState = false;
            this.sidenavState = state;
        },

        setSelected(item) {
            if (item && Array.isArray(item)) {
                this.selected = item;
            } else if (item && !Array.isArray(item)) {
                if (Array.isArray(this.selected) && this.selected.length > 0) {
                    this.selected =
                        this.selected[0][this.key] === item[this.key]
                            ? []
                            : [item];
                } else {
                    this.selected = [item];
                }
            } else {
                this.selected = [];
            }

            this.record =
                this.selected.length > 0
                    ? JSON.parse(JSON.stringify(this.selected[0]))
                    : JSON.parse(JSON.stringify(this.recordBase));
        },

        signOut(callback) {
            this.$http(`account/api/logout`, {
                method: "POST",
            }).then(() => {
                if (typeof callback === "function") {
                    callback();
                }

                setTimeout(() => {
                    Object.keys(localStorage).forEach((key) => {
                        localStorage.removeItem(key);
                    });

                    window.location.replace("/");
                }, 200);
            });
        },
    },
});
