Add the following code to theme's functions.php file:

function start_session() {
    if (!session_id()) {
        session_start();
    }
}
add_action('init', 'start_session', 1);

function store_utm_parameters_server_side() {
    $utm_params = array('utm_medium', 'utm_source', 'utm_campaign', 'utm_id', 'utm_content', 'ref');
    
    if (is_user_logged_in()) {
        $user_id = get_current_user_id();
        foreach ($utm_params as $param) {
            if (isset($_GET[$param])) {
                update_user_meta($user_id, $param, sanitize_text_field($_GET[$param]));
            }
        }
    } else {
        foreach ($utm_params as $param) {
            if (isset($_GET[$param])) {
                $_SESSION[$param] = sanitize_text_field($_GET[$param]);
            }
        }
    }
}
add_action('init', 'store_utm_parameters_server_side');

function get_utm_parameters() {
    $utm_params = array('utm_medium', 'utm_source', 'utm_campaign', 'utm_id', 'utm_content', 'ref');
    $utm_data = array();
    
    if (is_user_logged_in()) {
        $user_id = get_current_user_id();
        foreach ($utm_params as $param) {
            $utm_data[$param] = get_user_meta($user_id, $param, true);
        }
    } else {
        foreach ($utm_params as $param) {
            if (isset($_SESSION[$param])) {
                $utm_data[$param] = $_SESSION[$param];
            }
        }
    }

    return $utm_data;
}

function append_utm_parameters_to_links_server_side($url) {
    if (strpos($url, home_url()) === 0) {
        $utm_data = get_utm_parameters();
        foreach ($utm_data as $param => $value) {
            if (!empty($value)) {
                $url = add_query_arg($param, $value, $url);
            }
        }
    }
    return $url;
}
add_filter('woocommerce_get_cart_url', 'append_utm_parameters_to_links_server_side');
add_filter('woocommerce_get_checkout_url', 'append_utm_parameters_to_links_server_side');
add_filter('the_permalink', 'append_utm_parameters_to_links_server_side');
add_filter('wp_get_nav_menu_items', function($items) {
    foreach ($items as $item) {
        $item->url = append_utm_parameters_to_links_server_side($item->url);
    }
    return $items;
});

function append_utm_to_product_links($url, $product) {
    $utm_data = get_utm_parameters();
    foreach ($utm_data as $param => $value) {
        if (!empty($value)) {
            $url = add_query_arg($param, $value, $url);
        }
    }
    return $url;
}
add_filter('woocommerce_product_get_permalink', 'append_utm_to_product_links', 10, 2);
