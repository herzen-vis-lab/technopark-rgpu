<?php
/**
 * Template Name: Новая страница
 */
get_header();

$show_title = get_post_meta(get_the_ID(), '_page_show_title', true) !== '0';
$title_position = get_post_meta(get_the_ID(), '_page_title_position', true) ?: 'center';

// Получаем ID галереи
$gallery_ids = get_post_meta(get_the_ID(), '_page_gallery', true);

// Кнопки
$buttons = get_post_meta(get_the_ID(), '_page_buttons', true) ?: [];
?>

<main class="max-w-7xl mx-auto px-6 py-12">
    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
        
        <?php if ($show_title) : ?>
            <h2 class="text-black text-<?php echo esc_attr($title_position); ?> mb-8">
                <?php the_title(); ?>
            </h2>
        <?php endif; ?>
        
        <?php
        $gallery_ids = get_post_meta(get_the_ID(), '_page_gallery', true);
        if (!empty($gallery_ids)) {
            get_template_part('template-parts/gallery-slider', null, array('ids' => $gallery_ids));
        }
        ?>
        
        <div class="prose max-w-none text-body leading-relaxed mb-8">
            <?php the_content(); ?>
        </div>
        
        <?php if (!empty($buttons)) : ?>
            <div class="flex flex-col sm:flex-row gap-4 justify-start">
                <?php foreach ($buttons as $button) : ?>
                    <a href="<?php echo esc_url($button['link'] ?: '#'); ?>" 
                       class="tp-btn <?php echo $button['style'] === 'primary' ? 'tp-btn--primary' : 'tp-btn--outline'; ?>">
                        <?php echo esc_html($button['text']); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
    <?php endwhile; endif; ?>
</main>

<?php get_footer(); ?>