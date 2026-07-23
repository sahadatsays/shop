export const registerCategoryForm = (Alpine) => {
    Alpine.data('categoryForm', (config = {}) => ({
        name: config.name ?? '',
        slug: config.slug ?? '',
        autoSlug: config.autoSlug ?? !config.slug,
        imagePreview: config.imageUrl ?? null,
        bannerPreview: config.bannerUrl ?? null,

        init() {
            if (this.name && this.autoSlug) {
                this.syncSlug();
            }
        },

        onNameInput() {
            if (this.autoSlug) {
                this.syncSlug();
            }
        },

        syncSlug() {
            this.slug = this.name
                .toLowerCase()
                .trim()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-');
        },

        onSlugInput() {
            this.autoSlug = false;
        },

        previewFile(event, target) {
            const file = event.target.files?.[0];

            if (!file) {
                return;
            }

            const reader = new FileReader();
            reader.onload = (e) => {
                this[target] = e.target?.result ?? null;
            };
            reader.readAsDataURL(file);
        },
    }));
};
