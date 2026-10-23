<?php
/** Секция «Карточки»: сетка 3 / 4 / 6 карточек; виды — см. поле «Вид карточки» в inc/event-sections.php */
$s = $args['s'];
if (!$s['items'] && !$s['title']) return;
$style = $s['style'];
$with_photo = in_array($style, array('photo-top', 'photo-bg'), true);
?>
<?php if ($s['title']) : ?>
    <h2 class="tp-es-title tp-es-title--center"><?php echo esc_html($s['title']); ?></h2>
<?php endif; ?>
<div class="tp-es-cards tp-es-cards--<?php echo esc_attr($s['layout']); ?> tp-es-cards--<?php echo esc_attr($style); ?>">
    <?php foreach ($s['items'] as $card) :
        $bg = $card['bg'] ?: ($s['card_bg'] ?: '#64BDE1');
        $color = $card['color'] ?: ($s['card_color'] ?: '#FFFFFF');
        if ($style === 'outline') {
            // контурная: рамка цвета карточки, текст — свой цвет текста или тоже цвет карточки
            $css = 'border-color:' . $bg . ';color:' . ($card['color'] ?: $bg);
        } else {
            $css = 'background-color:' . $bg . ';color:' . $color;
        }
        $icon = tp_es_icon_html($card['icon'], 'tp-es-card-icon');
        $photo = ($with_photo && !empty($card['image'])) ? wp_get_attachment_image_url($card['image'], 'large') : '';
        ?>
        <div class="tp-es-card<?php echo $photo ? ' tp-es-card--has-photo' : ''; ?>" style="<?php echo esc_attr($css); ?>">
            <?php if ($photo) : ?>
                <div class="tp-es-card-photo"><img src="<?php echo esc_url($photo); ?>" alt="" loading="lazy"></div>
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
