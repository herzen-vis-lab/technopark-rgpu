<?php
/**
 * Конструктор секций для страницы мероприятия (CPT meropriyatiya).
 *
 * Секции хранятся одним JSON-массивом в мета-поле _event_sections.
 * Схема ниже — единственный источник правды: по ней строится форма в админке
 * (js/admin-event-sections.js) и очищаются данные при сохранении.
 * Если секций нет, single-meropriyatiya.php показывает старый шаблон.
 */

if (!defined('ABSPATH')) exit;

// ==================== СХЕМА ====================

function tp_es_icons() {
    static $icons = null;
    if ($icons === null) {
        $icons = require __DIR__ . '/lucide-icons.php';
    }
    return $icons;
}

function tp_es_button_fields() {
    return array(
        array('key' => 'text', 'type' => 'text', 'label' => 'Текст кнопки'),
        array('key' => 'link_type', 'type' => 'select', 'label' => 'Куда ведёт', 'default' => 'url',
            'options' => array('url' => 'Ссылка', 'anchor' => 'Якорь на секцию')),
        array('key' => 'url', 'type' => 'url', 'label' => 'Ссылка', 'show_if' => array('link_type', 'url')),
        array('key' => 'anchor', 'type' => 'anchor', 'label' => 'Slug секции', 'show_if' => array('link_type', 'anchor')),
        array('key' => 'new_tab', 'type' => 'checkbox', 'label' => 'Открывать в новой вкладке', 'show_if' => array('link_type', 'url')),
        array('key' => 'style', 'type' => 'select', 'label' => 'Вид', 'default' => 'primary',
            'options' => array('primary' => 'Заливка', 'outline' => 'Контур', 'ghost' => 'Прозрачная', 'dark' => 'Тёмная', 'white' => 'Белая')),
        array('key' => 'size', 'type' => 'select', 'label' => 'Размер', 'default' => 'm',
            'options' => array('s' => 'Маленькая', 'm' => 'Средняя', 'l' => 'Большая')),
        array('key' => 'btn_color', 'type' => 'color', 'label' => 'Цвет кнопки', 'default' => '',
            'help' => 'Пусто — по виду кнопки. У контурной это цвет рамки, у прозрачной не используется.'),
        array('key' => 'text_color', 'type' => 'color', 'label' => 'Цвет текста кнопки', 'default' => ''),
    );
}

function tp_es_schema() {
    $icon_field = array('key' => 'icon', 'type' => 'icon', 'label' => 'Иконка');

    return array(
        'common' => array(
            array('key' => 'slug', 'type' => 'slug', 'label' => 'Slug для якоря', 'help' => 'Латиницей, например registration. По нему работают якорные кнопки.'),
            array('key' => 'bg', 'type' => 'color', 'label' => 'Фон секции', 'default' => ''),
            array('key' => 'color', 'type' => 'color', 'label' => 'Цвет текста', 'default' => ''),
            array('key' => 'title_color', 'type' => 'color', 'label' => 'Цвет заголовков', 'default' => ''),
            array('key' => 'text_size', 'type' => 'select', 'label' => 'Размер текста', 'default' => 'm',
                'options' => array('s' => 'Мелкий', 'm' => 'Обычный', 'l' => 'Крупный', 'xl' => 'Очень крупный')),
            array('key' => 'hidden', 'type' => 'checkbox', 'label' => 'Скрыть секцию'),
        ),
        'types' => array(
            'cover' => array(
                'label' => 'Обложка',
                'fields' => array(
                    array('key' => 'image', 'type' => 'image', 'label' => 'Картинка'),
                    array('key' => 'text', 'type' => 'text', 'label' => 'Надпись поверх (необязательно)'),
                    array('key' => 'text_color', 'type' => 'color', 'label' => 'Цвет надписи', 'default' => '#FFFFFF'),
                    array('key' => 'text_pos', 'type' => 'select', 'label' => 'Положение надписи', 'default' => 'center',
                        'options' => array('center' => 'По центру', 'bottom-left' => 'Слева снизу')),
                    array('key' => 'overlay', 'type' => 'checkbox', 'label' => 'Затемнять картинку под надписью', 'default' => true),
                    array('key' => 'height', 'type' => 'select', 'label' => 'Высота', 'default' => 'm',
                        'options' => array('s' => 'Низкая', 'm' => 'Средняя', 'l' => 'Высокая')),
                    array('key' => 'buttons', 'type' => 'repeater', 'label' => 'Кнопки под надписью', 'item_label' => 'Кнопка',
                        'help' => 'Выравниваются по левому краю надписи. Кнопка без ссылки/якоря не выводится.',
                        'fields' => tp_es_button_fields()),
                ),
            ),
            'hero' => array(
                'label' => 'Hero',
                'fields' => array(
                    array('key' => 'title', 'type' => 'text', 'label' => 'Заголовок', 'help' => 'Пусто — название мероприятия.'),
                    array('key' => 'show_dates', 'type' => 'checkbox', 'label' => 'Показывать даты мероприятия', 'default' => true),
                    array('key' => 'text', 'type' => 'richtext', 'label' => 'Текст', 'small' => true),
                    array('key' => 'image', 'type' => 'image', 'label' => 'Квадратная картинка справа'),
                    array('key' => 'buttons', 'type' => 'repeater', 'label' => 'Кнопки', 'item_label' => 'Кнопка',
                        'help' => 'Кнопка без ссылки/якоря не выводится.',
                        'fields' => tp_es_button_fields(),
                        'default' => array(
                            array('text' => 'Регистрация', 'link_type' => 'url', 'style' => 'primary'),
                            array('text' => 'Положение', 'link_type' => 'url', 'style' => 'outline', 'new_tab' => true),
                        )),
                ),
            ),
            'cards' => array(
                'label' => 'Карточки',
                'fields' => array(
                    array('key' => 'title', 'type' => 'text', 'label' => 'Заголовок'),
                    array('key' => 'layout', 'type' => 'select', 'label' => 'Сетка', 'default' => '3',
                        'options' => array(
                            '3' => '3 карточки (в ряд и на мобилке)',
                            '4' => '4 карточки в ряд (на мобилке 2×2)',
                            '6' => '6 карточек (3 / 2 / 1 в ряд)',
                        )),
                    array('key' => 'style', 'type' => 'select', 'label' => 'Вид карточки', 'default' => 'bottom-left',
                        'options' => array(
                            'bottom-left' => 'Иконка сверху, надпись слева снизу',
                            'center' => 'Крупный текст по центру + подпись',
                            'icon-left' => 'Иконка слева, текст справа',
                            'photo-top' => 'Фото сверху, текст снизу',
                            'photo-bg' => 'Фото на фоне, текст поверх',
                            'outline' => 'Контурная (без заливки)',
                        )),
                    array('key' => 'card_bg', 'type' => 'color', 'label' => 'Цвет карточек', 'default' => '#64BDE1'),
                    array('key' => 'card_color', 'type' => 'color', 'label' => 'Цвет текста карточек', 'default' => '#FFFFFF'),
                    array('key' => 'items', 'type' => 'repeater', 'label' => 'Карточки', 'item_label' => 'Карточка',
                        'fields' => array(
                            $icon_field,
                            array('key' => 'title', 'type' => 'text', 'label' => 'Надпись / крупный текст'),
                            array('key' => 'caption', 'type' => 'text', 'label' => 'Подпись (необязательно)'),
                            array('key' => 'image', 'type' => 'image', 'label' => 'Фото карточки',
                                'help' => 'Для видов «Фото сверху» и «Фото на фоне».'),
                            array('key' => 'bg', 'type' => 'color', 'label' => 'Свой цвет карточки', 'default' => ''),
                            array('key' => 'color', 'type' => 'color', 'label' => 'Свой цвет текста', 'default' => ''),
                        )),
                ),
            ),
            'features' => array(
                'label' => 'Иконки',
                'fields' => array(
                    array('key' => 'title', 'type' => 'text', 'label' => 'Заголовок'),
                    array('key' => 'title_align', 'type' => 'select', 'label' => 'Заголовок', 'default' => 'left',
                        'options' => array('left' => 'Слева', 'right' => 'Справа')),
                    array('key' => 'icon_bg', 'type' => 'color', 'label' => 'Фон мини-карточки иконки', 'default' => '#64BDE1'),
                    array('key' => 'icon_color', 'type' => 'color', 'label' => 'Цвет иконки', 'default' => '#FFFFFF'),
                    array('key' => 'items', 'type' => 'repeater', 'label' => 'Пункты', 'item_label' => 'Пункт',
                        'fields' => array(
                            $icon_field,
                            array('key' => 'title', 'type' => 'text', 'label' => 'Заголовок'),
                            array('key' => 'text', 'type' => 'richtext', 'label' => 'Текст', 'small' => true),
                            array('key' => 'side', 'type' => 'select', 'label' => 'Расположение', 'default' => 'left',
                                'options' => array('left' => 'Слева', 'right' => 'Справа')),
                        )),
                ),
            ),
            'form' => array(
                'label' => 'Яндекс Форма',
                'fields' => array(
                    array('key' => 'title', 'type' => 'text', 'label' => 'Заголовок', 'default' => 'Регистрация'),
                    array('key' => 'src', 'type' => 'embed', 'label' => 'Ссылка на форму или код iframe',
                        'help' => 'Например https://forms.yandex.ru/u/XXXX/?iframe=1 — или вставьте целиком код встраивания.'),
                    array('key' => 'height', 'type' => 'number', 'label' => 'Высота, px', 'default' => 700, 'min' => 200, 'max' => 5000),
                ),
            ),
            'buttons' => array(
                'label' => 'Кнопки',
                'fields' => array(
                    array('key' => 'items', 'type' => 'repeater', 'label' => 'Кнопки (по центру)', 'item_label' => 'Кнопка',
                        'fields' => tp_es_button_fields()),
                ),
            ),
            'partners' => array(
                'label' => 'Партнёры',
                'fields' => array(
                    array('key' => 'title', 'type' => 'text', 'label' => 'Заголовок', 'default' => 'Партнёры'),
                    array('key' => 'per_row', 'type' => 'select', 'label' => 'В ряду на компьютере', 'default' => '5',
                        'options' => array('1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '8' => '8')),
                    array('key' => 'per_row_mobile', 'type' => 'select', 'label' => 'В ряду на мобилке', 'default' => '2',
                        'options' => array('1' => '1', '2' => '2', '3' => '3', '4' => '4')),
                    array('key' => 'height', 'type' => 'number', 'label' => 'Высота логотипа, px', 'default' => 90, 'min' => 1,
                        'help' => 'Логотип не обрезается: вписывается в эту высоту и ширину ячейки с сохранением пропорций.'),
                    array('key' => 'tiles', 'type' => 'checkbox', 'label' => 'Плитки под логотипами', 'default' => false),
                    array('key' => 'tile_bg', 'type' => 'color', 'label' => 'Цвет плиток', 'default' => '#FFFFFF', 'show_if' => array('tiles', true)),
                    array('key' => 'items', 'type' => 'repeater', 'label' => 'Логотипы', 'item_label' => 'Логотип', 'bulk' => 'image',
                        'fields' => array(
                            array('key' => 'image', 'type' => 'image', 'label' => 'Логотип'),
                            array('key' => 'url', 'type' => 'url', 'label' => 'Ссылка (необязательно)'),
                            array('key' => 'name', 'type' => 'text', 'label' => 'Название (для alt)'),
                            array('key' => 'scale', 'type' => 'number', 'label' => 'Масштаб, %', 'default' => 100, 'min' => 1,
                                'help' => 'Относительно остальных логотипов: 50 — вдвое меньше, 150 — в полтора раза больше.'),
                        )),
                ),
            ),
            'slider' => array(
                'label' => 'Слайдер фото/видео',
                'fields' => array(
                    array('key' => 'title', 'type' => 'text', 'label' => 'Заголовок'),
                    array('key' => 'ratio', 'type' => 'select', 'label' => 'Пропорции слайда', 'default' => '16/9',
                        'options' => array('16/9' => '16:9', '4/3' => '4:3', '3/2' => '3:2', '1/1' => '1:1', '21/9' => '21:9')),
                    array('key' => 'per_view', 'type' => 'select', 'label' => 'Слайдов на экране (компьютер)', 'default' => '1',
                        'options' => array('1' => '1', '2' => '2', '3' => '3')),
                    array('key' => 'items', 'type' => 'repeater', 'label' => 'Слайды', 'item_label' => 'Слайд', 'bulk' => 'image',
                        'fields' => array(
                            array('key' => 'kind', 'type' => 'select', 'label' => 'Тип', 'default' => 'image',
                                'options' => array('image' => 'Фото', 'video' => 'Видео')),
                            array('key' => 'image', 'type' => 'image', 'label' => 'Фото', 'show_if' => array('kind', 'image')),
                            array('key' => 'video', 'type' => 'url', 'label' => 'Ссылка на видео', 'show_if' => array('kind', 'video'),
                                'help' => 'VK Видео, YouTube, Rutube или прямая ссылка на .mp4'),
                            array('key' => 'caption', 'type' => 'text', 'label' => 'Подпись (необязательно)'),
                        )),
                ),
            ),
            'free' => array(
                'label' => 'Свободная секция',
                'fields' => array(
                    array('key' => 'width', 'type' => 'select', 'label' => 'Ширина', 'default' => 'narrow',
                        'options' => array('narrow' => 'Текстовая колонка', 'wide' => 'Во всю ширину')),
                    array('key' => 'content', 'type' => 'richtext', 'label' => 'Содержимое',
                        'help' => 'Текст, картинки, видео, шорткоды ([vk_video …]) и HTML — как в обычном редакторе.'),
                ),
            ),
            'faq' => array(
                'label' => 'Вопросы и ответы',
                'fields' => array(
                    array('key' => 'title', 'type' => 'text', 'label' => 'Заголовок', 'default' => 'Часто задаваемые вопросы'),
                    array('key' => 'item_bg', 'type' => 'color', 'label' => 'Цвет плашек вопросов', 'default' => '#FFFFFF'),
                    array('key' => 'item_color', 'type' => 'color', 'label' => 'Цвет текста вопросов', 'default' => '#000000'),
                    array('key' => 'items', 'type' => 'repeater', 'label' => 'Вопросы', 'item_label' => 'Вопрос',
                        'fields' => array(
                            array('key' => 'question', 'type' => 'text', 'label' => 'Вопрос'),
                            array('key' => 'answer', 'type' => 'richtext', 'label' => 'Ответ', 'small' => true),
                        )),
                ),
            ),
        ),
    );
}

// ==================== ОЧИСТКА ДАННЫХ ====================

function tp_es_sanitize_field($field, $value) {
    $default = isset($field['default']) ? $field['default'] : null;

    switch ($field['type']) {
        case 'text':
            return sanitize_text_field((string) $value);
        case 'textarea':
            return wp_kses_post((string) $value);
        case 'richtext':
            // Как в обычном редакторе: администратору можно любой HTML (iframe, видео), остальным — безопасный набор
            return current_user_can('unfiltered_html') ? (string) $value : wp_kses_post((string) $value);
        case 'url':
            return esc_url_raw(trim((string) $value));
        case 'slug':
        case 'anchor':
            return sanitize_title(ltrim((string) $value, '#'));
        case 'number':
            $n = is_numeric($value) ? intval($value) : intval($default);
            if (isset($field['min'])) $n = max($field['min'], $n);
            if (isset($field['max'])) $n = min($field['max'], $n);
            return $n;
        case 'select':
            $value = (string) $value;
            return array_key_exists($value, $field['options']) ? $value : (string) $default;
        case 'checkbox':
            return $value === null ? (bool) $default : (bool) $value;
        case 'color':
            $c = sanitize_hex_color((string) $value);
            return $c ? $c : '';
        case 'image':
            return absint($value);
        case 'embed':
            $value = (string) $value;
            if (stripos($value, '<iframe') !== false && preg_match('/\ssrc=["\']([^"\']+)["\']/i', $value, $m)) {
                $value = html_entity_decode($m[1]);
            }
            return esc_url_raw(trim($value), array('https'));
        case 'icon':
            $value = is_array($value) ? $value : array();
            $source = isset($value['source']) && in_array($value['source'], array('library', 'upload'), true) ? $value['source'] : 'none';
            $name = isset($value['name']) ? sanitize_key($value['name']) : '';
            return array(
                'source' => $source,
                'name'   => array_key_exists($name, tp_es_icons()) ? $name : '',
                'image'  => isset($value['image']) ? absint($value['image']) : 0,
                'radius' => isset($value['radius']) && in_array($value['radius'], array('none', 'rounded', 'circle'), true) ? $value['radius'] : 'none',
            );
        case 'repeater':
            $items = array();
            foreach ((is_array($value) ? $value : array()) as $item) {
                if (is_array($item)) $items[] = tp_es_sanitize_fields($field['fields'], $item);
            }
            return $items;
    }
    return '';
}

function tp_es_sanitize_fields($fields, $data) {
    $clean = array();
    foreach ($fields as $field) {
        $clean[$field['key']] = tp_es_sanitize_field($field, isset($data[$field['key']]) ? $data[$field['key']] : null);
    }
    return $clean;
}

function tp_es_sanitize_sections($raw) {
    $schema = tp_es_schema();
    $sections = array();
    foreach ((is_array($raw) ? $raw : array()) as $section) {
        if (!is_array($section) || empty($section['type']) || !isset($schema['types'][$section['type']])) continue;
        $type = $section['type'];
        $clean = array('type' => $type);
        $clean += tp_es_sanitize_fields($schema['common'], $section);
        $clean += tp_es_sanitize_fields($schema['types'][$type]['fields'], $section);
        $sections[] = $clean;
    }
    return $sections;
}

function tp_es_get_sections($post_id) {
    $json = get_post_meta($post_id, '_event_sections', true);
    $sections = $json ? json_decode($json, true) : array();
    return is_array($sections) ? $sections : array();
}

// ==================== АДМИНКА ====================

function tp_es_add_metabox() {
    add_meta_box('tp_event_sections', 'Секции страницы мероприятия', 'tp_es_render_metabox', 'meropriyatiya', 'normal', 'high');
}
add_action('add_meta_boxes', 'tp_es_add_metabox', 5);

function tp_es_render_metabox($post) {
    wp_nonce_field('tp_es_save', 'tp_es_nonce');
    $sections = tp_es_get_sections($post->ID);
    ?>
    <p class="description">
        Если добавлена хотя бы одна секция, страница мероприятия строится из секций, а старые поля
        (кнопки, галерея, карточки особенностей и текст из редактора) на странице не выводятся.
        Картинка в списке мероприятий — «Картинка карточки» в правой колонке.
    </p>
    <div id="tp-es-root" data-post-title="<?php echo esc_attr($post->post_title); ?>"></div>
    <input type="hidden" name="tp_event_sections" id="tp-es-input" value="<?php echo esc_attr(wp_json_encode($sections)); ?>">
    <?php
}

function tp_es_collect_image_ids($data, &$ids) {
    if (!is_array($data)) return;
    foreach ($data as $key => $value) {
        if (($key === 'image') && is_numeric($value) && $value) {
            $ids[] = (int) $value;
        } elseif (is_array($value)) {
            tp_es_collect_image_ids($value, $ids);
        }
    }
}

function tp_es_admin_assets($hook) {
    if (!in_array($hook, array('post.php', 'post-new.php'), true)) return;
    $screen = get_current_screen();
    if (!$screen || $screen->post_type !== 'meropriyatiya') return;

    wp_enqueue_media();
    wp_enqueue_editor(); // для «Свободной секции» (wp.editor.initialize)
    $dir = get_stylesheet_directory();
    $uri = get_stylesheet_directory_uri();
    wp_enqueue_style('tp-es-admin', $uri . '/css/admin-event-sections.css', array(), filemtime($dir . '/css/admin-event-sections.css'));
    wp_enqueue_script('tp-es-admin', $uri . '/js/admin-event-sections.js', array('jquery', 'jquery-ui-sortable'), filemtime($dir . '/js/admin-event-sections.js'), true);

    // Превью уже выбранных картинок
    $ids = array();
    global $post;
    if ($post) tp_es_collect_image_ids(tp_es_get_sections($post->ID), $ids);
    $images = array();
    foreach (array_unique($ids) as $id) {
        $src = wp_get_attachment_image_url($id, 'medium'); // medium не обрезается, превью показывает картинку целиком
        if ($src) $images[$id] = $src;
    }

    $icons = array();
    foreach (tp_es_icons() as $name => $icon) {
        $icons[] = array('name' => $name, 'label' => $icon[0], 'svg' => $icon[1]);
    }

    wp_localize_script('tp-es-admin', 'tpEventSections', array(
        'schema' => tp_es_schema(),
        'icons'  => $icons,
        'images' => $images,
    ));
}
add_action('admin_enqueue_scripts', 'tp_es_admin_assets');

function tp_es_save($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!isset($_POST['tp_es_nonce'], $_POST['tp_event_sections'])) return;
    if (!wp_verify_nonce($_POST['tp_es_nonce'], 'tp_es_save')) return;
    if (!current_user_can('edit_post', $post_id)) return;

    $raw = json_decode(wp_unslash($_POST['tp_event_sections']), true);
    $sections = tp_es_sanitize_sections($raw);

    if ($sections) {
        // wp_slash: update_post_meta снимает слэши, а в JSON они значимы
        update_post_meta($post_id, '_event_sections', wp_slash(wp_json_encode($sections, JSON_UNESCAPED_UNICODE)));
    } else {
        delete_post_meta($post_id, '_event_sections');
    }
}
add_action('save_post_meropriyatiya', 'tp_es_save');

// Featured image = картинка карточки в списке мероприятий
add_action('after_setup_theme', function () {
    add_theme_support('post-thumbnails', array('meropriyatiya'));
}, 11);

add_filter('post_type_labels_meropriyatiya', function ($labels) {
    $labels->featured_image        = 'Картинка карточки (список мероприятий)';
    $labels->set_featured_image    = 'Выбрать картинку карточки';
    $labels->remove_featured_image = 'Убрать картинку карточки';
    $labels->use_featured_image    = 'Использовать как картинку карточки';
    return $labels;
});

// ==================== ВЫВОД ====================

function tp_es_front_assets() {
    if (!is_singular('meropriyatiya') || !tp_es_get_sections(get_queried_object_id())) return;
    $dir = get_stylesheet_directory();
    $uri = get_stylesheet_directory_uri();
    wp_enqueue_style('tp-es', $uri . '/css/event-sections.css', array('technopark-style'), filemtime($dir . '/css/event-sections.css'));
    wp_enqueue_script('tp-es', $uri . '/js/event-sections.js', array(), filemtime($dir . '/js/event-sections.js'), true);
}
add_action('wp_enqueue_scripts', 'tp_es_front_assets');

function tp_es_icon_html($icon, $class = 'tp-es-icon') {
    if (empty($icon) || empty($icon['source'])) return '';

    if ($icon['source'] === 'library' && !empty($icon['name'])) {
        $icons = tp_es_icons();
        if (!isset($icons[$icon['name']])) return '';
        return '<svg class="' . esc_attr($class) . '" width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $icons[$icon['name']][1] . '</svg>';
    }

    if ($icon['source'] === 'upload' && !empty($icon['image'])) {
        $src = wp_get_attachment_image_url($icon['image'], 'medium');
        if (!$src) return '';
        return '<img class="' . esc_attr($class) . ' tp-es-icon-img tp-es-radius-' . esc_attr($icon['radius']) . '" src="' . esc_url($src) . '" alt="" loading="lazy">';
    }

    return '';
}

function tp_es_button_html($button) {
    if (empty($button['text'])) return '';

    if (($button['link_type'] ?? 'url') === 'anchor') {
        if (empty($button['anchor'])) return '';
        $href = '#' . $button['anchor'];
        $target = '';
    } else {
        if (empty($button['url'])) return '';
        $href = technopark_button_url($button['url']);
        $target = !empty($button['new_tab']) ? ' target="_blank" rel="noopener"' : '';
    }

    $style = in_array($button['style'] ?? '', array('primary', 'outline', 'ghost', 'dark', 'white'), true) ? $button['style'] : 'primary';

    // Свои цвета кнопки: у контурной «цвет кнопки» — это рамка, у остальных — заливка
    $css = '';
    if (!empty($button['btn_color'])) {
        // у контурной «цвет кнопки» — рамка; у прозрачной заливки нет вовсе, цвет кнопки ей не нужен
        $css .= $style === 'ghost' ? '' : ($style === 'outline'
            ? 'border-color:' . $button['btn_color'] . ';'
            : 'background-color:' . $button['btn_color'] . ';border-color:' . $button['btn_color'] . ';');
    }
    if (!empty($button['text_color'])) {
        $css .= 'color:' . $button['text_color'] . ';';
    }

    $size = in_array($button['size'] ?? '', array('s', 'l'), true) ? ' tp-es-btn--' . $button['size'] : '';

    return '<a href="' . esc_url($href) . '" class="tp-btn tp-btn--' . $style . $size . ' text-body"' . $target
        . ($css ? ' style="' . esc_attr($css) . '"' : '') . '>' . esc_html($button['text']) . '</a>';
}

/**
 * Ссылка на видео → что вставлять: iframe (VK, YouTube, Rutube) или <video> (файл).
 * Возвращает array('iframe' | 'video', src) или null.
 */
function tp_es_video_embed($url) {
    if (!$url) return null;

    // VK: vkvideo.ru/video-206057399_456239042, vk.com/video-…, vk.com/clip-…
    if (preg_match('~(?:vk\.com|vkvideo\.ru)/(?:video|clip)(-?\d+)_(\d+)~', $url, $m)) {
        return array('iframe', 'https://vk.com/video_ext.php?oid=' . $m[1] . '&id=' . $m[2] . '&hd=2');
    }
    if (strpos($url, 'video_ext.php') !== false) {
        return array('iframe', $url);
    }
    if (preg_match('~(?:youtube\.com/(?:watch\?v=|embed/|shorts/)|youtu\.be/)([\w-]{11})~', $url, $m)) {
        return array('iframe', 'https://www.youtube.com/embed/' . $m[1]);
    }
    if (preg_match('~rutube\.ru/(?:video|play/embed)/([0-9a-f]{32})~', $url, $m)) {
        return array('iframe', 'https://rutube.ru/play/embed/' . $m[1]);
    }
    if (preg_match('~\.(mp4|webm|mov)(\?.*)?$~i', $url)) {
        return array('video', $url);
    }
    return array('iframe', $url);
}

function tp_es_text_html($text) {
    return wpautop(wp_kses_post($text));
}

function tp_es_event_dates($post_id) {
    $start = get_post_meta($post_id, '_event_date_start', true);
    $end = get_post_meta($post_id, '_event_date_end', true);
    $text = $start ? date('d.m.Y', strtotime($start)) : '';
    if ($end && $end !== $start) {
        $text .= ' — ' . date('d.m.Y', strtotime($end));
    }
    return $text;
}

function tp_es_render_sections($post_id) {
    $sections = tp_es_get_sections($post_id);
    $has_hero = false;
    foreach ($sections as $section) {
        if ($section['type'] === 'hero' && empty($section['hidden'])) $has_hero = true;
    }
    if (!$has_hero) {
        echo '<h1 class="screen-reader-text">' . esc_html(get_the_title($post_id)) . '</h1>';
    }

    foreach ($sections as $index => $section) {
        if (!empty($section['hidden'])) continue;
        $type = $section['type'];

        $style = '';
        if (!empty($section['bg'])) $style .= 'background-color:' . $section['bg'] . ';';
        if (!empty($section['color'])) $style .= 'color:' . $section['color'] . ';';
        if (!empty($section['title_color'])) $style .= '--tp-es-title-color:' . $section['title_color'] . ';';
        $sizes = array('s' => '0.9', 'l' => '1.15', 'xl' => '1.3');
        if (isset($sizes[$section['text_size'] ?? ''])) $style .= '--tp-es-fs:' . $sizes[$section['text_size']] . ';';
        $classes = 'tp-es tp-es--' . $type . (!empty($section['bg']) ? ' tp-es--has-bg' : '');

        printf(
            '<section%s class="%s"%s>',
            !empty($section['slug']) ? ' id="' . esc_attr($section['slug']) . '"' : '',
            esc_attr($classes),
            $style ? ' style="' . esc_attr($style) . '"' : ''
        );
        echo '<div class="tp-es-inner">';
        get_template_part('template-parts/event-sections/' . $type, null, array(
            's'       => $section,
            'post_id' => $post_id,
            'index'   => $index,
        ));
        echo '</div></section>';
    }
}
