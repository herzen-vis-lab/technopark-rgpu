<?php
/**
 * Single template for Directions
 */

get_header();
?>

<main class="max-w-7xl mx-auto px-6 py-12">
    <?php if (have_posts()) : while (have_posts()) : the_post(); 
        
        $full_description = get_the_content();
        $gallery_ids = get_post_meta(get_the_ID(), '_direction_gallery', true);
        $buttons = get_post_meta(get_the_ID(), '_direction_buttons', true) ?: array();
        ?>
        
        <div class="mb-6">
            <a href="<?php echo home_url('/#directions'); ?>" class="text-primary text-body" style="text-decoration: none;">
                ← Все направления
            </a>
        </div>
        
        <h3 class="text-black mb-4"><?php the_title(); ?></h3>
        
        <?php if (!empty($gallery_ids)) : ?>
            <?php get_template_part('template-parts/gallery-slider', null, array('ids' => $gallery_ids)); ?>
        <?php endif; ?>
        
        <div class="text-body prose max-w-none mb-4">
            <?php echo wp_kses_post($full_description); ?>
        </div>
        
        <?php if (!empty($buttons)) : ?>
            <div class="flex flex-col sm:flex-row gap-4 mt-8 pt-4">
                <?php foreach ($buttons as $button) : ?>
                    <?php if (!empty($button['text']) && !empty($button['link'])) : ?>
                        <?php 
                        $btn_class = ($button['style'] === 'primary') 
                            ? 'tp-btn tp-btn--primary text-body w-full sm:w-auto text-center' 
                            : 'tp-btn tp-btn--outline text-body w-full sm:w-auto text-center';
                        ?>
                        <a href="<?php echo esc_url($button['link']); ?>" class="<?php echo esc_attr($btn_class); ?>">
                            <?php echo esc_html($button['text']); ?>
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
    <?php endwhile; endif; ?>
</main>

<?php get_footer(); ?>