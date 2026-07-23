export const registerCustomerForm = (Alpine) => {
    Alpine.data('customerForm', (config = {}) => ({
        addresses: config.addresses?.length
            ? config.addresses
            : [emptyAddress()],

        addAddress() {
            this.addresses.push(emptyAddress());
        },

        removeAddress(index) {
            if (this.addresses.length === 1) {
                this.addresses[0] = emptyAddress();

                return;
            }

            this.addresses.splice(index, 1);
        },

        setDefault(index) {
            this.addresses = this.addresses.map((address, i) => ({
                ...address,
                is_default: i === index,
            }));
        },
    }));
};

function emptyAddress() {
    return {
        label: '',
        type: 'shipping',
        name: '',
        phone: '',
        line1: '',
        line2: '',
        city: '',
        state: '',
        postal_code: '',
        country: 'US',
        is_default: false,
    };
}
