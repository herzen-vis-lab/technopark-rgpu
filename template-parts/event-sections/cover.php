<?php
/** Секция «Обложка»: вытянутая картинка, опционально с жирной надписью поверх */
$s = $args['s'];
$src = $s['image'] ? wp_get_attachment_image_url($s['image'], 'full') : '';
if (!$src && !$s['text']) return;
$alt = $s['image'] ? get_post_meta($s['image'], '_wp_attachment_image_alt', true) : '';
?>
<div class="tp-es-cover tp-es-cover--<?php echo esc_attr($s['height']); ?> tp-es-cover--<?php echo esc_attr($s['text_pos']); ?>">
    <?php if ($src) : ?>
        <img src="<?php echo esc_url($src); ?>" alt="<?php echo esc_attr($alt); ?>" class="tp-es-cover-img">
    <?php endif; ?>
    <?php if ($s['text']) : ?>
        <?php if ($s['overlay'] && $src) : ?><div class="tp-es-cover-overlay"></div><?php endif; ?>
        <p class="tp-es-cover-text" style="color: <?php echo esc_attr($s['text_color'] ?: '#FFFFFF'); ?>"><?php echo esc_html($s['text']); ?></p>
    <?php endif; ?>
</div>
