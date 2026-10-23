<?php
/** Секция «Обложка»: вытянутая картинка, опционально с жирной надписью и кнопками поверх */
$s = $args['s'];
$src = $s['image'] ? wp_get_attachment_image_url($s['image'], 'full') : '';
$buttons = array_filter(array_map('tp_es_button_html', $s['buttons'] ?? array()));
if (!$src && !$s['text'] && !$buttons) return;
$alt = $s['image'] ? get_post_meta($s['image'], '_wp_attachment_image_alt', true) : '';
$has_content = $s['text'] || $buttons;
?>
<div class="tp-es-cover tp-es-cover--<?php echo esc_attr($s['height']); ?> tp-es-cover--<?php echo esc_attr($s['text_pos']); ?>">
    <?php if ($src) : ?>
        <img src="<?php echo esc_url($src); ?>" alt="<?php echo esc_attr($alt); ?>" class="tp-es-cover-img">
    <?php endif; ?>
    <?php if ($has_content) : ?>
        <?php if ($s['overlay'] && $src) : ?><div class="tp-es-cover-overlay"></div><?php endif; ?>
        <!-- надпись и кнопки одним блоком: кнопки начинаются от левого края надписи -->
        <div class="tp-es-cover-content<?php echo $buttons ? ' tp-es-cover-content--with-buttons' : ''; ?>" style="color: <?php echo esc_attr($s['text_color'] ?: '#FFFFFF'); ?>">
            <?php if ($s['text']) : ?>
                <p class="tp-es-cover-text"><?php echo esc_html($s['text']); ?></p>
            <?php endif; ?>
            <?php if ($buttons) : ?>
                <div class="tp-es-buttons tp-es-cover-buttons"><?php echo implode('', $buttons); ?></div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
