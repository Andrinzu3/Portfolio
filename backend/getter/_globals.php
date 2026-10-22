<?php

// -> Die drei Akzentfarben der Seite
function get_akzentfarben(): array
{
    return [
        'orange' => '#F0A23B',
        'blau'   => '#7DB0E8',
        'gruen'  => '#93C97E',
    ];
}

// -> Hex-Wert einer Akzentfarbe anhand des Schlüssels (Fallback: Orange)
function get_akzent_hex(?string $key): string
{
    $farben = get_akzentfarben();
    return $farben[$key] ?? $farben['orange'];
}

// -> Akzentfarbe der aktuell angezeigten Seite
function get_page_accent(): string
{
    if (is_singular('projekt')) {
        return get_akzent_hex(carbon_get_post_meta(get_the_ID(), 'farbe'));
    }
    if (is_page_template('template-ueber-mich.php')) {
        return get_akzent_hex('blau');
    }
    if (is_page_template('template-impressum.php')) {
        return get_akzent_hex('gruen');
    }
    if (is_page_template('template-kontakt.php')) {
        return get_akzent_hex('orange');
    }
    return get_akzent_hex('orange');
}

// -> Welcher Navigationspunkt aktiv ist
function get_active_nav(): string
{
    if (is_front_page() || is_singular('projekt')) {
        return 'projekte';
    }
    if (is_page_template('template-ueber-mich.php')) {
        return 'ueber-mich';
    }
    if (is_page_template('template-impressum.php')) {
        return 'impressum';
    }
    if (is_page_template('template-kontakt.php')) {
        return 'kontakt';
    }
    return '';
}

// -> URL der ersten Seite mit einem bestimmten Template
function get_page_url_by_template(string $template): string
{
    $pages = get_posts([
        'post_type'   => 'page',
        'numberposts' => 1,
        'meta_key'    => '_wp_page_template',
        'meta_value'  => $template,
        'fields'      => 'ids',
    ]);
    return $pages ? get_permalink($pages[0]) : home_url('/');
}

// -> Kontaktdaten aus "Globales"
function get_kontakt(): array
{
    $social = [
        'Instagram' => carbon_get_theme_option('instagram'),
        'Vimeo'     => carbon_get_theme_option('vimeo'),
        'LinkedIn'  => carbon_get_theme_option('linkedin'),
    ];
    return [
        'email'  => carbon_get_theme_option('email'),
        'social' => array_filter($social),
    ];
}

// -> Intro-Medien der Startseite (max. 5)
function get_intro_medien(): array
{
    $ids = carbon_get_theme_option('intro_medien') ?: [];
    $medien = [];
    foreach (array_slice($ids, 0, 5) as $id) {
        $medien[] = get_media_by_id($id, 'xl');
    }
    return $medien;
}

// -> Bereiche als Liste ("Fotografie, Film" -> ['Fotografie', 'Film'])
function get_bereiche(): array
{
    $text = carbon_get_theme_option('bereiche') ?: '';
    return array_values(array_filter(array_map('trim', explode(',', $text))));
}