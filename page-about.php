<?php get_header(); ?>

<?php 
/* Template Name: О нас */

// Получаем данные из CPT about_content
$about_posts = get_posts(array(
    'post_type' => 'about_content',
    'posts_per_page' => 1,
));
$about_id = !empty($about_posts) ? $about_posts[0]->ID : 0;

$about_title = get_post_meta($about_id, '_about_content_title', true);
$about_text = get_post_meta($about_id, '_about_content_text', true);
$gallery_id = get_post_meta($about_id, '_about_content_gallery_id', true);

// Получаем гербы в виде массива
$emblems = get_post_meta($about_id, '_about_content_emblems', true);
if (empty($emblems)) {
    $emblems = array();
}
?>

<main class="max-w-7xl mx-auto px-6 py-12">
    
    <h2 class="text-black text-center py-2 mb-12"><?php echo esc_html($about_title); ?></h2>
    
    <div class="text-body mb-8"><?php echo wp_kses_post(nl2br($about_text)); ?></div>

    <?php 
    $gallery_ids = get_post_meta($about_id, '_about_content_gallery', true);
    if (!empty($gallery_ids)) : ?>
        <?php get_template_part('template-parts/gallery-slider', null, array('ids' => $gallery_ids)); ?>
    <?php endif; ?>

    <?php get_template_part('template-parts/carousel-solid'); ?>
    <?php get_template_part('template-parts/document-button'); ?>  
    <?php get_template_part('template-parts/team-carousel'); ?>
    
    <!-- ГЕРБЫ (с сортировкой) -->
    <?php if (!empty($emblems)) : ?>
        <div class="flex flex-wrap justify-center items-center gap-20 my-12">
            <?php foreach ($emblems as $emblem) : 
                if (empty($emblem['id'])) continue;
                $emblem_url = wp_get_attachment_url($emblem['id']);
                if ($emblem_url) : ?>
                    <div class="flex flex-col items-center gap-3">
                        <img src="<?php echo esc_url($emblem_url); ?>" 
                             alt="<?php echo esc_attr($emblem['title']); ?>"
                             class="h-[150px] w-auto object-contain">
                        <?php if (!empty($emblem['title'])) : ?>
                            <span class="text-small text-black/60"><?php echo esc_html($emblem['title']); ?></span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
          
</main>

<?php get_footer(); ?>