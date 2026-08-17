document.addEventListener('livewire:init', () => {
    Livewire.on('share-copied', (event) => {
        const url = event.url ?? event[0]?.url;

        if (url && navigator.clipboard) {
            navigator.clipboard.writeText(url);
        }
    });
});
