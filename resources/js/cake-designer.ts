import * as THREE from 'three';
import { OrbitControls } from 'three/addons/controls/OrbitControls.js';

type CakeOption = {
    id: number;
    kind: 'size' | 'coverage' | 'color' | 'topping' | 'template';
    slug: string;
    name: string;
    description?: string | null;
    metadata: Record<string, string | number | string[]>;
};

type CakeConfiguration = {
    size_id: number;
    coverage_id: number;
    color_ids: number[];
    topping_ids: number[];
    template_id: number | null;
};

const storageKey = 'delicias-sn-cake-design';

const colorFromOption = (option: CakeOption | undefined, fallback: string): string =>
    typeof option?.metadata.hex === 'string' ? option.metadata.hex : fallback;

export function initCakeDesigner(root: HTMLElement): void {
    const canvas = root.querySelector<HTMLCanvasElement>('[data-cake-canvas]');
    const stage = root.querySelector<HTMLElement>('[data-cake-stage]');
    const optionsScript = root.querySelector<HTMLScriptElement>('[data-cake-options]');

    if (!canvas || !stage || !optionsScript) {
        return;
    }

    const options = JSON.parse(optionsScript.textContent || '[]') as CakeOption[];
    const optionsById = new Map(options.map((option) => [option.id, option]));
    const optionsByKind = (kind: CakeOption['kind']): CakeOption[] =>
        options.filter((option) => option.kind === kind);
    const firstOption = (kind: CakeOption['kind']): CakeOption | undefined =>
        optionsByKind(kind)[0];

    const initial = (): CakeConfiguration => {
        const saved = localStorage.getItem(storageKey);

        if (saved) {
            try {
                const parsed = JSON.parse(saved) as CakeConfiguration;
                const size = optionsById.get(parsed.size_id);
                const coverage = optionsById.get(parsed.coverage_id);
                const colors = parsed.color_ids?.filter((id) => optionsById.get(id)?.kind === 'color') || [];
                const toppings = parsed.topping_ids?.filter((id) => optionsById.get(id)?.kind === 'topping') || [];

                if (size?.kind === 'size' && coverage?.kind === 'coverage' && colors.length > 0) {
                    return {
                        size_id: size.id,
                        coverage_id: coverage.id,
                        color_ids: colors.slice(0, 2),
                        topping_ids: toppings.slice(0, 8),
                        template_id: parsed.template_id ?? null,
                    };
                }
            } catch {
                localStorage.removeItem(storageKey);
            }
        }

        return {
            size_id: firstOption('size')?.id ?? 0,
            coverage_id: firstOption('coverage')?.id ?? 0,
            color_ids: firstOption('color') ? [firstOption('color')!.id] : [],
            topping_ids: [],
            template_id: null,
        };
    };

    let configuration = initial();
    let scene: THREE.Scene;
    let camera: THREE.PerspectiveCamera;
    let renderer: THREE.WebGLRenderer;
    let controls: OrbitControls;
    let cakeGroup: THREE.Group;

    const status = root.querySelector<HTMLElement>('[data-cake-status]');
    const setStatus = (message: string): void => {
        if (status) {
            status.textContent = message;
        }
    };

    const selected = (kind: CakeOption['kind']): CakeOption[] => {
        if (kind === 'size') {
            const option = optionsById.get(configuration.size_id);
            return option ? [option] : [];
        }
        if (kind === 'coverage') {
            const option = optionsById.get(configuration.coverage_id);
            return option ? [option] : [];
        }
        if (kind === 'color') {
            return configuration.color_ids.map((id) => optionsById.get(id)).filter((option): option is CakeOption => Boolean(option));
        }
        if (kind === 'topping') {
            return configuration.topping_ids.map((id) => optionsById.get(id)).filter((option): option is CakeOption => Boolean(option));
        }
        const option = optionsById.get(configuration.template_id ?? 0);
        return option ? [option] : [];
    };

    const updateButtons = (): void => {
        root.querySelectorAll<HTMLElement>('[data-cake-option]').forEach((button) => {
            const kind = button.dataset.optionKind as CakeOption['kind'];
            const id = Number(button.dataset.optionId);
            const isSelected = selected(kind).some((option) => option.id === id);
            button.dataset.selected = isSelected ? 'true' : 'false';
        });
    };

    const updateSummary = (): void => {
        const size = selected('size')[0];
        const coverage = selected('coverage')[0];
        const colors = selected('color');
        const toppings = selected('topping');
        const sizeMetadata = size?.metadata ?? {};
        const summary: Record<string, string> = {
            size: size?.name ?? '-',
            coverage: coverage?.name ?? '-',
            colors: colors.map((option) => option.name).join(' + ') || '-',
            servings: sizeMetadata.servings ? `${sizeMetadata.servings} aprox.` : '-',
            toppings: toppings.map((option) => option.name).join(', ') || 'Ninguna',
        };

        Object.entries(summary).forEach(([key, value]) => {
            const element = root.querySelector<HTMLElement>(`[data-cake-summary="${key}"]`);
            if (element) {
                element.textContent = value;
            }
        });
    };

    const persist = (): void => {
        localStorage.setItem(storageKey, JSON.stringify(configuration));
    };

    const material = (): THREE.MeshStandardMaterial => {
        const coverage = selected('coverage')[0];
        const colors = selected('color');
        const materialName = coverage?.metadata.material;

        return new THREE.MeshStandardMaterial({
            color: colorFromOption(colors[0], '#F4D9D5'),
            roughness: materialName === 'ganache' ? 0.28 : materialName === 'fondant' ? 0.62 : 0.82,
            metalness: materialName === 'ganache' ? 0.08 : 0,
        });
    };

    const addMesh = (geometry: THREE.BufferGeometry, meshMaterial: THREE.Material, position: THREE.Vector3, parent = cakeGroup): THREE.Mesh => {
        const mesh = new THREE.Mesh(geometry, meshMaterial);
        mesh.position.copy(position);
        parent.add(mesh);
        return mesh;
    };

    const addTopping = (option: CakeOption, radius: number, top: number, index: number): void => {
        const type = option.metadata.type;
        const angle = (index / Math.max(configuration.topping_ids.length, 1)) * Math.PI * 2;
        const distance = radius * 0.56;
        const position = new THREE.Vector3(Math.cos(angle) * distance, top, Math.sin(angle) * distance);

        if (type === 'strawberry' || type === 'cherry') {
            const color = type === 'strawberry' ? '#c94d5b' : '#8e263d';
            addMesh(new THREE.SphereGeometry(0.16, 20, 14), new THREE.MeshStandardMaterial({ color, roughness: 0.42 }), position);
            if (type === 'strawberry') {
                addMesh(new THREE.ConeGeometry(0.05, 0.12, 5), new THREE.MeshStandardMaterial({ color: '#4c8060' }), position.clone().add(new THREE.Vector3(0, 0.15, 0)));
            }
            return;
        }

        if (type === 'chocolate' || type === 'drip') {
            const chocolate = new THREE.MeshStandardMaterial({ color: '#5b3026', roughness: 0.28 });
            addMesh(type === 'drip' ? new THREE.CylinderGeometry(0.06, 0.06, 0.24, 12) : new THREE.BoxGeometry(0.2, 0.1, 0.14), chocolate, position);
            return;
        }

        if (type === 'macaron') {
            const macaron = new THREE.Group();
            const macaronMaterial = new THREE.MeshStandardMaterial({ color: index % 2 ? '#c98b8b' : '#c68b59', roughness: 0.72 });
            addMesh(new THREE.CylinderGeometry(0.14, 0.14, 0.08, 24), macaronMaterial, new THREE.Vector3(0, 0, 0), macaron);
            addMesh(new THREE.CylinderGeometry(0.1, 0.1, 0.03, 24), new THREE.MeshStandardMaterial({ color: '#fbf5ee' }), new THREE.Vector3(0, 0.05, 0), macaron);
            macaron.position.copy(position);
            macaron.rotation.y = angle;
            cakeGroup.add(macaron);
            return;
        }

        if (type === 'flower') {
            const flower = new THREE.Group();
            const petalMaterial = new THREE.MeshStandardMaterial({ color: index % 2 ? '#f4d9d5' : '#fffaf6', roughness: 0.64 });
            for (let petal = 0; petal < 5; petal += 1) {
                const petalAngle = (petal / 5) * Math.PI * 2;
                addMesh(new THREE.SphereGeometry(0.08, 12, 8), petalMaterial, new THREE.Vector3(Math.cos(petalAngle) * 0.1, 0, Math.sin(petalAngle) * 0.1), flower);
            }
            addMesh(new THREE.SphereGeometry(0.045, 12, 8), new THREE.MeshStandardMaterial({ color: '#c68b59' }), new THREE.Vector3(0, 0.03, 0), flower);
            flower.position.copy(position);
            cakeGroup.add(flower);
            return;
        }

        if (type === 'candles') {
            addMesh(new THREE.CylinderGeometry(0.035, 0.035, 0.42, 12), new THREE.MeshStandardMaterial({ color: '#c98b8b' }), position.clone().add(new THREE.Vector3(0, 0.2, 0)));
            addMesh(new THREE.ConeGeometry(0.06, 0.14, 12), new THREE.MeshStandardMaterial({ color: '#c68b59', emissive: '#c68b59', emissiveIntensity: 0.3 }), position.clone().add(new THREE.Vector3(0, 0.47, 0)));
            return;
        }

        if (type === 'topper') {
            const canvas = document.createElement('canvas');
            canvas.width = 512;
            canvas.height = 160;
            const context = canvas.getContext('2d');
            if (context) {
                context.fillStyle = '#6B2D3C';
                context.font = '700 64px Jost, sans-serif';
                context.textAlign = 'center';
                context.fillText('Tu idea', 256, 90);
            }
            const texture = new THREE.CanvasTexture(canvas);
            const sprite = new THREE.Sprite(new THREE.SpriteMaterial({ map: texture, transparent: true }));
            sprite.scale.set(1.2, 0.38, 1);
            sprite.position.set(0, top + 0.45, 0);
            cakeGroup.add(sprite);
            return;
        }

        const color = type === 'pearls' ? '#fffaf6' : '#c98b8b';
        addMesh(new THREE.SphereGeometry(0.045, 12, 8), new THREE.MeshStandardMaterial({ color, roughness: 0.45 }), position);
    };

    const disposeCake = (): void => {
        cakeGroup.traverse((object) => {
            const mesh = object as THREE.Mesh;
            if (mesh.geometry) {
                mesh.geometry.dispose();
            }
            if (mesh.material) {
                const materials = Array.isArray(mesh.material) ? mesh.material : [mesh.material];
                materials.forEach((meshMaterial) => {
                    const material = meshMaterial as THREE.Material & { map?: THREE.Texture };
                    material.map?.dispose();
                    material.dispose();
                });
            }
        });
        cakeGroup.clear();
    };

    const renderCake = (): void => {
        disposeCake();
        const size = selected('size')[0];
        const diameter = Number(size?.metadata.diameter_cm ?? 18);
        const height = Number(size?.metadata.height_cm ?? 7) / 5;
        const radius = diameter / 14;
        const cakeMaterial = material();
        const fillingMaterial = new THREE.MeshStandardMaterial({ color: '#fffaf6', roughness: 0.92 });
        const plateMaterial = new THREE.MeshStandardMaterial({ color: '#eadfd7', roughness: 0.45 });

        addMesh(new THREE.CylinderGeometry(radius * 1.16, radius * 1.16, 0.08, 64), plateMaterial, new THREE.Vector3(0, 0.04, 0));
        addMesh(new THREE.CylinderGeometry(radius, radius, height, 64), cakeMaterial, new THREE.Vector3(0, height / 2 + 0.1, 0));
        addMesh(new THREE.CylinderGeometry(radius * 1.01, radius * 1.01, 0.07, 64), fillingMaterial, new THREE.Vector3(0, height * 0.5 + 0.1, 0));
        addMesh(new THREE.CylinderGeometry(radius * 0.99, radius * 0.99, 0.08, 64), cakeMaterial, new THREE.Vector3(0, height + 0.12, 0));

        selected('color').slice(1).forEach((option, index) => {
            addMesh(new THREE.TorusGeometry(radius * 0.86, 0.035, 10, 48), new THREE.MeshStandardMaterial({ color: colorFromOption(option, '#C98B8B') }), new THREE.Vector3(0, height * (0.4 + index * 0.18) + 0.1, 0));
        });

        selected('topping').forEach((option, index) => addTopping(option, radius, height + 0.25, index));
        camera.lookAt(0, height * 0.58, 0);
    };

    const initializeScene = (): boolean => {
        try {
            scene = new THREE.Scene();
            camera = new THREE.PerspectiveCamera(30, 1, 0.1, 100);
            camera.position.set(0, 2.5, 5.2);
            renderer = new THREE.WebGLRenderer({ canvas, antialias: true, alpha: true, preserveDrawingBuffer: true });
            renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
            renderer.outputColorSpace = THREE.SRGBColorSpace;
            renderer.toneMapping = THREE.ACESFilmicToneMapping;
            renderer.toneMappingExposure = 1.15;
            controls = new OrbitControls(camera, canvas);
            controls.enableDamping = true;
            controls.minDistance = 3;
            controls.maxDistance = 8;
            controls.maxPolarAngle = Math.PI * 0.82;
            controls.target.set(0, 0.7, 0);

            scene.add(new THREE.HemisphereLight('#fffaf6', '#c68b59', 2.2));
            const keyLight = new THREE.DirectionalLight('#fffaf6', 3.2);
            keyLight.position.set(3, 5, 4);
            scene.add(keyLight);
            const fillLight = new THREE.DirectionalLight('#f4d9d5', 1.5);
            fillLight.position.set(-4, 2, -2);
            scene.add(fillLight);
            cakeGroup = new THREE.Group();
            scene.add(cakeGroup);

            const resize = (): void => {
                const width = stage.clientWidth;
                const height = stage.clientHeight;
                if (!width || !height) return;
                camera.aspect = width / height;
                camera.updateProjectionMatrix();
                renderer.setSize(width, height, false);
            };
            new ResizeObserver(resize).observe(stage);
            resize();
            renderCake();

            const animate = (): void => {
                controls.update();
                renderer.render(scene, camera);
                window.requestAnimationFrame(animate);
            };
            animate();
            window.addEventListener('pagehide', () => {
                controls.dispose();
                disposeCake();
                renderer.dispose();
            }, { once: true });
            return true;
        } catch {
            root.querySelector<HTMLElement>('[data-cake-fallback]')?.removeAttribute('hidden');
            canvas.hidden = true;
            return false;
        }
    };

    const applyTemplate = (option: CakeOption): void => {
        const metadata = option.metadata;
        const coverage = options.find((item) => item.kind === 'coverage' && item.slug === metadata.coverage);
        const color = options.find((item) => item.kind === 'color' && item.slug === metadata.color);
        const toppings = options.filter((item) => item.kind === 'topping' && Array.isArray(metadata.toppings) && metadata.toppings.includes(item.slug));
        configuration = {
            ...configuration,
            coverage_id: coverage?.id ?? configuration.coverage_id,
            color_ids: color ? [color.id] : configuration.color_ids,
            topping_ids: toppings.map((item) => item.id),
            template_id: option.id,
        };
        setStatus(`Diseño ${option.name.toLowerCase()} aplicado`);
    };

    root.querySelectorAll<HTMLElement>('[data-cake-option]').forEach((button) => {
        button.addEventListener('click', () => {
            const kind = button.dataset.optionKind as CakeOption['kind'];
            const id = Number(button.dataset.optionId);
            const option = optionsById.get(id);
            if (!option) return;
            if (kind === 'template') {
                applyTemplate(option);
            } else if (kind === 'size') {
                configuration.size_id = id;
                configuration.template_id = null;
            } else if (kind === 'coverage') {
                configuration.coverage_id = id;
                configuration.template_id = null;
            } else if (kind === 'color') {
                configuration.color_ids = configuration.color_ids.includes(id)
                    ? configuration.color_ids.filter((colorId) => colorId !== id)
                    : [...configuration.color_ids, id].slice(-2);
                configuration.template_id = null;
            } else if (kind === 'topping') {
                configuration.topping_ids = configuration.topping_ids.includes(id)
                    ? configuration.topping_ids.filter((toppingId) => toppingId !== id)
                    : [...configuration.topping_ids, id].slice(0, 8);
                configuration.template_id = null;
            }
            updateButtons();
            updateSummary();
            persist();
            renderCake();
        });
    });

    root.querySelector('[data-cake-reset]')?.addEventListener('click', () => {
        localStorage.removeItem(storageKey);
        configuration = {
            size_id: firstOption('size')?.id ?? 0,
            coverage_id: firstOption('coverage')?.id ?? 0,
            color_ids: firstOption('color') ? [firstOption('color')!.id] : [],
            topping_ids: [],
            template_id: null,
        };
        updateButtons();
        updateSummary();
        renderCake();
        setStatus('Diseño restablecido');
    });

    root.querySelector('[data-cake-camera]')?.addEventListener('click', () => {
        camera.position.set(0, 2.5, 5.2);
        controls.target.set(0, 0.7, 0);
        controls.update();
    });

    root.querySelector('[data-cake-download]')?.addEventListener('click', () => {
        const link = document.createElement('a');
        link.download = 'mi-torta-delicias-sn.png';
        link.href = renderer.domElement.toDataURL('image/png');
        link.click();
    });

    root.querySelector('[data-cake-whatsapp]')?.setAttribute('href', '#');
    root.querySelector('[data-cake-whatsapp]')?.addEventListener('click', (event) => {
        event.preventDefault();
        const phone = root.dataset.whatsapp || '';
        const summary = `Hola Delicias Sn! Quiero consultar por una torta personalizada de ${selected('size')[0]?.name ?? ''}, con ${selected('coverage')[0]?.name ?? ''}, colores ${selected('color').map((option) => option.name).join(' y ')} y detalles: ${selected('topping').map((option) => option.name).join(', ') || 'a definir'}.`;
        window.open(`https://wa.me/${phone}?text=${encodeURIComponent(summary)}`, '_blank', 'noopener');
    });

    const mobileSections = root.querySelectorAll<HTMLElement>('[data-cake-mobile-section]');
    const mobileTabs = root.querySelectorAll<HTMLButtonElement>('[data-cake-mobile-tab]');
    const setMobileSection = (section: string): void => {
        mobileSections.forEach((element) => element.classList.toggle('is-mobile-active', element.dataset.cakeMobileSection === section));
        mobileTabs.forEach((tab) => tab.classList.toggle('is-active', tab.dataset.cakeMobileTab === section));
    };

    mobileTabs.forEach((tab) => {
        tab.addEventListener('click', () => setMobileSection(tab.dataset.cakeMobileTab || 'stage'));
    });
    setMobileSection('stage');

    if (initializeScene()) {
        updateButtons();
        updateSummary();
        persist();
    }
}
