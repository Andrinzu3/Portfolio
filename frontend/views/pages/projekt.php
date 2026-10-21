<?php
$p = $args['data'];
$next = get_next_projekt($p['id']);
$infos = array_filter([
    'Kategorie'   => $p['kategorie'],
    'Jahr'        => $p['jahr'],
    'Meine Rolle' => $p['rolle'],
    'Kontext'     => $p['kontext'],
]);
?>
<section class="page-intro">
    <a class="back-link br" href="<?= esc_url(home_url('/#projekte')) ?>">← Alle Projekte</a>
    <h1 class="projekt-title"><span class="br"><?= esc_html($p['titel']) ?></span></h1>
</section>
 
<?php if ($infos) : ?>
    <div class="infobar">
        <?php foreach ($infos as $label => $wert) : ?>
            <div class="info">
                <span class="info__label"><?= esc_html($label) ?></span>
                <span class="info__value br"><?= esc_html($wert) ?></span>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
 
<section class="projekt-body">
    <div class="projekt-media">
        <?php if ($p['video']) : ?>
            <div class="projekt-media__video"><?= $p['video'] ?></div>
        <?php elseif ($p['cover']) : ?>
            <img class="projekt-media__img dev-img" data-dev src="<?= esc_url($p['cover']['src']) ?>" alt="<?= esc_attr($p['cover']['alt']) ?>">
        <?php endif; ?>
        <?php if ($p['format']) : ?>
            <div class="projekt-media__caption">
                <span class="br"><?= esc_html($p['format']) ?></span>
            </div>
        <?php endif; ?>
    </div>
 
    <?php if ($p['lead'] || trim(strip_tags($p['beschreibung']))) : ?>
        <div class="projekt-text">
            <h2 class="label">Über das Projekt</h2>
            <?php if ($p['lead']) : ?><p class="lead"><?= esc_html($p['lead']) ?></p><?php endif; ?>
            <div class="richtext"><?= wp_kses_post($p['beschreibung']) ?></div>
        </div>
    <?php endif; ?>
</section>
 
<?php if ($p['galerie']) : ?>
    <section class="galerie">
        <?php foreach ($p['galerie'] as $g) : ?>
            <figure class="galerie__item">
                <img class="dev-img" data-dev src="<?= esc_url($g['src']) ?>" alt="<?= esc_attr($g['alt']) ?>" loading="lazy">
            </figure>
        <?php endforeach; ?>
    </section>
<?php endif; ?>
 
<?php if ($next) : ?>
    <a class="next-projekt" href="<?= esc_url($next['url']) ?>">
        <span class="label">Nächstes Projekt</span>
        <span class="next-projekt__title"><span class="br"><?= esc_html($next['titel']) ?></span> →</span>
    </a>
<?php endif; ?>
