import {
    ArrowRight,
    CakeSlice,
    ChevronLeft,
    ChevronRight,
    Package,
    X,
} from 'lucide-react';
import * as React from 'react';
import { createPortal } from 'react-dom';

export interface RailProduct {
    id: number;
    slug: string;
    name: string;
    category: string;
    description: string;
    price: string;
    image?: string | null;
    tag?: string;
    details?: string[];
    url: string;
}

export interface ProductRailGalleryProps
    extends React.ComponentProps<'section'> {
    items: RailProduct[];
    eyebrow?: string;
    heading?: React.ReactNode;
    caption?: string;
    action?: React.ReactNode;
    autoplay?: boolean;
    interval?: number;
    onChoose?: (product: RailProduct) => void;
}

const FALLBACK_IMAGES: Record<string, string> = {
    'choco-dulce': 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?auto=format&fit=crop&w=900&q=85',
    'lemon-pie-de-campo': 'https://images.unsplash.com/photo-1519915028121-7d3463d20b13?auto=format&fit=crop&w=900&q=85',
    'cupcakes-de-la-semana': 'https://images.unsplash.com/photo-1587668178277-295251f900ce?auto=format&fit=crop&w=900&q=85',
    'red-velvet': 'https://images.unsplash.com/photo-1614707267537-2b1a87c1a0f6?auto=format&fit=crop&w=900&q=85',
    'lemon-pop': 'https://images.unsplash.com/photo-1519869325930-281384150729?auto=format&fit=crop&w=900&q=85',
    'cookies-chunky': 'https://images.unsplash.com/photo-1499636136210-6f4ee915583e?auto=format&fit=crop&w=900&q=85',
    'almendras-miel': 'https://images.unsplash.com/photo-1558961363-fa8fdf82db35?auto=format&fit=crop&w=900&q=85',
    'box-merienda': 'https://images.unsplash.com/photo-1542826438-6b8c1f7b7a2d?auto=format&fit=crop&w=900&q=85',
    'box-brunch': 'https://images.unsplash.com/photo-1551024506-0bccd828d307?auto=format&fit=crop&w=900&q=85',
    'box-sorpresa': 'https://images.unsplash.com/photo-1606890737304-57a1ca8a5b62?auto=format&fit=crop&w=900&q=85',
};

function productImage(product: RailProduct): string | null {
    return product.image || FALLBACK_IMAGES[product.slug] || null;
}

function ProductArt() {
    return (
        <div className="grid h-full place-items-center bg-[linear-gradient(145deg,#f4d9d5,#c98b8b)] text-bordo/65">
            <CakeSlice className="h-20 w-20" strokeWidth={1.1} />
        </div>
    );
}

function ProductDetail({
    product,
    onClose,
    onChoose,
}: {
    product: RailProduct;
    onClose: () => void;
    onChoose?: (product: RailProduct) => void;
}) {
    const image = productImage(product);

    React.useEffect(() => {
        const closeOnEscape = (event: KeyboardEvent) => {
            if (event.key === 'Escape') onClose();
        };

        document.addEventListener('keydown', closeOnEscape);
        return () => document.removeEventListener('keydown', closeOnEscape);
    }, [onClose]);

    return createPortal(
        <div className="fixed inset-0 z-50 grid place-items-center overflow-y-auto p-4 sm:p-8" role="dialog" aria-modal="true" aria-labelledby="product-rail-title">
            <button type="button" aria-label="Cerrar detalle" className="absolute inset-0 bg-ink/60 backdrop-blur-sm" onClick={onClose} />
            <div className="relative grid w-full max-w-3xl overflow-hidden rounded-[2rem] bg-crema shadow-2xl sm:grid-cols-2">
                <div className="relative min-h-72 bg-blush sm:min-h-[28rem]">
                    {image ? <img src={image} alt={product.name} className="h-full w-full object-cover" /> : <ProductArt />}
                    <span className="absolute left-5 top-5 rounded-full bg-crema/90 px-3 py-2 text-[10px] font-bold uppercase tracking-[0.12em] text-bordo">{product.tag || 'Favorito'}</span>
                </div>
                <div className="flex flex-col p-6 sm:p-9">
                    <button type="button" onClick={onClose} className="absolute right-4 top-4 grid h-10 w-10 place-items-center rounded-full bg-crema/90 text-bordo transition hover:bg-blush focus-visible:ring-2 focus-visible:ring-bordo" aria-label="Cerrar detalle"><X className="h-5 w-5" /></button>
                    <p className="eyebrow">{product.category}</p>
                    <h3 id="product-rail-title" className="mt-3 font-display text-3xl leading-tight text-bordo">{product.name}</h3>
                    <p className="mt-4 text-sm leading-7 text-ink/65">{product.description}</p>
                    {product.details && <ul className="mt-6 flex flex-wrap gap-2">{product.details.map((detail) => <li key={detail} className="rounded-full bg-white px-3 py-2 text-xs text-ink/65">{detail}</li>)}</ul>}
                    <div className="mt-auto flex items-end justify-between gap-4 border-t border-bordo/10 pt-6">
                        <div><p className="text-[10px] font-bold uppercase tracking-[0.12em] text-ink/45">Desde</p><p className="mt-1 font-display text-2xl text-bordo">{product.price}</p></div>
                        {onChoose ? <button type="button" onClick={() => onChoose(product)} className="btn-primary">Elegir opciones <ArrowRight className="h-4 w-4" /></button> : <a href={product.url} className="btn-primary">Ver opciones <ArrowRight className="h-4 w-4" /></a>}
                    </div>
                </div>
            </div>
        </div>,
        document.body,
    );
}

export default function ProductRailGallery({
    items,
    eyebrow = 'Los favoritos',
    heading = (
        <>
            Los más
            <br />
            <span className="font-script text-6xl font-normal text-rosa-viejo">
                pedidos
            </span>
        </>
    ),
    caption = 'Una selección de lo que más sale y de los favoritos de la casa.',
    action,
    autoplay = true,
    interval = 2000,
    onChoose,
    className,
    ...rest
}: ProductRailGalleryProps) {
    const [active, setActive] = React.useState(0);
    const [paused, setPaused] = React.useState(false);
    const [reducedMotion, setReducedMotion] = React.useState(false);
    const [selected, setSelected] = React.useState<RailProduct | null>(null);

    React.useEffect(() => {
        const media = window.matchMedia('(prefers-reduced-motion: reduce)');
        const update = () => setReducedMotion(media.matches);
        update();
        media.addEventListener('change', update);
        return () => media.removeEventListener('change', update);
    }, []);

    React.useEffect(() => {
        if (!autoplay || paused || reducedMotion || selected || items.length < 2) return;
        const timer = window.setTimeout(() => setActive((current) => (current + 1) % items.length), interval);
        return () => window.clearTimeout(timer);
    }, [active, autoplay, interval, items.length, paused, reducedMotion, selected]);

    if (items.length === 0) return null;

    const choose = (product: RailProduct) => {
        setSelected(null);
        onChoose?.(product);
    };

    return (
        <section {...rest} id="mas-pedidos" className={`overflow-hidden bg-crema px-4 py-20 sm:px-6 sm:py-28 lg:px-8 ${className ?? ''}`} onMouseEnter={() => setPaused(true)} onMouseLeave={() => setPaused(false)} onFocusCapture={() => setPaused(true)} onBlurCapture={(event) => { if (!event.currentTarget.contains(event.relatedTarget)) setPaused(false); }}>
            <div className="mx-auto max-w-7xl">
                <header className="flex flex-col justify-between gap-6 sm:flex-row sm:items-end">
                    <div className="max-w-xl"><p className="section-kicker">{eyebrow}</p><h2 className="mt-4 font-display text-4xl leading-tight tracking-[-0.03em] text-bordo sm:text-5xl">{heading}</h2></div>
                    <p className="max-w-xs text-sm leading-6 text-ink/60 sm:text-right">{caption}</p>
                </header>
                <div className="relative mx-auto mt-10 h-[28rem] max-w-5xl overflow-hidden rounded-[2rem] bg-white/35 [perspective:1200px] sm:h-[31rem]">
                    <div className="absolute inset-x-[8%] bottom-12 h-2 rounded-full bg-bordo/15 shadow-[0_8px_16px_rgba(107,45,60,0.14)]" />
                    <div className="absolute inset-x-[12%] bottom-10 h-px bg-bordo/25" />
                    {items.map((product, index) => {
                        const rawOffset = index - active;
                        const offset = rawOffset > Math.floor(items.length / 2) ? rawOffset - items.length : rawOffset < -Math.floor(items.length / 2) ? rawOffset + items.length : rawOffset;
                        const distance = Math.abs(offset);
                        const image = productImage(product);
                        return <button key={product.id} type="button" tabIndex={index === active ? 0 : -1} aria-label={`${product.name}, ${product.price}`} className="absolute left-1/2 top-6 w-[min(15rem,64vw)] origin-bottom rounded-[1.6rem] text-left outline-none transition-[transform,opacity,filter] duration-700 ease-[cubic-bezier(.16,1,.3,1)] focus-visible:ring-2 focus-visible:ring-bordo focus-visible:ring-offset-4 focus-visible:ring-offset-crema" style={{ opacity: distance > 2 ? 0 : distance === 2 ? 0.28 : distance === 1 ? 0.72 : 1, pointerEvents: distance > 2 ? 'none' : 'auto', transform: `translateX(calc(-50% + ${offset * 18}vw)) translateY(${distance * 1.4}rem) rotate(${offset * -4}deg) scale(${distance === 0 ? 1 : distance === 1 ? 0.88 : 0.74})`, zIndex: 20 - distance }} onClick={() => { setActive(index); setSelected(product); }}>
                            <span className="absolute left-1/2 top-[-1.25rem] h-5 w-px -translate-x-1/2 bg-bordo/30" /><span className="absolute left-1/2 top-[-1.45rem] h-2.5 w-9 -translate-x-1/2 rounded-full border-2 border-bordo/25 bg-caramelo/75" />
                            <span className="block overflow-hidden rounded-[1.6rem] border border-bordo/10 bg-crema shadow-[0_18px_38px_rgba(107,45,60,0.14)]"><span className="relative block h-56 overflow-hidden bg-blush">{image ? <img src={image} alt="" className="h-full w-full object-cover" /> : <ProductArt />}<span className="absolute left-3 top-3 rounded-full bg-crema/90 px-2.5 py-1.5 text-[9px] font-bold uppercase tracking-[0.1em] text-bordo">{product.tag || 'Favorito'}</span></span><span className="block bg-white p-4"><span className="block text-[10px] font-bold uppercase tracking-[0.12em] text-rosa-viejo">{product.category}</span><span className="mt-1 block truncate font-display text-xl text-bordo">{product.name}</span><span className="mt-3 flex items-center justify-between gap-3 border-t border-bordo/10 pt-3"><span className="font-display text-lg text-bordo">{product.price}</span><Package className="h-4 w-4 text-bordo/55" /></span></span></span>
                        </button>;
                    })}
                </div>
                <div className="relative z-10 mt-5 flex items-center justify-center gap-3"><button type="button" onClick={() => setActive((current) => (current - 1 + items.length) % items.length)} className="grid h-10 w-10 place-items-center rounded-full border border-bordo/15 text-bordo transition hover:border-bordo/40 hover:bg-blush focus-visible:ring-2 focus-visible:ring-bordo" aria-label="Producto anterior"><ChevronLeft className="h-5 w-5" /></button><div className="flex items-center gap-2" role="tablist" aria-label="Seleccionar producto">{items.map((product, index) => <button key={product.id} type="button" role="tab" aria-selected={active === index} aria-label={`Ver ${product.name}`} onClick={() => setActive(index)} className={`h-2 rounded-full transition-all ${active === index ? 'w-7 bg-bordo' : 'w-2 bg-rosa-viejo/40'}`} />)}</div><button type="button" onClick={() => setActive((current) => (current + 1) % items.length)} className="grid h-10 w-10 place-items-center rounded-full border border-bordo/15 text-bordo transition hover:border-bordo/40 hover:bg-blush focus-visible:ring-2 focus-visible:ring-bordo" aria-label="Producto siguiente"><ChevronRight className="h-5 w-5" /></button></div>
                <p className="mt-7 text-center text-xs font-semibold uppercase tracking-[0.12em] text-ink/45">Tocá una opción para verla de cerca</p>
                {action && <div className="mt-7 text-center">{action}</div>}
            </div>
            {selected && <ProductDetail product={selected} onClose={() => setSelected(null)} onChoose={onChoose ? choose : undefined} />}
        </section>
    );
}
