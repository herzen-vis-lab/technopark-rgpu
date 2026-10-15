<?php
/** Секция «Слайдер фото/видео»: прокрутка со snap, стрелки и точки (js/event-sections.js) */
$s = $args['s'];
$slides = array();
foreach ($s['items'] as $item) {
    if ($item['kind'] === 'video') {
        $embed = tp_es_video_embed($item['video']);
        if ($embed) $slides[] = array('video' => $embed, 'caption' => $item['caption']);
    } elseif ($item['image'] && ($src = wp_get_attachment_image_url($item['image'], 'large'))) {
        $slides[] = array('image' => $src, 'alt' => get_post_meta($item['image'], '_wp_attachment_image_alt', true), 'caption' => $item['caption']);
    }
}
if (!$slides) return;
$style = '--tp-es-ratio: ' . $s['ratio'] . '; --tp-es-per-view: ' . (int) $s['per_view'] . ';';
?>
<?php if ($s['title']) : ?>
    <h2 class="tp-es-title tp-es-title--center"><?php echo esc_html($s['title']); ?></h2>
<?php endif; ?>
<div class="tp-es-slider" data-tp-slider style="<?php echo esc_attr($style); ?>">
    <div class="tp-es-slider-viewport">
        <div class="tp-es-slider-track" data-tp-slider-track>
            <?php foreach ($slides as $i => $slide) : ?>
                <figure class="tp-es-slide" aria-label="<?php echo ($i + 1) . ' из ' . count($slides); ?>">
                    <div class="tp-es-slide-media">
                        <?php if (isset($slide['image'])) : ?>
                            <img src="<?php echo esc_url($slide['image']); ?>" alt="<?php echo esc_attr($slide['alt']); ?>" loading="lazy">
                        <?php elseif ($slide['video'][0] === 'video') : ?>
                            <video src="<?php echo esc_url($slide['video'][1]); ?>" controls preload="metadata" playsinline></video>
                        <?php else : ?>
                            <iframe src="<?php echo esc_url($slide['video'][1]); ?>" allow="autoplay; encrypted-media; fullscreen; picture-in-picture; screen-wake-lock" allowfullscreen loading="lazy" title="Видео"></iframe>
                        <?php endif; ?>
                    </div>
                    <?php if ($slide['caption']) : ?>
                        <figcaption class="tp-es-slide-caption text-small"><?php echo esc_html($slide['caption']); ?></figcaption>
                    <?php endif; ?>
                </figure>
            <?php endforeach; ?>
        </div>
        <?php if (count($slides) > 1) : ?>
            <button type="button" class="tp-es-slider-arrow tp-es-slider-arrow--prev" data-tp-slider-prev aria-label="Предыдущий слайд">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
            </button>
            <button type="button" class="tp-es-slider-arrow tp-es-slider-arrow--next" data-tp-slider-next aria-label="Следующий слайд">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
            </button>
        <?php endif; ?>
    </div>
    <?php if (count($slides) > 1) : ?>
        <div class="tp-es-slider-dots" data-tp-slider-dots></div>
    <?php endif; ?>
</div>
