<?php
/**
 * Plugin Name: My YouTube Plugin
 * Description: Integração com a API do YouTube para listar vídeos e criar páginas no WordPress.
 * Version: 1.0
 * Author: Seu Nome
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

require_once plugin_dir_path(__FILE__) . 'includes/class-my-youtube-plugin.php';

function my_youtube_plugin_init() {
    $plugin = new MyYouTubePlugin();
    $plugin->run();
}
add_action('plugins_loaded', 'my_youtube_plugin_init');
