<?php
if (! defined('ABSPATH')) exit;

class A7_Perfil_Image
{
   public function __construct()
   {
      add_filter('get_avatar', [$this, 'filter_get_avatar'], 10, 6);
      add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
      add_action('wp_ajax_a7pi_upload_avatar', [$this, 'handle_upload_avatar']);
      add_action('wp_ajax_a7pi_upload_avatar_file', [$this, 'handle_upload_avatar_file']);
      add_shortcode('a7_perfil_image_form', [$this, 'shortcode_form']);
   }

   public function enqueue_assets()
   {
      if (is_user_logged_in()) {
         wp_enqueue_script('a7pi-js', A7PI_URL . 'assets/js/perfil-image.js', ['jquery'], A7PI_VERSION, true);
         wp_localize_script('a7pi-js', 'A7PI', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('a7pi_upload_avatar'),
         ]);
         wp_enqueue_style('a7pi-css', A7PI_URL . 'assets/css/perfil-image.css', [], A7PI_VERSION);
      }
   }

   public function filter_get_avatar($avatar, $id_or_email, $size, $default, $alt, $args)
   {
      $user = false;
      if (is_numeric($id_or_email)) {
         $user = get_user_by('id', $id_or_email);
      } elseif (is_object($id_or_email) && isset($id_or_email->user_id)) {
         $user = get_user_by('id', $id_or_email->user_id);
      } elseif (is_string($id_or_email)) {
         $user = get_user_by('email', $id_or_email);
      }
      if (! $user) return $avatar;
      $custom_avatar = get_user_meta($user->ID, 'a7pi_avatar', true);
      if ($custom_avatar) {
         $options = get_option('a7pi_settings', []);
         $force_round = ! empty($options['force_round']);
         $size = isset($options['size']) ? intval($options['size']) : $size;
         $style = 'width:' . esc_attr($size) . 'px;height:' . esc_attr($size) . 'px;';
         if ($force_round) $style .= 'border-radius:50%;object-fit:cover;';
         return '<img src="' . esc_url($custom_avatar) . '" alt="' . esc_attr($alt) . '" style="' . esc_attr($style) . '" class="a7pi-avatar" />';
      }
      return $avatar;
   }

   public function handle_upload_avatar()
   {
      check_ajax_referer('a7pi_upload_avatar', 'nonce');
      if (! is_user_logged_in()) wp_send_json_error(__('Permissão negada.', 'a7-perfil-image'));
      $user_id = get_current_user_id();
      if (empty($_POST['attachment_id'])) wp_send_json_error(__('ID do arquivo não enviado.', 'a7-perfil-image'));
      $attachment_id = intval($_POST['attachment_id']);
      $url = wp_get_attachment_url($attachment_id);
      if (! $url) wp_send_json_error(__('Arquivo inválido.', 'a7-perfil-image'));
      update_user_meta($user_id, 'a7pi_avatar', esc_url_raw($url));
      wp_send_json_success(['url' => $url]);
   }

   public function handle_upload_avatar_file()
   {
      check_ajax_referer('a7pi_upload_avatar', 'nonce');
      if (!is_user_logged_in()) wp_send_json_error(__('Permissão negada.', 'a7-perfil-image'));
      if (empty($_FILES['avatar_file']) || !isset($_FILES['avatar_file']['tmp_name'])) {
         wp_send_json_error(__('Nenhum arquivo enviado.', 'a7-perfil-image'));
      }
      $file = $_FILES['avatar_file'];
      require_once(ABSPATH . 'wp-admin/includes/file.php');
      require_once(ABSPATH . 'wp-admin/includes/media.php');
      require_once(ABSPATH . 'wp-admin/includes/image.php');
      $attachment_id = media_handle_upload('avatar_file', 0);
      if (is_wp_error($attachment_id)) {
         wp_send_json_error($attachment_id->get_error_message());
      }
      $url = wp_get_attachment_url($attachment_id);
      if (!$url) wp_send_json_error(__('Erro ao obter URL do arquivo.', 'a7-perfil-image'));
      $user_id = get_current_user_id();
      update_user_meta($user_id, 'a7pi_avatar', esc_url_raw($url));
      wp_send_json_success(['url' => $url]);
   }

   public function shortcode_form()
   {
      if (! is_user_logged_in()) {
         return '<div class="a7pi-profile-form"><p style="color:red;font-weight:bold;">' . __('Você precisa estar logado para alterar sua foto de perfil.', 'a7-perfil-image') . '</p></div>';
      }
      $user_id = get_current_user_id();
      $avatar = get_user_meta($user_id, 'a7pi_avatar', true);
      $options = get_option('a7pi_settings', []);
      $size = isset($options['size']) ? intval($options['size']) : 96;
      $force_round = ! empty($options['force_round']);
      ob_start();
?>
      <div class="a7pi-profile-form">
         <div class="a7pi-avatar-preview" style="width:<?php echo esc_attr($size); ?>px;height:<?php echo esc_attr($size); ?>px;<?php if ($force_round) echo 'border-radius:50%;object-fit:cover;'; ?>">
            <img src="<?php echo esc_url($avatar ? $avatar : get_avatar_url($user_id)); ?>" alt="Avatar" style="width:100%;height:100%;<?php if ($force_round) echo 'border-radius:50%;object-fit:cover;'; ?>" />
         </div>
         <button type="button" class="button a7pi-upload-btn"><?php _e('Alterar foto de perfil', 'a7-perfil-image'); ?></button>
         <form class="a7pi-upload-file-form" style="margin-top:15px;">
            <label for="a7pi-upload-file" class="a7pi-upload-label" style="font-weight:bold;display:block;margin-bottom:5px;"><?php _e('Ou envie uma nova imagem:', 'a7-perfil-image'); ?></label>
            <input type="file" id="a7pi-upload-file" accept="image/*" style="display:block;padding:8px 0;" />
         </form>
      </div>
<?php
      return ob_get_clean();
   }
}

new A7_Perfil_Image();
