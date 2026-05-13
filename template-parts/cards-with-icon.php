<?php
/**
 * Универсальный шаблон для карточек с иконками
 */

$args = isset($args) ? $args : array();
$cards = isset($args['cards']) ? $args['cards'] : array();
$columns = isset($args['columns']) ? $args['columns'] : 3;
$bg_color = isset($args['bg_color']) ? $args['bg_color'] : 'bg-primary';
$text_color = isset($args['text_color']) ? $args['text_color'] : 'text-white';

if (empty($cards)) return;

// Определяем классы для сетки
$grid_classes = '';
if ($columns == 2) {
    $grid_classes = 'grid-cols-1 sm:grid-cols-2';
} elseif ($columns == 3) {
    $grid_classes = 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3';
} elseif ($columns == 4) {
    $grid_classes = 'grid-cols-2 lg:grid-cols-4';
} else {
    $grid_classes = 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3';
}
?>

<div class="grid <?php echo $grid_classes; ?> gap-6 mb-12">
    <?php foreach ($cards as $card) : ?>
        <div class="<?php echo $bg_color; ?> rounded-[14px] shadow-lg p-6 text-center flex flex-col items-center justify-center min-h-[200px]">
            
            <?php if (!empty($card['image_id'])) : 
                $image_url = wp_get_attachment_url($card['image_id']);
                if ($image_url && preg_match('/\.svg$/i', $image_url)) :
                    $svg_content = @file_get_contents($image_url);
                    if ($svg_content) :
                        // Удаляем старые атрибуты и делаем иконку цветом текущего текста
                        $svg_content = preg_replace('/\s(?:width|height|fill|stroke)=("|\')(.*?)\1/i', '', $svg_content);
                        $svg_content = str_replace('<svg', '<svg class="cards-with-icon-svg" style="width: 60px; height: 60px;"', $svg_content);
                        echo '<div class="mb-4 cards-with-icon-icon">' . $svg_content . '</div>';
                    endif;
                endif;
            elseif (!empty($card['svg'])) : ?>
                <div class="mb-4 cards-with-icon-icon">
                    <?php 
                    $svg = $card['svg'];
                    $svg = preg_replace('/\s(?:width|height|fill|stroke)=("|\')(.*?)\1/i', '', $svg);
                    $svg = str_replace('<svg', '<svg class="cards-with-icon-svg" style="width: 60px; height: 60px;"', $svg);
                    echo $svg;
                    ?>
                </div>
            <?php else : ?>
                <div class="mb-4">
                    <svg style="width: 60px; height: 60px;" fill="#ffffff" stroke="none" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                    </svg>
                </div>
            <?php endif; ?>
            
            <p class="text-body text-white"><?php echo esc_html($card['text']); ?></p>
        </div>
    <?php endforeach; ?>
</div>