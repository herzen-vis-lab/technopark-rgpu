<?php
/**
 * Секция «Партнёры»: логотипы в сетке.
 * Картинка не обрезается и сохраняет пропорции; «Масштаб, %» у логотипа меняет его размер относительно остальных.
 */
$s = $args['s'];
$items = array_filter($s['items'], function ($item) { return !empty($item['image']); });
if (!$items) return;

// Высота ряда — по самому крупному логотипу, чтобы увеличенные не вылезали из ячейки
$max_scale = max(array_map(function ($item) { return max(1, (int) ($item['scale'] ?? 100)); }, $items));
$tile_bg = !empty($s['tile_bg']) ? $s['tile_bg'] : '#FFFFFF';
$style = '--tp-es-tile-bg: ' . $tile_bg . '; --tp-es-per-row: ' . (int) $s['per_row'] . '; --tp-es-per-row-mobile: ' . (int) $s['per_row_mobile']
    . '; --tp-es-logo-h: ' . (int) $s['height'] . 'px; --tp-es-row-scale: ' . ($max_scale / 100) . ';';
?>
<?php if ($s['title']) : ?>
    <h2 class="tp-es-title tp-es-title--center"><?php echo esc_html($s['title']); ?></h2>
<?php endif; ?>
<div class="tp-es-partners<?php echo $s['tiles'] ? ' tp-es-partners--tiles' : ''; ?><?php echo ($s['align'] ?? 'center') === 'left' ? ' tp-es-partners--left' : ''; ?>" style="<?php echo esc_attr($style); ?>">
    <?php foreach ($items as $item) :
        // 'large' и крупнее WordPress не обрезает; в srcset попадают только размеры с теми же пропорциями
        $img = wp_get_attachment_image($item['image'], 'large', false, array(
            'class'   => 'tp-es-partner-img',
            'alt'     => $item['name'] ?: get_post_meta($item['image'], '_wp_attachment_image_alt', true),
            'loading' => 'lazy',
            'sizes'   => '(max-width: 767px) 50vw, 33vw',
        ));
        if (!$img) continue;
        $cell = 'style="--tp-es-scale: ' . (max(1, (int) ($item['scale'] ?? 100)) / 100) . '"';
        ?>
        <?php if ($item['url']) : ?>
            <a class="tp-es-partner" href="<?php echo esc_url($item['url']); ?>" target="_blank" rel="noopener" <?php echo $cell; ?>><span class="tp-es-partner-box"><?php echo $img; ?></span></a>
        <?php else : ?>
            <div class="tp-es-partner" <?php echo $cell; ?>><span class="tp-es-partner-box"><?php echo $img; ?></span></div>
        <?php endif; ?>
    <?php endforeach; ?>
</div>
