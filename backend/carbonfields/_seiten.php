<?php
use Carbon_Fields\Container;
use Carbon_Fields\Field;

// -> Felder für die Seite mit dem Template "Über mich"
Container::make('post_meta', 'Über mich')
    ->where('post_template', '=', 'template-ueber-mich.php')
    ->add_fields([
        Field::make('image', 'portraet', 'Porträt')->set_width(50),
        Field::make('text', 'ort', 'Ort (unter dem Porträt)')->set_width(50),
        Field::make('textarea', 'lead', 'Einleitung (gross)')
            ->set_rows(3),
        Field::make('textarea', 'text', 'Text')
            ->set_rows(5),
        Field::make('complex', 'fakten', 'Infos')
            ->set_layout('tabbed-horizontal')
            ->add_fields([
                Field::make('text', 'label', 'Bezeichnung')->set_width(50),
                Field::make('text', 'wert', 'Wert')->set_width(50),
            ]),
        Field::make('complex', 'werdegang', 'Werdegang')
            ->set_layout('tabbed-vertical')
            ->add_fields([
                Field::make('text', 'zeit', 'Zeit')->set_width(25),
                Field::make('text', 'titel', 'Titel')->set_width(45),
                Field::make('text', 'ort', 'Ort / Firma')->set_width(30),
            ])
            ->set_header_template('<%- zeit %> <%- titel %>'),
    ]);

// -> Felder für die Seite mit dem Template "Impressum"
Container::make('post_meta', 'Impressum & Datenschutz')
    ->where('post_template', '=', 'template-impressum.php')
    ->add_fields([
        Field::make('textarea', 'adresse', 'Adresse')
            ->set_rows(4)
            ->set_help_text('Eine Zeile pro Zeile, z. B. Name, Strasse, PLZ Ort, Land'),
        Field::make('text', 'telefon', 'Telefon (optional)'),
        Field::make('textarea', 'urheberrecht', 'Urheberrecht')
            ->set_rows(3)
            ->set_default_value('Alle Fotos, Videos und Texte auf dieser Website stammen, sofern nicht anders angegeben, von Andrin Zünd. Eine Verwendung ist nur mit vorheriger Zustimmung erlaubt.'),
        Field::make('text', 'stand', 'Datenschutz – Stand')
            ->set_help_text('z. B. Oktober 2026'),
        Field::make('complex', 'datenschutz', 'Datenschutz – Abschnitte')
            ->set_layout('tabbed-vertical')
            ->add_fields([
                Field::make('text', 'titel', 'Titel'),
                Field::make('textarea', 'text', 'Text')->set_rows(4),
            ])
            ->set_header_template('<%- titel %>'),
    ]);