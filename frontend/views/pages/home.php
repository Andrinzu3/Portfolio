<?php
$medien = get_intro_medien();
$untertitel = carbon_get_theme_option('untertitel') ?: 'Multimedia Production';
$bereiche = get_bereiche();
$clips = get_filmstreifen_clips();
$umkehren = carbon_get_theme_option('filmstreifen_umkehren');
$projekte = get_projekt_ids();
?>
<section class="hero" data-hero>
    <div class="hero__media" aria-hidden="true">
        <?php foreach ($medien as $i => $m) : ?>
            <?php if ($m['type'] === 'image') : ?>
                <img class="hero__frame" data-intro-frame src="<?= esc_url($m['src']) ?>"<?php if ($m['srcset']) : ?> srcset="<?= esc_attr($m['srcset']) ?>" sizes="100vw"<?php endif; ?> alt="" decoding="async"<?= $i === 0 ? ' fetchpriority="high"' : '' ?>>
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
    <?php if ($clips) : ?>
        <?php
        // -> Clips so oft wiederholen, dass der Streifen breiter als jeder Bildschirm ist
        $bilder = $clips;
        while (count($bilder) < 24) {
            $bilder = array_merge($bilder, $clips);
        }
        $dauer = round(count($bilder) * 180 / 25); // gleichbleibendes, ruhiges Tempo
        ?>
        <div class="filmstreifen filmstreifen--clips<?= $umkehren ? ' filmstreifen--umkehren' : '' ?>" aria-hidden="true">
            <div class="filmstreifen__band" style="animation-duration: <?= $dauer ?>s">
                <?php for ($i = 0; $i < 2; $i++) : // zweimal hintereinander = nahtlose Schlaufe ?>
                    <div class="filmstreifen__spur">
                        <?php foreach ($bilder as $src) : ?>
                            <span class="filmstreifen__bild">
                                <video src="<?= esc_url($src) ?>" muted autoplay loop playsinline preload="metadata"></video>
                            </span>
                        <?php endforeach; ?>
                    </div>
                <?php endfor; ?>
            </div>
        </div>
    <?php elseif ($bereiche) : ?>
        <?php
        // -> Wörter so oft wiederholen, dass der Streifen breiter als der Bildschirm ist
        $bilder = $bereiche;
        while (count($bilder) < 10) {
            $bilder = array_merge($bilder, $bereiche);
        }
        ?>
        <div class="filmstreifen">
            <span class="sr-only"><?= esc_html(implode(', ', $bereiche)) ?></span>
            <div class="filmstreifen__band" aria-hidden="true">
                <?php for ($i = 0; $i < 2; $i++) : // zweimal hintereinander = nahtlose Schlaufe ?>
                    <div class="filmstreifen__spur">
                        <?php foreach ($bilder as $b) : ?>
                            <span class="filmstreifen__bild"><span class="br"><?= esc_html($b) ?></span></span>
                        <?php endforeach; ?>
                    </div>
                <?php endfor; ?>
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