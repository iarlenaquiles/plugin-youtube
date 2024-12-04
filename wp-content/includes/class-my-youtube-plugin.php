<?php

class MyYouTubePlugin {
    private $api_key;

    public function __construct() {
        $this->api_key = '';
    }

    public function run() {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_post_sync_videos', [$this, 'sync_videos']);
    }

    public function add_admin_menu() {
        add_menu_page(
            'YouTube Sync',
            'YouTube Sync',
            'manage_options',
            'youtube-sync',
            [$this, 'render_admin_page'],
            'dashicons-video-alt3'
        );
    }

    public function render_admin_page() {
        echo '<div class="wrap"><h1>YouTube Sync</h1>';
        echo '<form method="post" action="' . admin_url('admin-post.php') . '">';
        echo '<input type="hidden" name="action" value="sync_videos">';
        submit_button('Sincronizar Vídeos');
        echo '</form></div>';
    }

    public function sync_videos() {
        $videos = $this->get_youtube_videos();

        foreach ($videos as $video) {
            if (stripos($video['title'], 'aula') !== false) {
                $this->create_page($video);
            }
        }

        wp_redirect(admin_url('admin.php?page=youtube-sync&sync=success'));
        exit;
    }

    private function get_youtube_videos() {
        $channel_id = 'SEU_CHANNEL_ID';
        $url = "https://www.googleapis.com/youtube/v3/search?key={$this->api_key}&channelId={$channel_id}&part=snippet&type=video";

        $response = wp_remote_get($url);
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        $videos = [];
        foreach ($data['items'] as $item) {
            $videos[] = [
                'title' => $item['snippet']['title'],
                'description' => $item['snippet']['description'],
                'videoId' => $item['id']['videoId'],
            ];
        }

        return $videos;
    }

    private function create_page($video) {
        $content = '<iframe width="560" height="315" src="https://www.youtube.com/embed/' . esc_attr($video['videoId']) . '" frameborder="0" allowfullscreen></iframe>';
        $page_data = [
            'post_title' => $video['title'],
            'post_content' => $content,
            'post_status' => 'publish',
            'post_type' => 'page',
        ];

        wp_insert_post($page_data);
    }
}
