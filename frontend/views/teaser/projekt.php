<?php
// -> Projektkachel in der Übersicht
$p = $args['data'];
$info = implode(' · ', array_filter([$p['kategorie'], $p['jahr']]));
?>
<a class="teaser" href="<?= esc_url($p['url']) ?>">
    <?php if ($p['cover_klein']) : ?>
        <img class="teaser__img dev-img" data-dev src="<?= esc_url($p['cover_klein']['src']) ?>" alt="<?= esc_attr($p['cover_klein']['alt']) ?>" loading="lazy">
    <?php else : ?>
        <div class="teaser__img"></div>
    <?php endif; ?>
    <div class="teaser__meta">
        <span class="teaser__title br"><?= esc_html($p['titel']) ?></span>
        <?php if ($info) : ?><span class="teaser__info"><?= esc_html($info) ?></span><?php endif; ?>
    </div>
</a>