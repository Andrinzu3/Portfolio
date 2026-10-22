<?php
use Carbon_Fields\Container;
use Carbon_Fields\Field;

// -> Inhaltsfelder für einzelne Projekte
Container::make('post_meta', 'Projektdetails')
    ->where('post_type', '=', 'projekt')
    ->add_fields([
        Field::make('image', 'cover', 'Titelbild')
            ->set_help_text('Erscheint in der Projektübersicht und auf der Projektseite (falls kein Video). Bei Video-Projekten optional – sonst wird das Vorschaubild des Videos verwendet.'),
        Field::make('select', 'farbe', 'Akzentfarbe')
            ->set_options([
                'orange' => 'Orange',
                'blau'   => 'Blau',
                'gruen'  => 'Grün',
            ])
            ->set_default_value('orange')
            ->set_width(25),
        Field::make('date', 'datum', 'Datum')
            ->set_input_format('d.m.Y', 'd.m.Y')
            ->set_storage_format('Y-m-d')
            ->set_help_text('Bestimmt die Reihenfolge (neuestes zuerst). Auf der Website steht nur das Jahr.')
            ->set_width(25),
        Field::make('text', 'rolle', 'Meine Rolle')
            ->set_help_text('z. B. Kamera, Schnitt')
            ->set_width(25),
        Field::make('text', 'kontext', 'Kontext')
            ->set_help_text('z. B. Schulprojekt, Auftrag')
            ->set_width(25),
        Field::make('text', 'beschreibung_lead', 'Über das Projekt – Einleitung')
            ->set_help_text('Ein Satz, gross dargestellt: Worum geht es?'),
        Field::make('rich_text', 'beschreibung', 'Über das Projekt – Text'),
        Field::make('text', 'video_url', 'Video-Link')
            ->set_attribute('type', 'url')
            ->set_help_text('YouTube- oder Vimeo-Link (optional). Ersetzt oben das Titelbild.'),
        Field::make('text', 'format', 'Format · Länge')
            ->set_help_text('z. B. 4K · 3:20 min (optional)'),
        Field::make('complex', 'galerie', 'Weitere Bilder')
            ->set_layout('grid')
            ->setup_labels(['plural_name' => 'Bilder', 'singular_name' => 'Bild'])
            ->add_fields([
                Field::make('image', 'bild', 'Bild'),
            ]),
    ]);