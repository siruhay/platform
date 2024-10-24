import axios from "axios";
import { usePageStore } from "@pinia/pageStore";

export function RequestInstance(url, options) {
    const store = usePageStore();

    /**
     * Save baseURL to localStorage
     */
    if (localStorage.getItem("baseURL") === null) {
        if (import.meta.env.VITE_APP_BACKEND) {
            localStorage.setItem(
                "baseURL",
                `${
                    import.meta.env.VITE_APP_PROTOCOL +
                    "://" +
                    import.meta.env.VITE_APP_BACKEND +
                    import.meta.env.VITE_APP_DOMAIN
                }`
            );
        } else {
            localStorage.setItem(
                "baseURL",
                `${
                    import.meta.env.VITE_APP_PROTOCOL +
                    "://" +
                    import.meta.env.VITE_APP_DOMAIN
                }`
            );
        }
    }

    let baseURL = null;

    if ("module" in store && "domain" in store.module) {
        baseURL = `${
            import.meta.env.VITE_APP_PROTOCOL +
            "://" +
            store.module.domain +
            import.meta.env.VITE_APP_DOMAIN
        }`;
    } else {
        baseURL = localStorage.getItem("baseURL");
    }

    /**
     * Override defaultOptions
     */
    const defaultOptions = Object.assign(
        {
            baseURL: baseURL,
            contentType: null,
            headers: {
                Accept: "application/json",
                "Content-Type": "application/json",
                "X-Requested-With": "XMLHttpRequest",
            },
            method: "GET",
            onUploadProgress: null,
            params: null,
            responseType: "json",
        },
        options
    );

    /**
     * coords injected
     */
    if (["POST", "PUT", "DELETE"].includes(defaultOptions.method)) {
        defaultOptions.params = {
            ...defaultOptions.params,
            coords: store.geoCoords,
        };
    }

    /**
     * check defaultOptions contentType
     */
    if (defaultOptions.contentType) {
        defaultOptions.headers["Content-Type"] = defaultOptions.contentType;
    }

    /**
     * check for method === POST or responseType === blob
     */
    if (
        defaultOptions.method === "POST" &&
        defaultOptions.responseType === "blob"
    ) {
        defaultOptions.headers["Content-Type"] = "multipart/form-data";
    }

    /**
     * check if token exists
     */
    axios.defaults.withCredentials = true;
    axios.defaults.withXSRFToken = true;

    if (store.$storage.getItem("token") !== null) {
        defaultOptions.headers["Authorization"] =
            "Bearer " + store.$storage.getItem("token");
    }

    /**
     * construct the request
     */
    let request = null;
    let htmlTag = document.getElementsByTagName("html");

    store.overlay = true;
    htmlTag[0].style.overflowY = "hidden";

    switch (defaultOptions.method) {
        case "DELETE":
            request = axios.delete(url, {
                baseURL: defaultOptions.baseURL,
                headers: defaultOptions.headers,
                data: defaultOptions.params,
                responseType: defaultOptions.responseType,
            });

            break;

        case "POST":
            request = axios.post(url, defaultOptions.params, {
                baseURL: defaultOptions.baseURL,
                headers: defaultOptions.headers,
                responseType: defaultOptions.responseType,

                onUploadProgress: defaultOptions.onUploadProgress,
            });

            break;

        case "PUT":
            request = axios.put(url, defaultOptions.params, {
                baseURL: defaultOptions.baseURL,
                headers: defaultOptions.headers,
                responseType: defaultOptions.responseType,
            });

            break;

        default:
            request = axios.get(url, {
                baseURL: defaultOptions.baseURL,
                headers: defaultOptions.headers,
                params: defaultOptions.params,
                responseType: defaultOptions.responseType,
            });

            break;
    }

    return request
        .then((response) => {
            store.overlay = false;
            htmlTag[0].style.overflowY = "scroll";

            let { status, message } = response.data;

            if (status === true && message) {
                store.snackbar.color = "green";
                store.snackbar.text = message;
                store.snackbar.state = true;
            }

            return response.data;
        })
        .catch((error) => {
            store.overlay = false;
            htmlTag[0].style.overflowY = "scroll";

            let status = error.response ? error.response.status : error.status;

            if (status === 401) {
                Object.keys(localStorage).forEach((key) => {
                    localStorage.removeItem(key);
                });

                window.location.replace("/");

                return;
            }

            if (error.response) {
                store.snackbar.color = "deep-orange";
                store.snackbar.text = error.response.data.message;
                store.snackbar.state = true;

                throw {
                    status: error.response.status,
                    message: error.response.data.message,
                };
            } else {
                error = error.toJSON();

                store.snackbar.color = "deep-orange";
                store.snackbar.text = error.message;
                store.snackbar.state = true;

                throw {
                    status: error.status,
                    message: error.message,
                };
            }
        });
}
