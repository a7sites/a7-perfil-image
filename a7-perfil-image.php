<?php
/*
Plugin Name: A7 Perfil Image
Description: Permite que usuários enviem e atualizem sua foto de perfil, substituindo o avatar padrão do WordPress.
Version: 1.0.0
Author: A7 Sites
Text Domain: a7-perfil-image
Domain Path: /languages
*/

if (! defined('ABSPATH')) exit;

define('A7PI_PATH', plugin_dir_path(__FILE__));
define('A7PI_URL', plugin_dir_url(__FILE__));

defined('A7PI_VERSION') || define('A7PI_VERSION', '1.0.0');

// Carrega arquivos principais
require_once A7PI_PATH . 'includes/class-a7-perfil-image.php';
require_once A7PI_PATH . 'includes/admin-page.php';

// Suporte a traduções
add_action('plugins_loaded', function () {
   load_plugin_textdomain('a7-perfil-image', false, dirname(plugin_basename(__FILE__)) . '/languages');
});
