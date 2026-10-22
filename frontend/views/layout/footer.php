<?php
$kontakt = get_kontakt();
$impressum_url = get_page_url_by_template('template-impressum.php');
$is_impressum = get_active_nav() === 'impressum';
$kontakt_url = get_page_url_by_template('template-kontakt.php');
?>
</main>

<footer id="kontakt" class="site-footer">
    <?php if (get_active_nav() !== 'kontakt') : ?>
        <a class="label site-footer__kontakt" href="<?= esc_url($kontakt_url) ?>">Kontakt →</a>
    <?php endif; ?>
    <?php if ($kontakt['email']) : ?>
        <a class="site-footer__mail" href="mailto:<?= esc_attr($kontakt['email']) ?>"><?= esc_html($kontakt['email']) ?></a>
    <?php endif; ?>
    <div class="site-footer__bottom">
        <div class="site-footer__links">
            <?php foreach ($kontakt['social'] as $name => $url) : ?>
                <a href="<?= esc_url($url) ?>" target="_blank" rel="noopener"><?= esc_html($name) ?></a>
            <?php endforeach; ?>
        </div>
        <div class="site-footer__links">
            <a href="<?= esc_url($impressum_url) ?>"<?= $is_impressum ? ' class="is-active br" aria-current="page"' : '' ?>>Impressum &amp; Datenschutz</a>
            <span>© <?= date('Y') ?> Andrin Zünd</span>
        </div>
    </div>
</footer>