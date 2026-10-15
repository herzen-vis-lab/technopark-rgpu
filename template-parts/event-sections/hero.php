<?php
/** Секция «Hero»: заголовок, текст и кнопки слева, квадратная картинка справа */
$s = $args['s'];
$title = $s['title'] ?: get_the_title($args['post_id']);
$dates = $s['show_dates'] ? tp_es_event_dates($args['post_id']) : '';
$src = $s['image'] ? wp_get_attachment_image_url($s['image'], 'large') : '';
$buttons = array_filter(array_map('tp_es_button_html', $s['buttons']));
?>
<div class="tp-es-hero<?php echo $src ? '' : ' tp-es-hero--no-image'; ?>">
    <div class="tp-es-hero-content">
        <h1 class="tp-es-hero-title"><?php echo esc_html($title); ?></h1>
        <?php if ($dates) : ?>
            <p class="tp-es-hero-dates text-small"><?php echo esc_html($dates); ?></p>
        <?php endif; ?>
        <?php if ($s['text']) : ?>
            <div class="tp-es-hero-text text-body"><?php echo tp_es_text_html($s['text']); ?></div>
        <?php endif; ?>
        <?php if ($buttons) : ?>
            <div class="tp-es-buttons"><?php echo implode('', $buttons); ?></div>
        <?php endif; ?>
    </div>
    <?php if ($src) : ?>
        <div class="tp-es-hero-media">
            <img src="<?php echo esc_url($src); ?>" alt="<?php echo esc_attr(get_post_meta($s['image'], '_wp_attachment_image_alt', true)); ?>">
        </div>
    <?php endif; ?>
</div>
