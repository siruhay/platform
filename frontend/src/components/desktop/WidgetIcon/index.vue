<template>
    <div class="text-overline mt-6">{{ title }}</div>
    <v-divider></v-divider>

    <help-list>
        <slot>
            <help-list-item :theme="theme" icon="arrow_back" title="Kembali">
                icon ini berfungsi untuk kembali ke form data, saat anda selesai
                dengan form ini.
            </help-list-item>

            <help-list-item
                v-if="['create', 'edit'].includes(mode)"
                :theme="theme"
                icon="save"
                title="Simpan"
                subtitle="icon ini berfungsi untuk menyimpan data baru atau
                        menyimpan perubahan data pada form."
            ></help-list-item>

            <help-list-item
                v-if="mode === 'show'"
                :theme="theme"
                icon="edit"
                title="Ubah"
                subtitle="icon ini berfungsi untuk membuka form edit, saat anda
                        akan mengubah data."
            ></help-list-item>

            <help-list-item
                v-if="mode === 'show'"
                :theme="theme"
                icon="delete"
                title="Hapus"
                subtitle="icon ini berfungsi untuk membuka dialog konfirmasi,
                        saat anda akan menghapus data."
            ></help-list-item>

            <help-list-item
                :theme="theme"
                icon="menu_open"
                title="Informasi"
                subtitle="icon ini berfungsi untuk membuka atau menutup panel
                        informasi, yang berisi petunjuk atas form."
            ></help-list-item>
        </slot>
    </help-list>
</template>

<script>
import { usePageStore } from "@pinia/pageStore";
import { storeToRefs } from "pinia";

export default {
    name: "widget-icon",

    props: {
        title: {
            type: String,
            default: "Icon",
        },

        mode: {
            type: String,
            default: "show",
        },
    },

    setup() {
        const store = usePageStore();

        const { theme } = storeToRefs(store);

        return {
            theme,
        };
    },
};
</script>
