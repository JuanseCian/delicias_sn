import Alpine from 'alpinejs';

declare global {
    interface Window {
        Alpine: typeof Alpine;
    }
}

type Product = {
    id: number;
    name: string;
    image: string | null;
    category: string;
    short_description?: string;
    description?: string;
    storage_instructions?: string;
    lead_time_hours?: number;
    sizes: ProductSizeOption[];
    ingredients?: { name: string; origin: string }[];
    allergens?: string[];
};

type ProductSizeOption = {
    id: number;
    label: string;
    price: number;
};

type CartItem = {
    key: string;
    product_id: number;
    product_size_id: number;
    quantity: number;
    note: string;
};

const storageKey = 'delicias-sn-cart';

const money = (value: number): string =>
    `$ ${new Intl.NumberFormat('es-AR').format(Math.round(value))}`;

const dateString = (date: Date): string => {
    const year = date.getFullYear();
    const month = `${date.getMonth() + 1}`.padStart(2, '0');
    const day = `${date.getDate()}`.padStart(2, '0');

    return `${year}-${month}-${day}`;
};

const dateLabel = (date: Date): string =>
    new Intl.DateTimeFormat('es-AR', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
    }).format(date);

Alpine.data(
    'deliciasApp',
    (catalog: Product[] = [], whatsapp = '5491100000000', initialCheckout = {}) => ({
        catalog,
        whatsapp,
        cart: [] as CartItem[],
        cartOpen: false,
        filter: 'all',
        selectedProduct: null as Product | null,
        selectedSize: 0,
        detailQuantity: 1,
        detailNote: '',
        checkout: {
            customer_name: '',
            phone: '',
            delivery_type: 'envio',
            address: '',
            delivery_date: '',
            time_slot: '9 a 12 h',
            notes: '',
            ...initialCheckout,
        },
        formErrors: {} as Record<string, string>,
        dateError: '',

        init() {
            try {
                const stored = JSON.parse(
                    localStorage.getItem(storageKey) || '[]',
                );
                this.cart = Array.isArray(stored)
                    ? stored.flatMap((item: Record<string, unknown>) => {
                          const productSizeId = Number(
                              item.product_size_id ?? item.size_id ?? 0,
                          );

                          if (!productSizeId) {
                              return [];
                          }

                          const normalizedItem = {
                              ...item,
                              product_size_id: productSizeId,
                          };
                          delete (normalizedItem as Record<string, unknown>)
                              .size_id;

                          return [normalizedItem as CartItem];
                      })
                    : [];
            } catch {
                this.cart = [];
            }

            const reducedMotion = window.matchMedia(
                '(prefers-reduced-motion: reduce)',
            ).matches;

            if (!reducedMotion) {
                document.documentElement.classList.add('motion-ready');
            }

            queueMicrotask(() => {
                if (!('IntersectionObserver' in window)) {
                    document
                        .querySelectorAll('.reveal')
                        .forEach((element) =>
                            element.classList.add('is-visible'),
                        );
                    return;
                }

                const observer = new IntersectionObserver(
                    (entries) => {
                        entries.forEach((entry) => {
                            if (entry.isIntersecting) {
                                entry.target.classList.add('is-visible');
                                observer.unobserve(entry.target);
                            }
                        });
                    },
                    { threshold: 0.12 },
                );

                document
                    .querySelectorAll('.reveal')
                    .forEach((element) => observer.observe(element));
            });
        },

        money,

        get cartCount() {
            return this.cart.reduce(
                (total: number, item: CartItem) => total + item.quantity,
                0,
            );
        },

        get subtotal() {
            return this.cart.reduce(
                (total: number, item: CartItem) => total + this.itemTotal(item),
                0,
            );
        },

        get detailMaxQuantity() {
            return 50;
        },

        get minimumDate() {
            const leadTime = this.cart.reduce((max: number, item: CartItem) => {
                const product = this.productFor(item.product_id);
                return Math.max(max, product?.lead_time_hours || 0);
            }, 48);
            const date = new Date();
            date.setHours(date.getHours() + leadTime, 0, 0, 0);
            date.setHours(0, 0, 0, 0);

            return date;
        },

        get minimumDateString() {
            return dateString(this.minimumDate);
        },

        get minimumDateLabel() {
            const label = dateLabel(this.minimumDate);
            return label.charAt(0).toUpperCase() + label.slice(1);
        },

        productFor(productId: number) {
            return this.catalog.find(
                (product: Product) => product.id === Number(productId),
            );
        },

        sizeFor(item: { product_id: number; product_size_id: number }) {
            return this.productFor(item.product_id)?.sizes.find(
                (size: ProductSizeOption) =>
                    size.id === Number(item.product_size_id),
            );
        },

        colorFor(_item: { product_id: number }) {
            return undefined;
        },

        itemTotal(item: CartItem) {
            return (this.sizeFor(item)?.price || 0) * item.quantity;
        },

        persist() {
            localStorage.setItem(storageKey, JSON.stringify(this.cart));
        },

        openProduct(productId: number) {
            this.selectedProduct = this.productFor(productId) || null;
            this.selectedSize = this.selectedProduct?.sizes[0]?.id || 0;
            this.detailQuantity = 1;
            this.detailNote = '';
        },

        closeProduct() {
            this.selectedProduct = null;
        },

        addSelectedToCart() {
            if (!this.selectedProduct || !this.selectedSize) return;
            const size = this.sizeFor({
                product_id: this.selectedProduct.id,
                product_size_id: this.selectedSize,
            });
            if (!size || this.detailQuantity > 50) return;
            const key = `${this.selectedProduct.id}-${this.selectedSize}-${encodeURIComponent(this.detailNote.trim().toLowerCase())}`;
            const existing = this.cart.find(
                (item: CartItem) =>
                    item.key === key && item.note === this.detailNote,
            );

            if (existing) {
                if (existing.quantity + this.detailQuantity > 50)
                    return;
                existing.quantity += this.detailQuantity;
            } else {
                this.cart.push({
                    key,
                    product_id: this.selectedProduct.id,
                    product_size_id: this.selectedSize,
                    quantity: this.detailQuantity,
                    note: this.detailNote,
                });
            }

            this.persist();
            this.closeProduct();
            this.cartOpen = true;
        },

        changeQuantity(key: string, amount: number) {
            const item = this.cart.find(
                (cartItem: CartItem) => cartItem.key === key,
            );
            if (!item) return;
            const maxQuantity = 50;
            item.quantity = Math.max(
                1,
                Math.min(maxQuantity, item.quantity + amount),
            );
            this.persist();
        },

        removeItem(key: string) {
            this.cart = this.cart.filter((item: CartItem) => item.key !== key);
            this.persist();
        },

        validateDate() {
            this.dateError = '';
            if (!this.checkout.delivery_date) return true;
            const selected = new Date(
                `${this.checkout.delivery_date}T00:00:00`,
            );
            const minimum = this.minimumDate;

            if (selected < minimum) {
                this.dateError = `Por los productos elegidos, la primera fecha disponible es el ${this.minimumDateLabel}.`;
                this.checkout.delivery_date = '';
                return false;
            }

            return true;
        },

        validateCheckout() {
            this.formErrors = {};

            if (!this.checkout.customer_name.trim()) {
                this.formErrors.customer_name = 'Decinos tu nombre y apellido.';
            }

            if (!this.checkout.phone.trim()) {
                this.formErrors.phone = 'Necesitamos un teléfono para contactarte por WhatsApp.';
            }

            if (this.checkout.delivery_type === 'envio' && !this.checkout.address.trim()) {
                this.formErrors.address = 'Completá la dirección para coordinar el envío.';
            }

            if (!this.checkout.delivery_date) {
                this.formErrors.delivery_date = 'Elegí una fecha para tu pedido.';
            } else if (!this.validateDate()) {
                this.formErrors.delivery_date = this.dateError;
            }

            const firstError = Object.keys(this.formErrors)[0];
            if (firstError) {
                document.querySelector<HTMLElement>(`[data-checkout-field="${firstError}"]`)?.focus();
                return false;
            }

            return true;
        },

        whatsappMessage() {
            const lines = [
                '🍰 Hola Delicias Sn! Quiero hacer este pedido:',
                '',
                ...this.cart.map((item: CartItem) => {
                    const product = this.productFor(item.product_id);
                    const size = this.sizeFor(item);
                    const note = item.note ? ` · Nota: ${item.note}` : '';
                    return `• ${product?.name} · ${size?.label} · x${item.quantity}${note}`;
                }),
                '',
                `Total: ${money(this.subtotal)}`,
            ];

            if (this.checkout.customer_name) {
                lines.push(
                    '',
                    '📦 Datos de entrega:',
                    `Nombre: ${this.checkout.customer_name}`,
                    `Teléfono: ${this.checkout.phone}`,
                    `Tipo: ${this.checkout.delivery_type === 'envio' ? 'Envío' : 'Retiro'}`,
                );
                if (this.checkout.address)
                    lines.push(`Dirección: ${this.checkout.address}`);
                if (this.checkout.delivery_date)
                    lines.push(
                        `Fecha: ${this.checkout.delivery_date}`,
                        `Horario: ${this.checkout.time_slot}`,
                    );
                if (this.checkout.notes)
                    lines.push(`Notas: ${this.checkout.notes}`);
            }

            return lines.join('\n');
        },

        whatsappUrl() {
            return `https://wa.me/${this.whatsapp}?text=${encodeURIComponent(this.whatsappMessage())}`;
        },

        openWhatsApp() {
            if (!this.cart.length) return;
            const popup = window.open(this.whatsappUrl(), '_blank', 'noopener');
            if (!popup) window.location.href = this.whatsappUrl();
        },
    }),
);

type EditorSize = {
    id?: number | string;
    label: string;
    price: number | string;
    uid?: string;
};

Alpine.data('productEditor', (initial: Record<string, any> = {}) => ({
    currentStep: 1,
    maxStep: 1,
    category_id: initial.category_id || '',
    name: initial.name || '',
    description: initial.description || '',
    storage_instructions: initial.storage_instructions || '',
    is_featured: Boolean(initial.is_featured),
    is_custom: Boolean(initial.is_custom),
    active: initial.active !== false,
    ingredients_text: initial.ingredients_text || '',
    sizes: (initial.sizes?.length
        ? initial.sizes
        : [{ label: '', price: '' }]
    ).map((size: EditorSize, index: number) => ({
        ...size,
        uid: size.uid || `size-${index}-${Date.now()}`,
    })),

    goToStep(step: number) {
        if (step > this.maxStep) return;

        this.currentStep = step;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    },

    nextStep() {
        const step = document.querySelector<HTMLElement>(`[data-product-step="${this.currentStep}"]`);
        const requiredFields = step?.querySelectorAll<HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement>(':required') ?? [];
        const invalidField = Array.from(requiredFields).find((field) => !field.checkValidity());

        if (invalidField) {
            invalidField.reportValidity();
            invalidField.focus();
            return;
        }

        this.maxStep = Math.max(this.maxStep, this.currentStep + 1);
        this.currentStep = Math.min(this.currentStep + 1, 3);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    },

    previousStep() {
        this.currentStep = Math.max(this.currentStep - 1, 1);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    },

    addSize() {
        this.sizes.push({
            label: '',
            price: '',
            uid: `size-${Date.now()}`,
        });
    },

    removeSize(index: number) {
        if (this.sizes.length > 1) this.sizes.splice(index, 1);
    },

}));

window.Alpine = Alpine;
Alpine.start();
