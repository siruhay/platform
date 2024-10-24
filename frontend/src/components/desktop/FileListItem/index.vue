<template>
	<v-sheet
		class="d-flex mb-2 overflow-hidden border-thin"
		color="grey-lighten-4"
		rounded
	>
		<v-sheet
			class="d-flex flex-column"
			color="transparent"
			width="64"
		>
			<div
				class="text-subtitle-2 pt-1 text-center text-grey-darken-2 font-weight-medium"
				style="height: 28px"
			>
				{{ value.id }}
			</div>

			<v-divider></v-divider>

			<div class="d-flex">
				<div
					class="d-flex align-center justify-center flex-grow-1 py-1"
				>
					<v-icon
						:color="value.path ? 'green' : 'deep-orange'"
						size="x-small"
						>{{ value.path ? "image" : "hide_image" }}</v-icon
					>
				</div>

				<v-divider vertical></v-divider>

				<div
					class="d-flex align-center justify-center flex-grow-1 py-1"
				>
					<v-icon
						:color="value.source ? 'green' : 'deep-orange'"
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
			<div
				class="text-subtitle-2 pt-1 text-left text-truncate px-2 text-grey-darken-2 font-weight-medium"
				style="height: 28px"
			>
				{{ value.name }}
			</div>

			<v-divider></v-divider>

			<div
				class="text-caption text-left px-2 text-truncate text-grey-darken-1"
			>
				{{ value.file }}
			</div>
		</v-sheet>

		<v-divider vertical></v-divider>

		<v-sheet
			class="d-flex align-center justify-center"
			color="transparent"
			width="64"
		>
			<v-btn
				icon
				@click="openFilePreview"
			>
				<v-icon>folder_open</v-icon>
			</v-btn>
		</v-sheet>
	</v-sheet>
</template>

<script>
import { usePageStore } from "@pinia/pageStore";
import { storeToRefs } from "pinia";

export default {
	name: "file-list-item",

	props: {
		value: Object,
	},

	setup() {
		const store = usePageStore();

		const { currentFile, dialogFile } = storeToRefs(store);

		return {
			currentFile,
			dialogFile,
		};
	},

	methods: {
		openFilePreview: function () {
			this.dialogFile = true;
			this.currentFile = this.value;
		},
	},
};
</script>
