<?php
$query = new WP_Query(array(
    'post_type' => 'stats',
    'posts_per_page' => 4,
    'orderby' => 'date',
    'order' => 'ASC',
));

$stats_items = array();
if ($query->have_posts()) {
    while ($query->have_posts()) {
        $query->the_post();

        $number = get_post_meta(get_the_ID(), '_stats_number', true);
        $text = get_post_meta(get_the_ID(), '_stats_text', true);
        $extra_description = get_the_excerpt();

        if (empty($number)) {
            $number = get_the_title();
        }
        if (empty($text)) {
            $text = get_the_excerpt() ?: wp_trim_words(get_the_content(), 10);
        }

        if (!empty($number) || !empty($text)) {
            $stats_items[] = array(
                'number' => $number,
                'text' => $text,
                'extra' => $extra_description,
            );
        }
    }
}

if (empty($stats_items)) {
    $stats_items = array(
        array('number' => '100', 'text' => 'лет работы', 'extra' => ''),
        array('number' => '20+', 'text' => 'направлений', 'extra' => ''),
        array('number' => '5000+', 'text' => 'участников', 'extra' => ''),
        array('number' => '300+', 'text' => 'мероприятий', 'extra' => ''),
    );
}
?>

<section id="stats" class="py-4">
    <h2 class="text-black text-center py-2">Мы в цифрах</h2>
    <div class="mt-8 grid grid-cols-2 gap-5 lg:grid-cols-4">
        <?php foreach ($stats_items as $item) : ?>
            <article class="bg-primary rounded-[14px] shadow-lg p-6 text-center flex flex-col items-center justify-center aspect-square">
                <?php if (!empty($item['number'])) : ?>
                    <div class="text-black-128 leading-none text-white"><?php echo esc_html($item['number']); ?></div>
                <?php endif; ?>

                <?php if (!empty($item['text'])) : ?>
                    <h3 class="mt-3 text-white"><?php echo esc_html($item['text']); ?></h3>
                <?php endif; ?>
                <?php if (!empty($item['extra'] ?? '')) : ?>
                    <p class="mt-2 text-small text-white"><?php echo esc_html($item['extra']); ?></p>
                <?php endif; ?>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<?php wp_reset_postdata(); ?>