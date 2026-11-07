import gsap from 'gsap';
import { useGSAP } from '@gsap/react';
import { useRef, type ReactNode } from 'react';

type Props = {
    children: ReactNode;
};

export default function AdjustableGlassDashboard({ children }: Props) {
    const dashboardRef = useRef<HTMLDivElement>(null);

    useGSAP(
        () => {
            if (
                window.matchMedia('(prefers-reduced-motion: reduce)').matches
            ) {
                return;
            }

            const sections = dashboardRef.current?.querySelectorAll(
                '[data-dashboard-reveal]',
            );

            if (!sections?.length) {
                return;
            }

            gsap.fromTo(
                sections,
                { opacity: 0, y: 14 },
                {
                    opacity: 1,
                    y: 0,
                    duration: 0.55,
                    ease: 'power2.out',
                    stagger: 0.07,
                },
            );
        },
        { scope: dashboardRef },
    );

    return (
        <div
            ref={dashboardRef}
            className="relative min-h-full overflow-hidden bg-[#FBF5EE] text-[#3B2A2A]"
        >
            <div className="pointer-events-none absolute -top-24 right-[-6rem] size-72 rounded-full bg-[#F4D9D5]/75 blur-3xl" />
            <div className="pointer-events-none absolute bottom-[-8rem] left-[-6rem] size-80 rounded-full bg-[#C98B8B]/15 blur-3xl" />
            {children}
        </div>
    );
}
