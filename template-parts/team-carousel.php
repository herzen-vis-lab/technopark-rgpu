<?php

$query = new WP_Query([
    'post_type'      => 'team',
    'posts_per_page' => -1,
    'orderby'        => 'menu_order',
    'order'          => 'ASC'
]);

$team_items = [];

if ($query->have_posts()) {
    while ($query->have_posts()) {
        $query->the_post();
        $photo = get_the_post_thumbnail_url(get_the_ID(), 'medium');
        
        $team_items[] = [
            'name'     => get_the_title(),
            'position' => get_the_excerpt() ?: 'Преподаватель',
            'photo'    => $photo ?: 'https://via.placeholder.com/120',
        ];
    }
} else {
    // Fallback
    $team_items = [
        ['name' => 'Иванов Иван Иванович', 'position' => 'Руководитель Технопарка', 'photo' => ''],
        ['name' => 'Петрова Анна Сергеевна с очень длинным именем', 'position' => 'Педагог-наставник, очень крутой человек с длинной должностью', 'photo' => ''],
        ['name' => 'Петрова Анна Сергеевна', 'position' => 'Педагог-наставник', 'photo' => ''],
        ['name' => 'Петрова Анна Сергеевна', 'position' => 'Педагог-наставник', 'photo' => ''],
        ['name' => 'Петрова Анна Сергеевна', 'position' => 'Педагог-наставник', 'photo' => ''],
        ['name' => 'Петрова Анна Сергеевна', 'position' => 'Педагог-наставник', 'photo' => ''],
    ];
}
wp_reset_postdata();
?>

<section id="team-carousel" class="py-12 overflow-visible">
    <div class="max-w-7xl mx-auto overflow-visible">
        <h2 class="text-black text-center mb-10">Наша команда</h2>

        <div class="relative overflow-visible">
            <div class="swiper teamSwiper overflow-visible pb-12">
                <div class="swiper-wrapper">
                    <?php foreach ($team_items as $member) : ?>
                    <div class="swiper-slide h-auto overflow-visible">
                        <div class="bg-white rounded-[14px] p-6 shadow-custom flex flex-col items-center text-center h-full">
                            <div class="w-[110px] h-[110px] md:w-[120px] md:h-[120px] rounded-full overflow-hidden border-4 border-white shadow-sm mb-6 flex-shrink-0">
                                <img src="<?php echo esc_url($member['photo']); ?>" 
                                     alt="<?php echo esc_attr($member['name']); ?>"
                                     class="w-full h-full object-cover">
                            </div>
                            <p class="text-body font-medium text-black mb-2">
                                <?php echo esc_html($member['name']); ?>
                            </p>
                            <p class="text-small text-black/70">
                                <?php echo esc_html($member['position']); ?>
                            </p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="swiper-pagination team-pagination mt-10"></div>
            </div>

            <div class="swiper-button-prev team-swiper-button-prev !hidden lg:!flex"></div>
            <div class="swiper-button-next team-swiper-button-next !hidden lg:!flex"></div>
        </div>
    </div>
</section>

<!-- Swiper -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

<script>
document.addEventListener('DOMContentLoaded', function () {
    new Swiper('.teamSwiper', {
        slidesPerView: 1,
        spaceBetween: 20,
        loop: true,
        grabCursor: true,
        
        // Навигация (стрелки)
        navigation: {
            nextEl: '.team-swiper-button-next',
            prevEl: '.team-swiper-button-prev',
        },
        
        // Пагинация (точки)
        pagination: {
            el: '.team-pagination',
            clickable: true,
        },

        breakpoints: {
            640: { slidesPerView: 2, spaceBetween: 20 },
            724: { slidesPerView: 3, spaceBetween: 24 },
            1280: { slidesPerView: 4, spaceBetween: 28 }
        }
    });
});
</script>