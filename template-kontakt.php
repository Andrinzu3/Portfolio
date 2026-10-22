<?php
/**
 * Template Name: Kontakt
 */
// -> Formular zuerst verarbeiten (vor jeder Ausgabe, damit die Weiterleitung klappt)
$status = handle_kontakt_formular();
?>
<?php get_header() ?>
<?php get_template_part('frontend/views/layout/header') ?>
<?php get_template_part('frontend/views/pages/kontakt', null, ['status' => $status]) ?>
<?php get_template_part('frontend/views/layout/footer') ?>
<?php get_footer() ?>