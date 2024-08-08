Add the following code to theme's functions.php file:

function store_utm_parameters() {
    $utm_params = array('utm_medium', 'utm_source', 'utm_campaign', 'utm_id', 'utm_content', 'ref');
    foreach ($utm_params as $param) {
        if (isset($_GET[$param])) {
            setcookie($param, $_GET[$param], time() + 3600, '/');
        }
    }
}
add_action('init', 'store_utm_parameters');

function append_utm_parameters_to_checkout($url) {
    $utm_params = array('utm_medium', 'utm_source', 'utm_campaign', 'utm_id', 'utm_content', 'ref');
    foreach ($utm_params as $param) {
        if (isset($_COOKIE[$param])) {
            $url = add_query_arg($param, $_COOKIE[$param], $url);
        }
    }
    return $url;
}
add_filter('woocommerce_get_checkout_url', 'append_utm_parameters_to_checkout');
