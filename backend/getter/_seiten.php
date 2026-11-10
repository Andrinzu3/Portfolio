<?php

// -> Inhalte der Seite "Über mich"
function get_ueber_mich_data(int $id): array
{
    $portraet = carbon_get_post_meta($id, 'portraet');
    return [
        'titel'     => get_the_title($id),
        'portraet'  => $portraet ? get_media_by_id($portraet, 'm') : null,
        'ort'       => carbon_get_post_meta($id, 'ort'),
        'lead'      => carbon_get_post_meta($id, 'lead'),
        'text'      => carbon_get_post_meta($id, 'text'),
        'fakten'    => carbon_get_post_meta($id, 'fakten') ?: [],
        'werdegang' => carbon_get_post_meta($id, 'werdegang') ?: [],
    ];
}

// -> Inhalte der Seite "Impressum & Datenschutz"
function get_impressum_data(int $id): array
{
    return [
        'titel'        => get_the_title($id),
        'adresse'      => carbon_get_post_meta($id, 'adresse'),
        'telefon'      => carbon_get_post_meta($id, 'telefon'),
        'urheberrecht' => carbon_get_post_meta($id, 'urheberrecht'),
        'stand'        => carbon_get_post_meta($id, 'stand'),
        'datenschutz'  => carbon_get_post_meta($id, 'datenschutz') ?: [],
    ];
}

// -> "Über mich" beim Filmstreifen auf der Startseite: Link zur Seite + Bild
//    (freigestelltes Porträt aus "Globales", sonst das Porträt der Seite "Über mich")
function get_ueber_mich_kurz(): array
{
    $ids = get_posts([
        'post_type'   => 'page',
        'numberposts' => 1,
        'meta_key'    => '_wp_page_template',
        'meta_value'  => 'template-ueber-mich.php',
        'fields'      => 'ids',
    ]);
    if (!$ids) {
        return ['url' => '', 'bild' => null];
    }
    $bild = carbon_get_theme_option('ich_bild') ?: carbon_get_post_meta($ids[0], 'portraet');
    return [
        'url'  => get_permalink($ids[0]),
        'bild' => $bild ? get_media_by_id($bild, 's') : null, // 600 px reicht (wird max. 240 px hoch angezeigt)
    ];
}