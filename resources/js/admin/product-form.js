export const registerProductForm = (Alpine) => {
    Alpine.data('productForm', (config = {}) => ({
        name: config.name ?? '',
        slug: config.slug ?? '',
        autoSlug: config.autoSlug ?? !config.slug,
        specifications: config.specifications ?? [{ name: '', value: '' }],
        attributes: config.attributes ?? [{ name: '', value: '' }],
        existingImages: config.existingImages ?? [],
        removeImages: [],

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

        addSpecification() {
            this.specifications.push({ name: '', value: '' });
        },

        removeSpecification(index) {
            if (this.specifications.length === 1) {
                this.specifications[0] = { name: '', value: '' };

                return;
            }

            this.specifications.splice(index, 1);
        },

        addAttribute() {
            this.attributes.push({ name: '', value: '' });
        },

        removeAttribute(index) {
            if (this.attributes.length === 1) {
                this.attributes[0] = { name: '', value: '' };

                return;
            }

            this.attributes.splice(index, 1);
        },

        toggleRemoveImage(id) {
            if (this.removeImages.includes(id)) {
                this.removeImages = this.removeImages.filter((imageId) => imageId !== id);
            } else {
                this.removeImages.push(id);
            }
        },

        isImageMarkedForRemoval(id) {
            return this.removeImages.includes(id);
        },
    }));
};
