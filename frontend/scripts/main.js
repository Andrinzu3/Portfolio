// ==========================================================
// Portfolio Andrin Zünd – Interaktionen
// ==========================================================

const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

// -> Intro: Ringe breiten sich hinter dem AZ-Logo aus wie bei einem Tropfen im Wasser.
//    Danach fliegen A und Z auseinander und werden zum Namen.
function initIntro() {
    const root = document.documentElement;
    const hero = document.querySelector('[data-hero]');
    const dauer = 1500; // ms, so lange steht das Logo in der Mitte (Aufprall nach 30 %)

    const finish = () => {
        root.classList.remove('intro-running');
        root.classList.add('intro-split'); // Logo geht auseinander -> Name
    };

    if (!hero || root.classList.contains('intro-seen') || reduceMotion) {
        finish();
        return;
    }

    root.style.setProperty('--intro-dauer', dauer + 'ms');
    root.classList.add('intro-play'); // Logo wächst, Ringe starten
    requestAnimationFrame(ringeZentrieren);
    setTimeout(finish, dauer);
}

// -> Ringe genau in die Mitte des AZ-Logos setzen
function ringeZentrieren() {
    const flaeche = document.querySelector('.intro-ringe');
    const ringe = flaeche && flaeche.querySelector('.hero__ringe');
    const teile = [...document.querySelectorAll('.wordmark .wm-a, .wordmark .wm-logo-z')];
    if (!ringe || !teile.length) return;
    const r = teile.map((t) => t.getBoundingClientRect());
    const links = Math.min(...r.map((x) => x.left));
    const rechts = Math.max(...r.map((x) => x.right));
    const oben = Math.min(...r.map((x) => x.top));
    const unten = Math.max(...r.map((x) => x.bottom));
    const m = flaeche.getBoundingClientRect();
    ringe.style.left = (links + rechts) / 2 - m.left + 'px';
    ringe.style.top = (oben + unten) / 2 - m.top + 'px';
}

// -> Scroll: Name wird grösser und verblasst
function initScrollName() {
    const name = document.querySelector('[data-scroll-name]');
    if (!name || reduceMotion) return;

    let ticking = false;
    const update = () => {
        const y = window.scrollY;
        const p = Math.min(1, y / 700);
        name.style.transform = `scale(${1 + p * 0.25})`;
        name.style.opacity = String(Math.max(0, 1 - y / 600));
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
    videos.forEach((v) => v.pause());
    if (reduceMotion) return;

    const play = (v) => v.play().catch(() => {});

    // Computer mit Maus: Vorschaubilder erst nach dem Laden der Seite holen (Seite ist so schneller fertig)
       if (hatMaus) {
        nachDemLaden(() => videos.forEach((v) => {
            v.addEventListener('loadedmetadata', () => abdecken(v));
            v.preload = 'metadata';
            v.load();
        }));
        window.addEventListener('resize', () => videos.forEach(abdecken));
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
    // Grösse eines Filmbilds erst beim Zeichnen messen (dann ist das Layout sicher fertig)
    const bildGroesse = () => {
        const cs = getComputedStyle(bilder[0]);
        const breiteInnen = bilder[0].clientWidth - parseFloat(cs.paddingLeft) - parseFloat(cs.paddingRight);
        return [Math.round(breiteInnen * dpr), Math.round(bilder[0].clientHeight * dpr)];
    };
    const elemente = [...videos]; // was gerade in jedem Bild steckt (Video oder Standbild)

    // Standbild für einen Clip erstellen und überall einsetzen, wo dieser Clip vorkommt
    const fertig = new Set(); // Clips, die schon ein Standbild haben
    const standbild = (video, src) => {
        if (fertig.has(src) || video.readyState < 2 || !video.videoWidth) return;
        const [w, h] = bildGroesse();
        if (!w || !h) return;
        const vorlage = standbildMalen(video, w, h);
        if (!vorlage) return; // Bild war noch nicht bereit -> später nochmals
        fertig.add(src);
        srcs.forEach((s, i) => {
            if (s !== src) return;
            const c = document.createElement('canvas');
            c.width = w;
            c.height = h;
            c.getContext('2d').drawImage(vorlage, 0, 0);
            elemente[i].replaceWith(c);
            elemente[i] = c;
            bilder[i].classList.add('standbild');
        });
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
        let erledigt = false;
        const weiter = () => {
            if (erledigt) return;
            erledigt = true;
            clearTimeout(notfall);
            standbild(lader, src);
            lader.pause();
            naechsterLaden();
        };
        const notfall = setTimeout(weiter, 3000); // falls ein Clip nicht lädt: nicht hängen bleiben
        lader.src = src;
        bildBereit(lader, weiter);
        lader.play().catch(() => {});
    };
    nachDemLaden(naechsterLaden); // Standbilder erst holen, wenn die Seite fertig geladen ist

    // Das eine Video, das in der Mitte spielt. Es liegt über dem Standbild und wird erst
    // sichtbar, wenn es wirklich ein Bild zeigt -> kein kurzes Schwarz beim Wechsel.
    const spieler = document.createElement('video');
    Object.assign(spieler, { muted: true, loop: true, playsInline: true });
    spieler.setAttribute('muted', '');
    spieler.setAttribute('playsinline', '');
    spieler.className = 'filmstreifen__spieler';
    let aktiv = -1;
    const zeigen = (i) => {
        if (i === aktiv) return;
        if (aktiv > -1) bilder[aktiv].classList.remove('spielt');
        aktiv = i;
        bilder[i].classList.add('spielt');
        spieler.classList.remove('bereit'); // erst einblenden, wenn ein Bild da ist
        bilder[i].appendChild(spieler);
        const src = srcs[i];
        if (spieler.getAttribute('src') !== src) spieler.src = src;
        bildBereit(spieler, () => {
            if (spieler.getAttribute('src') !== src) return;
            spieler.classList.add('bereit');
            standbild(spieler, src); // falls der Lader es nicht geschafft hat
        });
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
    // Es spielt das Bild in der Mitte der freien Fläche rechts vom Porträt (das Porträt steht vor dem Streifen)
    const ich = document.querySelector('.filmstreifen__ich');
    let spielpunkt = window.innerWidth / 2;
    const spielpunktMessen = () => {
        const rand = ich ? ich.querySelector('img, span').getBoundingClientRect().right : 0;
        spielpunkt = rand > 0 ? (rand + window.innerWidth) / 2 : window.innerWidth / 2;
    };
    spielpunktMessen();
    const ichBild = ich && ich.querySelector('img');
    if (ichBild && !ichBild.complete) ichBild.addEventListener('load', spielpunktMessen, { once: true });
    const mitte = () => Math.floor((spielpunkt - x) / bildBreite) % bilder.length;

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
            angehaltenBei = window.scrollY;
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

    // Seite rauf- oder runterscrollen -> Streifen läuft auch weiter
    let angehaltenBei = 0;
    window.addEventListener('scroll', () => {
        if (!laeuft && !ziehen && Math.abs(window.scrollY - angehaltenBei) > 30) laeuft = true;
    }, { passive: true });

    window.addEventListener('resize', () => {
        breite = spur.offsetWidth;
        bildBreite = bilder[0].offsetWidth;
        spielpunktMessen();
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

// -> Video füllt sein Filmbild ganz aus, ohne verzerrt zu werden (Ränder werden abgeschnitten).
//    Wie object-fit: cover, aber von Hand gerechnet, weil das in Safari nicht immer greift.
function abdecken(video) {
    const box = video.parentElement;
    if (!video.videoWidth || !box) return;
    const cs = getComputedStyle(box);
    const bw = box.clientWidth - parseFloat(cs.paddingLeft) - parseFloat(cs.paddingRight);
    const bh = box.clientHeight;
    const s = Math.max(bw / video.videoWidth, bh / video.videoHeight);
    Object.assign(video.style, {
        position: 'absolute',
        top: '50%',
        left: '50%',
        width: Math.ceil(video.videoWidth * s) + 'px',
        height: Math.ceil(video.videoHeight * s) + 'px',
        maxWidth: 'none',
        transform: 'translate(-50%, -50%)',
    });
}

// -> Etwas erst ausführen, wenn die Seite fertig geladen ist (Ladebalken voll)
function nachDemLaden(fn) {
    if (document.readyState === 'complete') fn();
    else window.addEventListener('load', fn, { once: true });
}

// -> Handy: "Über mich" mit Porträt erst beim Runterscrollen einblenden (beim Intro noch nicht sichtbar)
function initUeberMich() {
    const ich = document.querySelector('.filmstreifen__ich');
    if (!ich) return;
    const zeigen = () => {
        if (window.scrollY > 40) {
            ich.classList.add('ist-sichtbar');
            window.removeEventListener('scroll', zeigen);
        }
    };
    window.addEventListener('scroll', zeigen, { passive: true });
    zeigen();
}

// -> Aktuelles Bild eines Clips als schwarz-weisses Standbild (gleicher Look wie per CSS)
function standbildMalen(video, w, h) {
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
    try {
        // leer gezeichnet (iPhone liefert manchmal noch kein Bild)? -> abbrechen
        const d = ctx.getImageData(0, 0, w, h).data;
        let sichtbar = false;
        for (let i = 3; i < d.length; i += 400) if (d[i] > 0) { sichtbar = true; break; }
        if (!sichtbar) return null;
    } catch (e) {
        // Pixel nicht lesbar -> einfach weitermachen
    }
    ctx.globalCompositeOperation = 'saturation'; // schwarz-weiss
    ctx.fillStyle = '#808080';
    ctx.fillRect(0, 0, w, h);
    return c;
}

// -> Ruft "fertig" auf, sobald das Video wirklich ein Bild gezeichnet hat (wichtig fürs iPhone)
function bildBereit(video, fertig) {
    if ('requestVideoFrameCallback' in video) {
        video.requestVideoFrameCallback(() => fertig());
        return;
    }
    const pruefen = () => {
        if (video.currentTime > 0 && video.readyState >= 2) {
            video.removeEventListener('timeupdate', pruefen);
            fertig();
        }
    };
    video.addEventListener('timeupdate', pruefen);
}

initIntro();
initScrollName();
initFilmstreifen();
initUeberMich();
initDevelop();