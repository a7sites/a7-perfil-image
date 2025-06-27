# A7 Perfil Image

Permite que usuários enviem e atualizem sua foto de perfil diretamente pelo site ou pelo painel administrativo, substituindo o avatar padrão do WordPress.

## Funcionalidades

- Upload e atualização da foto de perfil pelo usuário autenticado (frontend via shortcode ou admin via configurações)
- Salva a imagem como user_meta, vinculada ao ID do usuário
- Substitui o avatar padrão do WordPress em todo o site (filtro `get_avatar`)
- Avatar exibido com estilo arredondado (opcional)
- Página de configurações em "Configurações > A7 Perfil Image" para definir tamanho e borda
- Suporte à biblioteca de mídia do WordPress (wp.media)
- Suporte a traduções
- Segurança com nonce e verificação de permissões

## Instalação

1. Faça upload da pasta `a7-perfil-image` para o diretório `wp-content/plugins/`.
2. Ative o plugin no painel do WordPress.

## Como usar

### No frontend (site)

1. Crie ou edite uma página/post.
2. Adicione o shortcode:
   ```
   [a7_perfil_image_form]
   ```
3. Salve e visualize a página logado para ver o formulário de upload.

### No admin (painel)

1. Vá em **Configurações > A7 Perfil Image**.
2. Altere sua foto de perfil pelo campo de upload.
3. Defina o tamanho padrão e se deseja borda arredondada.

## Configurações

- **Tamanho padrão da imagem (px):** Define o tamanho do avatar exibido.
- **Forçar estilo arredondado:** Exibe o avatar sempre com borda arredondada (border-radius: 50%).

## Segurança

- Apenas usuários autenticados podem alterar sua própria foto.
- Upload protegido por nonce e permissões do WordPress.

## Tradução

- Suporte a `load_plugin_textdomain`.
- Arquivo `.pot` incluso em `/languages`.

## Compatibilidade

- Compatível com qualquer tema moderno do WordPress.
- Não substitui o avatar de outros plugins que ignoram o filtro `get_avatar`.

## Suporte

Para dúvidas ou sugestões, entre em contato com o desenvolvedor A7 Sites.
