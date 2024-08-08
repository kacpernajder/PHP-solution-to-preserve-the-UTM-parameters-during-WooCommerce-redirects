Add the following code to theme's functions.php file:

// Store UTM parameters in cookies when they are first detected
function store_utm_parameters() {
    $utm_params = array('utm_medium', 'utm_source', 'utm_campaign', 'utm_id', 'utm_content', 'ref');
    foreach ($utm_params as $param) {
        if (isset($_GET[$param])) {
            setcookie($param, $_GET[$param], time() + 3600, '/');
        }
    }
}
add_action('init', 'store_utm_parameters');

// Append UTM parameters to any internal link
function append_utm_parameters_to_links($url) {
    // Only append UTM parameters to internal links
    if (strpos($url, home_url()) === 0) {
        $utm_params = array('utm_medium', 'utm_source', 'utm_campaign', 'utm_id', 'utm_content', 'ref');
        foreach ($utm_params as $param) {
            if (isset($_COOKIE[$param])) {
                $url = add_query_arg($param, $_COOKIE[$param], $url);
            }
        }
    }
    return $url;
}
add_filter('woocommerce_get_cart_url', 'append_utm_parameters_to_links');
add_filter('woocommerce_get_checkout_url', 'append_utm_parameters_to_links');
add_filter('the_permalink', 'append_utm_parameters_to_links');
add_filter('wp_get_nav_menu_items', function($items) {
    foreach ($items as $item) {
        $item->url = append_utm_parameters_to_links($item->url);
    }
    return $items;
});
