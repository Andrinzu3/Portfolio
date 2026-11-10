<!DOCTYPE html>
<html lang="<?php bloginfo('language') ?>">
<head>
    <meta charset="<?php bloginfo('charset') ?>">
    <meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover" />
    <meta name="theme-color" content="#12100E">
    <title><?php bloginfo('name') ?></title>
    <script>
        // -> Intro nur beim ersten Besuch oder beim Neuladen zeigen, nicht beim Navigieren innerhalb der Seite.
        //    Wer so weit unten neu lädt, dass der obere Teil nicht mehr sichtbar ist, sieht kein Intro.
        try {
            var nav = performance.getEntriesByType && performance.getEntriesByType('navigation')[0];
            var isReload = nav ? nav.type === 'reload' : false;
            var warUnten = isReload && parseFloat(sessionStorage.getItem('az-scroll') || '0') > window.innerHeight * 0.9;
            if ((sessionStorage.getItem('az-intro') && !isReload) || warUnten) {
                document.documentElement.classList.add('intro-seen');
            } else {
                // -> Header ist während dem Intro versteckt (Sicherheit: nach 5s sowieso wieder da)
                document.documentElement.classList.add('intro-running');
                if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                    document.documentElement.classList.add('intro-logo');
                }
                setTimeout(function () { document.documentElement.classList.remove('intro-running'); document.documentElement.classList.add('intro-play', 'intro-split'); }, 5000);
            }
            sessionStorage.setItem('az-intro', '1');
            // -> Beim Verlassen/Neuladen merken, wie weit unten man war
            window.addEventListener('pagehide', function () {
                try { sessionStorage.setItem('az-scroll', String(window.scrollY)); } catch (e) {}
            });
        } catch (e) {}
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Archivo:wght@400;500;600;700&family=Montserrat:wght@600;700;800&display=swap">
    <link rel="stylesheet" href="<?= get_template_directory_uri() ?>/frontend/styles/main.css?v=<?= filemtime(get_template_directory() . '/frontend/styles/main.css') ?>">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?> style="--accent: <?= esc_attr(get_page_accent()) ?>">