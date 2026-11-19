import { createRoot } from 'react-dom/client';

import { AnimatedTicket } from '@/components/ui/ticket-confirmation-card';
import type { TicketDetail, TransferData } from '@/components/ui/ticket-confirmation-card';

const root = document.querySelector<HTMLElement>('[data-ticket-confirmation]');

if (root) {
    const date = new Date(root.dataset.date || '');
    let details: TicketDetail[] = [];
    let transfer: TransferData | undefined;

    try {
        transfer = JSON.parse(root.dataset.transfer || 'null') ?? undefined;
    } catch {
        transfer = undefined;
    }

    try {
        details = JSON.parse(root.dataset.details || '[]') as TicketDetail[];
    } catch {
        details = [];
    }

    createRoot(root).render(
        <AnimatedTicket
            ticketId={root.dataset.ticketId || ''}
            amount={Number(root.dataset.amount || 0)}
            date={Number.isNaN(date.getTime()) ? new Date() : date}
            cardHolder={root.dataset.cardHolder || 'Delicias Sn'}
            whatsappUrl={root.dataset.whatsappUrl}
            deposit={root.dataset.deposit ? Number(root.dataset.deposit) : undefined}
            transfer={transfer}
            details={details}
            logoSrc={root.dataset.logo}
        />,
    );
}
