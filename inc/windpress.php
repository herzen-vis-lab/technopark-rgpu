<?php
/**
 * WindPress: без моргания стилей.
 *
 * В режиме «compiler» WindPress собирает Tailwind в браузере, и первую секунду страница
 * видна без CSS. В режиме «hybrid» готовый CSS из кэша вставляется прямо в <head>,
 * а компилятор лишь дособирает классы, которых в кэше нет.
 *
 * Кэш WindPress строит из содержимого записей, а шаблоны темы не сканирует — поэтому
 * регистрируем провайдер, который отдаёт ему PHP/JS-файлы темы.
 *
 * Кэш на сервере устаревает после каждого изменения шаблонов, пока кто-то не нажмёт
 * WindPress → Settings → Performance → Generate. Поэтому тема везёт свой собранный CSS
 * (css/windpress-cache.css) и вставляет в <head> тот, что свежее: после деплоя — файл темы,
 * после Generate на сервере — кэш WindPress.
 * Обновить файл темы: локально WindPress → Generate и скопировать
 * wp-content/uploads/windpress/cache/tailwind.css → css/windpress-cache.css.
 */

if (!defined('ABSPATH')) exit;

add_filter('f!windpress/core/runtime:append_header.mode', function () {
    return 'hybrid';
});

add_filter('f!windpress/core/cache:compile.providers', function (array $providers) {
    $providers[] = array(
        'id'          => 'technopark-theme',
        'name'        => 'Тема Technopark',
        'description' => 'PHP- и JS-файлы темы technopark-child',
        'callback'    => 'tp_windpress_theme_contents',
        'enabled'     => true,
        'type'        => 'theme',
    );
    return $providers;
});

function tp_windpress_theme_contents($metadata = array()) {
    $dir = get_stylesheet_directory();
    $contents = array();
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS));

    foreach ($files as $file) {
        $path = $file->getPathname();
        $relative = ltrim(substr($path, strlen($dir)), '/');
        if (!preg_match('/\.(php|js)$/', $relative) || strpos($relative, 'output/') === 0 || $relative === 'inc/lucide-icons.php') {
            continue;
        }
        $contents[] = array('name' => $relative, 'content' => file_get_contents($path));
    }

    return array('metadata' => array('next_batch' => false, 'total_batches' => 1), 'contents' => $contents);
}

function tp_windpress_theme_cache_file() {
    return get_stylesheet_directory() . '/css/windpress-cache.css';
}

// Кэш, который сгенерировал сам WindPress (uploads/windpress/cache/tailwind.css)
function tp_windpress_plugin_cache_file() {
    if (class_exists('\\WindPress\\WindPress\\Core\\Cache')) {
        return \WindPress\WindPress\Core\Cache::get_cache_path(\WindPress\WindPress\Core\Cache::CSS_CACHE_FILE);
    }
    return '';
}

function tp_windpress_use_theme_cache() {
    $theme = tp_windpress_theme_cache_file();
    if (!is_readable($theme)) return false;
    $plugin = tp_windpress_plugin_cache_file();
    return !$plugin || !is_readable($plugin) || filemtime($theme) > filemtime($plugin);
}

// Файл темы свежее — не даём WindPress вставлять свой устаревший кэш (компилятор при этом работает)
add_filter('f!windpress/core/runtime:append_header.exclude_admin', function ($skip) {
    return $skip || tp_windpress_use_theme_cache();
});

// …и вставляем CSS темы в то же место <head>, что и WindPress (до первой отрисовки)
add_action('wp_head', function () {
    if (is_admin() || !tp_windpress_use_theme_cache()) return;
    $css = file_get_contents(tp_windpress_theme_cache_file());
    if ($css) {
        echo "<style id='windpress-cached-inline-css'>" . wp_strip_all_tags($css) . "</style>\n";
    }
}, 1000001);
