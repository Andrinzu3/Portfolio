<?php
// -> Kategorien für Projekte (z. B. Fotografie, Film)
register_taxonomy('projekt_kategorie', ['projekt'], [
    'labels' => [
        'name'          => 'Kategorien',
        'singular_name' => 'Kategorie',
        'add_new_item'  => 'Neue Kategorie hinzufügen',
    ],
    'public'            => true,
    'hierarchical'      => true, // Checkboxen statt Freitext
    'show_admin_column' => true, // Kategorie in der Projektliste anzeigen
    'rewrite'           => ['slug' => 'kategorie'],
]);