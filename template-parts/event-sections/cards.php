<?php
/**
 * Секция «Карточки»: сетка 3 / 4 / 6 карточек.
 * Оформление (иконка, выравнивание, размеры) задаётся для всей секции, карточка может переопределить любое поле —
 * см. tp_es_card_look_fields() в inc/event-sections.php.
 */
$s = $args['s'];
if (!$s['items'] && !$s['title']) return;

$look_keys = array('icon_pos', 'valign', 'title_align', 'caption_align', 'title_size', 'caption_size', 'icon_size');
$photo = $s['photo'] ?? 'none';
$outline = ($s['fill'] ?? 'fill') === 'outline';
// фото сверху занимает квадрат само, карточка растёт по содержимому
$height = $photo === 'top' ? 'auto' : ($s['height'] ?? 'square');

$grid_classes = array(
    'tp-es-cards',
    'tp-es-cards--' . $s['layout'],
    'tp-es-cards--' . ($outline ? 'outline' : 'fill'),
    'tp-es-cards--photo-' . $photo,
    'tp-es-cards--h-' . $height,
);
if (in_array($s['icon_pos'] ?? '', array('left', 'right'), true)) {
    $grid_classes[] = 'tp-es-cards--icon-side'; // горизонтальные карточки на мобилке — в одну колонку
}
?>
<?php if ($s['title']) : ?>
    <h2 class="tp-es-title tp-es-title--center"><?php echo esc_html($s['title']); ?></h2>
<?php endif; ?>
<div class="<?php echo esc_attr(implode(' ', $grid_classes)); ?>">
    <?php foreach ($s['items'] as $card) :
        $look = array();
        foreach ($look_keys as $key) {
            $look[$key] = !empty($card[$key]) ? $card[$key] : ($s[$key] ?? '');
        }

        $bg = $card['bg'] ?: ($s['card_bg'] ?: '#64BDE1');
        $color = $card['color'] ?: ($s['card_color'] ?: '#FFFFFF');
        if ($outline) {
            // контурная: рамка цвета карточки, текст — свой цвет текста или тоже цвет карточки
            $css = 'border-color:' . $bg . ';color:' . ($card['color'] ?: $bg);
        } else {
            $css = 'background-color:' . $bg . ';color:' . $color;
        }

        $classes = array('tp-es-card');
        foreach ($look as $key => $value) {
            $classes[] = 'tp-es-card--' . str_replace('_', '-', $key) . '-' . $value;
        }
        $icon = tp_es_icon_html($card['icon'], 'tp-es-card-icon');
        $image = ($photo !== 'none' && !empty($card['image'])) ? wp_get_attachment_image_url($card['image'], 'large') : '';
        if ($image) $classes[] = 'tp-es-card--has-photo';
        ?>
        <div class="<?php echo esc_attr(implode(' ', $classes)); ?>" style="<?php echo esc_attr($css); ?>">
            <?php if ($image) : ?>
                <div class="tp-es-card-photo"><img src="<?php echo esc_url($image); ?>" alt="" loading="lazy"></div>
            <?php endif; ?>
            <div class="tp-es-card-body">
                <?php if ($icon) : ?><div class="tp-es-card-icon-wrap"><?php echo $icon; ?></div><?php endif; ?>
                <div class="tp-es-card-text">
                    <?php if ($card['title']) : ?><p class="tp-es-card-title"><?php echo esc_html($card['title']); ?></p><?php endif; ?>
                    <?php if ($card['caption']) : ?><p class="tp-es-card-caption"><?php echo esc_html($card['caption']); ?></p><?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
