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
    colors: ProductColorOption[];
    ingredients?: { name: string; origin: string }[];
    allergens?: string[];
};

type ProductSizeOption = {
    id: number;
    label: string;
    price: number;
    quantity: number;
};

type ProductColorOption = { id: number; name: string; hex: string };

type CartItem = {
    key: string;
    product_id: number;
    size_id: number;
    color_id?: number | null;
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
    (catalog: Product[] = [], whatsapp = '5491100000000') => ({
        catalog,
        whatsapp,
        cart: [] as CartItem[],
        cartOpen: false,
        filter: 'all',
        selectedProduct: null as Product | null,
        selectedSize: 0,
        selectedColor: 0,
        detailQuantity: 1,
        detailNote: '',
        checkout: {
            customer_name: '',
            phone: '',
            delivery_type: 'envio',
            address: '',
            delivery_date: '',
            time_slot: '9 a 12 h',
            payment_method: 'transferencia',
            notes: '',
        },
        dateError: '',

        init() {
            try {
                const stored = JSON.parse(
                    localStorage.getItem(storageKey) || '[]',
                );
                this.cart = Array.isArray(stored) ? stored : [];
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

        get deposit() {
            return Math.ceil(this.subtotal / 2);
        },

        get balance() {
            return this.subtotal - this.deposit;
        },

        get minimumDate() {
            const leadTime = this.cart.reduce((max: number, item: CartItem) => {
                const product = this.productFor(item.product_id);
                return Math.max(max, product?.lead_time_hours || 0);
            }, 0);
            const date = new Date();
            date.setHours(date.getHours() + leadTime, 0, 0, 0);
            date.setHours(0, 0, 0, 0);

            while (date.getDay() === 0) {
                date.setDate(date.getDate() + 1);
            }

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

        sizeFor(item: { product_id: number; size_id: number }) {
            return this.productFor(item.product_id)?.sizes.find(
                (size: ProductSizeOption) => size.id === Number(item.size_id),
            );
        },

        colorFor(item: { product_id: number; color_id?: number | null }) {
            return this.productFor(item.product_id)?.colors?.find(
                (color: ProductColorOption) =>
                    color.id === Number(item.color_id),
            );
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
            this.selectedColor = this.selectedProduct?.colors?.[0]?.id || 0;
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
                size_id: this.selectedSize,
            });
            if (!size || size.quantity < this.detailQuantity) return;
            const key = `${this.selectedProduct.id}-${this.selectedSize}-${this.selectedColor || 0}-${encodeURIComponent(this.detailNote.trim().toLowerCase())}`;
            const existing = this.cart.find(
                (item: CartItem) =>
                    item.key === key && item.note === this.detailNote,
            );

            if (existing) {
                if (existing.quantity + this.detailQuantity > size.quantity)
                    return;
                existing.quantity += this.detailQuantity;
            } else {
                this.cart.push({
                    key,
                    product_id: this.selectedProduct.id,
                    size_id: this.selectedSize,
                    color_id: this.selectedColor || null,
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
            const maxQuantity = this.sizeFor(item)?.quantity || 1;
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

            if (selected.getDay() === 0) {
                this.dateError =
                    'Los domingos descansamos. Elegí un día de lunes a sábado.';
                return false;
            }

            if (selected < minimum) {
                this.dateError = `Por los productos elegidos, la primera fecha disponible es el ${this.minimumDateLabel}.`;
                this.checkout.delivery_date = '';
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
                    const color = this.colorFor(item);
                    const note = item.note ? ` · Nota: ${item.note}` : '';
                    return `• ${product?.name} · ${size?.label}${color ? ` · ${color.name}` : ''} · x${item.quantity}${note}`;
                }),
                '',
                `Total: ${money(this.subtotal)}`,
                `Seña (50%): ${money(this.deposit)}`,
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
                        `Pago: ${this.checkout.payment_method}`,
                    );
                if (this.checkout.notes)
                    lines.push(`Notas: ${this.checkout.notes}`);
            }

            return lines.join('\n');
        },

        openWhatsApp() {
            if (!this.cart.length) return;
            const url = `https://wa.me/${this.whatsapp}?text=${encodeURIComponent(this.whatsappMessage())}`;
            window.open(url, '_blank', 'noopener');
        },
    }),
);

type EditorSize = {
    id?: number | string;
    label: string;
    price: number | string;
    quantity: number | string;
    uid?: string;
};

type EditorColor = {
    id?: number | string;
    name: string;
    hex: string;
    uid?: string;
};

Alpine.data('productEditor', (initial: Record<string, any> = {}) => ({
    category_id: initial.category_id || '',
    name: initial.name || '',
    short_description: initial.short_description || '',
    description: initial.description || '',
    storage_instructions: initial.storage_instructions || '',
    is_featured: Boolean(initial.is_featured),
    is_custom: Boolean(initial.is_custom),
    active: initial.active !== false,
    ingredients: initial.ingredients || [],
    allergens: initial.allergens || [],
    sizes: (initial.sizes?.length
        ? initial.sizes
        : [{ label: '', price: '', quantity: 10 }]
    ).map((size: EditorSize, index: number) => ({
        ...size,
        uid: size.uid || `size-${index}-${Date.now()}`,
    })),
    colors: (initial.colors || []).map((color: EditorColor, index: number) => ({
        ...color,
        uid: color.uid || `color-${index}-${Date.now()}`,
    })),

    addSize() {
        this.sizes.push({
            label: '',
            price: '',
            quantity: 10,
            uid: `size-${Date.now()}`,
        });
    },

    removeSize(index: number) {
        if (this.sizes.length > 1) this.sizes.splice(index, 1);
    },

    addColor() {
        this.colors.push({
            name: '',
            hex: '#C98B8B',
            uid: `color-${Date.now()}`,
        });
    },

    removeColor(index: number) {
        this.colors.splice(index, 1);
    },
}));

window.Alpine = Alpine;
Alpine.start();
