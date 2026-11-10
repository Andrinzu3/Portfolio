<?php
/**
 * Branding: eigenes AZ-Logo statt WordPress-Logo
 * - Admin-Leiste oben links
 * - Login-Seite
 */

// Pfad zum Logo (liegt im Theme unter assets/img/)
function az_logo_url() {
    return get_template_directory_uri() . '/frontend/assets/img/AZ orange.svg';
}

// 1) Admin-Leiste: WP-Logo entfernen, AZ-Logo einsetzen
add_action('admin_bar_menu', function ($bar) {
    $bar->remove_node('wp-logo');
    $bar->add_node([
        'id'    => 'az-logo',
        'title' => '<img src="' . esc_url(az_logo_url()) . '" alt="AZ" style="height:20px;vertical-align:middle;">',
        'href'  => home_url('/'),
        'meta'  => ['title' => 'Zur Website'],
    ]);
}, 11);

// 2) Login-Seite im Portfolio-Look
add_action('login_enqueue_scripts', function () { ?>
    <style>
        body.login { background: #12100E; }
        #login h1 a {
            background-image: url('<?php echo esc_url(az_logo_url()); ?>');
            background-size: contain;
            width: 124px;
            height: 80px;
        }
        .login #nav a, .login #backtoblog a { color: #9A968D !important; }
        .login #nav a:hover, .login #backtoblog a:hover { color: #F0A23B !important; }
        .wp-core-ui .button-primary { background: #F0A23B; border-color: #F0A23B; color: #12100E; }
    </style>
<?php });

// Logo auf der Login-Seite verlinkt zur Startseite
add_filter('login_headerurl', function () {
    return home_url('/');
});

// Text beim Logo (für Screenreader / Hover)
add_filter('login_headertext', function () {
    return 'Andrin Zünd';
});