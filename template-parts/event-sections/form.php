<?php
/** Секция «Яндекс Форма»: iframe с заголовком и заданной высотой */
$s = $args['s'];
if (!$s['src']) return;
?>
<?php if ($s['title']) : ?>
    <h2 class="tp-es-title tp-es-title--center"><?php echo esc_html($s['title']); ?></h2>
<?php endif; ?>
<div class="tp-es-form">
    <iframe src="<?php echo esc_url($s['src']); ?>" height="<?php echo (int) $s['height']; ?>" style="height: <?php echo (int) $s['height']; ?>px" title="<?php echo esc_attr($s['title'] ?: 'Форма'); ?>" loading="lazy"></iframe>
</div>
