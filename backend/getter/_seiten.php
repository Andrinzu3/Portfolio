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