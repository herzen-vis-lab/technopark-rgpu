<?php
/** Свободная секция: содержимое визуального редактора, обрабатывается как обычный контент записи */
$s = $args['s'];
if (trim($s['content']) === '') return;
?>
<div class="tp-es-free tp-es-free--<?php echo esc_attr($s['width']); ?> text-body prose max-w-none">
    <?php echo apply_filters('the_content', $s['content']); ?>
</div>
