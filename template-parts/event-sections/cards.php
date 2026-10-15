<?php
/** Секция «Карточки»: сетка 3 / 4 / 6 карточек с иконками */
$s = $args['s'];
if (!$s['items'] && !$s['title']) return;
?>
<?php if ($s['title']) : ?>
    <h2 class="tp-es-title tp-es-title--center"><?php echo esc_html($s['title']); ?></h2>
<?php endif; ?>
<div class="tp-es-cards tp-es-cards--<?php echo esc_attr($s['layout']); ?> tp-es-cards--<?php echo esc_attr($s['style']); ?>">
    <?php foreach ($s['items'] as $card) :
        $bg = $card['bg'] ?: ($s['card_bg'] ?: '#64BDE1');
        $color = $card['color'] ?: ($s['card_color'] ?: '#FFFFFF');
        $icon = tp_es_icon_html($card['icon'], 'tp-es-card-icon');
        ?>
        <div class="tp-es-card" style="background-color: <?php echo esc_attr($bg); ?>; color: <?php echo esc_attr($color); ?>">
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
