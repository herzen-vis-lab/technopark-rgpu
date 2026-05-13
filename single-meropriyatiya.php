<?php
/**
 * Single template for Meropriyatiya
 */

get_header();
?>

<main class="max-w-7xl mx-auto px-6 py-12">
    <?php if (have_posts()) : while (have_posts()) : the_post(); 
        
        $start = get_post_meta(get_the_ID(), '_event_date_start', true);
        $end = get_post_meta(get_the_ID(), '_event_date_end', true);

        $start_date = '';
        $end_date = '';
        if ($start) {
            $start_date = date('d.m.Y', strtotime($start));
        }
        if ($end) {
            $end_date = date('d.m.Y', strtotime($end));
        }

        $date_text = $start_date;
        if ($end_date && $end_date !== $start_date) {
            $date_text .= ' — ' . $end_date;
        }
        
        $full_description = apply_filters('the_content', get_the_content());
        
        $gallery_ids = get_post_meta(get_the_ID(), '_event_gallery', true);
        $stats_cards = get_post_meta(get_the_ID(), '_event_stats', true) ?: [];
        $event_buttons = get_post_meta(get_the_ID(), '_event_buttons', true) ?: [];
    ?>
        
        <div class="mb-6">
            <a href="<?php echo home_url('/event/'); ?>" class="text-primary text-body" style="text-decoration: none;">
                ← Все мероприятия
            </a>
        </div>

        <!-- Кнопки -->
        <?php if (!empty($event_buttons)) : ?>
            <div class="flex flex-col sm:flex-row gap-4 mb-8">
                <?php foreach ($event_buttons as $button) : ?>
                    <a href="<?php echo esc_url($button['link'] ?: '#'); ?>" 
                       class="tp-btn <?php echo $button['style'] === 'primary' ? 'tp-btn--primary' : 'tp-btn--outline'; ?>">
                        <?php echo esc_html($button['text']); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <h3 class="text-black mb-4"><?php the_title(); ?></h3>
        
        <?php if ($date_text) : ?>
            <p class="text-small text-black/60 mb-8"><?php echo esc_html($date_text); ?></p>
        <?php endif; ?>
        
        <!-- Галерея -->
        <?php if (!empty($gallery_ids)) : ?>
            <?php get_template_part('template-parts/gallery-slider', null, array('ids' => $gallery_ids)); ?>
        <?php endif; ?>
        
        <!-- Карточки с иконками -->
        <?php get_template_part('template-parts/cards-with-icon', null, array('cards' => $stats_cards)); ?>
        
        <!-- Полное описание -->
        <div class="text-body prose max-w-none mb-12">
            <?php echo wp_kses_post($full_description); ?>
        </div>
        
    <?php endwhile; endif; ?>
</main>

<?php get_footer(); ?>