export default function AppLogo() {
    return (
        <>
            <img
                src="/img/icono_delicias.png"
                alt="Delicias Sn"
                className="aspect-square size-8 rounded-xl object-cover"
            />
            <div className="ml-1 grid flex-1 text-left text-sm">
                <span className="mb-0.5 truncate font-display text-base leading-tight font-semibold text-[#6B2D3C]">
                    Delicias{' '}
                    <span className="font-script text-lg font-normal text-[#C98B8B]">
                        Sn
                    </span>
                </span>
            </div>
        </>
    );
}
