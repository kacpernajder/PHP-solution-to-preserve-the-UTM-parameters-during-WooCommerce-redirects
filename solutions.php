Add the following code to theme's functions.php file:

add_action('template_redirect', 'preserve_utms');

function preserve_utms() {
    if (!is_cart() && !is_checkout()) {
        return;
    }

    $utm_params = array('utm_medium', 'utm_source', 'utm_campaign', 'utm_id', 'utm_content', 'ref');
    $current_url = add_query_arg(null, null);
    $new_url = $current_url;

    foreach ($utm_params as $param) {
        if (isset($_GET[$param])) {
            $new_url = add_query_arg($param, $_GET[$param], $new_url);
        }
    }

    if ($current_url !== $new_url) {
        wp_safe_redirect($new_url);
        exit;
    }
}
