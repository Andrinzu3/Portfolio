<?php
/**
 * Template Name: Über mich
 */
?>
<?php get_header() ?>
<?php get_template_part('frontend/views/layout/header') ?>
<?php get_template_part('frontend/views/pages/ueber-mich', null, ['data' => get_ueber_mich_data(get_the_ID())]) ?>
<?php get_template_part('frontend/views/layout/footer') ?>
<?php get_footer() ?>