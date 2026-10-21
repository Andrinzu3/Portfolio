<?php
$d = $args['data'];
$kontakt = get_kontakt();
?>
<section class="page-intro">
    <h1 class="page-title">Impressum</h1>
</section>

<section class="split">
    <h2 class="label">Verantwortlich</h2>
    <div class="split__content split__content--row">
        <?php if ($d['adresse']) : ?>
            <address class="adresse"><?= nl2br(esc_html($d['adresse'])) ?></address>
        <?php endif; ?>
        <div class="infobar infobar--stack">
            <?php if ($kontakt['email']) : ?>
                <div class="info">
                    <span class="info__label">E-Mail</span>
                    <a class="info__value br" href="mailto:<?= esc_attr($kontakt['email']) ?>"><?= esc_html($kontakt['email']) ?></a>
                </div>
            <?php endif; ?>
            <?php if ($d['telefon']) : ?>
                <div class="info">
                    <span class="info__label">Telefon</span>
                    <span class="info__value br"><?= esc_html($d['telefon']) ?></span>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php if ($d['urheberrecht']) : ?>
    <section class="split split--line">
        <h2 class="label">Urheberrecht</h2>
        <p class="split__content body-text"><?= nl2br(esc_html($d['urheberrecht'])) ?></p>
    </section>
<?php endif; ?>

<?php if ($d['datenschutz']) : ?>
    <section class="split split--line">
        <div>
            <h2 class="section-title">Daten&shy;schutz</h2>
            <?php if ($d['stand']) : ?><p class="muted">Stand: <?= esc_html($d['stand']) ?></p><?php endif; ?>
        </div>
        <div class="split__content">
            <?php foreach ($d['datenschutz'] as $abschnitt) : ?>
                <div class="ds-item">
                    <h3 class="label"><?= esc_html($abschnitt['titel']) ?></h3>
                    <p class="body-text"><?= nl2br(esc_html($abschnitt['text'])) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>