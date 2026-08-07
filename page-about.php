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

// Гербы
$emblem_1_id = get_post_meta($about_id, '_about_content_emblem_1', true);
$emblem_2_id = get_post_meta($about_id, '_about_content_emblem_2', true);
$emblem_3_id = get_post_meta($about_id, '_about_content_emblem_3', true);
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
    
    <!-- ГЕРБЫ -->
    <?php if (!empty($emblem_1_id) || !empty($emblem_2_id) || !empty($emblem_3_id)) : ?>
        <div class="flex flex-wrap justify-center items-center gap-20 my-12">
            <?php if (!empty($emblem_1_id)) : ?>
                <?php echo wp_get_attachment_image($emblem_1_id, 'medium', false, ['class' => 'h-[150px] w-auto object-contain']); ?>
            <?php endif; ?>
            <?php if (!empty($emblem_2_id)) : ?>
                <?php echo wp_get_attachment_image($emblem_2_id, 'medium', false, ['class' => 'h-[150px] w-auto object-contain']); ?>
            <?php endif; ?>
            <?php if (!empty($emblem_3_id)) : ?>
                <?php echo wp_get_attachment_image($emblem_3_id, 'medium', false, ['class' => 'h-[150px] w-auto object-contain']); ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>        
          
</main>

<?php get_footer(); ?>