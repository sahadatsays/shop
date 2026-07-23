import { copyTextToClipboard } from './utils/clipboard';
import { extractErrorMessage, readJsonResponse } from './utils/fetch-error';

const MAX_UPLOAD_BYTES = 10 * 1024 * 1024;
const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg', 'pdf'];

export function registerMediaLibrary(Alpine) {
    Alpine.data('mediaLibrary', (config = {}) => ({
        dragging: false,
        uploading: false,

        validateFiles(fileList) {
            const errors = [];

            Array.from(fileList).forEach((file) => {
                const extension = file.name.split('.').pop()?.toLowerCase() ?? '';

                if (file.size > MAX_UPLOAD_BYTES) {
                    errors.push(`${file.name} is too large. Each file must be 10 MB or smaller.`);
                }

                if (! ALLOWED_EXTENSIONS.includes(extension)) {
                    errors.push(`${file.name} is not supported. Use JPG, PNG, WebP, GIF, SVG, or PDF.`);
                }
            });

            return errors;
        },

        notifyUploadFailure(message) {
            window.adminToast?.push({
                title: 'Upload failed',
                message,
                type: 'danger',
            });
        },

        handleDragOver(event) {
            event.preventDefault();
            this.dragging = true;
        },

        handleDragLeave() {
            this.dragging = false;
        },

        handleDrop(event) {
            event.preventDefault();
            this.dragging = false;
            this.uploadFiles(event.dataTransfer.files);
        },

        handleFileSelect(event) {
            this.uploadFiles(event.target.files);
            event.target.value = '';
        },

        async uploadFiles(fileList) {
            if (! fileList?.length) {
                return;
            }

            const validationErrors = this.validateFiles(fileList);

            if (validationErrors.length) {
                this.notifyUploadFailure(validationErrors.join(' '));

                return;
            }

            const formData = new FormData();
            Array.from(fileList).forEach((file) => formData.append('files[]', file));

            if (config.folderId) {
                formData.append('folder_id', config.folderId);
            }

            this.uploading = true;

            try {
                const response = await fetch(config.uploadUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: formData,
                });

                const payload = await readJsonResponse(response);

                if (! response.ok || ! payload) {
                    this.notifyUploadFailure(extractErrorMessage(
                        payload,
                        response.ok
                            ? 'Upload failed. The server returned an unexpected response.'
                            : `Upload failed (${response.status}). Please try again.`,
                    ));

                    return;
                }

                window.adminToast?.push({
                    title: 'Upload complete',
                    message: payload?.message ?? `${fileList.length} file(s) uploaded successfully.`,
                    type: 'success',
                });
                window.location.reload();
            } catch (error) {
                this.notifyUploadFailure(
                    error instanceof Error && error.message
                        ? error.message
                        : 'Network error. Check your connection and try again.',
                );
            } finally {
                this.uploading = false;
            }
        },

        async copyUrl(url) {
            await copyTextToClipboard(url);
            window.adminToast?.push({ title: 'URL copied to clipboard', type: 'success' });
        },
    }));

    Alpine.data('mediaCropper', (config = {}) => ({
        scale: 1,
        crop: { x: 40, y: 40, width: 200, height: 200 },
        dragging: false,
        resizing: false,
        startX: 0,
        startY: 0,

        init() {
            this.$nextTick(() => this.updateScale());
            window.addEventListener('resize', () => this.updateScale());
        },

        updateScale() {
            const image = this.$refs.image;
            if (! image?.naturalWidth) {
                return;
            }

            this.scale = image.clientWidth / image.naturalWidth;
        },

        onImageLoad() {
            this.updateScale();
            const image = this.$refs.image;
            const width = Math.min(image.clientWidth * 0.6, 320);
            const height = width * 0.75;
            this.crop = {
                x: (image.clientWidth - width) / 2,
                y: (image.clientHeight - height) / 2,
                width,
                height,
            };
        },

        startDrag(event) {
            this.dragging = true;
            this.startX = event.clientX - this.crop.x;
            this.startY = event.clientY - this.crop.y;
        },

        drag(event) {
            if (! this.dragging) {
                return;
            }

            const bounds = this.$refs.stage.getBoundingClientRect();
            const maxX = bounds.width - this.crop.width;
            const maxY = bounds.height - this.crop.height;

            this.crop.x = Math.max(0, Math.min(event.clientX - this.startX - bounds.left, maxX));
            this.crop.y = Math.max(0, Math.min(event.clientY - this.startY - bounds.top, maxY));
        },

        endDrag() {
            this.dragging = false;
            this.resizing = false;
        },

        startResize(event) {
            event.stopPropagation();
            this.resizing = true;
            this.startX = event.clientX;
            this.startY = event.clientY;
        },

        resize(event) {
            if (! this.resizing) {
                return;
            }

            const bounds = this.$refs.stage.getBoundingClientRect();
            const deltaX = event.clientX - this.startX;
            const deltaY = event.clientY - this.startY;
            this.startX = event.clientX;
            this.startY = event.clientY;

            this.crop.width = Math.max(40, Math.min(this.crop.width + deltaX, bounds.width - this.crop.x));
            this.crop.height = Math.max(40, Math.min(this.crop.height + deltaY, bounds.height - this.crop.y));
        },
    }));
}
