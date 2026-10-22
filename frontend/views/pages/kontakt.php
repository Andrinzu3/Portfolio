<?php
$status = $args['status'];
$w = $status['werte'];
$kontakt = get_kontakt();
?>
<section class="page-intro">
    <h1 class="page-title">Kontakt</h1>
    <p class="page-intro__text">Ein Projekt, eine Frage oder einfach Hallo? Schreib mir – ich melde mich so schnell wie möglich.</p>
</section>

<section class="split split--line" id="formular">
    <div class="kontakt-info">
        <h2 class="label">Direkt</h2>
        <?php if ($kontakt['email']) : ?>
            <a class="kontakt-info__mail" href="mailto:<?= esc_attr($kontakt['email']) ?>"><?= esc_html($kontakt['email']) ?></a>
        <?php endif; ?>
        <?php foreach ($kontakt['social'] as $name => $url) : ?>
            <a class="kontakt-info__link" href="<?= esc_url($url) ?>" target="_blank" rel="noopener"><?= esc_html($name) ?></a>
        <?php endforeach; ?>
    </div>

    <div class="split__content">
        <?php if ($status['typ'] === 'ok') : ?>
            <div class="formular-meldung formular-meldung--ok">
                                <p class="lead">Danke, deine E-Mail ist eingegangen!</p>
                <p class="body-text">Ich melde mich so schnell wie möglich bei dir.</p>
            </div>
        <?php else : ?>
            <?php if ($status['typ'] === 'fehler') : ?>
                <p class="formular-meldung formular-meldung--fehler"><?= esc_html($status['text']) ?></p>
            <?php endif; ?>

            <form class="formular" method="post" action="#formular">
                <?php wp_nonce_field('kontakt_senden', 'kontakt_nonce'); ?>
                <div class="formular__reihe">
                    <label class="feld">
                        <span class="feld__label">Name</span>
                        <input type="text" name="name" required autocomplete="name" value="<?= esc_attr($w['name']) ?>">
                    </label>
                    <label class="feld">
                        <span class="feld__label">E-Mail</span>
                        <input type="email" name="email" required autocomplete="email" value="<?= esc_attr($w['email']) ?>">
                    </label>
                </div>
                <label class="feld">
                    <span class="feld__label">Nachricht</span>
                    <textarea name="nachricht" rows="7" required><?= esc_textarea($w['nachricht']) ?></textarea>
                </label>
                <!-- Spam-Schutz: für Menschen unsichtbar -->
                <label class="formular__falle" aria-hidden="true">Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                <button class="formular__senden" type="submit"><span class="br">Nachricht senden →</span></button>
            </form>
        <?php endif; ?>
    </div>
</section>