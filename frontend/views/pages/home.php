<?php
$clips = get_filmstreifen_clips();
$projekte = get_projekt_ids();
$ich = get_ueber_mich_kurz();
?>
<div class="intro-ringe" aria-hidden="true">
    <div class="hero__ringe">
        <?php // Zwei Schläge ("dum dum"), pro Schlag zwei Ringe kurz hintereinander
        foreach ([0, 0.14, 0.55, 0.69] as $start) : ?>
            <span class="ring" style="--start: <?= $start ?>s"></span>
        <?php endforeach; ?>
    </div>
</div>
<section class="hero" data-hero>
    <div class="hero__name" data-scroll-name>
        <div class="hero__grow">
            <h1 class="hero__title">
                <span class="sr-only">Andrin Zünd</span>
                <?php get_template_part('frontend/views/layout/wordmark') ?>
            </h1>
        </div>
    </div>
</section>

<div class="after-hero">
    <?php if ($clips) : ?>
        <?php
        // -> Clips so oft wiederholen, dass der Streifen breiter als jeder Bildschirm ist (ca. 2700 px)
        $bilder = $clips;
        while (count($bilder) * 180 < 2700) {
            $bilder = array_merge($bilder, $clips);
        }
        $dauer = round(count($bilder) * 180 / 25); // gleichbleibendes, ruhiges Tempo
        ?>
        <div class="filmstreifen-wrap">
            <?php if ($ich['url']) : // Porträt hinter dem Streifen + Link "Über mich" ?>
                <a class="filmstreifen__ich" href="<?= esc_url($ich['url']) ?>">
                    <?php if ($ich['bild']) : ?>
                         <img src="<?= esc_url($ich['bild']['src']) ?>" alt="<?= esc_attr($ich['bild']['alt']) ?>" decoding="async">
                    <?php endif; ?>
                    <span class="filmstreifen__ich-text br">Über mich</span>
                </a>
            <?php endif; ?>
            <div class="filmstreifen filmstreifen--clips" aria-hidden="true">
                <div class="filmstreifen__band" style="animation-duration: <?= $dauer ?>s">
                    <?php for ($i = 0; $i < 2; $i++) : // zweimal hintereinander = nahtlose Schlaufe ?>
                        <div class="filmstreifen__spur">
                            <?php foreach ($bilder as $src) : ?>
                                <span class="filmstreifen__bild">
                                     <video src="<?= esc_url($src) ?>#t=0.1" muted loop playsinline preload="none"></video>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>
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