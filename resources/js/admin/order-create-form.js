export function registerOrderCreateForm(Alpine) {
    Alpine.data('orderCreateForm', (config) => ({
        customerMode: 'existing',
        customerQuery: '',
        customerResults: [],
        selectedCustomer: null,
        productQuery: '',
        productResults: [],
        items: [],
        shippingMethod: config.defaultShippingMethod ?? '',
        shippingRates: config.shippingRates ?? {},
        shippingAmount: Number(config.defaultShippingAmount ?? 0),
        orderDiscountType: '',
        orderDiscountValue: '',
        initialPaymentAmount: 0,
        taxRate: Number(config.taxRate ?? 0),
        currencySymbol: config.currencySymbol ?? '',
        searchingCustomers: false,
        searchingProducts: false,
        submitting: false,
        customerSearchUrl: config.customerSearchUrl,
        productSearchUrl: config.productSearchUrl,

        init() {
            this.$watch('customerQuery', (value) => {
                clearTimeout(this._customerTimer);
                this._customerTimer = setTimeout(() => this.searchCustomers(value), 250);
            });

            this.$watch('productQuery', (value) => {
                clearTimeout(this._productTimer);
                this._productTimer = setTimeout(() => this.searchProducts(value), 250);
            });

            this.$watch('shippingMethod', (method) => {
                if (method && this.shippingRates[method] != null) {
                    this.shippingAmount = Number(this.shippingRates[method]);
                } else {
                    // "Custom / none" selected — reset charge to 0
                    this.shippingAmount = 0;
                }
            });
        },

        async searchCustomers(query) {
            if (this.customerMode !== 'existing') {
                return;
            }

            this.searchingCustomers = true;

            try {
                const response = await fetch(`${this.customerSearchUrl}?q=${encodeURIComponent(query || '')}`, {
                    headers: { Accept: 'application/json' },
                });
                const payload = await response.json();
                this.customerResults = payload.data ?? [];
            } finally {
                this.searchingCustomers = false;
            }
        },

        selectCustomer(customer) {
            this.selectedCustomer = customer;
            this.customerQuery = customer.name;
            this.customerResults = [];

            const address = customer.addresses?.find((row) => row.is_default) ?? customer.addresses?.[0];

            if (!address) {
                return;
            }

            const [firstName, ...rest] = String(address.name || customer.name || '').split(' ');
            const fields = {
                first_name: firstName || '',
                last_name: rest.join(' ') || '',
                line1: address.line1 || '',
                line2: address.line2 || '',
                district: address.district || address.city || '',
                country: address.country || 'Bangladesh',
                phone: address.phone || customer.phone || '',
            };

            Object.entries(fields).forEach(([key, value]) => {
                const shipping = this.$refs[`shipping_${key}`];

                if (shipping) {
                    shipping.value = value;
                }
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
                if (existing.quantity < product.stock_quantity) {
                    existing.quantity += 1;
                }
            } else {
                this.items.push({
                    product_id: product.id,
                    name: product.name,
                    sku: product.sku,
                    image: product.image,
                    stock_quantity: product.stock_quantity,
                    unit_price_cents: product.price_cents,
                    quantity: 1,
                    discount_amount: 0,
                });
            }

            this.productQuery = '';
            this.productResults = [];
        },

        removeItem(index) {
            this.items.splice(index, 1);
        },

        lineGross(item) {
            return item.unit_price_cents * item.quantity;
        },

        lineDiscountCents(item) {
            return Math.round(Number(item.discount_amount || 0) * 100);
        },

        lineNet(item) {
            return Math.max(0, this.lineGross(item) - this.lineDiscountCents(item));
        },

        get shippingCents() {
            return Math.round(Number(this.shippingAmount || 0) * 100);
        },

        get initialPaymentCents() {
            return Math.round(Number(this.initialPaymentAmount || 0) * 100);
        },

        get subtotalCents() {
            return this.items.reduce((sum, item) => sum + this.lineNet(item), 0);
        },

        get orderDiscountCents() {
            const value = Number(this.orderDiscountValue || 0);

            if (!this.orderDiscountType || value <= 0) {
                return 0;
            }

            if (this.orderDiscountType === 'percent') {
                return Math.min(this.subtotalCents, Math.round(this.subtotalCents * (Math.min(value, 100) / 100)));
            }

            return Math.min(this.subtotalCents, Math.round(value * 100));
        },

        get taxCents() {
            return Math.round(Math.max(0, this.subtotalCents - this.orderDiscountCents) * this.taxRate);
        },

        get totalCents() {
            return Math.max(0, this.subtotalCents - this.orderDiscountCents) + this.shippingCents + this.taxCents;
        },

        formatMoney(cents) {
            return `${this.currencySymbol}${(Number(cents || 0) / 100).toFixed(2)}`;
        },

        onSubmit() {
            if (this.submitting) {
                return false;
            }

            this.submitting = true;

            return true;
        },
    }));
}
