import { createRoot } from 'react-dom/client';
import ProductRailGallery, {
    type RailProduct,
} from '@/components/ui/product-rail-gallery';

const root = document.querySelector<HTMLElement>('[data-product-rail]');

if (root) {
    const isHome = root.dataset.context !== 'catalog';
    const itemsScript = root.querySelector<HTMLScriptElement>(
        '[data-product-rail-items]',
    );
    const items = JSON.parse(itemsScript?.textContent || '[]') as RailProduct[];

    createRoot(root).render(
        <ProductRailGallery
            items={items}
            eyebrow={root.dataset.railEyebrow || undefined}
            heading={root.dataset.railHeading || undefined}
            autoplay
            interval={2000}
            action={
                isHome ? (
                    <a
                        href={root.dataset.catalogUrl || '/productos'}
                        target=""
                        rel="noreferrer"
                        className="btn-secondary"
                    >
                        Ver más productos <span aria-hidden="true">↗</span>
                    </a>
                ) : undefined
            }
            onChoose={
                isHome
                    ? (product) => {
                          window.dispatchEvent(
                              new CustomEvent('delicias-open-product', {
                                  detail: product.id,
                              }),
                          );
                      }
                    : undefined
            }
        />,
    );
}
