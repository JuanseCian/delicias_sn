import { Head, Link } from '@inertiajs/react';
import {
    ArrowUpRight,
    Check,
    CircleAlert,
    ExternalLink,
    ImagePlus,
    LayoutGrid,
    PackagePlus,
    Store,
    WandSparkles,
} from 'lucide-react';
import AdjustableGlassDashboard from '@/components/ui/adjustable-glass-dashboard';

type Props = {
    stats: {
        products: number;
        categories: number;
        with_images: number;
    };
};

export default function Dashboard({ stats }: Props) {
    const imageCoverage =
        stats.products > 0
            ? Math.round((stats.with_images / stats.products) * 100)
            : 0;
    const hasMissingImages = stats.with_images < stats.products;

    return (
        <>
            <Head title="Panel de gestión" />
            <AdjustableGlassDashboard>
                <div className="relative mx-auto max-w-7xl p-4 sm:p-6 lg:p-8">
                    <div
                        data-dashboard-reveal
                        className="flex flex-col justify-between gap-6 sm:flex-row sm:items-end"
                    >
                        <div>
                            <p className="eyebrow">Panel de gestión</p>
                            <h1 className="mt-3 max-w-2xl font-display text-4xl leading-[1.05] tracking-tight text-[#6B2D3C] sm:text-5xl">
                                Tu negocio, más simple de administrar{' '}
                                <span className="font-script text-5xl font-normal text-[#C98B8B]">
                                    y compartir
                                </span>
                            </h1>
                            <p className="mt-4 max-w-xl text-sm leading-6 text-[#3B2A2A]/65">
                                Desde acá podés mantener actualizado el catálogo
                                y revisar cómo lo ven tus clientes. No hace
                                falta tocar nada técnico.
                            </p>
                        </div>
                        <a
                            href="/gestion-delicias-sn-privada/productos/nuevo"
                            className="inline-flex h-12 items-center justify-center gap-2 rounded-2xl bg-[#6B2D3C] px-5 text-sm font-bold text-white shadow-[0_10px_24px_rgba(107,45,60,0.18)] transition hover:-translate-y-0.5 hover:bg-[#3B2A2A]"
                        >
                            <PackagePlus className="size-4" />
                            Agregar producto
                        </a>
                    </div>

                    <div
                        data-dashboard-reveal
                        className="mt-8 grid gap-4 md:grid-cols-3"
                    >
                        <StatCard
                            icon={<Store className="size-5" />}
                            label="Productos activos"
                            value={stats.products}
                            detail="En tu catálogo"
                        />
                        <StatCard
                            icon={<LayoutGrid className="size-5" />}
                            label="Categorías"
                            value={stats.categories}
                            detail="Ordenadas en la landing"
                        />
                        <StatCard
                            icon={<ImagePlus className="size-5" />}
                            label="Fotos cargadas"
                            value={`${stats.with_images}/${stats.products}`}
                            detail={
                                hasMissingImages
                                    ? 'Hay productos sin foto'
                                    : 'Catálogo con fotos'
                            }
                            accent={hasMissingImages}
                        />
                    </div>

                    <section
                        data-dashboard-reveal
                        className="mt-6 grid gap-5 lg:grid-cols-[1.2fr_0.8fr]"
                    >
                        <div className="rounded-[2rem] border border-[#6B2D3C]/10 bg-white/75 p-6 shadow-[0_12px_36px_rgba(59,42,42,0.08)] backdrop-blur-md sm:p-8">
                            <div className="flex items-start justify-between gap-4">
                                <div>
                                    <p className="section-kicker">
                                        Estado del catálogo
                                    </p>
                                    <h2 className="mt-3 font-display text-3xl text-[#6B2D3C]">
                                        Una vista clara para decidir rápido
                                    </h2>
                                </div>
                                <span className="grid size-11 shrink-0 place-items-center rounded-2xl bg-[#F4D9D5] text-[#6B2D3C]">
                                    {hasMissingImages ? (
                                        <CircleAlert className="size-5" />
                                    ) : (
                                        <Check className="size-5" />
                                    )}
                                </span>
                            </div>
                            <p className="mt-4 max-w-lg text-sm leading-6 text-[#3B2A2A]/65">
                                Las fotos reales ayudan a que cada producto se
                                luzca. Subí una imagen por producto y se va a
                                mostrar automáticamente en la landing, el
                                detalle y el carrito.
                            </p>
                            <div className="mt-7 flex items-center gap-4">
                                <div className="h-2 flex-1 overflow-hidden rounded-full bg-[#F4D9D5]">
                                    <div
                                        className="h-full rounded-full bg-[#6B2D3C] transition-all"
                                        style={{ width: `${imageCoverage}%` }}
                                    />
                                </div>
                                <span className="text-sm font-bold text-[#6B2D3C]">
                                    {imageCoverage}%
                                </span>
                            </div>
                            <a
                                href="/gestion-delicias-sn-privada/productos"
                                className="mt-6 inline-flex items-center gap-2 text-sm font-bold text-[#6B2D3C] transition hover:text-[#C98B8B]"
                            >
                                Gestionar productos
                                <ArrowUpRight className="size-4" />
                            </a>
                        </div>
                        <div className="rounded-[2rem] bg-[#6B2D3C] p-6 text-[#FBF5EE] shadow-[0_18px_55px_rgba(107,45,60,0.16)] sm:p-8">
                            <p className="section-kicker !text-[#F4D9D5]">
                                Accesos rápidos
                            </p>
                            <h2 className="mt-3 font-display text-3xl">
                                Lo importante, a mano
                            </h2>
                            <div className="mt-6 grid gap-3">
                                <QuickAction
                                    href="/gestion-delicias-sn-privada/productos"
                                    icon={<ImagePlus className="size-4" />}
                                    title="Gestionar productos"
                                    detail="Editar datos, stock y fotos"
                                    native
                                />
                                <QuickAction
                                    href="/gestion-delicias-sn-privada/personalizadas"
                                    icon={<WandSparkles className="size-4" />}
                                    title="Configurar diseñador"
                                    detail="Opciones, precios y solicitudes"
                                    native
                                />
                                <QuickAction
                                    href="/"
                                    icon={<ExternalLink className="size-4" />}
                                    title="Abrir página"
                                    detail="Ver la experiencia de tus clientes"
                                    external
                                />
                            </div>
                        </div>
                    </section>
                </div>
            </AdjustableGlassDashboard>
        </>
    );
}

function QuickAction({
    href,
    icon,
    title,
    detail,
    external = false,
    native = false,
}: {
    href: string;
    icon: React.ReactNode;
    title: string;
    detail: string;
    external?: boolean;
    native?: boolean;
}) {
    const className =
        'group flex items-center gap-3 rounded-2xl border border-white/10 bg-white/10 p-3 transition hover:bg-white/15';

    const content = (
        <>
            <span className="grid size-9 shrink-0 place-items-center rounded-xl bg-[#F4D9D5] text-[#6B2D3C]">
                {icon}
            </span>
            <span className="min-w-0 flex-1">
                <span className="block text-sm font-bold">{title}</span>
                <span className="mt-0.5 block truncate text-xs text-white/60">
                    {detail}
                </span>
            </span>
            <ArrowUpRight className="size-4 text-white/50 transition group-hover:translate-x-0.5 group-hover:-translate-y-0.5 group-hover:text-white" />
        </>
    );

    if (external) {
        return (
            <a
                href={href}
                target="_blank"
                rel="noreferrer"
                className={className}
            >
                {content}
            </a>
        );
    }

    if (native) {
        return (
            <a href={href} className={className}>
                {content}
            </a>
        );
    }

    return (
        <Link href={href} className={className}>
            {content}
        </Link>
    );
}

function StatCard({
    icon,
    label,
    value,
    detail,
    accent = false,
}: {
    icon: React.ReactNode;
    label: string;
    value: string | number;
    detail: string;
    accent?: boolean;
}) {
    return (
        <div className="rounded-[1.5rem] border border-[#6B2D3C]/10 bg-white/75 p-5 shadow-[0_9px_28px_rgba(59,42,42,0.08)] backdrop-blur-md">
            <div className="flex items-center justify-between">
                <span className="grid size-10 place-items-center rounded-xl bg-[#F4D9D5] text-[#6B2D3C]">
                    {icon}
                </span>
                <span
                    className={`size-2 rounded-full ${accent ? 'bg-[#C68B59]' : 'bg-[#72A67B]'}`}
                />
            </div>
            <p className="mt-5 text-xs font-bold tracking-[0.12em] text-[#3B2A2A]/45 uppercase">
                {label}
            </p>
            <p className="mt-1 font-display text-3xl text-[#6B2D3C]">{value}</p>
            <p className="mt-1 text-sm text-[#3B2A2A]/55">{detail}</p>
        </div>
    );
}

Dashboard.layout = {
    breadcrumbs: [],
};
