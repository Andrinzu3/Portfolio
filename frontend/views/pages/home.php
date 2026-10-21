<?php
$medien = get_intro_medien();
$untertitel = carbon_get_theme_option('untertitel') ?: 'Multimedia Production';
$bereiche = get_bereiche();
$projekte = get_projekt_ids();
?>
<section class="hero" data-hero>
    <div class="hero__media" aria-hidden="true">
        <?php foreach ($medien as $m) : ?>
            <?php if ($m['type'] === 'image') : ?>
                <img class="hero__frame" data-intro-frame src="<?= esc_url($m['src']) ?>" alt="">
            <?php else : ?>
                <video class="hero__frame" data-intro-frame src="<?= esc_url($m['src']) ?>" muted autoplay loop playsinline preload="auto"></video>
            <?php endif; ?>
        <?php endforeach; ?>
        <div class="hero__shade" data-intro-shade></div>
    </div>

    <div class="hero__name" data-scroll-name>
        <div class="hero__grow">
            <h1 class="hero__title">
                <span class="sr-only">Andrin Zünd</span>
                <?php get_template_part('frontend/views/layout/wordmark') ?>
            </h1>
        </div>
    </div>

    <p class="hero__sub" data-scroll-fade><?= esc_html($untertitel) ?></p>
</section>

<div class="after-hero">
    <?php if ($bereiche) : ?>
        <div class="bereiche">
            <?php foreach ($bereiche as $b) : ?>
                <span class="br"><?= esc_html($b) ?></span>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <section id="projekte" class="section">
        <h2 class="section-title">Ausgewählte Arbeiten</h2>
        <div class="projekt-grid">
            <?php foreach ($projekte as $id) : ?>
                <?php get_template_part('frontend/views/teaser/projekt', null, ['data' => get_projekt_data($id)]) ?>
            <?php endforeach; ?>
        </div>
    </section>
</div>