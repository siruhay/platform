<template>
    <v-navigation-drawer
        color="white"
        location="right"
        v-model="sidenavState"
        width="360"
        disable-resize-watcher
        style="height: calc(100dvh); z-index: 1009"
    >
        <template v-slot:prepend>
            <v-toolbar color="white" height="74">
                <v-toolbar-title class="text-overline">
                    {{ helpState ? "FILTER" : "INFORMASI" }}
                </v-toolbar-title>

                <v-spacer></v-spacer>

                <v-btn
                    icon
                    @click="
                        helpState = !helpState;
                        tabSidenav = helpState ? 'filter' : 'helpdesk';
                    "
                >
                    <v-icon
                        :style="
                            helpState
                                ? 'transform: rotate(180deg)'
                                : 'transform: rotate(0deg)'
                        "
                    >
                        {{ helpState ? "menu_open" : "filter_list" }}
                    </v-icon>
                </v-btn>
            </v-toolbar>

            <v-divider></v-divider>
        </template>

        <template v-slot:default>
            <v-sheet
                class="position-relative bg-transparent overflow-x-hidden overflow-y-auto scrollbar-none"
                height="calc(100dvh - 75px)"
                width="100%"
            >
                <v-sheet class="mx-auto" rounded="lg" flat>
                    <v-tabs-window
                        class="overflow-hidden rounded-lg"
                        v-model="tabSidenav"
                    >
                        <v-tabs-window-item value="helpdesk">
                            <v-card-text class="px-5">
                                <v-alert
                                    border="start"
                                    color="green"
                                    variant="tonal"
                                >
                                    <slot name="feed">
                                        <div class="text-caption text-justify">
                                            Form ini berfungsi untuk menampilkan
                                            semua data dalam tabel.
                                        </div>
                                    </slot>
                                </v-alert>

                                <template v-if="withSync">
                                    <div class="text-overline mt-6">Sync</div>
                                    <v-divider></v-divider>

                                    <v-card-text class="mt-3 pa-0">
                                        <slot
                                            name="sync"
                                            :parent="parent"
                                            :mapResponseData="mapResponseData"
                                        ></slot>
                                    </v-card-text>
                                </template>

                                <slot name="info"></slot>

                                <widget-icon title="Filter">
                                    <help-list-item
                                        :theme="theme"
                                        icon="toggle_off"
                                        title="Data Mode"
                                    >
                                        Filter ini berfungsi untuk beralih ke
                                        data trashed ON atau OFF, trashed
                                        berfungsi untuk menampilkan data yang
                                        telah di hapus sebelumnya.
                                    </help-list-item>

                                    <help-list-item
                                        :theme="theme"
                                        icon="text_fields"
                                        title="Pencarian"
                                    >
                                        Filter ini berfungsi untuk melakukan
                                        pencarian data berdasarkan input.
                                    </help-list-item>

                                    <slot name="filter"></slot>
                                </widget-icon>

                                <widget-icon>
                                    <help-list-item
                                        :theme="theme"
                                        icon="menu"
                                        title="Rail Mode"
                                    >
                                        icon ini berfungsi untuk beralih
                                        tampilan panel menu ke mode rail ON atau
                                        OFF.
                                    </help-list-item>

                                    <help-list-item
                                        :theme="theme"
                                        icon="filter_list"
                                        title="Filter"
                                    >
                                        icon ini berfungsi untuk membuka atau
                                        menutup panel filter, yang berisi
                                        pencarian atau filter data pada tabel.
                                    </help-list-item>

                                    <help-list-item
                                        :theme="theme"
                                        icon="tune"
                                        title="Informasi"
                                    >
                                        icon ini berfungsi untuk membuka atau
                                        menutup panel informasi, yang berisi
                                        petunjuk atas form.
                                    </help-list-item>

                                    <slot name="icon"></slot>
                                </widget-icon>
                            </v-card-text>
                        </v-tabs-window-item>

                        <v-tabs-window-item class="mt-2" value="filter">
                            <div
                                v-if="usetrash"
                                class="text-overline text-grey-darken-3 px-5 mt-1"
                            >
                                <small>mode data</small>
                            </div>

                            <v-card-text v-if="usetrash" class="px-5 pt-0 pb-2">
                                <div
                                    class="d-flex align-center justify-center bg-grey-lighten-4 rounded-lg py-1"
                                >
                                    <div
                                        class="caption flex-grow-1 text-center"
                                        :class="
                                            trashed
                                                ? 'font-weight-normal text-grey'
                                                : 'font-weight-medium'
                                        "
                                    >
                                        Default
                                    </div>
                                    <v-switch
                                        :disabled="hasSelected"
                                        density="compact"
                                        v-model="trashed"
                                        hide-details
                                        inset
                                    ></v-switch>
                                    <div
                                        class="caption flex-grow-1 text-center"
                                        :class="
                                            trashed
                                                ? 'font-weight-medium'
                                                : 'font-weight-normal text-grey'
                                        "
                                    >
                                        Trashed
                                    </div>
                                </div>
                            </v-card-text>

                            <v-divider v-if="usetrash" class="my-2"></v-divider>

                            <div
                                class="text-overline text-grey-darken-3 px-5 mt-1"
                            >
                                <small>pencarian data</small>
                            </div>

                            <v-card-text class="px-5 pt-0 pb-2">
                                <v-text-field
                                    :disabled="hasSelected"
                                    density="comfortable"
                                    placeholder="Cari Data"
                                    v-model="search"
                                    clearable
                                    hide-details
                                ></v-text-field>
                            </v-card-text>

                            <v-divider
                                class="my-2"
                                v-if="
                                    filters && Object.keys(filters).length > 0
                                "
                            ></v-divider>

                            <template
                                v-for="(filter, indexFilter) in filters"
                                :key="indexFilter"
                            >
                                <div
                                    class="d-flex text-overline text-grey-darken-3 px-5 align-center mt-2"
                                >
                                    <small
                                        class="text-grey-darken-3 cursor-default"
                                        >filter {{ filter.title }}</small
                                    >

                                    <v-spacer></v-spacer>

                                    <v-menu :disabled="hasSelected">
                                        <template v-slot:activator="{ props }">
                                            <span
                                                :class="
                                                    hasSelected
                                                        ? 'text-grey-lighten-2'
                                                        : 'text-blue'
                                                "
                                                class="cursor-pointer mr-2"
                                                v-bind="props"
                                                >({{ filter.operator }})</span
                                            >
                                        </template>

                                        <v-list
                                            density="compact"
                                            v-model:selected="filter.operator"
                                            selectable
                                        >
                                            <v-list-item
                                                v-for="(
                                                    operator, index
                                                ) in filter.operators"
                                                :key="index"
                                                :title="operator"
                                                :value="operator"
                                                density="compact"
                                            >
                                            </v-list-item>
                                        </v-list>
                                    </v-menu>

                                    <span class="mr-2 text-grey-lighten-2"
                                        >|</span
                                    >

                                    <v-btn
                                        class="text-white"
                                        :color="
                                            filter.used
                                                ? 'orange'
                                                : 'grey-lighten-2'
                                        "
                                        :disabled="
                                            !(
                                                filter.value &&
                                                filter.value !== ''
                                            )
                                        "
                                        density="compact"
                                        size="small"
                                        icon="cancel"
                                        flat
                                        @click="
                                            () => {
                                                filter.value = null;
                                                filter.used = false;
                                            }
                                        "
                                    ></v-btn>
                                </div>

                                <v-card-text class="px-5 pt-1 pb-2">
                                    <v-number-input
                                        v-if="filter.type === 'NumberInput'"
                                        :disabled="hasSelected"
                                        density="comfortable"
                                        control-variant="default"
                                        v-model="filter.value"
                                        @update:modelValue="
                                            () =>
                                                (filter.used =
                                                    filter.value &&
                                                    filter.value !== '')
                                        "
                                        hide-details
                                        inset
                                    ></v-number-input>

                                    <v-date-input
                                        v-if="filter.type === 'DateInput'"
                                        :disabled="hasSelected"
                                        density="comfortable"
                                        prepend-icon=""
                                        v-model="filter.value"
                                        @update:modelValue="
                                            () =>
                                                (filter.used =
                                                    filter.value &&
                                                    filter.value !== '')
                                        "
                                        hide-details
                                        readonly
                                    ></v-date-input>

                                    <v-select
                                        v-if="filter.type === 'Select'"
                                        :disabled="hasSelected"
                                        :items="filter.data"
                                        density="comfortable"
                                        v-model="filter.value"
                                        hide-details
                                        @update:modelValue="
                                            () =>
                                                (filter.used =
                                                    filter.value &&
                                                    filter.value !== '')
                                        "
                                    ></v-select>
                                </v-card-text>
                            </template>

                            <v-card-text class="flex-grow-1 bg-white">
                                <v-btn
                                    :color="theme"
                                    :disabled="hasSelected"
                                    rounded="pill"
                                    block
                                    flat
                                    @click="applyFilterData"
                                    >terapkan filter</v-btn
                                >
                            </v-card-text>
                        </v-tabs-window-item>
                    </v-tabs-window>
                </v-sheet>
            </v-sheet>
        </template>
    </v-navigation-drawer>
</template>

<script>
import { usePageStore } from "@pinia/pageStore";
import { storeToRefs } from "pinia";

export default {
    name: "page-filter",

    props: {
        withSync: Boolean,
    },

    setup() {
        const store = usePageStore();

        const {
            filters,
            hasSelected,
            helpState,
            page,
            params,
            parent,
            sidenavState,
            search,
            trashed,
            theme,
            usetrash,
        } = storeToRefs(store);

        const { getPageDatas, mapResponseData } = store;

        return {
            filters,
            hasSelected,
            helpState,
            page,
            params,
            parent,
            sidenavState,
            search,
            trashed,
            theme,
            usetrash,

            getPageDatas,
            mapResponseData,
        };
    },

    data: () => ({
        tabSidenav: "helpdesk",
    }),

    methods: {
        applyFilterData: function () {
            this.params.page = 1;

            this.getPageDatas(this.params);
        },
    },
};
</script>
