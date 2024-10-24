<template>
    <v-text-field
        :prefix="prefix"
        :hide-details="hideDetails"
        :readonly="readonly"
        v-model="formattedValue"
        ref="inputRef"
    ></v-text-field>
</template>

<script>
import { useCurrencyInput } from "vue-currency-input";

export default {
    name: "v-currency-field",

    props: {
        prefix: String,
        hideDetails: Boolean,
        minRange: [Number, String],
        maxRange: [Number, String],
        modelValue: Number,
        precision: [Number, String],
        readonly: Boolean,
    },

    setup(props) {
        let valueRange = { min: 0 };
        let precision = 0;

        if (props.minRange) {
            valueRange["min"] = parseInt(props.minRange);
        }

        if (props.maxRange) {
            valueRange["max"] = parseInt(props.maxRange);
        }

        if (props.precision) {
            precision = parseInt(props.precision);
        }

        const { inputRef, formattedValue, numberValue, setValue } =
            useCurrencyInput({
                currency: "IDR",
                currencyDisplay: "hidden",
                locale: "id",
                hideGroupingSeparatorOnFocus: false,
                precision: precision,
                valueRange: valueRange,
            });

        return {
            inputRef,
            formattedValue,
            numberValue,
            setValue,
        };
    },

    watch: {
        modelValue: function (value) {
            this.setValue(value);
        },
    },
};
</script>
