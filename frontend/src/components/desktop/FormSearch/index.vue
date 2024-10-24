<template>
    <v-dialog
        transition="dialog-bottom-transition"
        v-model="dialogStatus"
        fullscreen
    >
        <template v-slot:activator="{ isActive, props, targetRef }">
            <slot
                name="activator"
                :isActive="isActive"
                :props="props"
                :targetRef="targetRef"
            ></slot>
        </template>

        <template v-slot:default="{ isActive }">
            <v-sheet>
                <v-toolbar :color="theme" extension-height="56" extended>
                    <v-btn icon @click="isActive.value = false">
                        <v-icon>arrow_back</v-icon>
                    </v-btn>

                    <v-toolbar-title
                        class="text-body-2 font-weight-bold text-uppercase"
                        >Search</v-toolbar-title
                    >

                    <template v-slot:extension>
                        <v-card-text class="pt-0">
                            <v-text-field
                                density="compact"
                                prepend-inner-icon="search"
                                rounded="pill"
                                variant="solo"
                                @update:modelValue="onSearchData"
                                clearable
                                hide-details
                            ></v-text-field>
                        </v-card-text>
                    </template>
                </v-toolbar>

                <v-sheet height="calc(100dvh - 120px)">
                    <template v-if="records.length <= 0">
                        <div
                            class="d-flex align-center justify-center text-body-2 text-center text-grey"
                            style="height: calc(100dvh - 120px)"
                        >
                            Data tidak ditemukan
                        </div>
                    </template>

                    <v-list
                        v-else
                        :lines="lines"
                        class="bg-transparent overflow-y-auto overflow-x-hidden scrollbar-none"
                        height="calc(100dvh - 120px)"
                        select-strategy="single-leaf"
                        selectable
                        @update:selected="setSelected"
                    >
                        <template v-for="(record, index) in records">
                            <slot
                                :index="index"
                                :record="record"
                                :theme="theme"
                            ></slot>
                        </template>
                    </v-list>
                </v-sheet>
            </v-sheet>
        </template>
    </v-dialog>
</template>

<script>
import debounce from "debounce";

export default {
    name: "form-search",

    props: {
        dataUrl: String,
        dataParams: Object,
        lines: {
            type: String,
            default: "two",
        },
        theme: String,
    },

    emits: {
        "data:selected": null,
    },

    data: () => ({
        dialogStatus: false,
        records: [],
    }),

    methods: {
        onSearchData: debounce(function (val) {
            this.$http(this.dataUrl, {
                method: "GET",
                params: { ...this.dataParams, findOn: val },
            }).then((results) => (this.records = results));
        }, 500),

        setSelected: function (selected) {
            this.$emit("data:selected", selected[0]);
            this.dialogStatus = false;
        },
    },

    watch: {
        dialogStatus: function (status) {
            if (!status) {
                this.records = [];
            }
        },
    },
};
</script>
