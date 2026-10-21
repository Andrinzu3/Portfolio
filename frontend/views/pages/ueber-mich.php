<?php
$d = $args['data'];
?>
<section class="page-intro">
    <h1 class="page-title"><?= esc_html($d['titel']) ?></h1>
</section>

<section class="about">
    <figure class="about__portrait">
        <?php if ($d['portraet']) : ?>
            <img class="dev-img" data-dev src="<?= esc_url($d['portraet']['src']) ?>" alt="<?= esc_attr($d['portraet']['alt']) ?>">
        <?php endif; ?>
        <figcaption>
            <span class="br">Andrin Zünd</span>
            <?php if ($d['ort']) : ?><span class="br"><?= esc_html($d['ort']) ?></span><?php endif; ?>
        </figcaption>
    </figure>
    <div class="about__text">
        <?php if ($d['lead']) : ?><p class="lead"><?= nl2br(esc_html($d['lead'])) ?></p><?php endif; ?>
        <?php if ($d['text']) : ?><p class="body-text"><?= nl2br(esc_html($d['text'])) ?></p><?php endif; ?>
        <?php if ($d['fakten']) : ?>
            <div class="infobar infobar--inline">
                <?php foreach ($d['fakten'] as $f) : ?>
                    <div class="info">
                        <span class="info__label"><?= esc_html($f['label']) ?></span>
                        <span class="info__value br"><?= esc_html($f['wert']) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php if ($d['werdegang']) : ?>
    <section class="split split--line">
        <h2 class="label">Werdegang</h2>
        <div class="split__content">
            <?php foreach ($d['werdegang'] as $w) : ?>
                <div class="station">
                    <span class="station__zeit br"><?= esc_html($w['zeit']) ?></span>
                    <span class="station__titel"><?= esc_html($w['titel']) ?></span>
                    <span class="station__ort"><?= esc_html($w['ort']) ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>