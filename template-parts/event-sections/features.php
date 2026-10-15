<?php
/** Секция «Иконки»: пункты «иконка в мини-карточке + заголовок и текст», слева или справа */
$s = $args['s'];
if (!$s['items'] && !$s['title']) return;
$icon_style = 'background-color: ' . ($s['icon_bg'] ?: '#64BDE1') . '; color: ' . ($s['icon_color'] ?: '#FFFFFF');
?>
<?php if ($s['title']) : ?>
    <h2 class="tp-es-title tp-es-title--<?php echo esc_attr($s['title_align']); ?>"><?php echo esc_html($s['title']); ?></h2>
<?php endif; ?>
<div class="tp-es-features">
    <?php foreach ($s['items'] as $item) :
        $icon = tp_es_icon_html($item['icon'], 'tp-es-feature-icon');
        ?>
        <div class="tp-es-feature tp-es-feature--<?php echo esc_attr($item['side']); ?>">
            <?php if ($icon) : ?>
                <div class="tp-es-feature-badge<?php echo $item['icon']['source'] === 'upload' ? ' tp-es-feature-badge--image' : ''; ?>" style="<?php echo esc_attr($icon_style); ?>"><?php echo $icon; ?></div>
            <?php endif; ?>
            <div class="tp-es-feature-body">
                <?php if ($item['title']) : ?><h3 class="tp-es-feature-title"><?php echo esc_html($item['title']); ?></h3><?php endif; ?>
                <?php if ($item['text']) : ?><div class="tp-es-feature-text text-body"><?php echo tp_es_text_html($item['text']); ?></div><?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>
