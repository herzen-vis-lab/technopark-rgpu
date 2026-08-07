<?php
/**
 * functions.php - Technopark Child Theme
 * Реорганизовано для удобной админ-панели с разделением на страницы
 */

// ==================== ОБЩИЕ ФУНКЦИИ ====================

// Основная функция подключения ассетов
function technopark_enqueue_assets() {
    wp_enqueue_style(
        'windpress-main',
        content_url('/uploads/windpress/data/main.css'),
        array(),
        '1.0.0'
    );

    wp_enqueue_style(
        'technopark-style',
        get_stylesheet_uri(),
        array('windpress-main'),
        '1.0.3'
    );

    wp_enqueue_script(
        'technopark-script',
        get_stylesheet_directory_uri() . '/js/script.js',
        array(),
        '1.0.2',
        true
    );
}
add_action('wp_enqueue_scripts', 'technopark_enqueue_assets');

add_filter('body_class', function($classes) {
    $classes[] = 'var(--bg-neutral-100);';
    return $classes;
});

// ==================== АДМИН-МЕНЮ ====================

function technopark_admin_menu_organize() {
    add_menu_page(
        'Технопарк',
        'Технопарк',
        'manage_options',
        'technopark-main',
        'technopark_main_dashboard',
        'dashicons-building',
        2
    );

}
add_action('admin_menu', 'technopark_admin_menu_organize');

// Главная панель
function technopark_main_dashboard() {
    echo '<div class="wrap"><h1>Технопарк</h1>';
    echo '<p>Выберите раздел для редактирования контента страниц.</p>';
    echo '</div>';
}

// Панель Главной страницы
function technopark_home_dashboard() {
    echo '<div class="wrap"><h1>Главная страница</h1>';
    echo '<p>Редактируйте секции главной страницы:</p>';
    echo '<ul>';
    echo '<li><a href="' . admin_url('edit.php?post_type=logo') . '">Логотипы в шапке</a></li>';
    $front_page_id = get_option('page_on_front');
    if ($front_page_id) {
        echo '<li><a href="' . admin_url('post.php?post=' . $front_page_id . '&action=edit') . '">Hero секция</a></li>';
    } else {
        echo '<li>Hero секция: <a href="' . admin_url('options-reading.php') . '">выберите главную страницу</a></li>';
    }
    echo '<li><a href="' . admin_url('edit.php?post_type=stats') . '">Статистика (Мы в цифрах)</a></li>';
    echo '<li><a href="' . admin_url('edit.php?post_type=directions') . '">Направления</a></li>';
    echo '<li><a href="' . admin_url('edit.php?post_type=faq') . '">FAQ</a></li>';
    echo '</ul>';
    echo '</div>';
}

// Панель О нас
function technopark_about_dashboard() {
    echo '<div class="wrap"><h1>О нас</h1>';
    echo '<p>Редактируйте секции страницы "О нас":</p>';
    echo '<ul>';
    $about_page_id = get_page_by_title('О нас') ? get_page_by_title('О нас')->ID : null;
    if ($about_page_id) {
        echo '<li><a href="' . admin_url('post.php?post=' . $about_page_id . '&action=edit') . '">Заголовок, текст, фото, гербы</a></li>';
    } else {
        echo '<li>Создайте страницу "О нас" для редактирования заголовка, текста, фото, гербов</li>';
    }
    echo '<li><a href="' . admin_url('edit.php?post_type=carousel') . '">Карусель (carousel-solid)</a></li>';
    echo '<li><a href="' . admin_url('edit.php?post_type=document') . '">Документы (docs)</a></li>';
    echo '<li><a href="' . admin_url('edit.php?post_type=team') . '">Команда (team-carousel)</a></li>';
    echo '</ul>';
    echo '</div>';
}

// Панель Мероприятия
function technopark_events_dashboard() {
    echo '<div class="wrap"><h1>Мероприятия</h1>';
    echo '<p>Редактируйте секции страницы "Мероприятия":</p>';
    echo '<ul>';
    echo '<li><a href="' . admin_url('edit.php?post_type=meropriyatiya') . '">Мероприятия (event-section)</a></li>';
    echo '</ul>';
    echo '</div>';
}



// ==================== О НАС ====================
function create_carousel_post_type() {
    register_post_type('carousel', array(
        'labels' => array(
            'name'               => 'Карусель аудиторий',
            'singular_name'      => 'Слайд карусели',
            'add_new_item'       => 'Добавить слайд',
            'edit_item'          => 'Редактировать слайд',
            'all_items'          => 'Аудитории',
        ),
        'public'              => true,
        'has_archive'         => false,
        'menu_icon'           => 'dashicons-images-alt2',
        'supports'            => array('title', 'editor', 'excerpt', 'thumbnail'),
        'show_in_menu'        => 'technopark-main',
    ));
}
add_action('init', 'create_carousel_post_type');


// ==================== КАРУСЕЛЬ (МЕТАБОКСЫ) ====================

function add_carousel_metaboxes() {
    add_meta_box('carousel_image', 'Фото слайда', 'render_carousel_image', 'carousel', 'normal', 'high');
    add_meta_box('carousel_buttons', 'Кнопки', 'render_carousel_buttons', 'carousel', 'normal', 'high');
}
add_action('add_meta_boxes', 'add_carousel_metaboxes');

function render_carousel_image($post) {
    $image_id = get_post_meta($post->ID, '_slide_image', true);
    $image_url = $image_id ? wp_get_attachment_url($image_id) : '';
    ?>
    <button type="button" class="button" id="upload_carousel_image">Выбрать изображение</button>
    <input type="hidden" id="carousel_image_id" name="carousel_image_id" value="<?php echo esc_attr($image_id); ?>">
    <div id="carousel_image_preview" style="margin-top:10px;">
        <?php if ($image_url) : ?>
            <img src="<?php echo esc_url($image_url); ?>" style="max-width:300px;">
        <?php endif; ?>
    </div>
    <script>
    jQuery(document).ready(function($) {
        $('#upload_carousel_image').click(function(e) {
            e.preventDefault();
            var frame = wp.media({ title: 'Выберите изображение', multiple: false });
            frame.on('select', function() {
                var attachment = frame.state().get('selection').first().toJSON();
                $('#carousel_image_id').val(attachment.id);
                $('#carousel_image_preview').html('<img src="' + attachment.url + '" style="max-width:300px;">');
            });
            frame.open();
        });
    });
    </script>
    <?php
}

function render_carousel_buttons($post) {
    $btn1_text = get_post_meta($post->ID, '_button1_text', true);
    $btn1_link = get_post_meta($post->ID, '_button1_link', true);
    $btn1_style = get_post_meta($post->ID, '_button1_style', true);
    $btn2_text = get_post_meta($post->ID, '_button2_text', true);
    $btn2_link = get_post_meta($post->ID, '_button2_link', true);
    $btn2_style = get_post_meta($post->ID, '_button2_style', true);
    ?>
    <h4>Кнопка 1</h4>
    <input type="text" name="button1_text" value="<?php echo esc_attr($btn1_text); ?>" placeholder="Текст" style="width:30%">
    <input type="url" name="button1_link" value="<?php echo esc_attr($btn1_link); ?>" placeholder="Ссылка" style="width:50%">
    <select name="button1_style">
        <option value="outline" <?php selected($btn1_style, 'outline'); ?>>Outline</option>
        <option value="primary" <?php selected($btn1_style, 'primary'); ?>>Primary</option>
    </select>
    <h4>Кнопка 2</h4>
    <input type="text" name="button2_text" value="<?php echo esc_attr($btn2_text); ?>" placeholder="Текст" style="width:30%">
    <input type="url" name="button2_link" value="<?php echo esc_attr($btn2_link); ?>" placeholder="Ссылка" style="width:50%">
    <select name="button2_style">
        <option value="outline" <?php selected($btn2_style, 'outline'); ?>>Outline</option>
        <option value="primary" <?php selected($btn2_style, 'primary'); ?>>Primary</option>
    </select>
    <?php
}

function save_carousel_metaboxes($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;
    
    if (isset($_POST['carousel_image_id'])) {
        update_post_meta($post_id, '_slide_image', intval($_POST['carousel_image_id']));
    }
    
    $fields = ['button1_text', 'button1_link', 'button1_style', 'button2_text', 'button2_link', 'button2_style'];
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field]));
        }
    }
}
add_action('save_post', 'save_carousel_metaboxes');

// Подключаем медиафайлы для карусели
function enqueue_media_for_carousel() {
    global $pagenow;
    if (($pagenow == 'post.php' || $pagenow == 'post-new.php') && get_post_type() == 'carousel') {
        wp_enqueue_media();
        wp_enqueue_script('jquery');
    }
}
add_action('admin_enqueue_scripts', 'enqueue_media_for_carousel');


// ==================== CPT ДЛЯ ДОКУМЕНТОВ ====================
function documents_cpt() {
    register_post_type('document', array(
        'labels' => array(
            'name' => 'Документы',
            'singular_name' => 'Документ',
            'add_new_item' => 'Добавить документ',
            'edit_item' => 'Редактировать документ',
            'all_items' => 'Документы',
        ),
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => 'technopark-main',
        'menu_icon' => 'dashicons-media-document',
        'supports' => array('title'),
    ));
}
add_action('init', 'documents_cpt');

// Добавляем метабокс для ссылки
function add_document_link_metabox() {
    add_meta_box(
        'document_link_metabox',
        'Ссылка на документ',
        'render_document_link_metabox',
        'document',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'add_document_link_metabox');

function render_document_link_metabox($post) {
    $link = get_post_meta($post->ID, '_document_link', true);
    ?>
    <input type="url" name="document_link" value="<?php echo esc_attr($link); ?>" style="width:100%" placeholder="https://...">
    <p class="description">Вставьте ссылку на документ (PDF, DOC и т.д.)</p>
    <?php
}

function save_document_link($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;
    if (get_post_type($post_id) !== 'document') return;
    
    if (isset($_POST['document_link'])) {
        update_post_meta($post_id, '_document_link', esc_url_raw($_POST['document_link']));
    }
}
add_action('save_post', 'save_document_link');



function create_team_post_type() {
    register_post_type('team', array(
        'labels' => array(
            'name'               => 'Команда',
            'singular_name'      => 'Сотрудник',
            'add_new_item'       => 'Добавить сотрудника',
            'edit_item'          => 'Редактировать сотрудника',
        ),
        'public'              => true,
        'has_archive'         => false,
        'menu_icon'           => 'dashicons-groups',
        'supports'            => array('title', 'editor', 'thumbnail'),
        'show_in_menu'        => 'technopark-main',
    ));
}



add_action('init', 'create_team_post_type');


// ==================== МЕРОПРИЯТИЯ ====================
function create_events_post_type() {
    register_post_type('meropriyatiya', array(
        'labels' => array(
            'name'               => 'Мероприятия',
            'singular_name'      => 'Мероприятие',
            'add_new_item'       => 'Добавить мероприятие',
            'edit_item'          => 'Редактировать мероприятие',
        ),
        'public'              => true,
        'has_archive'         => true,
        'menu_icon'           => 'dashicons-calendar-alt',
        'supports'            => array('title', 'editor', 'excerpt', 'thumbnail'),
        'rewrite'             => array('slug' => 'events'),
        'show_in_menu'        => 'technopark-main',
    ));
}
add_action('init', 'create_events_post_type');



// ==================== КОП ====================
function create_projects_post_type() {
    register_post_type('project', array(
        'labels' => array(
            'name'               => 'Коробочные образовательные продукты',
            'singular_name'      => 'КОП',
            'add_new_item'       => 'Добавить КОП',
            'edit_item'          => 'Редактировать КОП',
        ),
        'public'              => true,
        'has_archive'         => true,
        'menu_icon'           => 'dashicons-portfolio',
        'supports'            => array('title', 'editor', 'excerpt', 'thumbnail'),
        'rewrite'             => array('slug' => 'projects'),
        'show_in_menu'        => 'technopark-main',
    ));
}
add_action('init', 'create_projects_post_type');



// ==================== TAXONOMY - НАПРАВЛЕНИЯ ====================
function create_project_direction_taxonomy() {
    register_taxonomy('project_direction', 'project', array(
        'labels' => array(
            'name'          => 'Направления',
            'singular_name' => 'Направление',
            'add_new_item'  => 'Добавить направление',
        ),
        'hierarchical'      => true,
        'public'            => true,
        'show_ui'           => true,
        'show_in_rest'      => true,
        'show_admin_column' => true,
    ));
}
add_action('init', 'create_project_direction_taxonomy');

// ==================== TAXONOMY - ВОЗРАСТ ====================
function create_project_age_taxonomy() {
    register_taxonomy('project_age', 'project', array(
        'labels' => array(
            'name'          => 'Возрастные группы',
            'singular_name' => 'Возрастная группа',
            'add_new_item'  => 'Добавить возраст',
        ),
        'hierarchical'      => true,
        'public'            => true,
        'show_ui'           => true,
        'show_in_rest'      => true,
        'show_admin_column' => true,
    ));
}
add_action('init', 'create_project_age_taxonomy');


// ==================== CPT "О НАС" (КОНТЕНТ) ====================
function create_about_content_cpt() {
    register_post_type('about_content', array(
        'labels' => array(
            'name' => 'О нас',
            'singular_name' => 'Содержимое',
            'add_new_item' => 'Добавить содержимое',
            'edit_item' => 'Редактировать О нас',
            'all_items' => 'О нас',
        ),
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => 'technopark-main',
        'menu_icon' => 'dashicons-info',
        'supports' => array('title'),
    ));
}
add_action('init', 'create_about_content_cpt');

// Метабоксы для about_content
function add_about_content_metaboxes() {
    add_meta_box('about_content_title', 'Заголовок страницы', 'render_about_content_title', 'about_content', 'normal', 'high');
    add_meta_box('about_content_text', 'Основной текст', 'render_about_content_text', 'about_content', 'normal', 'high');
    add_meta_box('about_content_gallery', 'Галерея изображений', 'render_about_content_gallery', 'about_content', 'normal', 'high');
    add_meta_box('about_content_emblems', 'Гербы', 'render_about_content_emblems', 'about_content', 'normal', 'high');
}
add_action('add_meta_boxes', 'add_about_content_metaboxes');

function render_about_content_title($post) {
    $value = get_post_meta($post->ID, '_about_content_title', true);
    ?>
    <input type="text" name="about_content_title" value="<?php echo esc_attr($value); ?>" style="width:100%" placeholder="Кванториум и Технопарк">
    <?php
}

function render_about_content_text($post) {
    $value = get_post_meta($post->ID, '_about_content_text', true);
    ?>
    <textarea name="about_content_text" rows="10" style="width:100%"><?php echo esc_textarea($value); ?></textarea>
    <?php
}

function render_about_content_gallery($post) {
    $gallery_ids = get_post_meta($post->ID, '_about_content_gallery', true);
    $gallery_array = !empty($gallery_ids) ? explode(',', $gallery_ids) : [];
    ?>
    <div class="about-gallery-wrap">
        <button type="button" class="button" id="upload_about_gallery">+ Добавить изображения</button>
        <input type="hidden" id="about_gallery_ids" name="about_gallery_ids" value="<?php echo esc_attr($gallery_ids); ?>">
        <div id="about_gallery_preview" style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 15px;">
            <?php 
            if (!empty($gallery_array)) {
                foreach ($gallery_array as $id) {
                    $img = wp_get_attachment_image_src($id, 'thumbnail');
                    if ($img) {
                        echo '<div class="gallery-item" data-id="' . $id . '" style="position: relative; display: inline-block;">
                                <img src="' . $img[0] . '" style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px;">
                                <button type="button" class="remove-gallery-item" style="position: absolute; top: -8px; right: -8px; background: red; color: white; border: none; border-radius: 50%; width: 20px; height: 20px; cursor: pointer;">×</button>
                              </div>';
                    }
                }
            }
            ?>
        </div>
    </div>
    <script>
    jQuery(document).ready(function($) {
        let frame;
        
        $('#upload_about_gallery').click(function(e) {
            e.preventDefault();
            
            if (frame) {
                frame.open();
                return;
            }
            
            frame = wp.media({
                title: 'Выберите изображения для галереи',
                multiple: true,
                library: { type: 'image' }
            });
            
            frame.on('select', function() {
                var selection = frame.state().get('selection');
                var currentIds = $('#about_gallery_ids').val() ? $('#about_gallery_ids').val().split(',') : [];
                
                selection.map(function(attachment) {
                    attachment = attachment.toJSON();
                    if (!currentIds.includes(String(attachment.id))) {
                        currentIds.push(attachment.id);
                        $('#about_gallery_preview').append('<div class="gallery-item" data-id="' + attachment.id + '" style="position: relative; display: inline-block;">' +
                            '<img src="' + attachment.sizes.thumbnail.url + '" style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px;">' +
                            '<button type="button" class="remove-gallery-item" style="position: absolute; top: -8px; right: -8px; background: red; color: white; border: none; border-radius: 50%; width: 20px; height: 20px; cursor: pointer;">×</button>' +
                            '</div>');
                    }
                });
                
                $('#about_gallery_ids').val(currentIds.join(','));
            });
            
            frame.open();
        });
        
        $(document).on('click', '.remove-gallery-item', function() {
            var item = $(this).closest('.gallery-item');
            var id = item.data('id');
            item.remove();
            
            var ids = $('#about_gallery_ids').val().split(',');
            var newIds = ids.filter(function(i) { return i != id; });
            $('#about_gallery_ids').val(newIds.join(','));
        });
    });
    </script>
    <?php
}

function render_about_content_emblems($post) {
    for ($i = 1; $i <= 3; $i++) {
        $emblem_id = get_post_meta($post->ID, '_about_content_emblem_' . $i, true);
        $emblem_url = $emblem_id ? wp_get_attachment_url($emblem_id) : '';
        ?>
        <div style="margin-bottom:20px; padding:10px; border:1px solid #ddd;">
            <h4>Герб <?php echo $i; ?></h4>
            <button type="button" class="button upload-emblem" data-num="<?php echo $i; ?>">Выбрать изображение</button>
            <input type="hidden" name="about_content_emblem_<?php echo $i; ?>" id="about_content_emblem_<?php echo $i; ?>_id" value="<?php echo esc_attr($emblem_id); ?>">
            <div id="about_content_emblem_<?php echo $i; ?>_preview" style="margin-top:10px;">
                <?php if ($emblem_url) : ?>
                    <img src="<?php echo esc_url($emblem_url); ?>" style="max-width:150px;">
                <?php endif; ?>
            </div>
        </div>
        <?php
    }
    ?>
    <script>
    jQuery(document).ready(function($) {
        $('.upload-emblem').click(function(e) {
            e.preventDefault();
            var num = $(this).data('num');
            var frame = wp.media({ title: 'Выберите изображение', multiple: false });
            frame.on('select', function() {
                var attachment = frame.state().get('selection').first().toJSON();
                $('#about_content_emblem_' + num + '_id').val(attachment.id);
                $('#about_content_emblem_' + num + '_preview').html('<img src="' + attachment.url + '" style="max-width:150px;">');
            });
            frame.open();
        });
    });
    </script>
    <?php
}

function save_about_content_metaboxes($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;
    if (get_post_type($post_id) !== 'about_content') return;
    
    if (isset($_POST['about_content_title'])) {
        update_post_meta($post_id, '_about_content_title', sanitize_text_field($_POST['about_content_title']));
    }
    if (isset($_POST['about_content_text'])) {
        update_post_meta($post_id, '_about_content_text', sanitize_textarea_field($_POST['about_content_text']));
    }
    if (isset($_POST['about_gallery_ids'])) {
        update_post_meta($post_id, '_about_content_gallery', sanitize_text_field($_POST['about_gallery_ids']));
    }
    for ($i = 1; $i <= 3; $i++) {
        if (isset($_POST['about_content_emblem_' . $i])) {
            update_post_meta($post_id, '_about_content_emblem_' . $i, intval($_POST['about_content_emblem_' . $i]));
        }
    }
}
add_action('save_post', 'save_about_content_metaboxes');

function enqueue_media_for_about_content() {
    global $pagenow, $post;
    if (($pagenow == 'post.php' || $pagenow == 'post-new.php') && isset($post) && get_post_type($post) == 'about_content') {
        wp_enqueue_media();
        wp_enqueue_script('jquery');
    }
}
add_action('admin_enqueue_scripts', 'enqueue_media_for_about_content');

// ==================== Главная ====================
function logos_cpt() {
    register_post_type('logo', array(
        'labels' => array(
            'name' => 'Логотипы в шапке',
            'singular_name' => 'Логотип',
            'add_new_item' => 'Добавить логотип',
            'edit_item' => 'Редактировать логотип',
            'all_items' => 'Логотипы на главной странице',
        ),
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => 'technopark-main',
        'menu_icon' => 'dashicons-format-image',
        'supports' => array('title'),
    ));
}
add_action('init', 'logos_cpt');

// Добавляем метабокс для загрузки изображения
function add_logo_image_metabox() {
    add_meta_box(
        'logo_image_metabox',
        'Изображение логотипа',
        'render_logo_image_metabox',
        'logo',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'add_logo_image_metabox');

function render_logo_image_metabox($post) {
    $image_id = get_post_meta($post->ID, '_logo_image', true);
    $image_url = $image_id ? wp_get_attachment_url($image_id) : '';
    ?>
    <div>
        <button type="button" class="button" id="upload_logo_image">Выбрать изображение</button>
        <input type="hidden" id="logo_image_id" name="logo_image_id" value="<?php echo esc_attr($image_id); ?>">
        <div id="logo_image_preview" style="margin-top: 10px;">
            <?php if ($image_url) : ?>
                <img src="<?php echo esc_url($image_url); ?>" style="max-width: 150px; height: auto;">
            <?php endif; ?>
        </div>
    </div>
    <script>
    jQuery(document).ready(function($) {
        $('#upload_logo_image').click(function(e) {
            e.preventDefault();
            var frame = wp.media({ title: 'Выберите изображение', multiple: false, library: { type: 'image' } });
            frame.on('select', function() {
                var attachment = frame.state().get('selection').first().toJSON();
                $('#logo_image_id').val(attachment.id);
                $('#logo_image_preview').html('<img src="' + attachment.url + '" style="max-width: 150px; height: auto;">');
            });
            frame.open();
        });
    });
    </script>
    <?php
}

function save_logo_image($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;
    if (get_post_type($post_id) !== 'logo') return;
    
    if (isset($_POST['logo_image_id'])) {
        update_post_meta($post_id, '_logo_image', intval($_POST['logo_image_id']));
    }
}
add_action('save_post', 'save_logo_image');

// Подключаем медиафайлы
function enqueue_media_for_logos() {
    global $pagenow;
    if (($pagenow == 'post.php' || $pagenow == 'post-new.php') && get_post_type() == 'logo') {
        wp_enqueue_media();
        wp_enqueue_script('jquery');
    }
}
add_action('admin_enqueue_scripts', 'enqueue_media_for_logos');


// ==================== CPT ДЛЯ СТАТИСТИКИ ====================
function create_stats_post_type() {
    register_post_type('stats', array(
        'labels' => array(
            'name' => 'Статистика',
            'singular_name' => 'Статистика',
            'add_new_item' => 'Добавить статистику',
            'edit_item' => 'Редактировать статистику',
            'all_items' => 'Статистика',
        ),
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => 'technopark-main',
        'menu_icon' => 'dashicons-chart-bar',
        'supports' => array('title'),
    ));
}
add_action('init', 'create_stats_post_type');


// ==================== CPT ДЛЯ НАПРАВЛЕНИЙ ====================
function create_directions_post_type() {
    register_post_type('directions', array(
        'labels' => array(
            'name' => 'Направления',
            'singular_name' => 'Направление',
            'add_new_item' => 'Добавить направление',
            'edit_item' => 'Редактировать направление',
            'all_items' => 'Направления',
        ),
        'public' => true,           
        'publicly_queryable' => true,  
        'has_archive' => true,      
        'rewrite' => array('slug' => 'directions'), 
        'show_ui' => true,
        'show_in_menu' => 'technopark-main',
        'menu_icon' => 'dashicons-admin-site',
        'supports' => array('title', 'editor'), 
    ));
}
add_action('init', 'create_directions_post_type');


// ==================== CPT ДЛЯ FAQ ====================
function create_faq_post_type() {
    register_post_type('faq', array(
        'labels' => array(
            'name' => 'FAQ',
            'singular_name' => 'FAQ',
            'add_new_item' => 'Добавить вопрос',
            'edit_item' => 'Редактировать вопрос',
            'all_items' => 'FAQ',
        ),
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => 'technopark-main',
        'menu_icon' => 'dashicons-editor-help',
        'supports' => array('title', 'editor'),
    ));
}
add_action('init', 'create_faq_post_type');


// ==================== МЕТАБОКСЫ ДЛЯ СТАТИСТИКИ ====================
function add_stats_metabox() {
    add_meta_box(
        'stats_fields',
        'Поля статистики',
        'render_stats_metabox',
        'stats',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'add_stats_metabox');

function render_stats_metabox($post) {
    $number = get_post_meta($post->ID, '_stats_number', true);
    $text = get_post_meta($post->ID, '_stats_text', true);
    ?>
    <p>
        <label>Число / цифра:</label><br>
        <input type="text" name="stats_number" value="<?php echo esc_attr($number); ?>" style="width:100%" placeholder="Например: 100, 20+, 5000+">
    </p>
    <p>
        <label>Текст под числом:</label><br>
        <input type="text" name="stats_text" value="<?php echo esc_attr($text); ?>" style="width:100%" placeholder="Например: лет работы, направлений, участников">
    </p>
    <?php
}

function save_stats_metabox($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;
    if (get_post_type($post_id) !== 'stats') return;
    
    if (isset($_POST['stats_number'])) {
        update_post_meta($post_id, '_stats_number', sanitize_text_field($_POST['stats_number']));
    }
    if (isset($_POST['stats_text'])) {
        update_post_meta($post_id, '_stats_text', sanitize_text_field($_POST['stats_text']));
    }
}
add_action('save_post', 'save_stats_metabox');


// ==================== ПОЛЯ ДЛЯ МЕРОПРИЯТИЙ ====================

// Добавляем метабоксы
function add_event_metaboxes() {
    add_meta_box('event_dates', 'Даты мероприятия', 'render_event_dates_metabox', 'meropriyatiya', 'normal', 'high');
    add_meta_box('event_short_desc', 'Краткое описание (для карточки)', 'render_event_short_desc_metabox', 'meropriyatiya', 'normal', 'high');
    add_meta_box('event_gallery', 'Галерея изображений', 'render_event_gallery_metabox', 'meropriyatiya', 'normal', 'high');
    add_meta_box('event_stats_cards', 'Карточки особенностей', 'render_event_stats_metabox', 'meropriyatiya', 'normal', 'high');
}
add_action('add_meta_boxes', 'add_event_metaboxes');

// Добавляем метабокс для кнопок в мероприятия
function add_event_buttons_metabox() {
    add_meta_box(
        'event_buttons_metabox',
        'Кнопки',
        'render_event_buttons_metabox',
        'meropriyatiya',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'add_event_buttons_metabox');

function render_event_buttons_metabox($post) {
    $buttons = get_post_meta($post->ID, '_event_buttons', true) ?: [];
    echo '<div id="event-buttons-wrap">';
    foreach ($buttons as $index => $button) {
        echo '<div class="event-button-item" style="margin-bottom:15px;padding:10px;border:1px solid #ddd;">';
        echo '<input type="text" name="event_buttons['.$index.'][text]" value="'.esc_attr($button['text']).'" placeholder="Текст кнопки" style="width:30%"> ';
        echo '<input type="url" name="event_buttons['.$index.'][link]" value="'.esc_attr($button['link']).'" placeholder="Ссылка" style="width:40%"> ';
        echo '<select name="event_buttons['.$index.'][style]" style="width:20%">';
        echo '<option value="primary" '.selected($button['style'], 'primary', false).'>Primary</option>';
        echo '<option value="outline" '.selected($button['style'], 'outline', false).'>Outline</option>';
        echo '</select> ';
        echo '<button type="button" class="remove-event-button button">Удалить</button>';
        echo '</div>';
    }
    echo '</div><button type="button" id="add-event-button" class="button">+ Добавить кнопку</button>';
    ?>
    <script>
    jQuery(document).ready(function($) {
        $('#add-event-button').click(function() {
            var index = $('.event-button-item').length;
            var html = '<div class="event-button-item" style="margin-bottom:15px;padding:10px;border:1px solid #ddd;">' +
                '<input type="text" name="event_buttons['+index+'][text]" placeholder="Текст кнопки" style="width:30%"> ' +
                '<input type="url" name="event_buttons['+index+'][link]" placeholder="Ссылка" style="width:40%"> ' +
                '<select name="event_buttons['+index+'][style]" style="width:20%">' +
                '<option value="primary">Primary</option>' +
                '<option value="outline">Outline</option>' +
                '</select> ' +
                '<button type="button" class="remove-event-button button">Удалить</button></div>';
            $('#event-buttons-wrap').append(html);
        });
        $(document).on('click', '.remove-event-button', function() { $(this).closest('.event-button-item').remove(); });
    });
    </script>
    <?php
}

// Даты мероприятия (с нормальным полем date)
function render_event_dates_metabox($post) {
    $date_start = get_post_meta($post->ID, '_event_date_start', true);
    $date_end = get_post_meta($post->ID, '_event_date_end', true);
    ?>
    <p>
        <label>Дата начала: <span style="color:red;">*</span></label><br>
        <input type="date" name="event_date_start" value="<?php echo esc_attr($date_start); ?>" style="width:100%" required>
    </p>
    <p>
        <label>Дата окончания (если один день, оставьте пустым):</label><br>
        <input type="date" name="event_date_end" value="<?php echo esc_attr($date_end); ?>" style="width:100%">
    </p>
    <?php
}

// Краткое описание для карточки
function render_event_short_desc_metabox($post) {
    $value = get_post_meta($post->ID, '_event_short_description', true);
    ?>
    <textarea name="event_short_description" rows="3" style="width:100%; max-width:100%;" required><?php echo esc_textarea($value); ?></textarea>
    <p class="description">Это описание будет отображаться на карточке мероприятия в списке.</p>
    <?php
}

// Галерея
function render_event_gallery_metabox($post) {
    $gallery_ids = get_post_meta($post->ID, '_event_gallery', true);
    ?>
    <div class="event-gallery-wrap">
        <button type="button" class="button" id="upload_event_gallery">Выбрать изображения</button>
        <input type="hidden" id="event_gallery_ids" name="event_gallery_ids" value="<?php echo esc_attr($gallery_ids); ?>">
        <div id="event_gallery_preview" style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 15px;">
            <?php 
            if ($gallery_ids) {
                $ids = explode(',', $gallery_ids);
                foreach ($ids as $id) {
                    $img = wp_get_attachment_image_src($id, 'thumbnail');
                    if ($img) {
                        echo '<div class="gallery-item" data-id="' . $id . '" style="position: relative;"><img src="' . $img[0] . '" style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px;"><button type="button" class="remove-gallery-item" style="position: absolute; top: -8px; right: -8px; background: red; color: white; border: none; border-radius: 50%; width: 20px; height: 20px; cursor: pointer;">×</button></div>';
                    }
                }
            }
            ?>
        </div>
    </div>
    <script>
    jQuery(document).ready(function($) {
        $('#upload_event_gallery').click(function(e) {
            e.preventDefault();
            var frame = wp.media({ title: 'Выберите изображения', multiple: true, library: { type: 'image' } });
            frame.on('select', function() {
                var selection = frame.state().get('selection');
                var ids = [];
                var preview = $('#event_gallery_preview');
                preview.empty();
                selection.map(function(attachment) {
                    attachment = attachment.toJSON();
                    ids.push(attachment.id);
                    preview.append('<div class="gallery-item" data-id="' + attachment.id + '" style="position: relative;"><img src="' + attachment.sizes.thumbnail.url + '" style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px;"><button type="button" class="remove-gallery-item" style="position: absolute; top: -8px; right: -8px; background: red; color: white; border: none; border-radius: 50%; width: 20px; height: 20px; cursor: pointer;">×</button></div>');
                });
                $('#event_gallery_ids').val(ids.join(','));
            });
            frame.open();
        });
        $(document).on('click', '.remove-gallery-item', function() {
            var item = $(this).closest('.gallery-item');
            var id = item.data('id');
            item.remove();
            var ids = $('#event_gallery_ids').val().split(',');
            var newIds = ids.filter(function(i) { return i != id; });
            $('#event_gallery_ids').val(newIds.join(','));
        });
    });
    </script>
    <?php
}

// Карточки статистики
function render_event_stats_metabox($post) {
    $stats = get_post_meta($post->ID, '_event_stats', true) ?: [];
    ?>
    <div id="event-stats-wrap">
        <?php foreach ($stats as $index => $stat) : 
            $image_id = isset($stat['image_id']) ? $stat['image_id'] : '';
            $svg_text = isset($stat['text']) ? $stat['text'] : '';
            ?>
            <div class="event-stat-item" style="margin-bottom:20px;padding:15px;border:1px solid #ddd;background:#f9f9f9;">
                <div style="margin-bottom:10px;">
                    <label style="display:block;font-weight:bold;margin-bottom:5px;">Иконка (SVG):</label>
                    <button type="button" class="button select-icon" data-index="<?php echo $index; ?>">Выбрать SVG иконку</button>
                    <input type="hidden" name="event_stats[<?php echo $index; ?>][image_id]" class="image-id-<?php echo $index; ?>" value="<?php echo esc_attr($image_id); ?>">
                    <div style="margin-top:5px;font-size:12px;color:#666;" id="filename-<?php echo $index; ?>">
                        <?php if ($image_id) : ?>
                            <?php echo basename(wp_get_attachment_url($image_id)); ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div>
                    <label style="display:block;font-weight:bold;margin-bottom:5px;">Текст карточки:</label>
                    <input type="text" name="event_stats[<?php echo $index; ?>][text]" value="<?php echo esc_attr($svg_text); ?>" placeholder="Текст карточки" style="width:100%;">
                </div>
                <button type="button" class="remove-stat button" style="margin-top:10px;">Удалить</button>
            </div>
        <?php endforeach; ?>
    </div>
    <button type="button" id="add-stat" class="button button-primary">+ Добавить карточку</button>
    
    <script>
    jQuery(document).ready(function($) {
        let mediaFrame;
        
        function openMediaFrame(index) {
            if (mediaFrame) {
                mediaFrame.close();
            }
            
            mediaFrame = wp.media({
                title: 'Выберите SVG иконку',
                button: { text: 'Выбрать' },
                multiple: false,
                library: { type: 'image/svg+xml' }
            });
            
            mediaFrame.on('select', function() {
                var attachment = mediaFrame.state().get('selection').first().toJSON();
                $('.image-id-' + index).val(attachment.id);
                $('#filename-' + index).text(attachment.filename || 'Файл выбран');
            });
            
            mediaFrame.open();
        }
        
        $(document).on('click', '.select-icon', function() {
            openMediaFrame($(this).data('index'));
        });
        
        let statIndex = $('.event-stat-item').length;
        
        $('#add-stat').click(function() {
            var html = '<div class="event-stat-item" style="margin-bottom:20px;padding:15px;border:1px solid #ddd;background:#f9f9f9;">' +
                '<div style="margin-bottom:10px;">' +
                '<label style="display:block;font-weight:bold;margin-bottom:5px;">Иконка (SVG):</label>' +
                '<button type="button" class="button select-icon" data-index="'+statIndex+'">Выбрать SVG иконку</button>' +
                '<input type="hidden" name="event_stats['+statIndex+'][image_id]" class="image-id-'+statIndex+'" value="">' +
                '<div style="margin-top:5px;font-size:12px;color:#666;" id="filename-'+statIndex+'"></div>' +
                '</div>' +
                '<div>' +
                '<label style="display:block;font-weight:bold;margin-bottom:5px;">Текст карточки:</label>' +
                '<input type="text" name="event_stats['+statIndex+'][text]" placeholder="Текст карточки" style="width:100%;">' +
                '</div>' +
                '<button type="button" class="remove-stat button" style="margin-top:10px;">Удалить</button>' +
                '</div>';
            $('#event-stats-wrap').append(html);
            statIndex++;
        });
        
        $(document).on('click', '.remove-stat', function() {
            if (confirm('Удалить карточку?')) {
                $(this).closest('.event-stat-item').remove();
            }
        });
    });
    </script>
    <?php
}

// Новая версия сохранения (простая, без закрашивания)
function save_event_metaboxes($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;
    
    // Сохраняем карточки с изображениями
    if (isset($_POST['event_stats'])) {
        $stats = $_POST['event_stats'];
        foreach ($stats as &$stat) {
            $stat['text'] = isset($stat['text']) ? sanitize_text_field($stat['text']) : '';
            $stat['image_id'] = isset($stat['image_id']) ? intval($stat['image_id']) : '';
        }
        update_post_meta($post_id, '_event_stats', $stats);
    }
    
    // Сохраняем галерею
    if (isset($_POST['event_gallery_ids'])) {
        update_post_meta($post_id, '_event_gallery', sanitize_text_field($_POST['event_gallery_ids']));
    }
    
    // Сохраняем кнопки
    if (isset($_POST['event_buttons'])) {
        update_post_meta($post_id, '_event_buttons', $_POST['event_buttons']);
    }
    
    // Сохраняем даты и описание
    $date_start = isset($_POST['event_date_start']) ? sanitize_text_field($_POST['event_date_start']) : '';
    $date_end = isset($_POST['event_date_end']) ? sanitize_text_field($_POST['event_date_end']) : '';
    $short_description = isset($_POST['event_short_description']) ? sanitize_textarea_field($_POST['event_short_description']) : '';
    
    update_post_meta($post_id, '_event_date_start', $date_start);
    update_post_meta($post_id, '_event_date_end', $date_end);
    update_post_meta($post_id, '_event_short_description', $short_description);
}
add_action('save_post', 'save_event_metaboxes');

// Показываем ошибки валидации в админке
function display_event_errors() {
    global $post;
    if ($post && $post->post_type == 'meropriyatiya') {
        $errors = get_transient('event_errors_' . $post->ID);
        if ($errors) {
            echo '<div class="notice notice-error is-dismissible"><ul>';
            foreach ($errors as $error) {
                echo '<li>' . esc_html($error) . '</li>';
            }
            echo '</ul></div>';
            delete_transient('event_errors_' . $post->ID);
        }
        
        // Восстанавливаем временно сохранённые данные
        $temp_start = get_post_meta($post->ID, '_event_date_start_temp', true);
        if ($temp_start) {
            echo '<div class="notice notice-warning">Внимание! Данные не были сохранены из-за ошибок. Исправьте их и сохраните снова.</div>';
        }
    }
}
add_action('admin_notices', 'display_event_errors');

// Подключаем медиафайлы (одна функция для всех)
function enqueue_media_for_custom_post_types() {
    global $pagenow;
    $allowed_post_types = array('directions', 'meropriyatiya');
    if (($pagenow == 'post.php' || $pagenow == 'post-new.php') && in_array(get_post_type(), $allowed_post_types)) {
        wp_enqueue_media();
        wp_enqueue_script('jquery');
    }
}
add_action('admin_enqueue_scripts', 'enqueue_media_for_custom_post_types');


// ==================== ПОЛЯ ДЛЯ НАПРАВЛЕНИЙ ====================

// Добавляем метабокс для краткого описания
function add_direction_short_desc_metabox() {
    add_meta_box(
        'direction_short_desc_metabox',
        'Краткое описание (для карточки)',
        'render_direction_short_desc_metabox',
        'directions',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'add_direction_short_desc_metabox');

function render_direction_short_desc_metabox($post) {
    $value = get_post_meta($post->ID, '_direction_card_description', true);
    ?>
    <textarea name="direction_card_description" rows="3" style="width:100%; max-width:100%;" maxlength="350"><?php echo esc_textarea($value); ?></textarea>
    <p class="description">Максимум 350 символов. Это описание будет на карточке направления.</p>
    <?php
}

// Добавляем метабокс для галереи
function add_direction_gallery_metabox() {
    add_meta_box(
        'direction_gallery_metabox',
        'Галерея изображений',
        'render_direction_gallery_metabox',
        'directions',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'add_direction_gallery_metabox');

function render_direction_gallery_metabox($post) {
    $gallery_ids = get_post_meta($post->ID, '_direction_gallery', true);
    ?>
    <div class="direction-gallery-wrap">
        <button type="button" class="button" id="upload_direction_gallery">Выбрать изображения</button>
        <input type="hidden" id="direction_gallery_ids" name="direction_gallery_ids" value="<?php echo esc_attr($gallery_ids); ?>">
        <div id="direction_gallery_preview" class="gallery-preview" style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 15px;">
            <?php 
            if ($gallery_ids) {
                $ids = explode(',', $gallery_ids);
                foreach ($ids as $id) {
                    $img = wp_get_attachment_image_src($id, 'thumbnail');
                    if ($img) {
                        echo '<div class="gallery-item" data-id="' . $id . '" style="position: relative;"><img src="' . $img[0] . '" style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px;"><button type="button" class="remove-gallery-item" style="position: absolute; top: -8px; right: -8px; background: red; color: white; border: none; border-radius: 50%; width: 20px; height: 20px; cursor: pointer;">×</button></div>';
                    }
                }
            }
            ?>
        </div>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        $('#upload_direction_gallery').click(function(e) {
            e.preventDefault();
            var frame = wp.media({
                title: 'Выберите изображения для галереи',
                multiple: true,
                library: { type: 'image' }
            });
            
            frame.on('select', function() {
                var selection = frame.state().get('selection');
                var ids = [];
                var preview = $('#direction_gallery_preview');
                preview.empty();
                
                selection.map(function(attachment) {
                    attachment = attachment.toJSON();
                    ids.push(attachment.id);
                    preview.append('<div class="gallery-item" data-id="' + attachment.id + '" style="position: relative;"><img src="' + attachment.sizes.thumbnail.url + '" style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px;"><button type="button" class="remove-gallery-item" style="position: absolute; top: -8px; right: -8px; background: red; color: white; border: none; border-radius: 50%; width: 20px; height: 20px; cursor: pointer;">×</button></div>');
                });
                
                $('#direction_gallery_ids').val(ids.join(','));
            });
            
            frame.open();
        });
        
        $(document).on('click', '.remove-gallery-item', function() {
            var item = $(this).closest('.gallery-item');
            var id = item.data('id');
            item.remove();
            
            var ids = $('#direction_gallery_ids').val().split(',');
            var newIds = ids.filter(function(i) { return i != id; });
            $('#direction_gallery_ids').val(newIds.join(','));
        });
    });
    </script>
    <?php
}

// Добавляем метабокс для SVG иконки
function add_direction_svg_metabox() {
    add_meta_box(
        'direction_svg_metabox',
        'Иконка направления (только SVG)',
        'render_direction_svg_metabox',
        'directions',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'add_direction_svg_metabox');

function render_direction_svg_metabox($post) {
    $image_id = get_post_meta($post->ID, '_direction_icon_image_id', true);
    $image_url = $image_id ? wp_get_attachment_url($image_id) : '';
    ?>
    <div>
        <button type="button" class="button" id="upload_direction_icon">Выбрать SVG иконку</button>
        <input type="hidden" id="direction_icon_id" name="direction_icon_id" value="<?php echo esc_attr($image_id); ?>">
        <div id="direction_icon_preview" style="margin-top:10px;">
            <?php if ($image_url) : ?>
                <div style="width:80px;height:80px;" class="text-primary">
                    <?php echo file_get_contents($image_url); ?>
                </div>
            <?php endif; ?>
        </div>
        <p class="description">Загрузите SVG-файл(желательно solid). Иконка автоматически перекрасится.</p>
    </div>
    <script>
    jQuery(document).ready(function($) {
        $('#upload_direction_icon').click(function(e) {
            e.preventDefault();
            var frame = wp.media({
                title: 'Выберите SVG иконку',
                multiple: false,
                library: { type: 'image/svg+xml' } // ТОЛЬКО SVG
            });
            frame.on('select', function() {
                var attachment = frame.state().get('selection').first().toJSON();
                if (!attachment.url.match(/\.svg$/i)) {
                    alert('Пожалуйста, загрузите только SVG файл!');
                    return;
                }
                $('#direction_icon_id').val(attachment.id);
                $.get(attachment.url, function(svgContent) {
                    $('#direction_icon_preview').html('<div style="width:80px;height:80px;" class="text-primary">' + svgContent + '</div>');
                });
            });
            frame.open();
        });
    });
    </script>
    <?php
}

/**
 * Очищает SVG и заменяет fill на currentColor
 */
function sanitize_svg_for_primary($svg) {
    // Удаляем DOCTYPE и XML-декларацию
    $svg = preg_replace('/<!DOCTYPE[^>]*>/', '', $svg);
    $svg = preg_replace('/<\?xml[^>]*\?>/', '', $svg);
    
    // Удаляем version, id, xml:space и другие лишние атрибуты
    $svg = preg_replace('/version="[^"]*"/', '', $svg);
    $svg = preg_replace('/id="[^"]*"/', '', $svg);
    $svg = preg_replace('/xml:space="[^"]*"/', '', $svg);
    $svg = preg_replace('/xmlns:xlink="[^"]*"/', '', $svg);
    
    // Заменяем все fill="..." на fill="currentColor"
    $svg = preg_replace('/fill="[^"]*"/', 'fill="currentColor"', $svg);
    
    // Заменяем все stroke="..." на stroke="currentColor"
    $svg = preg_replace('/stroke="[^"]*"/', 'stroke="currentColor"', $svg);
    
    // Если нет ни fill, ни stroke — добавляем оба
    if (strpos($svg, 'fill=') === false && strpos($svg, 'stroke=') === false && strpos($svg, '<svg') !== false) {
        $svg = str_replace('<svg', '<svg fill="currentColor" stroke="currentColor"', $svg);
    } elseif (strpos($svg, 'fill=') === false && strpos($svg, '<svg') !== false) {
        $svg = str_replace('<svg', '<svg fill="currentColor"', $svg);
    } elseif (strpos($svg, 'stroke=') === false && strpos($svg, '<svg') !== false) {
        $svg = str_replace('<svg', '<svg stroke="currentColor"', $svg);
    }
    
    // Убираем лишние пробелы и переносы строк
    $svg = preg_replace('/\s+/', ' ', $svg);
    
    return trim($svg);
}

// Добавляем метабокс для кнопок (как в page.php)
function add_direction_buttons_metabox() {
    add_meta_box(
        'direction_buttons_metabox',
        'Кнопки',
        'render_direction_buttons_metabox',
        'directions',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'add_direction_buttons_metabox');

function render_direction_buttons_metabox($post) {
    $buttons = get_post_meta($post->ID, '_direction_buttons', true) ?: [];
    echo '<div id="direction-buttons-wrap">';
    foreach ($buttons as $index => $button) {
        echo '<div class="direction-button-item" style="margin-bottom:15px;padding:10px;border:1px solid #ddd;">';
        echo '<input type="text" name="direction_buttons['.$index.'][text]" value="'.esc_attr($button['text']).'" placeholder="Текст кнопки" style="width:30%"> ';
        echo '<input type="url" name="direction_buttons['.$index.'][link]" value="'.esc_attr($button['link']).'" placeholder="Ссылка" style="width:40%"> ';
        echo '<select name="direction_buttons['.$index.'][style]" style="width:20%">';
        echo '<option value="outline" '.selected($button['style'], 'outline', false).'>Outline</option>';
        echo '<option value="primary" '.selected($button['style'], 'primary', false).'>Primary</option>';
        echo '</select> ';
        echo '<button type="button" class="remove-button button">Удалить</button>';
        echo '</div>';
    }
    echo '</div><button type="button" id="add-button" class="button">+ Добавить кнопку</button>';
    ?>
    <script>
    jQuery(document).ready(function($) {
        $('#add-button').click(function() {
            var index = $('.direction-button-item').length;
            var html = '<div class="direction-button-item" style="margin-bottom:15px;padding:10px;border:1px solid #ddd;">' +
                '<input type="text" name="direction_buttons['+index+'][text]" placeholder="Текст кнопки" style="width:30%"> ' +
                '<input type="url" name="direction_buttons['+index+'][link]" placeholder="Ссылка" style="width:40%"> ' +
                '<select name="direction_buttons['+index+'][style]" style="width:20%">' +
                '<option value="outline">Outline</option>' +
                '<option value="primary">Primary</option>' +
                '</select> ' +
                '<button type="button" class="remove-button button">Удалить</button></div>';
            $('#direction-buttons-wrap').append(html);
        });
        $(document).on('click', '.remove-button', function() { $(this).closest('.direction-button-item').remove(); });
    });
    </script>
    <?php
}

// ==================== ОБЩЕЕ СОХРАНЕНИЕ ДЛЯ НАПРАВЛЕНИЙ ====================
function save_direction_all_metaboxes($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;
    if (get_post_type($post_id) !== 'directions') return;
    
    // Сохраняем краткое описание
    if (isset($_POST['direction_card_description'])) {
        update_post_meta($post_id, '_direction_card_description', sanitize_textarea_field($_POST['direction_card_description']));
    }
    
    // Сохраняем галерею
    if (isset($_POST['direction_gallery_ids'])) {
        update_post_meta($post_id, '_direction_gallery', sanitize_text_field($_POST['direction_gallery_ids']));
    }
    
    // Сохраняем только ID иконки
    if (isset($_POST['direction_icon_id'])) {
        update_post_meta($post_id, '_direction_icon_image_id', intval($_POST['direction_icon_id']));
    }
    
    // Сохраняем кнопки
    if (isset($_POST['direction_buttons'])) {
        update_post_meta($post_id, '_direction_buttons', $_POST['direction_buttons']);
    }
}
add_action('save_post', 'save_direction_all_metaboxes');


// ==================== ПОЛЯ ДЛЯ ПРОЕКТОВ (КОП) ====================

// Добавляем метабоксы
function add_project_metaboxes() {
    add_meta_box('project_view', 'Вид / Профиль', 'render_project_view_metabox', 'project', 'normal', 'high');
    add_meta_box('project_gallery', 'Галерея изображений', 'render_project_gallery_metabox', 'project', 'normal', 'high');
}
add_action('add_meta_boxes', 'add_project_metaboxes');

// Вид / Профиль
function render_project_view_metabox($post) {
    $value = get_post_meta($post->ID, '_project_view', true);
    ?>
    <input type="text" name="project_view" value="<?php echo esc_attr($value); ?>" style="width:100%" placeholder="Коробочный образовательный продукт">
    <p class="description">Короткий текст, который будет отображаться перед заголовком.</p>
    <?php
}

// Галерея изображений
function render_project_gallery_metabox($post) {
    $gallery_ids = get_post_meta($post->ID, '_project_gallery', true);
    ?>
    <div class="project-gallery-wrap">
        <button type="button" class="button" id="upload_project_gallery">Выбрать изображения</button>
        <input type="hidden" id="project_gallery_ids" name="project_gallery_ids" value="<?php echo esc_attr($gallery_ids); ?>">
        <div id="project_gallery_preview" style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 15px;">
            <?php 
            if ($gallery_ids) {
                $ids = explode(',', $gallery_ids);
                foreach ($ids as $id) {
                    $img = wp_get_attachment_image_src($id, 'thumbnail');
                    if ($img) {
                        echo '<div class="gallery-item" data-id="' . $id . '" style="position: relative;"><img src="' . $img[0] . '" style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px;"><button type="button" class="remove-gallery-item" style="position: absolute; top: -8px; right: -8px; background: red; color: white; border: none; border-radius: 50%; width: 20px; height: 20px; cursor: pointer;">×</button></div>';
                    }
                }
            }
            ?>
        </div>
    </div>
    <script>
    jQuery(document).ready(function($) {
        $('#upload_project_gallery').click(function(e) {
            e.preventDefault();
            var frame = wp.media({ title: 'Выберите изображения', multiple: true, library: { type: 'image' } });
            frame.on('select', function() {
                var selection = frame.state().get('selection');
                var ids = [];
                var preview = $('#project_gallery_preview');
                preview.empty();
                selection.map(function(attachment) {
                    attachment = attachment.toJSON();
                    ids.push(attachment.id);
                    preview.append('<div class="gallery-item" data-id="' + attachment.id + '" style="position: relative;"><img src="' + attachment.sizes.thumbnail.url + '" style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px;"><button type="button" class="remove-gallery-item" style="position: absolute; top: -8px; right: -8px; background: red; color: white; border: none; border-radius: 50%; width: 20px; height: 20px; cursor: pointer;">×</button></div>');
                });
                $('#project_gallery_ids').val(ids.join(','));
            });
            frame.open();
        });
        $(document).on('click', '.remove-gallery-item', function() {
            var item = $(this).closest('.gallery-item');
            var id = item.data('id');
            item.remove();
            var ids = $('#project_gallery_ids').val().split(',');
            var newIds = ids.filter(function(i) { return i != id; });
            $('#project_gallery_ids').val(newIds.join(','));
        });
    });
    </script>
    <?php
}

// Сохранение
function save_project_metaboxes($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;
    
    // Сохраняем вид / профиль
    if (isset($_POST['project_view'])) {
        update_post_meta($post_id, '_project_view', sanitize_text_field($_POST['project_view']));
    }
    
    // Сохраняем галерею
    if (isset($_POST['project_gallery_ids'])) {
        update_post_meta($post_id, '_project_gallery', sanitize_text_field($_POST['project_gallery_ids']));
    }
}
add_action('save_post', 'save_project_metaboxes');

// Подключаем медиафайлы для проектов
function enqueue_media_for_projects() {
    global $pagenow;
    if (($pagenow == 'post.php' || $pagenow == 'post-new.php') && get_post_type() == 'project') {
        wp_enqueue_media();
        wp_enqueue_script('jquery');
    }
}
add_action('admin_enqueue_scripts', 'enqueue_media_for_projects');






// ==================== МЕТАБОКСЫ ДЛЯ СТРАНИЦЫ "НОВАЯ СТРАНИЦА" ====================

function add_page_metaboxes() {
    global $post;
    
    if (!$post || $post->post_type !== 'page') return;
    
    // Получаем имя файла шаблона
    $template = get_post_meta($post->ID, '_wp_page_template', true);
    
    // Показываем метабоксы только для шаблона page-temp.php
    if ($template !== 'page-temp.php') {
        return;
    }
    
    add_meta_box('page_gallery', 'Галерея изображений', 'render_page_gallery_metabox', 'page', 'normal', 'high');
    add_meta_box('page_buttons', 'Кнопки', 'render_page_buttons_metabox', 'page', 'normal', 'high');
    add_meta_box('page_extra', 'Дополнительные настройки', 'render_page_extra_metabox', 'page', 'side', 'default');
}
add_action('add_meta_boxes', 'add_page_metaboxes');

// Галерея изображений
function render_page_gallery_metabox($post) {
    $gallery_ids = get_post_meta($post->ID, '_page_gallery', true);
    ?>
    <div class="page-gallery-wrap">
        <button type="button" class="button" id="upload_page_gallery">Выбрать изображения</button>
        <input type="hidden" id="page_gallery_ids" name="page_gallery_ids" value="<?php echo esc_attr($gallery_ids); ?>">
        <div id="page_gallery_preview" style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 15px;">
            <?php 
            if ($gallery_ids) {
                $ids = explode(',', $gallery_ids);
                foreach ($ids as $id) {
                    $img = wp_get_attachment_image_src($id, 'thumbnail');
                    if ($img) {
                        echo '<div class="gallery-item" data-id="' . $id . '" style="position: relative;"><img src="' . $img[0] . '" style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px;"><button type="button" class="remove-gallery-item" style="position: absolute; top: -8px; right: -8px; background: red; color: white; border: none; border-radius: 50%; width: 20px; height: 20px; cursor: pointer;">×</button></div>';
                    }
                }
            }
            ?>
        </div>
    </div>
    <script>
    jQuery(document).ready(function($) {
        $('#upload_page_gallery').click(function(e) {
            e.preventDefault();
            var frame = wp.media({ title: 'Выберите изображения', multiple: true, library: { type: 'image' } });
            frame.on('select', function() {
                var selection = frame.state().get('selection');
                var ids = [];
                var preview = $('#page_gallery_preview');
                preview.empty();
                selection.map(function(attachment) {
                    attachment = attachment.toJSON();
                    ids.push(attachment.id);
                    preview.append('<div class="gallery-item" data-id="' + attachment.id + '" style="position: relative;"><img src="' + attachment.sizes.thumbnail.url + '" style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px;"><button type="button" class="remove-gallery-item" style="position: absolute; top: -8px; right: -8px; background: red; color: white; border: none; border-radius: 50%; width: 20px; height: 20px; cursor: pointer;">×</button></div>');
                });
                $('#page_gallery_ids').val(ids.join(','));
            });
            frame.open();
        });
        $(document).on('click', '.remove-gallery-item', function() {
            var item = $(this).closest('.gallery-item');
            var id = item.data('id');
            item.remove();
            var ids = $('#page_gallery_ids').val().split(',');
            var newIds = ids.filter(function(i) { return i != id; });
            $('#page_gallery_ids').val(newIds.join(','));
        });
    });
    </script>
    <?php
}

// Кнопки (неограниченное количество)
function render_page_buttons_metabox($post) {
    $buttons = get_post_meta($post->ID, '_page_buttons', true) ?: [];
    echo '<div id="page-buttons-wrap">';
    foreach ($buttons as $index => $button) {
        echo '<div class="page-button-item" style="margin-bottom:15px;padding:10px;border:1px solid #ddd;">';
        echo '<input type="text" name="page_buttons['.$index.'][text]" value="'.esc_attr($button['text']).'" placeholder="Текст кнопки" style="width:30%"> ';
        echo '<input type="url" name="page_buttons['.$index.'][link]" value="'.esc_attr($button['link']).'" placeholder="Ссылка" style="width:40%"> ';
        echo '<select name="page_buttons['.$index.'][style]" style="width:20%">';
        echo '<option value="primary" '.selected($button['style'], 'primary', false).'>Primary</option>';
        echo '<option value="outline" '.selected($button['style'], 'outline', false).'>Outline</option>';
        echo '</select> ';
        echo '<button type="button" class="remove-page-button button">Удалить</button>';
        echo '</div>';
    }
    echo '</div><button type="button" id="add-page-button" class="button">+ Добавить кнопку</button>';
    ?>
    <script>
    jQuery(document).ready(function($) {
        $('#add-page-button').click(function() {
            var index = $('.page-button-item').length;
            var html = '<div class="page-button-item" style="margin-bottom:15px;padding:10px;border:1px solid #ddd;">' +
                '<input type="text" name="page_buttons['+index+'][text]" placeholder="Текст кнопки" style="width:30%"> ' +
                '<input type="url" name="page_buttons['+index+'][link]" placeholder="Ссылка" style="width:40%"> ' +
                '<select name="page_buttons['+index+'][style]" style="width:20%">' +
                '<option value="primary">Primary</option>' +
                '<option value="outline">Outline</option>' +
                '</select> ' +
                '<button type="button" class="remove-page-button button">Удалить</button></div>';
            $('#page-buttons-wrap').append(html);
        });
        $(document).on('click', '.remove-page-button', function() { $(this).closest('.page-button-item').remove(); });
    });
    </script>
    <?php
}

// Дополнительные настройки
function render_page_extra_metabox($post) {
    $show_title = get_post_meta($post->ID, '_page_show_title', true);
    $title_position = get_post_meta($post->ID, '_page_title_position', true);
    ?>
    <p>
        <label>
            <input type="checkbox" name="page_show_title" value="1" <?php checked($show_title, '1'); ?>>
            Показывать заголовок страницы
        </label>
    </p>
    <p>
        <label>Позиция заголовка:</label><br>
        <select name="page_title_position" style="width:100%">
            <option value="center" <?php selected($title_position, 'center'); ?>>По центру</option>
            <option value="left" <?php selected($title_position, 'left'); ?>>Слева</option>
        </select>
    </p>
    <?php
}

// Сохранение
function save_page_metaboxes($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;
    
    // Сохраняем кнопки (даже если пустой массив)
    if (isset($_POST['page_buttons'])) {
        update_post_meta($post_id, '_page_buttons', $_POST['page_buttons']);
    } else {
        // Если кнопок нет в POST, значит удалили все — очищаем мета-поле
        delete_post_meta($post_id, '_page_buttons');
    }
    
    // Сохраняем галерею
    if (isset($_POST['page_gallery_ids'])) {
        update_post_meta($post_id, '_page_gallery', sanitize_text_field($_POST['page_gallery_ids']));
    }
    
    // Сохраняем дополнительные настройки
    update_post_meta($post_id, '_page_show_title', isset($_POST['page_show_title']) ? '1' : '0');
    
    if (isset($_POST['page_title_position'])) {
        update_post_meta($post_id, '_page_title_position', sanitize_text_field($_POST['page_title_position']));
    }
}
add_action('save_post', 'save_page_metaboxes');

// Подключаем медиафайлы для страниц
function enqueue_media_for_pages() {
    global $pagenow;
    if (($pagenow == 'post.php' || $pagenow == 'post-new.php') && get_post_type() == 'page') {
        wp_enqueue_media();
        wp_enqueue_script('jquery');
    }
}
add_action('admin_enqueue_scripts', 'enqueue_media_for_pages');

// Отключаем FSE (блочные шаблоны) родительской темы
add_action('after_setup_theme', function() {
    remove_theme_support('block-templates');
});

// Принудительно используем классические шаблоны дочерней темы
add_filter('template_include', function($template) {
    if (is_404()) {
        $child_404 = get_stylesheet_directory() . '/404.php';
        if (file_exists($child_404)) {
            return $child_404;
        }
    }
    if (is_page()) {
        $child_page = get_stylesheet_directory() . '/page.php';
        if (file_exists($child_page)) {
            return $child_page;
        }
    }
    if (is_single()) {
        $child_single = get_stylesheet_directory() . '/single.php';
        if (file_exists($child_single)) {
            return $child_single;
        }
    }
    return $template;
}, 999);


// ==================== ФУТЕР ====================
function footer_settings_cpt() {
    register_post_type('footer_settings', [
        'labels' => ['name' => 'Футер', 'singular_name' => 'Настройка футера'],
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => 'technopark-main',
        'menu_icon' => 'dashicons-admin-generic',
        'supports' => ['title'],
    ]);
}
add_action('init', 'footer_settings_cpt');

// Метабоксы
function add_footer_metaboxes() {
    add_meta_box('footer_docs', 'Документы', 'render_footer_docs', 'footer_settings', 'normal', 'high');
    add_meta_box('footer_contacts', 'Контакты', 'render_footer_contacts', 'footer_settings', 'normal', 'high');
    add_meta_box('footer_socials', 'Соцсети (SVG-иконки)', 'render_footer_socials', 'footer_settings', 'normal', 'high');
}
add_action('add_meta_boxes', 'add_footer_metaboxes');

function render_footer_docs($post) {
    $docs = get_post_meta($post->ID, '_footer_docs', true) ?: [];
    echo '<div id="footer-docs-wrap">';
    foreach ($docs as $index => $doc) {
        echo '<div class="footer-doc-item" style="margin-bottom:15px;padding:10px;border:1px solid #ddd;">';
        echo '<input type="text" name="footer_docs['.$index.'][title]" value="'.esc_attr($doc['title']).'" placeholder="Название" style="width:30%"> ';
        echo '<input type="url" name="footer_docs['.$index.'][link]" value="'.esc_attr($doc['link']).'" placeholder="Ссылка" style="width:60%"> ';
        echo '<button type="button" class="remove-doc button">Удалить</button>';
        echo '</div>';
    }
    echo '</div><button type="button" id="add-doc" class="button">+ Добавить документ</button>';
    ?>
    <script>
    jQuery(document).ready(function($) {
        $('#add-doc').click(function() {
            var index = $('.footer-doc-item').length;
            var html = '<div class="footer-doc-item" style="margin-bottom:15px;padding:10px;border:1px solid #ddd;">' +
                '<input type="text" name="footer_docs['+index+'][title]" placeholder="Название" style="width:30%"> ' +
                '<input type="url" name="footer_docs['+index+'][link]" placeholder="Ссылка" style="width:60%"> ' +
                '<button type="button" class="remove-doc button">Удалить</button></div>';
            $('#footer-docs-wrap').append(html);
        });
        $(document).on('click', '.remove-doc', function() { $(this).closest('.footer-doc-item').remove(); });
    });
    </script>
    <?php
}

function render_footer_contacts($post) {
    $contacts = get_post_meta($post->ID, '_footer_contacts', true) ?: [];
    echo '<div id="footer-contacts-wrap">';
    foreach ($contacts as $index => $contact) {
        echo '<div class="footer-contact-item" style="margin-bottom:15px;padding:10px;border:1px solid #ddd;">';
        echo '<input type="text" name="footer_contacts['.$index.'][text]" value="'.esc_attr($contact['text']).'" placeholder="Текст" style="width:40%"> ';
        echo '<input type="url" name="footer_contacts['.$index.'][link]" value="'.esc_attr($contact['link']).'" placeholder="Ссылка" style="width:50%"> ';
        echo '<button type="button" class="remove-contact button">Удалить</button>';
        echo '</div>';
    }
    echo '</div><button type="button" id="add-contact" class="button">+ Добавить контакт</button>';
    ?>
    <script>
    jQuery(document).ready(function($) {
        $('#add-contact').click(function() {
            var index = $('.footer-contact-item').length;
            var html = '<div class="footer-contact-item" style="margin-bottom:15px;padding:10px;border:1px solid #ddd;">' +
                '<input type="text" name="footer_contacts['+index+'][text]" placeholder="Текст" style="width:40%"> ' +
                '<input type="url" name="footer_contacts['+index+'][link]" placeholder="Ссылка" style="width:50%"> ' +
                '<button type="button" class="remove-contact button">Удалить</button></div>';
            $('#footer-contacts-wrap').append(html);
        });
        $(document).on('click', '.remove-contact', function() { $(this).closest('.footer-contact-item').remove(); });
    });
    </script>
    <?php
}

function render_footer_socials($post) {
    $socials = get_post_meta($post->ID, '_footer_socials', true) ?: [];
    echo '<div id="footer-socials-wrap">';
    foreach ($socials as $index => $social) {
        echo '<div class="footer-social-item" style="margin-bottom:15px;padding:10px;border:1px solid #ddd;">';
        echo '<input type="url" name="footer_socials['.$index.'][link]" value="'.esc_attr($social['link']).'" placeholder="Ссылка" style="width:40%"><br>';
        echo '<textarea name="footer_socials['.$index.'][icon]" rows="3" placeholder="Вставьте SVG-код иконки" style="width:100%">'.esc_textarea($social['icon']).'</textarea>';
        echo '<p class="description"><strong>Где взять SVG?</strong> Бесплатные сайты <a href="https://svg4.ru/" target="_blank">SVG4</a>, <a href="https://heroicons.com/" target="_blank">Heroicons</a>. Скопируйте код и вставьте сюда.</p>' ;
        echo '<button type="button" class="remove-social button">Удалить</button>';
        echo '</div>';
    }
    echo '</div><button type="button" id="add-social" class="button">+ Добавить соцсеть</button>';
    ?>
    <script>
    jQuery(document).ready(function($) {
        $('#add-social').click(function() {
            var index = $('.footer-social-item').length;
            var html = '<div class="footer-social-item" style="margin-bottom:15px;padding:10px;border:1px solid #ddd;">' +
                '<input type="url" name="footer_socials['+index+'][link]" placeholder="Ссылка" style="width:40%"><br>' +
                '<textarea name="footer_socials['+index+'][icon]" rows="3" placeholder="Вставьте SVG-код иконки" style="width:100%"></textarea>' +
                '<p class="description"><strong>Где взять SVG?</strong> Бесплатные сайты <a href="https://svg4.ru/" target="_blank">SVG4</a>, <a href="https://heroicons.com/" target="_blank">Heroicons</a>. Скопируйте код и вставьте сюда.</p>' ;
                '<button type="button" class="remove-social button">Удалить</button></div>';
            $('#footer-socials-wrap').append(html);
        });
        $(document).on('click', '.remove-social', function() { $(this).closest('.footer-social-item').remove(); });
    });
    </script>
    <?php
}

function save_footer_meta($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;
    if (get_post_type($post_id) !== 'footer_settings') return;
    
    $fields = ['footer_docs', 'footer_contacts', 'footer_socials'];
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, '_' . $field, $_POST[$field]);
        }
    }
}
add_action('save_post', 'save_footer_meta');



// ==================== МЕТАБОКСЫ ДЛЯ СТРАНИЦЫ КОП ====================
function add_kop_metaboxes() {
    global $post;
    
    if (!$post || $post->post_type !== 'page') return;
    
    // Проверяем, что это шаблон page-kop.php
    $template = get_post_meta($post->ID, '_wp_page_template', true);
    if ($template !== 'page-projects.php') return;
    
    add_meta_box('kop_title', 'Заголовок страницы', 'render_kop_title', 'page', 'normal', 'high');
    add_meta_box('kop_text', 'Текст под заголовком (опционально)', 'render_kop_text', 'page', 'normal', 'high');
}
add_action('add_meta_boxes', 'add_kop_metaboxes');

function render_kop_title($post) {
    $value = get_post_meta($post->ID, '_kop_title', true);
    ?>
    <input type="text" name="kop_title" value="<?php echo esc_attr($value); ?>" style="width:100%" placeholder="Коробочные образовательные продукты">
    <p class="description">Заголовок страницы (по умолчанию: "Коробочные образовательные продукты")</p>
    <?php
}

function render_kop_text($post) {
    $value = get_post_meta($post->ID, '_kop_text', true);
    ?>
    <textarea name="kop_text" rows="4" style="width:100%" placeholder="Введите дополнительный текст..."><?php echo esc_textarea($value); ?></textarea>
    <p class="description">Дополнительный текст под заголовком (можно оставить пустым)</p>
    <?php
}

function save_kop_metaboxes($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;
    
    if (isset($_POST['kop_title'])) {
        update_post_meta($post_id, '_kop_title', sanitize_text_field($_POST['kop_title']));
    }
    if (isset($_POST['kop_text'])) {
        update_post_meta($post_id, '_kop_text', sanitize_textarea_field($_POST['kop_text']));
    }
}
add_action('save_post', 'save_kop_metaboxes');


// ==================== CPT ДЛЯ HERO ====================
function create_hero_cpt() {
    register_post_type('hero', array(
        'labels' => array(
            'name' => 'Hero секция',
            'singular_name' => 'Hero',
            'add_new_item' => 'Добавить Hero',
            'edit_item' => 'Редактировать Hero',
            'all_items' => 'Hero секция',
        ),
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => 'technopark-main',
        'menu_icon' => 'dashicons-welcome-widgets-menus',
        'supports' => array('title'),
    ));
}
add_action('init', 'create_hero_cpt');

// Метабоксы для Hero
function add_hero_cpt_metaboxes() {
    add_meta_box('hero_title', 'Заголовок', 'render_hero_cpt_title', 'hero', 'normal', 'high');
    add_meta_box('hero_text', 'Текст', 'render_hero_cpt_text', 'hero', 'normal', 'high');
    add_meta_box('hero_image', 'Фото', 'render_hero_cpt_image', 'hero', 'normal', 'high');
    add_meta_box('hero_buttons', 'Кнопки (максимум 2)', 'render_hero_cpt_buttons', 'hero', 'normal', 'high');
}
add_action('add_meta_boxes', 'add_hero_cpt_metaboxes');

function render_hero_cpt_title($post) {
    $value = get_post_meta($post->ID, '_hero_title', true);
    ?>
    <input type="text" name="hero_title" value="<?php echo esc_attr($value); ?>" style="width:100%" placeholder="Технопарк — место, где рождаются идеи">
    <?php
}

function render_hero_cpt_text($post) {
    $value = get_post_meta($post->ID, '_hero_text', true);
    ?>
    <textarea name="hero_text" rows="5" style="width:100%" placeholder="Текст hero секции..."><?php echo esc_textarea($value); ?></textarea>
    <?php
}

function render_hero_cpt_image($post) {
    $image_id = get_post_meta($post->ID, '_hero_image', true);
    $image_url = $image_id ? wp_get_attachment_url($image_id) : '';
    ?>
    <div>
        <button type="button" class="button" id="upload_hero_image">Выбрать изображение</button>
        <input type="hidden" id="hero_image_id" name="hero_image_id" value="<?php echo esc_attr($image_id); ?>">
        <div id="hero_image_preview" style="margin-top:10px;">
            <?php if ($image_url) : ?>
                <img src="<?php echo esc_url($image_url); ?>" style="max-width:300px;">
            <?php endif; ?>
        </div>
    </div>
    <script>
    jQuery(document).ready(function($) {
        $('#upload_hero_image').click(function(e) {
            e.preventDefault();
            var frame = wp.media({ title: 'Выберите изображение', multiple: false });
            frame.on('select', function() {
                var attachment = frame.state().get('selection').first().toJSON();
                $('#hero_image_id').val(attachment.id);
                $('#hero_image_preview').html('<img src="' + attachment.url + '" style="max-width:300px;">');
            });
            frame.open();
        });
    });
    </script>
    <?php
}

function render_hero_cpt_buttons($post) {
    $button_1_text = get_post_meta($post->ID, '_hero_button_1_text', true);
    $button_1_link = get_post_meta($post->ID, '_hero_button_1_link', true);
    $button_2_text = get_post_meta($post->ID, '_hero_button_2_text', true);
    $button_2_link = get_post_meta($post->ID, '_hero_button_2_link', true);
    ?>
    <div style="display:flex; gap:20px; flex-wrap:wrap;">
        <div style="flex:1; border:1px solid #ddd; padding:15px;">
            <h4>Кнопка 1</h4>
            <input type="text" name="hero_button_1_text" value="<?php echo esc_attr($button_1_text); ?>" placeholder="Текст кнопки" style="width:100%">
            <input type="url" name="hero_button_1_link" value="<?php echo esc_attr($button_1_link); ?>" placeholder="Ссылка" style="width:100%; margin-top:10px;">
        </div>
        <div style="flex:1; border:1px solid #ddd; padding:15px;">
            <h4>Кнопка 2</h4>
            <input type="text" name="hero_button_2_text" value="<?php echo esc_attr($button_2_text); ?>" placeholder="Текст кнопки" style="width:100%">
            <input type="url" name="hero_button_2_link" value="<?php echo esc_attr($button_2_link); ?>" placeholder="Ссылка" style="width:100%; margin-top:10px;">
        </div>
    </div>
    <?php
}

function save_hero_cpt_metaboxes($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;
    if (get_post_type($post_id) !== 'hero') return;
    
    if (isset($_POST['hero_title'])) {
        update_post_meta($post_id, '_hero_title', sanitize_text_field($_POST['hero_title']));
    }
    if (isset($_POST['hero_text'])) {
        update_post_meta($post_id, '_hero_text', sanitize_textarea_field($_POST['hero_text']));
    }
    if (isset($_POST['hero_image_id'])) {
        update_post_meta($post_id, '_hero_image', intval($_POST['hero_image_id']));
    }
    if (isset($_POST['hero_button_1_text'])) {
        update_post_meta($post_id, '_hero_button_1_text', sanitize_text_field($_POST['hero_button_1_text']));
    }
    if (isset($_POST['hero_button_1_link'])) {
        update_post_meta($post_id, '_hero_button_1_link', esc_url_raw($_POST['hero_button_1_link']));
    }
    if (isset($_POST['hero_button_2_text'])) {
        update_post_meta($post_id, '_hero_button_2_text', sanitize_text_field($_POST['hero_button_2_text']));
    }
    if (isset($_POST['hero_button_2_link'])) {
        update_post_meta($post_id, '_hero_button_2_link', esc_url_raw($_POST['hero_button_2_link']));
    }
}
add_action('save_post', 'save_hero_cpt_metaboxes');

function enqueue_media_for_hero_cpt() {
    global $pagenow, $post;
    if (($pagenow == 'post.php' || $pagenow == 'post-new.php') && isset($post) && get_post_type($post) == 'hero') {
        wp_enqueue_media();
        wp_enqueue_script('jquery');
    }
}
add_action('admin_enqueue_scripts', 'enqueue_media_for_hero_cpt');


// ==================== CPT ДЛЯ ШАПКИ ====================
function create_header_cpt() {
    register_post_type('header_settings', array(
        'labels' => array(
            'name' => 'Настройки шапки',
            'singular_name' => 'Настройка шапки',
            'add_new_item' => 'Добавить настройку',
            'edit_item' => 'Редактировать шапку',
            'all_items' => 'Настройки шапки',
        ),
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => 'technopark-main',
        'menu_icon' => 'dashicons-layout',
        'supports' => array('title'),
    ));
}
add_action('init', 'create_header_cpt');

// Метабоксы для шапки
function add_header_metaboxes() {
    add_meta_box('header_logo', 'Основной логотип', 'render_header_logo', 'header_settings', 'normal', 'high');
    add_meta_box('header_menu_items', 'Пункты меню', 'render_header_menu_items', 'header_settings', 'normal', 'high');
}
add_action('add_meta_boxes', 'add_header_metaboxes');

function render_header_logo($post) {
    $logo_id = get_post_meta($post->ID, '_header_logo', true);
    $logo_url = $logo_id ? wp_get_attachment_url($logo_id) : '';
    $logo_mime = $logo_id ? get_post_mime_type($logo_id) : '';
    ?>
    <div>
        <button type="button" class="button" id="upload_header_logo">Выбрать логотип (PNG, JPG, SVG)</button>
        <input type="hidden" id="header_logo_id" name="header_logo_id" value="<?php echo esc_attr($logo_id); ?>">
        <div id="header_logo_preview" style="margin-top:10px;">
            <?php if ($logo_url) : 
                if ($logo_mime === 'image/svg+xml') : ?>
                    <div style="max-width:150px;">
                        <?php echo file_get_contents($logo_url); ?>
                    </div>
                <?php else : ?>
                    <img src="<?php echo esc_url($logo_url); ?>" style="max-width:150px; height:auto;">
                <?php endif; 
            endif; ?>
        </div>
        <p class="description">Поддерживаются PNG, JPG, SVG. SVG будет отображаться как есть.</p>
    </div>
    <script>
    jQuery(document).ready(function($) {
        $('#upload_header_logo').click(function(e) {
            e.preventDefault();
            var frame = wp.media({
                title: 'Выберите логотип',
                multiple: false,
                library: { type: ['image', 'image/svg+xml'] }
            });
            frame.on('select', function() {
                var attachment = frame.state().get('selection').first().toJSON();
                $('#header_logo_id').val(attachment.id);
                
                if (attachment.mime === 'image/svg+xml') {
                    $.get(attachment.url, function(data) {
                        var svg = $(data).find('svg');
                        if (svg.length) {
                            var svgString = $('<div>').append(svg.clone()).html();
                            $('#header_logo_preview').html('<div style="max-width:150px;">' + svgString + '</div>');
                        } else {
                            $('#header_logo_preview').html('<img src="' + attachment.url + '" style="max-width:150px; height:auto;">');
                        }
                    });
                } else {
                    $('#header_logo_preview').html('<img src="' + attachment.url + '" style="max-width:150px; height:auto;">');
                }
            });
            frame.open();
        });
    });
    </script>
    <?php
}

function render_header_menu_items($post) {
    $menu_items = get_post_meta($post->ID, '_header_menu_items', true) ?: [];
    echo '<div id="header-menu-wrap">';
    echo '<p class="description"><strong>Подсказка:</strong> Чтобы создать выпадающее меню, оставьте поле "Ссылка" пустым. Пункты меню можно перетаскивать.</p>';
    
    foreach ($menu_items as $index => $item) {
        $has_children = !empty($item['children']);
        ?>
        <div class="header-menu-item" style="margin-bottom:15px;padding:10px;border:1px solid #ddd; background:#f9f9f9;">
            <div class="menu-item-main" style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
                <input type="text" name="header_menu_items[<?php echo $index; ?>][text]" value="<?php echo esc_attr($item['text']); ?>" placeholder="Название пункта" style="width:25%">
                <input type="url" name="header_menu_items[<?php echo $index; ?>][link]" value="<?php echo esc_attr($item['link']); ?>" placeholder="Ссылка (оставьте пустым для меню-аккордеон)" style="width:35%">
                <select name="header_menu_items[<?php echo $index; ?>][target]" style="width:15%">
                    <option value="_self" <?php selected($item['target'], '_self'); ?>>Текущее окно</option>
                    <option value="_blank" <?php selected($item['target'], '_blank'); ?>>Новое окно</option>
                </select>
                <button type="button" class="button add-child-item" data-parent="<?php echo $index; ?>">+ Добавить подпункт</button>
                <button type="button" class="button remove-menu-item button-link-delete">Удалить пункт</button>
            </div>
            
            <?php if (!empty($item['children'])) : ?>
                <div class="menu-item-children" style="margin-top:15px; margin-left:30px; padding-left:15px; border-left:2px solid #ddd;">
                    <h4>Подпункты:</h4>
                    <div class="children-wrap">
                        <?php foreach ($item['children'] as $child_index => $child) : ?>
                            <div class="child-item" style="display:flex; gap:10px; margin-bottom:10px; flex-wrap:wrap;">
                                <input type="text" name="header_menu_items[<?php echo $index; ?>][children][<?php echo $child_index; ?>][text]" value="<?php echo esc_attr($child['text']); ?>" placeholder="Название" style="width:30%">
                                <input type="url" name="header_menu_items[<?php echo $index; ?>][children][<?php echo $child_index; ?>][link]" value="<?php echo esc_attr($child['link']); ?>" placeholder="Ссылка" style="width:45%">
                                <select name="header_menu_items[<?php echo $index; ?>][children][<?php echo $child_index; ?>][target]" style="width:15%">
                                    <option value="_self" <?php selected($child['target'], '_self'); ?>>Текущее окно</option>
                                    <option value="_blank" <?php selected($child['target'], '_blank'); ?>>Новое окно</option>
                                </select>
                                <button type="button" class="button remove-child-item">Удалить</button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }
    echo '</div><button type="button" id="add-menu-item" class="button">+ Добавить пункт меню</button>';
    ?>
    <script>
    jQuery(document).ready(function($) {
        // Счётчик для новых пунктов
        let menuIndex = $('.header-menu-item').length;
        
        // Добавление основного пункта
        $('#add-menu-item').click(function() {
            var html = '<div class="header-menu-item" style="margin-bottom:15px;padding:10px;border:1px solid #ddd; background:#f9f9f9;">' +
                '<div class="menu-item-main" style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">' +
                '<input type="text" name="header_menu_items['+menuIndex+'][text]" placeholder="Название пункта" style="width:25%"> ' +
                '<input type="url" name="header_menu_items['+menuIndex+'][link]" placeholder="Ссылка (оставьте пустым для меню-аккордеон)" style="width:35%"> ' +
                '<select name="header_menu_items['+menuIndex+'][target]" style="width:15%">' +
                '<option value="_self">Текущее окно</option>' +
                '<option value="_blank">Новое окно</option>' +
                '</select> ' +
                '<button type="button" class="button add-child-item" data-parent="'+menuIndex+'">+ Добавить подпункт</button> ' +
                '<button type="button" class="button remove-menu-item button-link-delete">Удалить пункт</button>' +
                '</div></div>';
            $('#header-menu-wrap').append(html);
            menuIndex++;
        });
        
        // Добавление подпункта
        $(document).on('click', '.add-child-item', function() {
            var parent = $(this).data('parent');
            var parentDiv = $(this).closest('.header-menu-item');
            var childrenWrap = parentDiv.find('.children-wrap');
            var childIndex = childrenWrap.find('.child-item').length;
            
            if (childrenWrap.length === 0) {
                parentDiv.append('<div class="menu-item-children" style="margin-top:15px; margin-left:30px; padding-left:15px; border-left:2px solid #ddd;">' +
                    '<h4>Подпункты:</h4><div class="children-wrap"></div></div>');
                childrenWrap = parentDiv.find('.children-wrap');
            }
            
            var html = '<div class="child-item" style="display:flex; gap:10px; margin-bottom:10px; flex-wrap:wrap;">' +
                '<input type="text" name="header_menu_items['+parent+'][children]['+childIndex+'][text]" placeholder="Название" style="width:30%"> ' +
                '<input type="url" name="header_menu_items['+parent+'][children]['+childIndex+'][link]" placeholder="Ссылка" style="width:45%"> ' +
                '<select name="header_menu_items['+parent+'][children]['+childIndex+'][target]" style="width:15%">' +
                '<option value="_self">Текущее окно</option>' +
                '<option value="_blank">Новое окно</option>' +
                '</select> ' +
                '<button type="button" class="button remove-child-item">Удалить</button></div>';
            childrenWrap.append(html);
        });
        
        // Удаление основного пункта
        $(document).on('click', '.remove-menu-item', function() {
            $(this).closest('.header-menu-item').remove();
        });
        
        // Удаление подпункта
        $(document).on('click', '.remove-child-item', function() {
            $(this).closest('.child-item').remove();
        });
    });
    </script>
    <?php
}

function save_header_metaboxes($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;
    if (get_post_type($post_id) !== 'header_settings') return;
    
    if (isset($_POST['header_logo_id'])) {
        update_post_meta($post_id, '_header_logo', intval($_POST['header_logo_id']));
    }
    if (isset($_POST['header_menu_items'])) {
        update_post_meta($post_id, '_header_menu_items', $_POST['header_menu_items']);
    }
}
add_action('save_post', 'save_header_metaboxes');

function enqueue_media_for_header() {
    global $pagenow, $post;
    if (($pagenow == 'post.php' || $pagenow == 'post-new.php') && isset($post) && get_post_type($post) == 'header_settings') {
        wp_enqueue_media();
        wp_enqueue_script('jquery');
    }
}
add_action('admin_enqueue_scripts', 'enqueue_media_for_header');

// Разрешаем загрузку SVG
function allow_svg_upload($mimes) {
    $mimes['svg'] = 'image/svg+xml';
    $mimes['svgz'] = 'image/svg+xml';
    return $mimes;
}
add_filter('upload_mimes', 'allow_svg_upload');

// Добавляем поддержку SVG в медиабиблиотеке
function fix_svg_display($response, $attachment, $meta) {
    if ($response['mime'] === 'image/svg+xml') {
        $response['sizes'] = array(
            'thumbnail' => array(
                'url' => $response['url'],
                'width' => 100,
                'height' => 100,
            ),
        );
    }
    return $response;
}
add_filter('wp_prepare_attachment_for_js', 'fix_svg_display', 10, 3);

// Добавляем SVG в медиабиблиотеку
function add_svg_to_media_library($response, $attachment, $meta) {
    if ($response['mime'] === 'image/svg+xml') {
        $response['sizes']['full'] = array(
            'url' => $response['url'],
            'width' => 0,
            'height' => 0,
        );
    }
    return $response;
}
add_filter('wp_prepare_attachment_for_js', 'add_svg_to_media_library', 10, 3);
