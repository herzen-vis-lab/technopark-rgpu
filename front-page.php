<?php
/**
 * Front Page Template
 */

get_header();

// Получаем логотипы
$logos = get_posts(array(
    'post_type' => 'logo',
    'posts_per_page' => -1,
    'orderby' => 'menu_order',
    'order' => 'ASC',
));
?>

<!-- ШАПКА С ЛОГОТИПАМИ -->
<?php if (!empty($logos)) : ?>
<div class="bg-primary py-3">
    <div class="max-w-7xl mx-auto px-6">
        <div class="flex flex-wrap items-center justify-center gap-8">
            <?php foreach ($logos as $logo) : 
                $image_id = get_post_meta($logo->ID, '_logo_image', true);
                $image_url = $image_id ? wp_get_attachment_url($image_id) : '';
                if ($image_url) : 
                    $image_meta = wp_get_attachment_metadata($image_id);
                    $width = isset($image_meta['width']) ? $image_meta['width'] : 120;
                    $height = isset($image_meta['height']) ? $image_meta['height'] : 60;
                    ?>
                    <img src="<?php echo esc_url($image_url); ?>" 
                         alt="Логотип"
                         width="<?php echo esc_attr($width); ?>"
                         height="<?php echo esc_attr($height); ?>"
                         class="h-15 w-auto object-contain">
            <?php endif; endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<main class="max-w-7xl mx-auto px-6 py-12">
    <?php get_template_part('template-parts/hero'); ?>
    <?php get_template_part('template-parts/stats'); ?>
    <?php get_template_part('template-parts/directions'); ?>
    <?php get_template_part('template-parts/faq'); ?>
</main>

<?php get_footer(); ?>