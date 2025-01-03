<?php
/*
Plugin Name: Medidas de seguridad
Description: Desactiva el archivo xmlrpc.php, versión WP, JSON, CSS y JS
Version: 1.0
Author: SYSTEMSGT
*/

// Desactiva el archivo xmlrpc.php
add_filter('xmlrpc_enabled', '__return_false');

// Bloquea el acceso directo al archivo xmlrpc.php
function bloquear_acceso_xmlrpc() {
    if (strpos($_SERVER['REQUEST_URI'], 'xmlrpc.php') !== false) {
        wp_die('El acceso a xmlrpc.php está deshabilitado en este sitio.');
    }
}
add_action('init', 'bloquear_acceso_xmlrpc');

// Elimina la versión de los archivos CSS y JS
function wpdanger_remove_ver($src, $handle) {
    $handles = ['style','script'];
    if (strpos($src, 'ver=') && !in_array($handle, $handles, true)) {
        $src = remove_query_arg('ver', $src);
    }
    return $src;
}
add_filter('style_loader_src', 'wpdanger_remove_ver', 9999, 2);
add_filter('script_loader_src', 'wpdanger_remove_ver', 9999, 2);

// Elimina la versión de WordPress en la cabecera
remove_action('wp_head', 'wp_generator');
add_filter('the_generator', '__return_false');

// Desactiva la REST API
add_filter('rest_enabled', '__return_false');
add_filter('rest_jsonp_enabled', '__return_false');
remove_action('template_redirect', 'rest_output_link_header', 11, 0);
remove_action('wp_head', 'wp_oembed_add_discovery_links', 10);
remove_action('wp_head', 'rest_output_link_wp_head', 10);
add_filter('rest_authentication_errors', function($result) {
    if (!is_user_logged_in()) {
        return new WP_Error('rest_forbidden', __('REST API restricted to authenticated users.', 'disable-json-api'), array('status' => rest_authorization_required_code()));
    }
    return $result;
});