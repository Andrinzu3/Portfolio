// ==========================================================
// Portfolio Andrin Zünd – Interaktionen
// ==========================================================
 
const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
 
// -> Intro: Bilder blitzen mit harten Schnitten hinter dem Namen auf
function initIntro() {
    const root = document.documentElement;
    const media = document.querySelector('.hero__media');
    const frames = [...document.querySelectorAll('[data-intro-frame]')];
    const finish = () => root.classList.remove('intro-running');
    if (!media) {
        finish();
        return;
    }
    const introSeen = root.classList.contains('intro-seen');
    if (!frames.length || reduceMotion || introSeen) {
        media.classList.add('is-done');
        finish();
        return;
    }
 
    const tempo = 180; // ms pro Bild
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
