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
    <?php
    $collections_url = defined('TP_COLLECTIONS_URL') ? TP_COLLECTIONS_URL : 'https://collections.herzen.spb.ru';
    // Оформление каталога под сайт технопарка: набор «Технопарк РГПУ» из генератора collections.herzen.spb.ru/embed
    $collections_theme = 't_bg=ffffff&t_text=000000&t_muted=666666&t_accent=64BDE1&t_font=Roboto&t_fw=300&t_fs=16&t_lh=1.6&t_pad=4&t_h_font=Roboto&t_h_fw=400&t_h_color=000000&t_col_icon=0&t_chip_bg=ffffff&t_chip_text=000000&t_chip_border=64BDE1&t_chip_radius=20&t_chip_h=32&t_chip_fs=14&t_chip_label=000000&t_btn_bg=ffffff&t_btn_text=000000&t_btn_border=64BDE1&t_btn_radius=20&t_btn_h=40&t_btn_fs=14&t_btn_fw=400&t_input_bg=ffffff&t_input_border=64BDE1&t_input_radius=20&t_card_bg=ffffff&t_card_border=none&t_card_radius=14&t_card_shadow=2&t_card_lift=1&t_card_img_zoom=0&t_card_title_fs=18&t_card_title_fw=500&t_card_col_badge=0&t_badge_360=64BDE1&t_badge_3d=000000&t_badge_media=5A9AB5&t_panel_bg=ffffff&t_panel_border=none&t_panel_radius=20&t_panel_shadow=2&t_img_radius=14';
    ?>
    <!-- без рамки и тени: каталог оформлен под сайт и выглядит частью страницы -->
    <div class="mt-8">
        <iframe src="<?php echo esc_url($collections_url . '/catalog?collection=kop&show_desc=0&show_chars=1&embed=1&' . $collections_theme); ?>"
                title="Коробочные образовательные продукты — виртуальные коллекции РГПУ"
                width="100%" height="450" style="display: block; width: 100%; height: 450px; border: 0;"
                allow="fullscreen" allowfullscreen loading="lazy"></iframe>
    </div>
    <script src="<?php echo esc_url($collections_url . '/embed.js'); ?>" async></script>
</main>

<?php get_footer(); ?>