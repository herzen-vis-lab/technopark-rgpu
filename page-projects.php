<?php
/**
 * Template Name: КОП (Коробочные образовательные продукты)
 */

get_header();

// Получаем данные из метабоксов
$kop_title = get_post_meta(get_the_ID(), '_kop_title', true);
$kop_text = get_post_meta(get_the_ID(), '_kop_text', true);

// Значения по умолчанию
if (empty($kop_title)) {
    $kop_title = 'Коробочные образовательные продукты';
}
?>

<main class="max-w-7xl mx-auto px-6 py-12">
    
    <!-- Заголовок -->
    <h2 class="text-black text-center mb-4"><?php echo esc_html($kop_title); ?></h2>
    
    <!-- Текст (опционально) -->
    <?php if (!empty($kop_text)) : ?>
        <div class="text-body mb-2 mt-8">
            <?php echo wp_kses_post(nl2br($kop_text)); ?>
        </div>
    <?php endif; ?>
    
    <!-- Секция проектов -->
    <?php get_template_part('template-parts/projects-section'); ?>
</main>

<?php get_footer(); ?>