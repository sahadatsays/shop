export function registerPurchaseCreateForm(Alpine) {
    Alpine.data('purchaseCreateForm', (config) => ({
        productQuery: '',
        productResults: [],
        items: config.initialItems || [],
        shippingCents: Number(config.shippingCents ?? 0),
        discountCents: Number(config.discountCents ?? 0),
        taxCents: Number(config.taxCents ?? 0),
        currencySymbol: config.currencySymbol ?? '',
        searchingProducts: false,
        submitting: false,
        productSearchUrl: config.productSearchUrl,

        init() {
            this.$watch('productQuery', (value) => {
                clearTimeout(this._productTimer);
                this._productTimer = setTimeout(() => this.searchProducts(value), 250);
            });
        },

        async searchProducts(query) {
            if (!query || query.length < 1) {
                this.productResults = [];
                return;
            }

            this.searchingProducts = true;

            try {
                const response = await fetch(`${this.productSearchUrl}?q=${encodeURIComponent(query)}`, {
                    headers: { Accept: 'application/json' },
                });
                const payload = await response.json();
                this.productResults = payload.data ?? [];
            } finally {
                this.searchingProducts = false;
            }
        },

        addProduct(product) {
            const existing = this.items.find((item) => item.product_id === product.id);

            if (existing) {
                existing.quantity += 1;
            } else {
                this.items.push({
                    product_id: product.id,
                    name: product.name,
                    sku: product.sku,
                    unit_cost_cents: Number(product.cost_cents || 0),
                    quantity: 1,
                    discount_cents: 0,
                    tax_cents: 0,
                });
            }

            this.productQuery = '';
            this.productResults = [];
        },

        removeItem(index) {
            this.items.splice(index, 1);
        },

        lineGross(item) {
            return Number(item.unit_cost_cents || 0) * Number(item.quantity || 0);
        },

        lineNet(item) {
            return Math.max(
                0,
                this.lineGross(item) - Number(item.discount_cents || 0) + Number(item.tax_cents || 0),
            );
        },

        get subtotalCents() {
            return this.items.reduce((sum, item) => sum + this.lineNet(item), 0);
        },

        get grandTotalCents() {
            return Math.max(
                0,
                this.subtotalCents - Number(this.discountCents || 0) + Number(this.shippingCents || 0) + Number(this.taxCents || 0),
            );
        },

        formatMoney(cents) {
            return `${this.currencySymbol}${(Number(cents || 0) / 100).toFixed(2)}`;
        },

        onSubmit() {
            if (this.submitting) {
                return false;
            }

            if (this.items.length === 0) {
                window.adminToast?.push({ title: 'Add at least one product.', type: 'error' });
                return false;
            }

            this.submitting = true;

            return true;
        },
    }));
}
