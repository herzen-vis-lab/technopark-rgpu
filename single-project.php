<?php
/**
 * Single template for Project (КОП)
 */

get_header();
?>

<main class="max-w-7xl mx-auto px-6 py-12">
    <?php if (have_posts()) : while (have_posts()) : the_post(); 
        
        // Получаем данные из метабоксов
        $project_view = get_post_meta(get_the_ID(), '_project_view', true);
        $full_description = apply_filters('the_content', get_the_content());
        
        // Возрастные группы (таксономия)
        $ages = wp_get_post_terms(get_the_ID(), 'project_age', ['fields' => 'names']);
        $age_text = implode(', ', $ages);
        
        // Направления (таксономия)
        $directions = wp_get_post_terms(get_the_ID(), 'project_direction', ['fields' => 'names']);
        $direction_text = implode(', ', $directions);
        
        // Галерея изображений
        $gallery_ids = get_post_meta(get_the_ID(), '_project_gallery', true);
    ?>
        
        <!-- Хлебные крошки -->
        <div class="mb-6">
            <a href="<?php echo home_url('/kop/'); ?>" class="text-primary text-body" style="text-decoration: none;">
                ← Все коробочные образовательные продукты
            </a>
        </div>
        
        <!-- Вид / Профиль -->
        <?php if ($project_view) : ?>
            <p class="text-small text-black mb-2"><?php echo esc_html($project_view); ?></p>
        <?php endif; ?>        
        
        <!-- Заголовок -->
        <h3 class="text-black mb-4"><?php the_title(); ?></h3>
        
        <!-- Возраст -->
        <?php if ($age_text) : ?>
            <p class="text-small text-black/60 mb-2">Возраст: <?php echo esc_html($age_text); ?></p>
        <?php endif; ?>
        
        <!-- Направление -->
        <?php if ($direction_text) : ?>
            <p class="text-small text-black/60 mb-8">Направление: <?php echo esc_html($direction_text); ?></p>
        <?php endif; ?>
        
        <!-- СЛАЙДЕР / ФОТО -->
        <?php if (!empty($gallery_ids)) : ?>
            <?php get_template_part('template-parts/gallery-slider', null, array('ids' => $gallery_ids)); ?>
        <?php endif; ?>
        
        <!-- Полное описание -->
        <div class="text-body prose max-w-none mb-12">
            <?php echo wp_kses_post($full_description); ?>
        </div>
        
    <?php endwhile; endif; ?>
</main>

<?php get_footer(); ?>