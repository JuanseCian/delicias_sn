import AppLogo from '@/components/app-logo';
import { useSidebar } from '@/components/ui/sidebar';

export function AppSidebarHeader() {
    const { openMobile, toggleSidebar } = useSidebar();

    return (
        <header className="sticky top-0 z-10 flex h-16 shrink-0 items-center border-b border-[#6B2D3C]/10 bg-[#FBF5EE]/80 px-4 backdrop-blur-xl md:hidden sm:px-6">
            <button
                type="button"
                onClick={toggleSidebar}
                aria-label={openMobile ? 'Cerrar menú lateral' : 'Abrir menú lateral'}
                className="rounded-2xl p-1 transition hover:bg-[#F4D9D5]/70 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#6B2D3C]"
            >
                <AppLogo />
            </button>
        </header>
    );
}
