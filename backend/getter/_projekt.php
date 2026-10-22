<?php

// -> IDs aller Projekte, sortiert nach Projektdatum (neuestes zuerst)
function get_projekt_ids(): array
{
    $ids = get_posts([
        'post_type'   => 'projekt',
        'numberposts' => -1,
        'orderby'     => 'date',
        'order'       => 'DESC',
        'fields'      => 'ids',
    ]);
    usort($ids, fn($a, $b) => strcmp(get_projekt_datum($b), get_projekt_datum($a)));
    return $ids;
}

// -> Projektdatum als "JJJJ-MM-TT" (Fallback: altes Feld "Jahr", sonst Veröffentlichungsdatum)
function get_projekt_datum(int $id): string
{
    $datum = get_post_meta($id, '_datum', true);
    if ($datum) {
        return $datum;
    }
    $jahr = get_post_meta($id, '_jahr', true);
    if ($jahr) {
        return $jahr . '-01-01';
    }
    return get_the_date('Y-m-d', $id);
}

// -> Alle Daten eines Projekts für das Frontend
function get_projekt_data(int $id): array
{
    $terms = get_the_terms($id, 'projekt_kategorie');
    $kategorie = ($terms && !is_wp_error($terms)) ? $terms[0]->name : '';

    $cover_id = carbon_get_post_meta($id, 'cover');
    $video_url = carbon_get_post_meta($id, 'video_url');

    $galerie = [];
    foreach ((array) carbon_get_post_meta($id, 'galerie') as $item) {
        $bild_id = (int) ($item['bild'] ?? 0);
        $src = $bild_id ? (wp_get_attachment_image_url($bild_id, 'large') ?: wp_get_attachment_image_url($bild_id, 'full')) : '';
        if ($src) {
            $galerie[] = [
                'src' => $src,
                'alt' => get_post_meta($bild_id, '_wp_attachment_image_alt', true) ?: '',
            ];
        }
    }

    return [
        'id'               => $id,
        'titel'            => get_the_title($id),
        'url'              => get_permalink($id),
        'kategorie'        => $kategorie,
        'jahr'             => substr(get_projekt_datum($id), 0, 4),
        'rolle'            => carbon_get_post_meta($id, 'rolle'),
        'kontext'          => carbon_get_post_meta($id, 'kontext'),
        'lead'             => carbon_get_post_meta($id, 'beschreibung_lead'),
        'beschreibung'     => wpautop(carbon_get_post_meta($id, 'beschreibung') ?: ''),
        'video'            => $video_url ? wp_oembed_get($video_url) : '',
        'cover'            => $cover_id ? get_media_by_id($cover_id, 'l') : null,
        'cover_klein'      => $cover_id ? get_media_by_id($cover_id, 's') : get_video_thumbnail($video_url),
        'format'           => carbon_get_post_meta($id, 'format'),
        'galerie'          => $galerie,
        'farbe'            => get_akzent_hex(carbon_get_post_meta($id, 'farbe')),
    ];
}

// -> Vorschaubild eines YouTube- oder Vimeo-Videos (wird 1 Woche zwischengespeichert)
function get_video_thumbnail(?string $url): ?array
{
    if (!$url) {
        return null;
    }
    $cache_key = 'video_thumb_' . md5($url);
    $src = get_transient($cache_key);

    if ($src === false) {
        $src = '';
        // YouTube: Bild in hoher Auflösung direkt über die Video-ID
        if (preg_match('~(?:youtu\.be/|v=|embed/|shorts/)([A-Za-z0-9_-]{11})~', $url, $m)) {
            $src = 'https://i.ytimg.com/vi/' . $m[1] . '/maxresdefault.jpg';
            $check = wp_remote_head($src);
            if (is_wp_error($check) || wp_remote_retrieve_response_code($check) !== 200) {
                $src = 'https://i.ytimg.com/vi/' . $m[1] . '/hqdefault.jpg';
            }
        } else {
            // Vimeo & andere: über oEmbed
            $data = _wp_oembed_get_object()->get_data($url, ['width' => 1280]);
            if ($data && !empty($data->thumbnail_url)) {
                $src = $data->thumbnail_url;
            }
        }
        set_transient($cache_key, $src, WEEK_IN_SECONDS);
    }

    return $src ? ['src' => $src, 'alt' => '', 'type' => 'image'] : null;
}

// -> Nächstes Projekt (nach dem letzten kommt wieder das erste)
function get_next_projekt(int $id): ?array
{
    $ids = get_projekt_ids();
    if (count($ids) < 2) {
        return null;
    }
    $index = array_search($id, $ids, true);
    $next = $ids[($index === false ? 0 : $index + 1) % count($ids)];
    return [
        'titel' => get_the_title($next),
        'url'   => get_permalink($next),
    ];
}