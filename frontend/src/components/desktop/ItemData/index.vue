<template>
    <tr>
        <td>
            <v-checkbox-btn
                :model-value="isSelected(internalItem)"
                color="blue-grey"
                @update:model-value="toggleSelect(internalItem)"
            ></v-checkbox-btn>
        </td>

        <slot
            :headers="headers"
            :item="item"
            :index="index"
            :internalItem="internalItem"
            :isSelected="isSelected"
            :toggleSelect="toggleSelect"
        >
            <td
                v-for="(column, colIndex) in headers"
                :key="colIndex"
                @click="toggleSelect(internalItem)"
            >
                {{ item[column.value] }}
            </td>
        </slot>
    </tr>
</template>

<script>
export default {
    name: "item-data",

    props: {
        headers: Array,
        item: Object,
        index: Number,
        internalItem: Object,
        isSelected: Function,
        toggleSelect: Function,
    },
};
</script>
