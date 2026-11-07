import { Link } from '@inertiajs/react';
import {
    ArrowUpRight,
    LayoutDashboard,
    PackageOpen,
    Plus,
    Store,
} from 'lucide-react';
import AppLogo from '@/components/app-logo';
import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import type { NavItem } from '@/types';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutDashboard,
    },
    {
        title: 'Productos',
        href: '/gestion-delicias-sn-privada/productos',
        icon: PackageOpen,
        native: true,
    },
];

export function AppSidebar() {
    return (
        <Sidebar collapsible="icon" variant="inset" className="p-2">
            <SidebarHeader className="gap-3 px-1 pt-1">
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton
                            size="lg"
                            asChild
                            className="h-14 rounded-2xl px-3 group-data-[collapsible=icon]:px-2 hover:bg-[#F4D9D5]/70"
                        >
                            <Link href={dashboard()} prefetch>
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>

                <div className="rounded-2xl border border-[#6B2D3C]/10 bg-white/65 p-3 shadow-[0_8px_24px_rgba(107,45,60,0.06)] backdrop-blur-md group-data-[collapsible=icon]:hidden">
                    <p className="text-[10px] font-bold tracking-[0.16em] text-[#C98B8B] uppercase">
                        Tu espacio
                    </p>
                    <p className="mt-1 text-sm font-semibold text-[#3B2A2A]">
                        Catálogo de Delicias
                    </p>
                    <p className="mt-1 text-xs leading-4 text-[#3B2A2A]/55">
                        Todo listo para mostrar tus productos.
                    </p>
                    <a
                        href="/gestion-delicias-sn-privada/productos/nuevo"
                        className="mt-3 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-[#6B2D3C] px-3 py-2 text-xs font-bold text-white transition hover:bg-[#3B2A2A]"
                    >
                        <Plus className="size-3.5" />
                        Nuevo producto
                    </a>
                </div>
            </SidebarHeader>

            <SidebarContent className="px-1">
                <NavMain items={mainNavItems} />
            </SidebarContent>

            <SidebarFooter className="gap-3 px-1 pb-1">
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton
                            asChild
                            tooltip={{ children: 'Vista pública' }}
                            className="h-10 rounded-xl text-[#3B2A2A]/65 hover:bg-[#F4D9D5]/60 hover:text-[#6B2D3C]"
                        >
                            <a href="/" target="_blank" rel="noreferrer">
                                <Store className="size-4" />
                                <span>Vista pública</span>
                                <ArrowUpRight className="ml-auto size-3.5" />
                            </a>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
