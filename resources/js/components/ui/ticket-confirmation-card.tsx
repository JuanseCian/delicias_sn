import { ArrowUpRight, CakeSlice, CalendarHeart, Check, Copy, Heart, Landmark, Truck, UserRound, X } from 'lucide-react';
import * as React from 'react';
import { createPortal } from 'react-dom';

import { cn } from '@/lib/utils';

// --- Íconos y piezas decorativas ---

const AnimatedCheck = () => (
    <svg
        viewBox="0 0 52 52"
        className="dt-check h-12 w-12"
        fill="none"
        stroke="currentColor"
        strokeWidth="3.2"
        strokeLinecap="round"
        strokeLinejoin="round"
        aria-hidden="true"
    >
        <circle className="dt-check-circle" cx="26" cy="26" r="22" />
        <path className="dt-check-tick" d="M16 27.5 23 34l13.5-15" />
    </svg>
);

const ScallopedEdge = ({ className }: { className?: string }) => (
    <svg
        viewBox="0 0 320 12"
        preserveAspectRatio="none"
        className={cn('block h-3 w-full', className)}
        aria-hidden="true"
    >
        <path
            fill="currentColor"
            d={`M0 0H320V0${Array.from({ length: 16 })
                .map((_, index) => {
                    const x = 320 - index * 20;

                    return `Q${x - 10} 12 ${x - 20} 0`;
                })
                .join('')}Z`}
        />
    </svg>
);

const WhatsAppIcon = (props: React.SVGProps<SVGSVGElement>) => (
    <svg {...props} viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
        <path d="M12.04 2a9.9 9.9 0 0 0-8.5 14.98L2 22l5.17-1.5A9.94 9.94 0 1 0 12.04 2Zm0 18.13a8.2 8.2 0 0 1-4.18-1.15l-.3-.18-3.07.89.9-2.99-.2-.31a8.2 8.2 0 1 1 6.85 3.74Zm4.5-6.14c-.25-.12-1.46-.72-1.68-.8-.23-.08-.39-.12-.56.12-.16.25-.64.8-.78.97-.14.16-.29.18-.53.06a6.7 6.7 0 0 1-3.32-2.9c-.25-.43.25-.4.71-1.33.08-.16.04-.3-.02-.43-.06-.12-.56-1.34-.76-1.84-.2-.48-.4-.41-.56-.42h-.47a.9.9 0 0 0-.66.3c-.22.25-.86.85-.86 2.07s.88 2.4 1 2.56c.13.17 1.74 2.65 4.2 3.72 1.56.67 2.17.73 2.95.62.48-.07 1.46-.6 1.67-1.18.2-.58.2-1.07.14-1.18-.06-.1-.22-.16-.47-.28Z" />
    </svg>
);

const DashedLine = () => (
    <div className="relative my-1 h-0" aria-hidden="true">
        <div className="absolute top-1/2 -left-4 h-8 w-8 -translate-y-1/2 rounded-full bg-crema shadow-[inset_-6px_0_10px_-8px_rgba(107,45,60,.25)]" />
        <div className="absolute top-1/2 -right-4 h-8 w-8 -translate-y-1/2 rounded-full bg-crema shadow-[inset_6px_0_10px_-8px_rgba(107,45,60,.25)]" />
        <div className="mx-5 border-t-2 border-dashed border-bordo/15" />
    </div>
);

const SprinkleShower = () => {
    const colors = ['#9B2739', '#1F5A46', '#C49B43', '#F3DED0', '#DCEAE0'];

    return (
        <div
            className="pointer-events-none fixed inset-0 z-0 overflow-hidden motion-reduce:hidden"
            aria-hidden="true"
        >
            {Array.from({ length: 70 }).map((_, index) => {
                const isHeart = index % 9 === 0;
                const style = {
                    left: `${(index * 37 + 11) % 100}%`,
                    color: colors[index % colors.length],
                    backgroundColor: isHeart ? undefined : colors[index % colors.length],
                    '--rot': `${(index * 47) % 360}deg`,
                    '--sway': `${((index * 13) % 9) - 4}vw`,
                    animationDuration: `${3 + (index % 6) * 0.35}s`,
                    animationDelay: `${(index % 10) * 0.14}s`,
                } as React.CSSProperties;

                return isHeart ? (
                    <Heart key={index} className="dt-sprinkle absolute h-3.5 w-3.5 fill-current" style={style} />
                ) : (
                    <span
                        key={index}
                        className={cn('dt-sprinkle absolute rounded-full', index % 3 === 0 ? 'h-2 w-2' : 'h-3.5 w-1.5')}
                        style={style}
                    />
                );
            })}
        </div>
    );
};

// --- Panel lateral con datos de transferencia ---

export interface TransferData {
    alias: string;
    cbu: string;
    holder: string;
    cuil: string;
}

const CopyButton = ({ value, label }: { value: string; label: string }) => {
    const [copied, setCopied] = React.useState(false);

    const copy = async () => {
        try {
            await navigator.clipboard.writeText(value);
        } catch {
            const field = document.createElement('textarea');
            field.value = value;
            document.body.appendChild(field);
            field.select();
            document.execCommand('copy');
            field.remove();
        }

        setCopied(true);
        window.setTimeout(() => setCopied(false), 1800);
    };

    return (
        <button
            type="button"
            onClick={copy}
            aria-label={`Copiar ${label}`}
            className={cn(
                'inline-flex shrink-0 items-center gap-1.5 rounded-full border px-3 py-1.5 text-xs font-bold transition duration-300',
                copied
                    ? 'border-[#4c8060] bg-[#4c8060] text-white'
                    : 'border-bordo/15 bg-white text-bordo hover:border-bordo hover:bg-bordo hover:text-white',
            )}
        >
            {copied ? <Check className="h-3.5 w-3.5" /> : <Copy className="h-3.5 w-3.5" />}
            <span aria-live="polite">{copied ? 'Copiado' : 'Copiar'}</span>
        </button>
    );
};

const TransferPanel = ({
    open,
    onClose,
    transfer,
    deposit,
    remaining,
    ticketId,
    whatsappUrl,
}: {
    open: boolean;
    onClose: () => void;
    transfer: TransferData;
    deposit: string;
    remaining: string;
    ticketId: string;
    whatsappUrl?: string;
}) => {
    const [mounted, setMounted] = React.useState(open);
    const [visible, setVisible] = React.useState(false);
    const closeRef = React.useRef<HTMLButtonElement>(null);

    React.useEffect(() => {
        if (open) {
            setMounted(true);
            const frame = window.requestAnimationFrame(() =>
                window.requestAnimationFrame(() => setVisible(true)),
            );
            document.documentElement.style.overflow = 'hidden';

            return () => window.cancelAnimationFrame(frame);
        }

        setVisible(false);
        document.documentElement.style.overflow = '';
        const timer = window.setTimeout(() => setMounted(false), 450);

        return () => window.clearTimeout(timer);
    }, [open]);

    React.useEffect(() => {
        if (!visible) {
            return;
        }

        closeRef.current?.focus({ preventScroll: true });
        const onKey = (event: KeyboardEvent) => event.key === 'Escape' && onClose();
        window.addEventListener('keydown', onKey);

        return () => window.removeEventListener('keydown', onKey);
    }, [visible, onClose]);

    if (!mounted) {
        return null;
    }

    const fields = [
        { label: 'Alias', value: transfer.alias, mono: true },
        { label: 'CBU', value: transfer.cbu, mono: true },
        { label: 'Titular', value: transfer.holder, mono: false },
        { label: 'CUIL', value: transfer.cuil, mono: true },
    ];
    const allData = [
        ...fields.map((field) => `${field.label}: ${field.value}`),
        `Monto de la seña: ${deposit}`,
        `Referencia: ${ticketId}`,
    ].join('\n');

    return createPortal(
        <div className="fixed inset-0 z-[80]" role="dialog" aria-modal="true" aria-labelledby="transfer-panel-title">
            <div
                className={cn(
                    'absolute inset-0 bg-ink/50 backdrop-blur-[3px] transition-opacity duration-400',
                    visible ? 'opacity-100' : 'opacity-0',
                )}
                onClick={onClose}
                aria-hidden="true"
            />
            <aside
                className={cn(
                    'absolute inset-y-0 right-0 flex w-full max-w-md flex-col overflow-hidden bg-crema shadow-[-24px_0_70px_-20px_rgba(59,42,42,.45)] transition-transform duration-500 ease-[cubic-bezier(.16,1,.3,1)] sm:rounded-l-[2rem]',
                    visible ? 'translate-x-0' : 'translate-x-full',
                )}
            >
                <header className="relative overflow-hidden bg-bordo px-6 pt-6 pb-8 text-crema">
                    <div className="absolute -top-10 -right-6 h-32 w-32 rounded-full bg-rosa-viejo/30 blur-2xl" aria-hidden="true" />
                    <div className="relative flex items-start justify-between gap-4">
                        <div>
                            <p className="font-script text-3xl leading-none text-blush">Señá tu pedido</p>
                            <h2 id="transfer-panel-title" className="mt-1.5 font-display text-2xl">Datos de transferencia</h2>
                        </div>
                        <button
                            ref={closeRef}
                            type="button"
                            onClick={onClose}
                            aria-label="Cerrar datos de transferencia"
                            className="grid h-10 w-10 shrink-0 place-items-center rounded-full border border-white/20 bg-white/10 transition duration-300 hover:rotate-90 hover:bg-white/25"
                        >
                            <X className="h-4 w-4" />
                        </button>
                    </div>
                    <div className="relative mt-5 flex items-end justify-between rounded-2xl bg-white/10 px-4 py-3 ring-1 ring-white/15">
                        <div>
                            <p className="text-[10px] font-bold tracking-[0.16em] text-blush uppercase">Monto a transferir · 50%</p>
                            <p className="mt-1 font-display text-3xl">{deposit}</p>
                        </div>
                        <CopyButton value={deposit.replace(/[^\d]/g, '')} label="monto" />
                    </div>
                </header>

                <div className="flex-1 space-y-3 overflow-y-auto px-6 py-6">
                    {fields.map((field, index) => (
                        <div
                            key={field.label}
                            className={cn(
                                'flex items-center justify-between gap-3 rounded-2xl bg-white px-4 py-3.5 shadow-[0_8px_22px_-16px_rgba(107,45,60,.45)] transition-all duration-500 ease-[cubic-bezier(.16,1,.3,1)]',
                                visible ? 'translate-x-0 opacity-100' : 'translate-x-6 opacity-0',
                            )}
                            style={{ transitionDelay: visible ? `${150 + index * 60}ms` : '0ms' }}
                        >
                            <div className="min-w-0">
                                <p className="text-[10px] font-bold tracking-[0.16em] text-rosa-viejo uppercase">{field.label}</p>
                                <p className={cn('mt-0.5 break-all text-ink', field.mono ? 'font-mono text-[0.95rem] font-semibold' : 'font-semibold')}>
                                    {field.value}
                                </p>
                            </div>
                            <CopyButton value={field.value} label={field.label} />
                        </div>
                    ))}

                    <div className="flex items-center justify-between gap-3 rounded-2xl border border-dashed border-bordo/20 px-4 py-3">
                        <p className="text-xs leading-5 text-ink/60">
                            Copiá todos los datos juntos, con el monto y tu número de pedido.
                        </p>
                        <CopyButton value={allData} label="todos los datos" />
                    </div>

                    <ol className="space-y-2.5 rounded-2xl bg-blush/60 px-4 py-4 text-sm text-ink/75">
                        {[
                            `Transferí ${deposit} al alias o CBU.`,
                            `Poné ${ticketId} como referencia.`,
                            'Envianos el comprobante por WhatsApp.',
                            `El resto (${remaining}) lo abonás al recibir o retirar.`,
                        ].map((step, index) => (
                            <li key={step} className="flex gap-3">
                                <span className="grid h-5 w-5 shrink-0 place-items-center rounded-full bg-bordo text-[10px] font-bold text-white">
                                    {index + 1}
                                </span>
                                <span>{step}</span>
                            </li>
                        ))}
                    </ol>
                </div>

                {whatsappUrl ? (
                    <div className="border-t border-bordo/10 bg-crema px-6 py-5">
                        <a
                            href={whatsappUrl}
                            target="_blank"
                            rel="noopener"
                            className="flex w-full items-center justify-center gap-2.5 rounded-2xl bg-bordo px-5 py-3.5 text-sm font-bold text-white transition duration-300 hover:-translate-y-0.5 hover:bg-[#5a2532]"
                        >
                            <WhatsAppIcon className="h-5 w-5" />
                            Enviar comprobante por WhatsApp
                        </a>
                    </div>
                ) : null}
            </aside>
        </div>,
        document.body,
    );
};

// --- Ticket principal ---

export interface TicketDetail {
    label: string;
    value: string;
    icon?: 'calendar' | 'delivery' | 'person' | 'cake';
}

export interface TicketProps extends React.HTMLAttributes<HTMLDivElement> {
    ticketId: string;
    amount: number;
    date: Date;
    cardHolder: string;
    whatsappUrl?: string;
    deposit?: number;
    transfer?: TransferData;
    details?: TicketDetail[];
    logoSrc?: string;
    icon?: React.ReactNode;
    locale?: string;
    currency?: string;
    title?: string;
    description?: string;
}

const detailIcons = {
    calendar: CalendarHeart,
    delivery: Truck,
    person: UserRound,
    cake: CakeSlice,
};

const rowsCount = (details: TicketDetail[]) => details.length + 1;

const AnimatedTicket = React.forwardRef<HTMLDivElement, TicketProps>(
    (
        {
            className,
            ticketId,
            amount,
            date,
            cardHolder,
            whatsappUrl,
            deposit,
            transfer,
            details = [],
            logoSrc,
            icon,
            locale = 'es-AR',
            currency = 'ARS',
            title = '¡Reserva recibida!',
            description = 'Tu pedido quedó reservado con cariño',
            ...props
        },
        ref,
    ) => {
        const [showSprinkles, setShowSprinkles] = React.useState(false);
        const [transferOpen, setTransferOpen] = React.useState(false);
        const closeTransfer = React.useCallback(() => setTransferOpen(false), []);

        React.useEffect(() => {
            const mountTimer = window.setTimeout(() => setShowSprinkles(true), 250);
            const unmountTimer = window.setTimeout(() => setShowSprinkles(false), 6500);

            return () => {
                window.clearTimeout(mountTimer);
                window.clearTimeout(unmountTimer);
            };
        }, []);

        const money = new Intl.NumberFormat(locale, {
            style: 'currency',
            currency,
            maximumFractionDigits: 0,
        });
        const formattedAmount = money.format(amount);
        const depositAmount = deposit ?? Math.ceil(amount / 2);
        const formattedDeposit = money.format(depositAmount);
        const formattedRemaining = money.format(Math.max(amount - depositAmount, 0));
        const actionsDelay = 0.9 + rowsCount(details) * 0.08;
        const formattedDate = new Intl.DateTimeFormat(locale, {
            day: 'numeric',
            month: 'short',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            hour12: false,
        })
            .format(date)
            .replace(',', ' ·');

        const rows: TicketDetail[] = [
            { label: 'Titular', value: cardHolder, icon: 'person' },
            ...details,
        ];

        return (
            <>
                {showSprinkles && <SprinkleShower />}
                <div
                    ref={ref}
                    className={cn(
                        'dt-ticket relative z-10 w-full max-w-sm font-sans text-ink',
                        className,
                    )}
                    {...props}
                >
                    <div className="relative overflow-visible rounded-t-[2rem] bg-white shadow-[0_24px_60px_-18px_rgba(107,45,60,.35)]">
                        {/* Cabecera bordo */}
                        <div className="relative overflow-hidden rounded-t-[2rem] bg-bordo px-7 pt-7 pb-14 text-center text-crema">
                            <div className="absolute -top-10 -right-8 h-32 w-32 rounded-full bg-rosa-viejo/25 blur-xl" aria-hidden="true" />
                            <div className="absolute -bottom-12 -left-10 h-36 w-36 rounded-full bg-caramelo/25 blur-xl" aria-hidden="true" />
                            {logoSrc ? (
                                <img
                                    src={logoSrc}
                                    alt="Delicias Sn"
                                    className="dt-rise relative mx-auto h-10 w-10 rounded-full bg-crema object-contain p-1"
                                    style={{ '--d': '.15s' } as React.CSSProperties}
                                />
                            ) : null}
                            <p
                                className="dt-rise relative mt-3 font-script text-4xl leading-none text-blush"
                                style={{ '--d': '.25s' } as React.CSSProperties}
                            >
                                Gracias
                            </p>
                            <h1
                                className="dt-rise relative mt-1 font-display text-[1.7rem] leading-tight"
                                style={{ '--d': '.35s' } as React.CSSProperties}
                            >
                                {title}
                            </h1>
                            <p
                                className="dt-rise relative mt-1.5 text-sm text-crema/75"
                                style={{ '--d': '.45s' } as React.CSSProperties}
                            >
                                {description}
                            </p>
                        </div>

                        {/* Sello con check */}
                        <div className="relative -mt-10 flex justify-center">
                            <div className="dt-stamp grid h-20 w-20 place-items-center rounded-full bg-crema text-bordo shadow-[0_10px_30px_-10px_rgba(107,45,60,.5)] ring-4 ring-white">
                                {icon ?? <AnimatedCheck />}
                            </div>
                        </div>

                        <div className="px-7 pt-5 pb-6">
                            <div className="dt-rise grid grid-cols-2 gap-4" style={{ '--d': '.75s' } as React.CSSProperties}>
                                <div>
                                    <p className="eyebrow">Pedido</p>
                                    <p className="mt-2 font-mono text-sm font-semibold text-bordo">{ticketId}</p>
                                </div>
                                <div className="text-right">
                                    <p className="eyebrow">Total</p>
                                    <p className="mt-1.5 font-display text-2xl text-bordo">{formattedAmount}</p>
                                </div>
                            </div>
                            <p
                                className="dt-rise mt-3 text-xs text-ink/50"
                                style={{ '--d': '.8s' } as React.CSSProperties}
                            >
                                Reservado el {formattedDate}
                            </p>
                        </div>

                        <DashedLine />

                        <ul className="space-y-2.5 px-7 pt-6 pb-2">
                            {rows.map((row, index) => {
                                const Icon = detailIcons[row.icon ?? 'cake'];

                                return (
                                    <li
                                        key={row.label}
                                        className="dt-rise flex items-center gap-3 rounded-2xl bg-crema/80 px-3.5 py-2.5"
                                        style={{ '--d': `${0.85 + index * 0.08}s` } as React.CSSProperties}
                                    >
                                        <span className="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-blush text-bordo">
                                            <Icon className="h-4 w-4" strokeWidth={1.8} />
                                        </span>
                                        <div className="min-w-0">
                                            <p className="text-[10px] font-bold tracking-[0.14em] text-rosa-viejo uppercase">{row.label}</p>
                                            <p className="truncate text-sm font-semibold text-ink">{row.value}</p>
                                        </div>
                                    </li>
                                );
                            })}
                        </ul>

                        <div
                            className="dt-rise mx-7 mt-4 overflow-hidden rounded-3xl bg-gradient-to-br from-blush via-[#f8e6e1] to-[#f3dcc6] p-4 ring-1 ring-bordo/10"
                            style={{ '--d': `${actionsDelay}s` } as React.CSSProperties}
                        >
                            <div className="flex items-start justify-between gap-3">
                                <div>
                                    <p className="text-[10px] font-bold tracking-[0.16em] text-bordo/70 uppercase">Seña para confirmar · 50%</p>
                                    <p className="mt-1 font-display text-3xl leading-none text-bordo">{formattedDeposit}</p>
                                </div>
                                <span className="dt-pulse rounded-full bg-bordo px-2.5 py-1 text-[10px] font-bold tracking-[0.1em] text-white uppercase">Pendiente</span>
                            </div>
                            <p className="mt-2.5 text-xs leading-5 text-ink/65">
                                Para confirmar tu reserva abonás el 50% del pedido. El resto ({formattedRemaining}) lo pagás al recibirlo o retirarlo.
                            </p>
                            {transfer ? (
                                <button
                                    type="button"
                                    onClick={() => setTransferOpen(true)}
                                    aria-haspopup="dialog"
                                    className="group mt-3.5 flex w-full items-center justify-between gap-3 rounded-2xl bg-white/85 px-4 py-3 text-left text-sm font-bold text-bordo shadow-[0_8px_20px_-14px_rgba(107,45,60,.6)] ring-1 ring-bordo/10 transition duration-300 hover:-translate-y-0.5 hover:bg-white hover:ring-bordo/25"
                                >
                                    <span className="flex items-center gap-2.5">
                                        <span className="grid h-8 w-8 place-items-center rounded-full bg-bordo text-white">
                                            <Landmark className="h-4 w-4" strokeWidth={1.8} />
                                        </span>
                                        Ver datos para señar
                                    </span>
                                    <span className="text-lg transition-transform duration-300 group-hover:translate-x-1" aria-hidden="true">→</span>
                                </button>
                            ) : null}
                        </div>

                        {whatsappUrl ? (
                            <div
                                className="dt-rise px-7 pt-4 pb-7"
                                style={{ '--d': `${actionsDelay + 0.08}s` } as React.CSSProperties}
                            >
                                <a
                                    href={whatsappUrl}
                                    target="_blank"
                                    rel="noopener"
                                    className="group flex w-full items-center justify-center gap-2.5 rounded-2xl bg-bordo px-5 py-3.5 text-sm font-bold text-white shadow-[0_14px_28px_-14px_rgba(107,45,60,.9)] transition duration-300 hover:-translate-y-0.5 hover:bg-[#5a2532] active:translate-y-0"
                                >
                                    <WhatsAppIcon className="h-5 w-5" />
                                    Enviar por WhatsApp
                                    <ArrowUpRight className="h-4 w-4 transition-transform duration-300 group-hover:translate-x-0.5 group-hover:-translate-y-0.5" />
                                </a>
                                <p className="mt-2.5 text-center text-xs text-ink/50">
                                    Enviá el detalle completo de tu pedido.
                                </p>
                            </div>
                        ) : (
                            <div className="pb-5" />
                        )}
                    </div>
                    <ScallopedEdge className="-mt-px text-white drop-shadow-[0_8px_10px_rgba(107,45,60,.12)]" />
                </div>
                {transfer ? (
                    <TransferPanel
                        open={transferOpen}
                        onClose={closeTransfer}
                        transfer={transfer}
                        deposit={formattedDeposit}
                        remaining={formattedRemaining}
                        ticketId={ticketId}
                        whatsappUrl={whatsappUrl}
                    />
                ) : null}
            </>
        );
    },
);

AnimatedTicket.displayName = 'AnimatedTicket';

export { AnimatedTicket };
