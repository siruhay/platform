<template>
    <v-sheet
        class="d-flex mb-2 overflow-hidden border-thin"
        color="grey-lighten-4"
        rounded
    >
        <v-sheet class="d-flex flex-column" color="transparent" width="64">
            <div
                class="d-flex align-center justify-center"
                style="height: 33px"
            >
                <div
                    class="text-subtitle-1 text-center text-grey-darken-2 font-weight-bold"
                >
                    {{ value.id }}
                </div>
            </div>

            <v-divider></v-divider>

            <div class="d-flex">
                <div
                    class="d-flex flex-column align-center justify-center flex-grow-1 py-1"
                >
                    <div
                        class="text-caption text-grey-darken-1"
                        style="font-size: 9px !important; line-height: 1"
                    >
                        local
                    </div>
                    <v-icon
                        :color="value.path ? 'blue' : 'deep-orange'"
                        size="x-small"
                        >{{ value.path ? "folder_zip" : "folder_off" }}</v-icon
                    >
                </div>

                <v-divider vertical></v-divider>

                <div
                    class="d-flex flex-column align-center justify-center flex-grow-1 py-1"
                >
                    <div
                        class="text-caption text-grey-darken-1"
                        style="font-size: 9px !important; line-height: 1"
                    >
                        cloud
                    </div>
                    <v-icon
                        :color="value.source ? 'blue' : 'deep-orange'"
                        size="x-small"
                        >{{ value.source ? "cloud_done" : "cloud_off" }}</v-icon
                    >
                </div>
            </div>
        </v-sheet>

        <v-divider vertical></v-divider>

        <v-sheet
            class="d-flex flex-column flex-grow-1"
            color="transparent"
            width="64"
        >
            <div class="d-flex align-center" style="height: 33px">
                <div
                    class="text-subtitle-1 text-left text-truncate px-2 text-grey-darken-2 font-weight-medium"
                >
                    {{ value.name }}
                </div>
            </div>

            <v-divider></v-divider>

            <div class="d-flex align-center" style="height: 30px">
                <div
                    class="text-caption text-left px-2 text-truncate text-grey-darken-1"
                >
                    {{ value.file }}
                </div>
            </div>
        </v-sheet>

        <template v-if="value.path || value.source || !readonly">
            <v-divider vertical></v-divider>

            <v-sheet class="d-flex flex-column" color="transparent" width="64">
                <div
                    v-if="(value.path || value.source) && readonly"
                    class="d-flex align-center justify-center h-100"
                >
                    <v-btn
                        rounded="lg"
                        variant="text"
                        icon
                        @click="openFilePreview"
                    >
                        <v-icon>folder_open</v-icon>
                    </v-btn>
                </div>

                <div
                    v-else-if="!value.path && !value.source && !readonly"
                    class="d-flex align-center justify-center h-100"
                >
                    <v-btn
                        rounded="lg"
                        variant="text"
                        icon
                        @click="dialogUpload = true"
                    >
                        <v-icon>file_upload</v-icon>
                    </v-btn>
                </div>

                <template v-else>
                    <div
                        class="d-flex align-center justify-center"
                        style="height: 33px"
                    >
                        <v-icon @click="openFilePreview">folder_open</v-icon>
                    </div>

                    <v-divider></v-divider>

                    <div class="d-flex" style="height: 30px">
                        <div
                            class="d-flex flex-column align-center justify-center flex-grow-1 py-1"
                        >
                            <v-icon
                                :disabled="!!value.path && !!value.source"
                                color="blue"
                                size="small"
                                @click="dialogSync = true"
                            >
                                {{
                                    !!value.path && !!value.source
                                        ? "sync_disabled"
                                        : "sync"
                                }}
                            </v-icon>
                        </div>

                        <v-divider vertical></v-divider>

                        <div
                            class="d-flex flex-column align-center justify-center flex-grow-1 py-1"
                        >
                            <v-icon
                                color="blue"
                                size="small"
                                @click="dialogUpload = true"
                                >file_upload</v-icon
                            >
                        </div>
                    </div>
                </template>
            </v-sheet>

            <v-dialog v-model="dialogSync" max-width="340">
                <v-sheet elevation="9" rounded="lg">
                    <v-card-text class="text-center">
                        <v-icon class="v-icon--size-dialog text-blue-grey"
                            >cloud_sync</v-icon
                        >

                        <div class="text-subtitle-1 font-weight-medium my-1">
                            Sync Ulang Dokumen
                        </div>

                        <div
                            class="text-caption text-grey-darken-1"
                            style="line-height: 1.15rem"
                        >
                            Proses sync dokumen akan mengambil data dari server
                            SiASN dan menyimpan dokumen ke server SiMASTEN.
                        </div>
                    </v-card-text>

                    <v-card-text class="pt-0">
                        <v-row dense>
                            <v-col cols="6">
                                <v-btn
                                    :color="theme"
                                    rounded="pill"
                                    variant="outlined"
                                    block
                                    @click="dialogSync = false"
                                    >BATAL</v-btn
                                >
                            </v-col>

                            <v-col cols="6">
                                <v-btn
                                    :color="theme"
                                    rounded="pill"
                                    variant="flat"
                                    block
                                    @click="
                                        dialogSync = false;
                                        $emit('click:sync', $event);
                                    "
                                    >SYNC</v-btn
                                >
                            </v-col>
                        </v-row>
                    </v-card-text>
                </v-sheet>
            </v-dialog>

            <v-dialog v-model="dialogUpload" max-width="340">
                <v-sheet>
                    <v-card-text>
                        <v-row dense>
                            <v-col cols="12">
                                <div class="d-flex align-center justify-center">
                                    <v-avatar color="blue" size="56">
                                        <v-file-input
                                            accept="application/pdf"
                                            label="File"
                                            prepend-icon="file_upload"
                                            v-model="fileupload"
                                            hide-input
                                        ></v-file-input>
                                    </v-avatar>
                                </div>
                            </v-col>

                            <v-col cols="12">
                                <div
                                    class="bg-grey-lighten-2 rounded py-2 px-3 text-truncate mt-2"
                                >
                                    {{
                                        fileupload
                                            ? fileupload.name
                                            : "Belum ada file yang di pilih."
                                    }}
                                </div>
                            </v-col>
                        </v-row>
                    </v-card-text>

                    <v-card-text class="d-flex pt-0 px-2 pb-2">
                        <v-spacer></v-spacer>

                        <v-btn
                            variant="text"
                            @click="
                                dialogUpload = false;
                                fileupload = null;
                            "
                            >BATAL</v-btn
                        >

                        <v-btn
                            :disabled="!fileupload"
                            variant="text"
                            @click="
                                dialogUpload = false;
                                $emit('click:upload', fileupload);
                                fileupload = null;
                            "
                            >UPLOAD</v-btn
                        >
                    </v-card-text>
                </v-sheet>
            </v-dialog>
        </template>
    </v-sheet>
</template>

<script>
import { usePageStore } from "@pinia/pageStore";
import { storeToRefs } from "pinia";

export default {
    name: "file-list-item",

    props: {
        readonly: Boolean,
        value: Object,
    },

    emits: {
        "click:sync": null,
        "click:upload": null,
    },

    setup() {
        const store = usePageStore();

        const { currentFile, dialogFile, theme } = storeToRefs(store);

        return {
            currentFile,
            dialogFile,
            theme,
        };
    },

    data: () => ({
        dialogSync: false,
        dialogUpload: false,
        fileupload: null,
    }),

    methods: {
        openFilePreview: function () {
            this.dialogFile = true;
            this.currentFile = this.value;
        },
    },
};
</script>
