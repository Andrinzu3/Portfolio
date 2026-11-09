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

// -> Filmstreifen-Clips
//    Computer: Clip läuft nur, solange die Maus auf dem Bild ist.
//    Smartphone: Streifen läuft von selbst, lässt sich mit dem Finger schieben,
//    Antippen lässt ihn weiterlaufen. Nur das Bild in der Mitte ist ein echtes Video.
function initFilmstreifen() {
    const streifen = document.querySelector('.filmstreifen--clips');
    if (!streifen) return;
    const band = streifen.querySelector('.filmstreifen__band');
    const spur = streifen.querySelector('.filmstreifen__spur');
    const bilder = [...streifen.querySelectorAll('.filmstreifen__bild')];
    const videos = bilder.map((b) => b.querySelector('video'));
    const hatMaus = window.matchMedia('(hover: hover)').matches;
    videos.forEach((v) => {
        v.pause();
        if (hatMaus || reduceMotion) hellPruefen(v);
    });
    if (reduceMotion) return;

    const play = (v) => v.play().catch(() => {});

    // Computer mit Maus
    if (hatMaus) {
        bilder.forEach((bild, i) => {
            bild.addEventListener('mouseenter', () => play(videos[i]));
            bild.addEventListener('mouseleave', () => videos[i].pause());
        });
        return;
    }

    // Smartphone / Tablet: nur EIN echtes Video (in der Mitte). Die anderen Bilder werden
    // zu Standbildern (<canvas>), sobald ihr erstes Bild bereitsteht – vorher bleibt das Video sichtbar.
    streifen.classList.add('filmstreifen--touch');
    const dauer = parseFloat(band.style.animationDuration) || 40; // Sekunden für eine Runde
    const srcs = videos.map((v) => v.getAttribute('src'));
    const dpr = Math.min(2, window.devicePixelRatio || 1);
    const w = Math.round(videos[0].clientWidth * dpr);
    const h = Math.round(videos[0].clientHeight * dpr);
    const elemente = [...videos]; // was gerade in jedem Bild steckt (Video oder Standbild)
    const akzent = getComputedStyle(streifen).getPropertyValue('--accent').trim() || '#F0A23B';

    // Standbild für einen Clip erstellen und überall einsetzen, wo dieser Clip vorkommt
    const fertig = {};
    const standbild = (video, src) => {
        if (src in fertig || video.readyState < 2 || !video.videoWidth) return;
        const hell = istHell(video);
        const vorlage = standbildMalen(video, w, h, akzent, hell);
        fertig[src] = hell;
        srcs.forEach((s, i) => {
            if (s !== src) return;
            const c = document.createElement('canvas');
            c.width = w;
            c.height = h;
            c.getContext('2d').drawImage(vorlage, 0, 0);
            if (i === aktiv) c.style.display = 'none';
            elemente[i].replaceWith(c);
            elemente[i] = c;
            bilder[i].classList.add('standbild');
        });
        if (srcs[aktiv] === src) spieler.classList.toggle('ist-hell', hell);
    };

    // Jeden Clip einmal kurz laden, um sein erstes Bild zu holen
    const lader = document.createElement('video');
    Object.assign(lader, { muted: true, playsInline: true, preload: 'auto' });
    lader.setAttribute('muted', '');
    lader.setAttribute('playsinline', '');
    lader.className = 'filmstreifen__lader';
    streifen.appendChild(lader);
    const offen = [...new Set(srcs)];
    const naechsterLaden = () => {
        const src = offen.shift();
        if (!src) { lader.remove(); return; }
        const weiter = () => {
            clearTimeout(notfall);
            standbild(lader, src);
            lader.pause();
            naechsterLaden();
        };
        const notfall = setTimeout(weiter, 3000); // falls ein Clip nicht lädt: nicht hängen bleiben
        lader.addEventListener('loadeddata', weiter, { once: true });
        lader.src = src;
        lader.play().catch(() => {});
    };
    naechsterLaden();

    // Das eine Video, das in der Mitte spielt
    const spieler = document.createElement('video');
    Object.assign(spieler, { muted: true, loop: true, playsInline: true });
    spieler.setAttribute('muted', '');
    spieler.setAttribute('playsinline', '');
    spieler.addEventListener('loadeddata', () => {
        const src = spieler.getAttribute('src');
        if (!(src in fertig)) spieler.classList.toggle('ist-hell', istHell(spieler));
        standbild(spieler, src); // falls der Lader es nicht geschafft hat
    });
    let aktiv = -1;
    const zeigen = (i) => {
        if (i === aktiv) return;
        if (aktiv > -1) {
            bilder[aktiv].classList.remove('spielt');
            elemente[aktiv].style.display = '';
        }
        aktiv = i;
        bilder[i].classList.add('spielt');
        elemente[i].style.display = 'none';
        bilder[i].appendChild(spieler);
        if (spieler.getAttribute('src') !== srcs[i]) {
            spieler.classList.toggle('ist-hell', !!fertig[srcs[i]]);
            spieler.src = srcs[i];
        }
        play(spieler);
    };

    let breite = spur.offsetWidth;
    let bildBreite = bilder[0].offsetWidth;
    let x = 0;
    let laeuft = true;
    let ziehen = null; // { startX, startPos, bewegt }
    let sichtbar = false;
    let zuletzt = performance.now();

    const setzen = () => {
        x = ((x % breite) - breite) % breite; // nahtlos im Kreis
        band.style.transform = `translate3d(${x}px, 0, 0)`;
    };
    const mitte = () => Math.floor((window.innerWidth / 2 - x) / bildBreite) % bilder.length;

    const schritt = (jetzt) => {
        if (!sichtbar) return;
        const dt = Math.min(0.1, (jetzt - zuletzt) / 1000);
        zuletzt = jetzt;
        if (laeuft && !ziehen) {
            x -= (breite / dauer) * dt;
            setzen();
        }
        zeigen(mitte());
        requestAnimationFrame(schritt);
    };

    // Finger: schieben = anhalten und selbst bewegen, antippen = weiterlaufen
    streifen.addEventListener('pointerdown', (e) => {
        ziehen = { startX: e.clientX, startPos: x, bewegt: false };
    });
    streifen.addEventListener('pointermove', (e) => {
        if (!ziehen) return;
        const dx = e.clientX - ziehen.startX;
        if (Math.abs(dx) > 8) {
            ziehen.bewegt = true;
            laeuft = false;
        }
        if (ziehen.bewegt) {
            x = ziehen.startPos + dx;
            setzen();
        }
    });
    streifen.addEventListener('pointerup', () => {
        if (ziehen && !ziehen.bewegt) laeuft = true; // angetippt -> läuft weiter
        ziehen = null;
    });
    streifen.addEventListener('pointercancel', () => { ziehen = null; }); // senkrecht gescrollt

    // Irgendwo anders hintippen -> Streifen läuft weiter (nicht beim Scrollen)
    let tippStart = null;
    document.addEventListener('pointerdown', (e) => {
        tippStart = streifen.contains(e.target) ? null : { x: e.clientX, y: e.clientY };
    });
    document.addEventListener('pointerup', (e) => {
        if (tippStart && Math.hypot(e.clientX - tippStart.x, e.clientY - tippStart.y) < 10) laeuft = true;
        tippStart = null;
    });
    document.addEventListener('pointercancel', () => { tippStart = null; });

    window.addEventListener('resize', () => {
        breite = spur.offsetWidth;
        bildBreite = bilder[0].offsetWidth;
    });

    new IntersectionObserver(([entry]) => {
        sichtbar = entry.isIntersecting;
        if (sichtbar) {
            zuletzt = performance.now();
            requestAnimationFrame(schritt);
        } else {
            spieler.pause();
        }
    }).observe(streifen);
}

// -> Aktuelles Bild eines Clips als orange eingefärbtes Standbild (ähnlicher Look wie per CSS).
//    Arbeitet nur mit Überblend-Modi, liest keine Pixel -> funktioniert immer.
function standbildMalen(video, w, h, akzent, hell) {
    const c = document.createElement('canvas');
    c.width = w;
    c.height = h;
    const ctx = c.getContext('2d');
    // wie object-fit: cover
    const vw = video.videoWidth;
    const vh = video.videoHeight;
    const s = Math.max(w / vw, h / vh);
    const sw = w / s;
    const sh = h / s;
    ctx.drawImage(video, (vw - sw) / 2, (vh - sh) / 2, sw, sh, 0, 0, w, h);
    ctx.globalCompositeOperation = 'saturation'; // schwarz-weiss
    ctx.fillStyle = '#808080';
    ctx.fillRect(0, 0, w, h);
    if (hell) {
        ctx.globalCompositeOperation = 'difference'; // umkehren
        ctx.fillStyle = '#FFFFFF';
        ctx.fillRect(0, 0, w, h);
    }
    ctx.globalCompositeOperation = 'multiply'; // orange färben
    ctx.fillStyle = akzent;
    ctx.fillRect(0, 0, w, h);
    return c;
}

// -> Ist der Clip hell (z. B. Zeichnung auf Weiss)? Falls nicht messbar: ja.
function istHell(video) {
    try {
        const c = document.createElement('canvas');
        c.width = 32;
        c.height = 18;
        const ctx = c.getContext('2d', { willReadFrequently: true });
        ctx.drawImage(video, 0, 0, c.width, c.height);
        const d = ctx.getImageData(0, 0, c.width, c.height).data;
        let summe = 0;
        for (let i = 0; i < d.length; i += 4) summe += 0.2126 * d[i] + 0.7152 * d[i + 1] + 0.0722 * d[i + 2];
        return summe / (d.length / 4) / 255 > 0.5;
    } catch (e) {
        return true;
    }
}

// -> Prüft, ob ein Clip hell ist (z. B. Zeichnung auf weissem Grund).
//    Helle Clips werden umgekehrt, damit sie auf dem dunklen Streifen gut sichtbar sind.
function hellPruefen(video) {
    const messen = () => {
        try {
            const c = document.createElement('canvas');
            c.width = 32;
            c.height = 18;
            const ctx = c.getContext('2d', { willReadFrequently: true });
            ctx.drawImage(video, 0, 0, c.width, c.height);
            const d = ctx.getImageData(0, 0, c.width, c.height).data;
            let summe = 0;
            for (let i = 0; i < d.length; i += 4) {
                summe += 0.2126 * d[i] + 0.7152 * d[i + 1] + 0.0722 * d[i + 2];
            }
            const hell = summe / (d.length / 4) / 255; // 0 = schwarz, 1 = weiss
            video.classList.toggle('ist-hell', hell > 0.5);
        } catch (e) {
            // Video nicht lesbar -> so lassen
        }
    };
    if (video.readyState >= 2) messen();
    else video.addEventListener('loadeddata', messen, { once: true });
}

initIntro();
initScrollName();
initFilmstreifen();
initDevelop();

initIntro();
initScrollName();
initFilmstreifen();
initDevelop();