<?php
$active = get_active_nav();
$nav = [
    'projekte'   => ['Projekte', home_url('/#projekte')],
    'ueber-mich' => ['Über mich', get_page_url_by_template('template-ueber-mich.php')],
    'kontakt'    => ['Kontakt', '#kontakt'],
];
?>
<div class="grain" aria-hidden="true">
    <svg width="100%" height="100%">
        <filter id="grain-noise">
            <feTurbulence type="fractalNoise" baseFrequency="0.9" numOctaves="3" stitchTiles="stitch"/>
            <feColorMatrix type="saturate" values="0"/>
        </filter>
        <rect width="100%" height="100%" filter="url(#grain-noise)"/>
    </svg>
</div>

<header class="site-header">
    <a class="site-header__logo" href="<?= esc_url(home_url('/')) ?>" aria-label="Andrin Zünd – Startseite">
        <?php get_template_part('frontend/views/layout/logo') ?>
    </a>
    <nav class="site-header__nav" aria-label="Hauptnavigation">
        <?php foreach ($nav as $key => [$label, $url]) : ?>
            <a href="<?= esc_url($url) ?>"<?= $key === $active ? ' class="is-active br" aria-current="page"' : '' ?>><?= esc_html($label) ?></a>
        <?php endforeach; ?>
    </nav>
</header>

<main class="site-main">