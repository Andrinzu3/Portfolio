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
// Baut aus einem Logo-SVG ein Favicon mit dunklem Quadrat
function az_favicon_svg($datei) {
    $pfad = get_template_directory() . '/frontend/assets/img/' . $datei;
    if (!file_exists($pfad)) return '';

    $logo = file_get_contents($pfad);
    $logo = preg_replace('/<\?xml.*?\?>/s', '', $logo);
    $logo = preg_replace('/<!--.*?-->/s', '', $logo);

    // Logo mittig ins Quadrat setzen
    $logo = preg_replace_callback('/<svg\b[^>]*>/', function ($m) {
        $tag = preg_replace('/\s(x|y|width|height)="[^"]*"/', '', $m[0]);
        return str_replace('<svg', '<svg x="10" y="24" width="80" height="52"', $tag);
    }, $logo, 1);

    $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">'
         . '<rect width="100" height="100" rx="20" fill="#12100E"/>'
         . $logo . '</svg>';

    return 'data:image/svg+xml,' . rawurlencode($svg);
}

// Favicon: wechselt alle 2 Sekunden die Farbe
function az_favicon() {
    $icons = array_values(array_filter([
        az_favicon_svg('AZ_orange.svg'),
        az_favicon_svg('AZ_blau.svg'),
        az_favicon_svg('AZ_gruen.svg'),
    ]));

    if (!$icons) {
        echo '<!-- AZ-Favicon: keine Logo-Dateien in frontend/assets/img gefunden -->';
        return;
    }
    ?>
    <link rel="icon" type="image/svg+xml" id="az-favicon" href="<?php echo esc_attr($icons[0]); ?>">
    <script>
    (function () {
        var icons = <?php echo wp_json_encode($icons); ?>;
        var i = 0;
        setInterval(function () {
            i = (i + 1) % icons.length;
            document.getElementById('az-favicon').href = icons[i];
        }, 2000);
    })();
    </script>
    <?php
}
add_action('wp_head', 'az_favicon');
add_action('admin_head', 'az_favicon');
add_action('login_head', 'az_favicon');