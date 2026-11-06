declare module 'alpinejs' {
    const Alpine: {
        data(name: string, callback: (...args: any[]) => any): void;
        start(): void;
    };

    export default Alpine;
}
