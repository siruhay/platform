<template>
	<v-sheet
		class="position-relative"
		:height="height"
	>
		<v-overlay :value="!FILELOADED">
			<v-progress-circular
				indeterminate
				size="64"
			></v-progress-circular>
		</v-overlay>

		<v-responsive
			:id="container"
			:height="height"
			:width="width"
			class="page-wrapper position-absolute overflow-auto bg-grey-lighten-2"
		></v-responsive>
	</v-sheet>
</template>

<script>
import debounce from "debounce";
import { usePageStore } from "@pinia/pageStore";
import { storeToRefs } from "pinia";

import * as pdfjsLib from "pdfjs-dist/build/pdf.min.mjs";
import * as pdfjsViewer from "pdfjs-dist/web/pdf_viewer.mjs";

const MAX_CANVAS_PIXELS = 0;
const TEXT_LAYER_MODE = 0;
// const MAX_IMAGE_SIZE = 1024 * 1024;
const CMAP_URL = "/pdfjs/cmaps/";
const CMAP_PACKED = true;

const DEFAULT_SCALE_DELTA = 1.1;
const MIN_SCALE = 0.25;
const MAX_SCALE = 5.0;
const DEFAULT_SCALE_VALUE = "auto";

pdfjsLib.GlobalWorkerOptions.workerSrc = "/pdfjs/pdf.worker.min.mjs";

export default {
	name: "widget-pdf",

	props: {
		container: {
			type: String,
			default: "viewerContainer",
		},

		datapath: String,

		height: {
			type: String,
			default: "calc(100dvh - 64px)",
		},

		width: {
			type: String,
			default: "100%",
		},

		value: null,
	},

	setup() {
		const store = usePageStore();
		const pdfLoadingTask = null;
		const pdfDocument = null;
		const pdfViewer = null;
		const pdfHistory = null;
		const linkService = null;
		const eventBus = null;
		const l10n = null;
		const progress = 0;

		let FILELOADED = false;
		let pageNumber = 0;
		let pageCount = 0;

		const { currentFile } = storeToRefs(store);

		return {
			FILELOADED,
			pageNumber,
			pageCount,

			currentFile,
			pdfLoadingTask,
			pdfDocument,
			pdfViewer,
			pdfHistory,
			linkService,
			eventBus,
			l10n,
			progress,
		};
	},

	mounted() {
		this.init();
	},

	unmounted() {
		if (this.pdfLoadingTask) {
			return this.close();
		}
	},

	methods: {
		init() {
			this.eventBus = new pdfjsViewer.EventBus();

			this.linkService = new pdfjsViewer.PDFLinkService({
				eventBus: this.eventBus,
			});

			this.l10n = new pdfjsViewer.GenericL10n();

			this.pdfViewer = new pdfjsViewer.PDFViewer({
				container: document.getElementById(this.container),
				eventBus: this.eventBus,
				linkService: this.linkService,
				l10n: this.l10n,
				maxCanvasPixels: MAX_CANVAS_PIXELS,
				textLayerMode: TEXT_LAYER_MODE,
			});

			this.linkService.setViewer(this.pdfViewer);

			this.pdfHistory = new pdfjsViewer.PDFHistory({
				eventBus: this.eventBus,
				linkService: this.linkService,
			});

			this.linkService.setHistory(this.pdfHistory);

			this.eventBus.on("pagesinit", () => {
				this.pdfViewer.currentScaleValue = DEFAULT_SCALE_VALUE;
			});

			this.eventBus.on(
				"pagechanging",
				(evt) => {
					this.pageNumber = evt.pageNumber;
					this.pageCount = this.pagesCount();
				},
				true
			);
		},

		open(params) {
			if (this.pdfLoadingTask) {
				return this.close().then(
					function () {
						return this.open(params);
					}.bind(this)
				);
			}

			const parseURL = new URL(this.datapath);

			parseURL.searchParams.append("path", params.path ?? params.source);

			parseURL.searchParams.append(
				"disk",
				params.path ? "siruhay" : "siasn"
			);

			this.pdfLoadingTask = pdfjsLib.getDocument({
				url: parseURL.href,
				// maxImageSize: MAX_IMAGE_SIZE,
				cMapUrl: CMAP_URL,
				cMapPacked: CMAP_PACKED,
				enableWebGL: true,
				withCredentials: true,
				verbosity: 0,
				httpHeaders: {
					Authorization: "Bearer " + this.$storage.getItem("token"),
					"X-Requested-With": "XMLHttpRequest",
				},
			});

			this.pdfLoadingTask.onProgress = (progressData) => {
				const percent = Math.round(
					(progressData.loaded / progressData.total) * 100
				);

				if (percent > 100 || isNaN(percent)) {
					this.progress = 100;
				}
			};

			return this.pdfLoadingTask.promise
				.then((pdfDocument) => {
					this.pdfDocument = pdfDocument;
					this.pdfViewer.setDocument(pdfDocument);
					this.linkService.setDocument(pdfDocument);
					this.pdfHistory.initialize({
						fingerprint: pdfDocument.fingerprints[0],
					});

					this.FILELOADED = true;
				})
				.catch((error) => {
					this.FILELOADED = true;

					if (error.status === 413) {
						this.$emit("fileNotExists", null);
					} else {
						this.$emit("error", "Jenis file tidak sesuai.");
					}
				});
		},

		close() {
			if (!this.pdfLoadingTask) {
				return Promise.resolve();
			}

			const promise = this.pdfLoadingTask.destroy();

			this.pdfLoadingTask = null;

			if (this.pdfDocument) {
				this.pdfDocument = null;
			}

			if (this.pdfViewer) {
				this.pdfViewer.setDocument(null);
			}

			if (this.linkService) {
				this.linkService.setDocument(null, null);
			}

			if (this.pdfHistory) {
				this.pdfHistory.reset();
			}

			return promise;
		},

		pagesCount() {
			return this.pdfDocument.numPages;
		},

		page() {
			return this.pdfViewer.currentPageNumber;
		},

		rotateLeft: debounce(function () {
			this.pdfViewer.pagesRotation =
				parseInt(this.pdfViewer.pagesRotation) - 90;
		}, 150),

		rotateRight: debounce(function () {
			this.pdfViewer.pagesRotation =
				parseInt(this.pdfViewer.pagesRotation) + 90;
		}, 150),

		zoomIn: debounce(function () {
			let newScale = this.pdfViewer.currentScale;

			newScale = (newScale * DEFAULT_SCALE_DELTA).toFixed(2);
			newScale = Math.ceil(newScale * 10) / 10;
			newScale = Math.min(MAX_SCALE, newScale);

			if (newScale < MAX_SCALE) {
				this.pdfViewer.currentScaleValue = newScale;
			}
		}, 150),

		zoomOut: debounce(function () {
			let newScale = this.pdfViewer.currentScale;

			newScale = (newScale / DEFAULT_SCALE_DELTA).toFixed(2);
			newScale = Math.floor(newScale * 10) / 10;
			newScale = Math.max(MIN_SCALE, newScale);

			if (newScale > MIN_SCALE) {
				this.pdfViewer.currentScaleValue = newScale;
			}
		}, 150),

		error: function pdfViewError(message, moreInfo) {
			const moreInfoText = [
				`PDF.js v${pdfjsLib.version || "?"} (build: ${
					pdfjsLib.build || "?"
				})`,
			];
			if (moreInfo) {
				moreInfoText.push(`Message: ${moreInfo.message}`);

				if (moreInfo.stack) {
					moreInfoText.push(`Stack: ${moreInfo.stack}`);
				} else {
					if (moreInfo.filename) {
						moreInfoText.push(`File: ${moreInfo.filename}`);
					}
					if (moreInfo.lineNumber) {
						moreInfoText.push(`Line: ${moreInfo.lineNumber}`);
					}
				}
			}

			console.error(`${message}\n\n${moreInfoText.join("\n")}`);
		},
	},

	watch: {
		currentFile: {
			handler: function (params) {
				if (Object.keys(params).length === 0) {
					if (this.pdfLoadingTask) {
						this.close();
					}

					return;
				}

				if (!this.datapath.includes("undefined")) {
					this.open(params);
				}
			},

			deep: true,
			immediate: true,
		},
	},
};
</script>

<style>
.page-wrapper .page {
	margin: 0 auto;
	padding: 0 1px;
}

.page-wrapper .page > .canvasWrapper canvas {
	width: 100%;
}
</style>
