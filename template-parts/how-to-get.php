<?php
/**
 * «Как добраться»: карта университета слева, адрес и режим работы справа.
 * Используется на странице «О нас» и через шорткод [tp_how_to_get title="…"] (например, «Как пройти в Кванториум»).
 */
$title = isset($args['title']) ? $args['title'] : 'Как добраться';
$icons = function_exists('tp_es_icons') ? tp_es_icons() : array();
$icon = function ($name) use ($icons) {
    if (!isset($icons[$name])) return '';
    return '<svg class="tp-how-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $icons[$name][1] . '</svg>';
};
// ВКонтакте — ссылка и иконка те же, что в футере (Технопарк → Футер → Соцсети)
$vk_link = 'https://vk.com/technoparkrgpu';
$vk_svg = '';
$footer = get_posts(array('post_type' => 'footer_settings', 'posts_per_page' => 1));
if ($footer) {
    foreach ((array) get_post_meta($footer[0]->ID, '_footer_socials', true) as $social) {
        if (!empty($social['link']) && preg_match('~(^|\.)vk\.(com|ru)$~', (string) parse_url($social['link'], PHP_URL_HOST))) {
            $vk_link = $social['link'];
            $vk_svg = preg_replace(array('/\s(width|height)="[^"]*"/', '/fill="#0+"/'), array('', 'fill="currentColor"'), (string) $social['icon']);
            $vk_svg = str_replace('<svg', '<svg class="tp-how-icon" aria-hidden="true"', $vk_svg);
        }
    }
}

// [иконка, подпись, текст, ссылка]
$items = array(
    array($icon('map-pin'), 'Адрес', 'наб. реки Мойки, 48-50-52АК, Санкт-Петербург', ''),
    array($icon('building-2'), 'Вход', '1 корпус, вход со стороны торца', ''),
    array($icon('clock'), 'Режим работы', 'Открыто с понедельника по пятницу', ''),
    array($icon('mail'), 'Почта', 'technopark.herzen@yandex.ru', 'mailto:technopark.herzen@yandex.ru'),
    array($vk_svg ?: $icon('link'), 'ВКонтакте', preg_replace('~^https?://(www\.)?~', '', $vk_link), $vk_link),
);
?>
<section class="tp-how py-12">
    <?php if ($title) : ?>
        <h2 class="text-black text-center mb-10"><?php echo esc_html($title); ?></h2>
    <?php endif; ?>
    <div class="tp-how-grid">
        <div class="tp-how-map">
            <iframe src="https://map.herzen.spb.ru/?division=technopark&amp;lang=ru&amp;captions=1&amp;shape=circle&amp;lat=59.93331145079211&amp;lng=30.321088957238896&amp;zoom=16"
                    title="Карта: как добраться до Технопарка"
                    allow="fullscreen" allowfullscreen loading="lazy"></iframe>
        </div>
        <div class="tp-how-info">
            <?php foreach ($items as $item) : ?>
                <div class="tp-how-item">
                    <div class="tp-how-badge"><?php echo $item[0]; ?></div>
                    <div>
                        <p class="tp-how-label text-small"><?php echo esc_html($item[1]); ?></p>
                        <?php if ($item[3]) : ?>
                            <a class="tp-how-text tp-how-link text-body" href="<?php echo esc_url($item[3]); ?>"<?php echo strpos($item[3], 'http') === 0 ? ' target="_blank" rel="noopener"' : ''; ?>><?php echo esc_html($item[2]); ?></a>
                        <?php else : ?>
                            <p class="tp-how-text text-body"><?php echo esc_html($item[2]); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
