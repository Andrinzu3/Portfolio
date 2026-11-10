<?php
use Carbon_Fields\Container;
use Carbon_Fields\Field;

// -> Inhaltsfeld-Konfiguration der globalen Einstellungen des Themes
Container::make('theme_options', 'Globales')
    ->add_tab('Startseite', [
        Field::make('media_gallery', 'filmstreifen_clips', 'Filmstreifen-Clips')
            ->set_type(['video'])
            ->set_help_text('Ein Clip pro Filmbild, ca. 6–8 Stück, Reihenfolge per Drag & Drop.<br>'
                . '<strong>Format:</strong> MP4 mit Codec H.264 (nicht MOV, nicht H.265/HEVC), <strong>ohne Ton</strong>.<br>'
                . '<strong>Seitenverhältnis:</strong> 3:2 (wie Kleinbild), z. B. <strong>480 × 320 px</strong>. Andere Formate werden mittig zugeschnitten.<br>'
                . '<strong>Länge:</strong> 2–5 Sekunden (läuft in Schlaufe). <strong>Bildrate:</strong> 24 oder 25 fps.<br>'
                . '<strong>Dateigrösse:</strong> pro Clip unter <strong>300 KB</strong> (max. 1 MB). Bitrate ca. 0,5–1 Mbit/s.<br>'
                . 'Farbe egal: wird automatisch schwarz-weiss angezeigt.'),
        Field::make('image', 'ich_bild', 'Freigestelltes Porträt beim Filmstreifen')
            ->set_help_text('Steht vor dem Filmstreifen (unten bündig), daneben „[Über mich]“ als Link.<br>'
                . '<strong>Format:</strong> PNG mit <strong>transparentem Hintergrund</strong> (freigestellt).<br>'
                . '<strong>Zuschnitt:</strong> oben <strong>direkt bei den Haaren</strong> abschneiden (der Text beginnt auf dieser Höhe), unten z. B. auf Brusthöhe. '
                . 'Person eher links/mittig, rechts neben dem Kopf etwas leerer Platz für den Text.<br>'
                . '<strong>Grösse:</strong> ca. 800 × 800 px (quadratisch oder hochkant), unter <strong>500 KB</strong>.<br>'
                . 'Leer lassen = Porträt von der Seite „Über mich“.'),
    ])
    ->add_tab('Kontakt', [
        Field::make('text', 'email', 'E-Mail')
            ->set_attribute('type', 'email'),
        Field::make('text', 'instagram', 'Instagram-Link')->set_attribute('type', 'url'),
        Field::make('text', 'vimeo', 'Vimeo-Link')->set_attribute('type', 'url'),
        Field::make('text', 'linkedin', 'LinkedIn-Link')->set_attribute('type', 'url'),
    ]);