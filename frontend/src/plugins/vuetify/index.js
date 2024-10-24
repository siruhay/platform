import "vuetify/styles";

import { h } from "vue";
import { createVuetify } from "vuetify";
import { VLigatureIcon } from "vuetify/components";
import { aliases } from "vuetify/iconsets/md";
import { VDateInput } from "vuetify/labs/VDateInput";
import { VNumberInput } from "vuetify/labs/VNumberInput";

export default createVuetify({
    components: {
        VDateInput,
        VNumberInput,
    },

    icons: {
        defaultSet: "md",
        aliases,
        sets: {
            md: {
                component: (props) =>
                    h(VLigatureIcon, {
                        ...props,
                        class: "material-symbols-outlined",
                    }),
            },
        },
    },
});
