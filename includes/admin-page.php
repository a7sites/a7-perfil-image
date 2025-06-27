<?php
if (! defined('ABSPATH')) exit;

add_action('admin_menu', function () {
   add_options_page(
      __('A7 Perfil Image', 'a7-perfil-image'),
      'A7 Perfil Image',
      'manage_options',
      'a7-perfil-image',
      'a7pi_render_admin_page'
   );
});

add_action('admin_init', function () {
   register_setting('a7pi_settings_group', 'a7pi_settings');
   add_settings_section('a7pi_section', '', null, 'a7pi_settings');
   add_settings_field('a7pi_size', __('Tamanho padrão da imagem (px)', 'a7-perfil-image'), 'a7pi_field_size', 'a7pi_settings', 'a7pi_section');
   add_settings_field('a7pi_force_round', __('Forçar estilo arredondado', 'a7-perfil-image'), 'a7pi_field_force_round', 'a7pi_settings', 'a7pi_section');
});

function a7pi_field_size()
{
   $options = get_option('a7pi_settings', []);
   $size = isset($options['size']) ? intval($options['size']) : 96;
   echo '<input type="number" name="a7pi_settings[size]" value="' . esc_attr($size) . '" min="24" max="512" />';
}

function a7pi_field_force_round()
{
   $options = get_option('a7pi_settings', []);
   $checked = ! empty($options['force_round']) ? 'checked' : '';
   echo '<input type="checkbox" name="a7pi_settings[force_round]" value="1" ' . $checked . ' />';
}

function a7pi_render_admin_page()
{
   $user_id = get_current_user_id();
   $avatar = get_user_meta($user_id, 'a7pi_avatar', true);
   $options = get_option('a7pi_settings', []);
   $size = isset($options['size']) ? intval($options['size']) : 96;
   $force_round = ! empty($options['force_round']);
?>
   <div class="wrap">
      <h1><?php _e('A7 Perfil Image - Configurações', 'a7-perfil-image'); ?></h1>
      <div style="margin-bottom:30px;">
         <h2><?php _e('Sua foto de perfil', 'a7-perfil-image'); ?></h2>
         <div class="a7pi-profile-form">
            <div class="a7pi-avatar-preview" style="width:<?php echo esc_attr($size); ?>px;height:<?php echo esc_attr($size); ?>px;<?php if ($force_round) echo 'border-radius:50%;object-fit:cover;'; ?>">
               <img src="<?php echo esc_url($avatar ? $avatar : get_avatar_url($user_id)); ?>" alt="Avatar" style="width:100%;height:100%;<?php if ($force_round) echo 'border-radius:50%;object-fit:cover;'; ?>" />
            </div>
            <form class="a7pi-upload-file-form" style="margin-top:15px;" onsubmit="return false;">
               <label for="a7pi-upload-file-admin" class="a7pi-upload-label" style="font-weight:bold;display:block;margin-bottom:5px;">
                  <?php _e('Envie uma nova imagem:', 'a7-perfil-image'); ?>
               </label>
               <input type="file" id="a7pi-upload-file-admin" accept="image/*" style="display:block;padding:8px 0;" />
            </form>
         </div>
      </div>
      <form method="post" action="options.php">
         <?php
         settings_fields('a7pi_settings_group');
         do_settings_sections('a7pi_settings');
         submit_button();
         ?>
      </form>
   </div>
   <script>
      jQuery(document).ready(function($) {
         $('#a7pi-upload-file-admin').on('change', function(e) {
            var file = this.files[0];
            if (!file) return;
            var formData = new FormData();
            formData.append('action', 'a7pi_upload_avatar_file');
            formData.append('nonce', '<?php echo esc_js(wp_create_nonce('a7pi_upload_avatar')); ?>');
            formData.append('avatar_file', file);
            $('.a7pi-upload-label').text('Enviando...');
            $.ajax({
               url: ajaxurl,
               type: 'POST',
               data: formData,
               processData: false,
               contentType: false,
               success: function(resp) {
                  $('.a7pi-upload-label').text('Envie uma nova imagem:');
                  if (resp.success && resp.data.url) {
                     $('.a7pi-avatar-preview img').attr('src', resp.data.url);
                  } else {
                     alert((resp.data && resp.data.message) || resp.data || 'Erro ao enviar imagem.');
                  }
               },
               error: function() {
                  $('.a7pi-upload-label').text('Envie uma nova imagem:');
                  alert('Erro ao enviar imagem.');
               }
            });
         });
      });
   </script>
<?php
}
