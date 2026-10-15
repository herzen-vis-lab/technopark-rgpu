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
    
    <!-- Каталог КОП с сайта коллекций (коллекция kop на collections.herzen.spb.ru).
         embed.js подгоняет высоту iframe под содержимое; height="450" — запасная высота без скрипта.
         Прежняя сетка работ из админки: get_template_part('template-parts/projects-section') -->
    <?php $collections_url = defined('TP_COLLECTIONS_URL') ? TP_COLLECTIONS_URL : 'https://collections.herzen.spb.ru'; ?>
    <div class="mt-8 rounded-3xl shadow-custom overflow-hidden">
        <iframe src="<?php echo esc_url($collections_url . '/catalog?collection=kop&show_desc=0&embed=1'); ?>"
                title="Коробочные образовательные продукты — виртуальные коллекции РГПУ"
                width="100%" height="450" style="display: block; width: 100%; height: 450px; border: 0;"
                allow="fullscreen" allowfullscreen loading="lazy"></iframe>
    </div>
    <script src="<?php echo esc_url($collections_url . '/embed.js'); ?>" async></script>
</main>

<?php get_footer(); ?>