jQuery(document).ready(function($){
    $('.a7pi-upload-btn').on('click', function(e){
        e.preventDefault();
        var frame = wp.media({
            title: A7PI.choose || 'Escolha uma imagem',
            button: { text: A7PI.select || 'Selecionar' },
            multiple: false
        });
        frame.on('select', function(){
            var attachment = frame.state().get('selection').first().toJSON();
            $.post(A7PI.ajax_url, {
                action: 'a7pi_upload_avatar',
                nonce: A7PI.nonce,
                attachment_id: attachment.id
            }, function(resp){
                if(resp.success && resp.data.url){
                    $('.a7pi-avatar-preview img').attr('src', resp.data.url);
                } else {
                    alert(resp.data || 'Erro ao salvar imagem.');
                }
            });
        });
        frame.open();
    });

    $('#a7pi-upload-file').on('change', function(e){
        var file = this.files[0];
        if (!file) return;
        var formData = new FormData();
        formData.append('action', 'a7pi_upload_avatar_file');
        formData.append('nonce', A7PI.nonce);
        formData.append('avatar_file', file);
        $('.a7pi-upload-label').text('Enviando...');
        $.ajax({
            url: A7PI.ajax_url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(resp){
                $('.a7pi-upload-label').text('Ou envie uma nova imagem:');
                if(resp.success && resp.data.url){
                    $('.a7pi-avatar-preview img').attr('src', resp.data.url);
                } else {
                    alert((resp.data && resp.data.message) || resp.data || 'Erro ao enviar imagem.');
                }
            },
            error: function(){
                $('.a7pi-upload-label').text('Ou envie uma nova imagem:');
                alert('Erro ao enviar imagem.');
            }
        });
    });
}); 