<?php get_header() ?>
<?php get_template_part('frontend/views/layout/header') ?>
<?php get_template_part('frontend/views/pages/projekt', null, ['data' => get_projekt_data(get_the_ID())]) ?>
<?php get_template_part('frontend/views/layout/footer') ?>
<?php get_footer() ?>