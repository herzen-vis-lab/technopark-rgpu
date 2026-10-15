<?php
/**
 * WindPress: без моргания стилей.
 *
 * В режиме «compiler» WindPress собирает Tailwind в браузере, и первую секунду страница
 * видна без CSS. В режиме «hybrid» готовый CSS из кэша вставляется прямо в <head>,
 * а компилятор лишь дособирает классы, которых в кэше нет.
 *
 * Кэш WindPress строит из содержимого записей, а шаблоны темы не сканирует — поэтому
 * регистрируем провайдер, который отдаёт ему PHP/JS-файлы темы. После изменения шаблонов
 * кэш нужно пересобрать: WindPress → Settings → Performance → Generate.
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
