export class Storage {
    constructor() {
        this.store = {
            setItem: (key, val) => {
                try {
                    window.localStorage.setItem(
                        key,
                        window.btoa(JSON.stringify(val))
                    );
                } catch (error) {
                    window.localStorage.removeItem(key);
                }
            },

            getItem: (key) => {
                try {
                    return JSON.parse(
                        window.atob(window.localStorage.getItem(key))
                    );
                } catch (error) {
                    return null;
                }
            },

            hasItem: (key) => {
                if (
                    Object.prototype.hasOwnProperty.call(
                        window.localStorage,
                        key
                    )
                ) {
                    return (
                        JSON.parse(
                            window.atob(window.localStorage.getItem(key))
                        ) !== undefined
                    );
                } else {
                    return false;
                }
            },

            removeItem: (key) => {
                return window.localStorage.removeItem(key);
            },

            clear: () => {
                return window.localStorage.clear();
            },

            validated: false,
        };
    }

    hasKey(key) {
        let exists = this.store.getItem(key);

        if (exists) {
            return true;
        }

        return false;
    }

    setItem(key, val) {
        this.store.setItem(key, val);
    }

    getItem(key) {
        return this.store.getItem(key);
    }

    hasItem(key) {
        return this.store.getItem(key) !== undefined;
    }

    removeItem(key) {
        return this.store.removeItem(key);
    }

    get authorized() {
        return this.store.hasItem("authorized")
            ? this.store.getItem("authorized")
            : false;
    }

    get modules() {
        return this.store.hasItem("modules")
            ? this.store.getItem("modules")
            : [];
    }

    get secured() {
        return this.store.hasItem("secured")
            ? this.store.getItem("secured")
            : false;
    }

    set validated(value) {
        this.store.validated = value;
    }

    clear() {
        return new Promise((resolve, reject) => {
            try {
                Object.keys(window.localStorage).forEach((key) => {
                    if (["information"].indexOf(key) === -1) {
                        window.localStorage.removeItem(key);
                    }
                });

                resolve();
            } catch (error) {
                reject(error);
            }
        });
    }
}
