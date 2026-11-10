const roots = document.querySelectorAll<HTMLElement>('[data-cake-designer]');

if (roots.length > 0) {
    void import('./cake-designer').then(({ initCakeDesigner }) => {
        roots.forEach(initCakeDesigner);
    });
}
