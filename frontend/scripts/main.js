// ==========================================================
// Portfolio Andrin Zünd – Interaktionen
// ==========================================================

const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

// -> Intro: Bilder blitzen mit harten Schnitten hinter dem AZ-Logo auf.
//    Das Logo steht in der Mitte, solange die Bilder laufen (jedes nur einmal) – danach wird es zum Namen.
function initIntro() {
    const root = document.documentElement;
    const media = document.querySelector('.hero__media');
    const frames = [...document.querySelectorAll('[data-intro-frame]')];
    const tempo = 180; // ms pro Bild (schnelle Schnitte)

    const finish = () => {
        root.classList.remove('intro-running');
        root.classList.add('intro-split'); // Logo geht auseinander -> Name
    };

    if (!media) {
        finish();
        return;
    }
    const introSeen = root.classList.contains('intro-seen');
    if (introSeen || reduceMotion) {
        media.classList.add('is-done');
        finish();
        return;
    }
    if (!frames.length) {
        // Keine Intro-Bilder: Logo steht kurz und geht dann auseinander
        media.classList.add('is-done');
        root.style.setProperty('--intro-dauer', '1000ms');
        root.classList.add('intro-play');
        setTimeout(finish, 1000);
        return;
    }

    // Logo steht so lange in der Mitte, wie die Bilder brauchen
    root.style.setProperty('--intro-dauer', frames.length * tempo + 'ms');

    // Erst starten, wenn die Bilder geladen sind (max. 2 Sekunden warten) -> keine leeren Blitzer
    const geladen = frames
        .filter((frame) => frame.tagName === 'IMG')
        .map((img) => (img.decode ? img.decode().catch(() => {}) : Promise.resolve()));
    const maxWarten = new Promise((resolve) => setTimeout(resolve, 2000));
    Promise.race([Promise.all(geladen), maxWarten]).then(() => starteBilder(frames, media, tempo, finish));
}

// -> Bilder nacheinander zeigen, jedes genau einmal
function starteBilder(frames, media, tempo, finish) {
    document.documentElement.classList.add('intro-play'); // Logo beginnt jetzt zu wachsen
    let index = 0;
    const show = (i) => {
        frames.forEach((frame, k) => frame.classList.toggle('is-active', k === i));
    };

    show(index);
    const timer = setInterval(() => {
        index += 1;
        if (index >= frames.length) {
            clearInterval(timer);
            media.classList.add('is-done');
            finish();
            frames.forEach((frame) => {
                if (frame.tagName === 'VIDEO') frame.pause();
            });
            return;
        }
        show(index);
    }, tempo);
}

// -> Scroll: Name wird grösser und verblasst, Untertitel blendet aus
function initScrollName() {
    const name = document.querySelector('[data-scroll-name]');
    const fade = document.querySelector('[data-scroll-fade]');
    if (!name || reduceMotion) return;

    let ticking = false;
    const update = () => {
        const y = window.scrollY;
        const p = Math.min(1, y / 700);
        name.style.transform = `scale(${1 + p * 0.25})`;
        name.style.opacity = String(Math.max(0, 1 - y / 600));
        if (fade) fade.style.opacity = String(Math.max(0, 1 - y / 200));
        ticking = false;
    };

    window.addEventListener('scroll', () => {
        if (!ticking) {
            window.requestAnimationFrame(update);
            ticking = true;
        }
    }, { passive: true });
    update();
}

// -> Bilder "entwickeln" sich nacheinander, sobald sie sichtbar werden
function initDevelop() {
    const images = [...document.querySelectorAll('[data-dev]')];
    if (!images.length) return;

    if (reduceMotion || !('IntersectionObserver' in window)) {
        images.forEach((img) => img.classList.add('is-developed'));
        return;
    }

    let queueAt = 0;
    const develop = (img) => {
        const now = Date.now();
        const at = Math.max(now, queueAt + 450);
        queueAt = at;
        setTimeout(() => img.classList.add('is-developed'), at - now);
    };

    const observer = new IntersectionObserver((entries) => {
        entries
            .filter((entry) => entry.isIntersecting)
            .forEach((entry) => {
                observer.unobserve(entry.target);
                develop(entry.target);
            });
    }, { threshold: 0.35 });

    images.forEach((img) => observer.observe(img));
}

initIntro();
initScrollName();
initDevelop();