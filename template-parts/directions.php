<?php
$query = new WP_Query(array(
    'post_type' => 'directions',
    'posts_per_page'  => 6,
    'orderby' => 'date',
    'order' => 'ASC',
));

$directions_items = array();
if ($query->have_posts()) {
    while ($query->have_posts()) {
        $query->the_post();
        $post_id = get_the_ID();
        
        $buttons = get_post_meta($post_id, '_direction_buttons', true);
        if (empty($buttons)) {
            $buttons = array();
        }

        $icon_image_id = get_post_meta($post_id, '_direction_icon_image_id', true);
        $icon_url = $icon_image_id ? wp_get_attachment_url($icon_image_id) : '';

        $directions_items[] = array(
            'id' => $post_id,
            'title' => get_the_title(),
            'description' => get_post_meta($post_id, '_direction_card_description', true) ?: wp_trim_words(get_the_content(), 30),
            'icon_url' => $icon_url,
            'buttons' => $buttons,
            'link' => get_permalink(),
        );
    }
}

wp_reset_postdata();
?>

<section id="directions" class="py-4">
    <h2 class="text-black text-center py-2">Направления</h2>
    <div class="mt-8 grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
        <?php foreach ($directions_items as $item) : ?>
            <article class="tp-direction-card flex h-auto flex-col rounded-[16px] bg-white p-6 shadow-custom"
                data-card-link="<?php echo esc_url($item['link']); ?>"
                role="link" tabindex="0">
                
                <!-- Иконка -->
                <div class="mb-5 flex items-center justify-center mx-auto">
                    <?php if (!empty($item['icon_url'])) : ?>
                        <?php 
                        $svg_content = @file_get_contents($item['icon_url']);
                        if ($svg_content) {
                            $svg_content = preg_replace('/width="[^"]*"/', '', $svg_content);
                            $svg_content = preg_replace('/height="[^"]*"/', '', $svg_content);
                            $svg_content = str_replace('<svg', '<svg style="width: 80px; height: 80px;" class="text-primary" fill="currentColor"', $svg_content);
                            echo $svg_content;
                        }
                        ?>
                    <?php else : ?>
                        <svg style="width: 80px; height: 80px;" class="text-primary" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M19.14 12.94c.04-.31.06-.62.06-.94s-.02-.63-.07-.94l2.03-1.58a.5.5 0 0 0 .12-.64l-1.92-3.32a.5.5 0 0 0-.6-.22l-2.39.96a7.1 7.1 0 0 0-1.63-.95l-.36-2.54a.5.5 0 0 0-.49-.43h-3.84a.5.5 0 0 0-.49.42l-.36 2.55a7.2 7.2 0 0 0-1.63.95l-2.39-.96a.5.5 0 0 0-.6.22L2.7 8.84a.5.5 0 0 0 .12.64l2.03 1.58c-.05.31-.08.62-.08.94s.03.63.08.94l-2.03 1.58a.5.5 0 0 0-.12.64l1.92 3.32c.13.23.4.32.6.22l2.39-.96c.5.39 1.05.71 1.63.95l.36 2.54c.04.24.25.42.49.42h3.84c.24 0 .45-.18.49-.42l.36-2.54c.58-.24 1.13-.56 1.63-.95l2.39.96c.22.09.47 0 .6-.22l1.92-3.32a.5.5 0 0 0-.12-.64l-2.03-1.58ZM12 15.5A3.5 3.5 0 1 1 12 8.5a3.5 3.5 0 0 1 0 7Z"/>
                        </svg>
                    <?php endif; ?>
                </div>

                <h3 class="text-black text-center"><?php echo esc_html($item['title']); ?></h3>
                <p class="mt-3 text-body text-black text-center"><?php echo esc_html($item['description']); ?></p>

                <div class="mt-auto flex flex-col gap-3 pt-6">
                    <?php foreach ($item['buttons'] as $button) : ?>
                        <?php if (!empty($button['text']) && !empty($button['link'])) : ?>
                            <?php $btn_class = ($button['style'] === 'primary') ? 'tp-btn tp-btn--primary text-body' : 'tp-btn tp-btn--outline text-body'; ?>
                            <a href="<?php echo esc_url($button['link']); ?>" class="<?php echo esc_attr($btn_class); ?>">
                                <?php echo esc_html($button['text']); ?>
                            </a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const cards = document.querySelectorAll('.tp-direction-card[data-card-link]');
    cards.forEach((card) => {
        const url = card.getAttribute('data-card-link');
        if (!url || url === '#') return;

        card.addEventListener('click', function (event) {
            if (event.target.closest('a, button')) return;
            window.location.href = url;
        });

        card.addEventListener('keydown', function (event) {
            if (event.key !== 'Enter' && event.key !== ' ') return;
            event.preventDefault();
            window.location.href = url;
        });
    });
});
</script>