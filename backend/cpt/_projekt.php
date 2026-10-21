<?php
// -> Custom Post Type "Projekt"
register_post_type('projekt', [
    'labels' => [
        'name'          => 'Projekte',
        'singular_name' => 'Projekt',
        'add_new'       => 'Neues Projekt',
        'add_new_item'  => 'Neues Projekt hinzufügen',
        'edit_item'     => 'Projekt bearbeiten',
        'all_items'     => 'Alle Projekte',
    ],
    'public'        => true,
    'has_archive'   => false,
    'menu_icon'     => 'dashicons-camera',
    'menu_position' => 5,
    'rewrite'       => ['slug' => 'projekte'],
    'supports'      => ['title', 'page-attributes'], // page-attributes = Reihenfolge festlegen
]);