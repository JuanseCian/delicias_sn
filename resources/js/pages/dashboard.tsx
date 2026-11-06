import { Head, Link } from '@inertiajs/react';
import { ArrowUpRight, ImagePlus, LayoutGrid, Store } from 'lucide-react';
import { dashboard } from '@/routes';

type Props = {
    stats: {
        products: number;
        categories: number;
        with_images: number;
    };
};

export default function Dashboard({ stats }: Props) {
    return (
        <>
            <Head title="Panel de gestión" />
            <div className="min-h-full bg-[#FBF5EE] p-4 text-[#3B2A2A] sm:p-6 lg:p-8">
                <div className="mx-auto max-w-7xl">
                    <div className="flex flex-col justify-between gap-5 sm:flex-row sm:items-end">
                        <div>
                            <p className="text-xs font-bold tracking-[0.18em] text-[#C98B8B] uppercase">
                                Administración
                            </p>
                            <h1 className="mt-2 font-display text-4xl tracking-tight text-[#6B2D3C] sm:text-5xl">
                                Hola, equipo{' '}
                                <span className="font-script text-5xl font-normal text-[#C98B8B]">
                                    Delicias
                                </span>
                            </h1>
                            <p className="mt-3 max-w-xl text-sm leading-6 text-[#3B2A2A]/60">
                                Desde acá podés mantener el catálogo listo para
                                que cada producto se vea tan rico como en
                                persona.
                            </p>
                        </div>
                        <Link
                            href="/gestion-delicias-sn-privada/productos"
                            className="inline-flex h-12 items-center justify-center gap-2 rounded-full bg-[#6B2D3C] px-5 text-sm font-bold text-white shadow-[0_8px_22px_rgba(107,45,60,0.16)] transition hover:-translate-y-0.5 hover:bg-[#3B2A2A]"
                        >
                            <ImagePlus className="size-4" /> Cargar imágenes
                        </Link>
                    </div>

                    <div className="mt-8 grid gap-4 md:grid-cols-3">
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
                                stats.with_images === stats.products
                                    ? 'Catálogo completo'
                                    : 'Faltan imágenes reales'
                            }
                            accent={stats.with_images < stats.products}
                        />
                    </div>

                    <section className="mt-6 grid gap-5 lg:grid-cols-[1.2fr_0.8fr]">
                        <div className="rounded-[2rem] border border-[#6B2D3C]/10 bg-white p-6 shadow-[0_9px_28px_rgba(59,42,42,0.08)] sm:p-8">
                            <div className="flex items-start justify-between gap-4">
                                <div>
                                    <p className="text-xs font-bold tracking-[0.16em] text-[#C98B8B] uppercase">
                                        Próximo paso
                                    </p>
                                    <h2 className="mt-2 font-display text-3xl text-[#6B2D3C]">
                                        Dale vida al catálogo
                                    </h2>
                                </div>
                                <span className="grid size-11 place-items-center rounded-2xl bg-[#F4D9D5] text-[#6B2D3C]">
                                    <ImagePlus className="size-5" />
                                </span>
                            </div>
                            <p className="mt-4 max-w-lg text-sm leading-6 text-[#3B2A2A]/60">
                                Las fotos reales hacen que tus productos se
                                luzcan. Subí una imagen por producto y se va a
                                mostrar automáticamente en la landing, el
                                detalle y el carrito.
                            </p>
                            <Link
                                href="/gestion-delicias-sn-privada/productos"
                                className="mt-6 inline-flex items-center gap-2 text-sm font-bold text-[#6B2D3C] transition hover:text-[#C98B8B]"
                            >
                                Ir a imágenes{' '}
                                <ArrowUpRight className="size-4" />
                            </Link>
                        </div>
                        <div className="rounded-[2rem] bg-[#6B2D3C] p-6 text-[#FBF5EE] shadow-[0_18px_55px_rgba(107,45,60,0.16)] sm:p-8">
                            <p className="text-xs font-bold tracking-[0.16em] text-[#F4D9D5] uppercase">
                                Acceso rápido
                            </p>
                            <h2 className="mt-3 font-display text-3xl">
                                Ver cómo lo ven tus clientes
                            </h2>
                            <p className="mt-3 text-sm leading-6 text-white/65">
                                Abrí la landing pública para revisar el catálogo
                                y el flujo de reserva.
                            </p>
                            <a
                                href="/"
                                target="_blank"
                                rel="noreferrer"
                                className="mt-6 inline-flex items-center gap-2 rounded-full bg-[#F4D9D5] px-4 py-3 text-sm font-bold text-[#6B2D3C] transition hover:bg-white"
                            >
                                Abrir landing{' '}
                                <ArrowUpRight className="size-4" />
                            </a>
                        </div>
                    </section>
                </div>
            </div>
        </>
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
        <div className="rounded-[1.5rem] border border-[#6B2D3C]/10 bg-white p-5 shadow-[0_9px_28px_rgba(59,42,42,0.08)]">
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
    breadcrumbs: [
        {
            title: 'Dashboard',
            href: dashboard(),
        },
    ],
};
