export default function AppLogo() {
    return (
        <>
            <img
                src="/img/icono_delicias.png"
                alt=""
                className="aspect-square size-9 rounded-xl object-cover shadow-sm"
            />
            <img
                src="/img/delicias_nombre.png"
                alt="Delicias Pastelería"
                className="ml-1 h-10 min-w-0 flex-1 object-contain object-left group-data-[collapsible=icon]:hidden"
            />
        </>
    );
}
