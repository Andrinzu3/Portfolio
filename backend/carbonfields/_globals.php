<?php
use Carbon_Fields\Container;
use Carbon_Fields\Field;

// -> Inhaltsfeld-Konfiguration der globalen Einstellungen des Themes
Container::make('theme_options', 'Globales')
    ->add_tab('Startseite', [
        Field::make('media_gallery', 'intro_medien', 'Intro-Bilder und -Clips')
            ->set_type(['image', 'video'])
            ->set_help_text('Ca. 5 Stück. Sie blitzen beim Laden der Startseite schnell hinter dem Namen auf. Clips: 2–4 Sekunden, ohne Ton, gut komprimiert (MP4).'),
        Field::make('text', 'untertitel', 'Untertitel unter dem Namen')
            ->set_default_value('Multimedia Production'),
        Field::make('text', 'bereiche', 'Bereiche (mit Komma getrennt)')
            ->set_default_value('Fotografie, Film, Kamera, Schnitt, Regie')
            ->set_help_text('Erscheinen als orange Zeile unter dem Intro.'),
    ])
    ->add_tab('Kontakt', [
        Field::make('text', 'email', 'E-Mail')
            ->set_attribute('type', 'email'),
        Field::make('text', 'instagram', 'Instagram-Link')->set_attribute('type', 'url'),
        Field::make('text', 'vimeo', 'Vimeo-Link')->set_attribute('type', 'url'),
        Field::make('text', 'linkedin', 'LinkedIn-Link')->set_attribute('type', 'url'),
    ]);