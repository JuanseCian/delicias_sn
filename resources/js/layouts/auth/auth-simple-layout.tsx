import { Link } from '@inertiajs/react';
import { home } from '@/routes';
import type { AuthLayoutProps } from '@/types';

export default function AuthSimpleLayout({
    children,
    title,
    description,
}: AuthLayoutProps) {
    return (
        <div className="relative flex min-h-svh flex-col items-center justify-center overflow-hidden bg-[#FBF5EE] p-5 text-[#3B2A2A] sm:p-8">
            <div className="pointer-events-none absolute top-12 -left-24 h-72 w-72 rounded-full bg-[#F4D9D5] opacity-70 blur-3xl" />
            <div className="pointer-events-none absolute -right-20 -bottom-32 h-96 w-96 rounded-full bg-[#C98B8B] opacity-20 blur-3xl" />
            <div className="relative z-10 w-full max-w-lg">
                <div className="flex flex-col gap-8">
                    <div className="flex flex-col items-center gap-5 text-center">
                        <Link
                            href={home()}
                            className="group flex items-center gap-3"
                        >
                            <img
                                src="/img/icono_delicias.png"
                                alt=""
                                className="h-12 w-12 rounded-full object-cover transition-transform group-hover:rotate-6"
                            />
                            <img
                                src="/img/delicias_nombre.png"
                                alt="Delicias Pastelería"
                                className="brand-wordmark h-12 w-44 object-contain object-left"
                            />
                        </Link>

                        <div className="space-y-2">
                            <h1 className="font-display text-4xl font-semibold tracking-tight text-[#6B2D3C] sm:text-5xl">
                                {title}
                            </h1>
                            <p className="text-base text-[#3B2A2A]/65">
                                {description}
                            </p>
                        </div>
                    </div>
                    <div className="rounded-[2rem] border border-white/70 bg-white p-7 shadow-[0_18px_55px_rgba(107,45,60,0.11)] sm:p-10">
                        {children}
                    </div>
                    <Link
                        href={home()}
                        className="text-center text-xs font-semibold tracking-[0.14em] text-[#6B2D3C]/60 uppercase transition hover:text-[#6B2D3C]"
                    >
                        ← Volver a la landing
                    </Link>
                </div>
            </div>
        </div>
    );
}
