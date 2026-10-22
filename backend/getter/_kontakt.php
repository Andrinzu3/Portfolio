<?php

// -> Kontaktformular: prüfen, E-Mail senden, danach Seite neu laden (verhindert doppeltes Senden)
function handle_kontakt_formular(): array
{
    $status = ['typ' => '', 'werte' => ['name' => '', 'email' => '', 'nachricht' => '']];

    if (isset($_GET['gesendet'])) {
        $status['typ'] = 'ok';
        return $status;
    }
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST' || !isset($_POST['kontakt_nonce'])) {
        return $status;
    }

    $name      = sanitize_text_field(wp_unslash($_POST['name'] ?? ''));
    $email     = sanitize_email(wp_unslash($_POST['email'] ?? ''));
    $nachricht = sanitize_textarea_field(wp_unslash($_POST['nachricht'] ?? ''));
    $status['werte'] = ['name' => $name, 'email' => $email, 'nachricht' => $nachricht];

    // Spam-Schutz: verstecktes Feld muss leer bleiben
    if (!empty($_POST['website'])) {
        $status['typ'] = 'ok';
        return $status;
    }
    if (!wp_verify_nonce($_POST['kontakt_nonce'], 'kontakt_senden')) {
        $status['typ'] = 'fehler';
        $status['text'] = 'Die Seite war zu lange offen. Bitte lade sie neu und versuch es nochmals.';
        return $status;
    }
    if ($name === '' || !is_email($email) || $nachricht === '') {
        $status['typ'] = 'fehler';
        $status['text'] = 'Bitte fülle Name, eine gültige E-Mail und deine Nachricht aus.';
        return $status;
    }

    $an = get_kontakt()['email'] ?: get_option('admin_email');
    $gesendet = wp_mail(
        $an,
        'Portfolio-Anfrage von ' . $name,
        "Name: $name\nE-Mail: $email\n\n$nachricht",
        ['Reply-To: ' . $name . ' <' . $email . '>']
    );

    if (!$gesendet) {
        $status['typ'] = 'fehler';
        $status['text'] = 'Die Nachricht konnte nicht gesendet werden. Schreib mir bitte direkt per E-Mail.';
        return $status;
    }

    wp_safe_redirect(add_query_arg('gesendet', '1', get_permalink()) . '#formular');
    exit;
}