const clickSoundUrl = '/audio/click.mp3';
let clickAudio: HTMLAudioElement | null = null;
let publicClickSoundInitialized = false;

export function playClickSound() {
    if (typeof window === 'undefined') return;

    clickAudio ??= new Audio(clickSoundUrl);
    clickAudio.volume = 0.45;
    clickAudio.currentTime = 0;
    void clickAudio.play().catch(() => undefined);
}

export function initializePublicClickSound() {
    if (typeof document === 'undefined' || typeof window === 'undefined') return;
    if (publicClickSoundInitialized) return;

    publicClickSoundInitialized = true;

    document.addEventListener('click', (event) => {
        if (!window.location.pathname.startsWith('/kelas-publik/')) return;

        const target = event.target instanceof Element
            ? event.target.closest('a, button, input[type="radio"], input[type="checkbox"], select, [role="button"]')
            : null;

        if (target) playClickSound();
    }, true);
}