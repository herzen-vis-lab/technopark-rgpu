<?php
$query = new WP_Query([
    'post_type'      => 'carousel',     
    'posts_per_page' => -1,
    'orderby'        => 'menu_order',
    'order'          => 'ASC'
]);

$slides = [];

if ($query->have_posts()) {
    while ($query->have_posts()) {
        $query->the_post();
        
        $image_id = get_post_meta(get_the_ID(), '_slide_image', true);
        $image_url = $image_id ? wp_get_attachment_url($image_id) : '';
        
        $slides[] = [
            'title'       => get_the_title(),
            'description' => get_the_excerpt() ?: wp_strip_all_tags(get_the_content()),
            'image'       => $image_url,
            'button1'     => [
                'text' => get_post_meta(get_the_ID(), '_button1_text', true) ?: '3D тур',
                'link' => get_post_meta(get_the_ID(), '_button1_link', true) ?: '#',
                'type' => get_post_meta(get_the_ID(), '_button1_style', true) ?: 'outline'
            ],
            'button2'     => [
                'text' => get_post_meta(get_the_ID(), '_button2_text', true) ?: 'Забронировать',
                'link' => get_post_meta(get_the_ID(), '_button2_link', true) ?: '#',
                'type' => get_post_meta(get_the_ID(), '_button2_style', true) ?: 'primary'
            ],
        ];
    }
} else {
    $slides = $fallback_slides;
}

wp_reset_postdata();
?>

<section id="carousel-solid" class="relative py-8">
    <div class="max-w-7xl mx-auto">

        <!-- Карусель -->
        <div id="solid-carousel" class="relative rounded-3xl shadow-custom overflow-hidden">
            
            <?php foreach ($slides as $index => $slide) : ?>
            <div class="carousel-slide transition-opacity duration-700 <?php echo $index === 0 ? 'opacity-100 relative' : 'opacity-0 pointer-events-none absolute inset-0'; ?>" 
                 data-index="<?php echo $index; ?>">

                <div class="grid grid-cols-1 lg:grid-cols-5">
                    
                    <!-- Левая часть с текстом и кнопками -->
                    <div class="lg:col-span-2 bg-primary flex items-center justify-center p-8 lg:p-12">
                        <div class="w-full max-w-lg text-center lg:text-left flex flex-col h-full">
                            <div class="flex-1">
                                <h3 class="text-white leading-tight mb-2 mt-2">
                                    <?php echo esc_html($slide['title']); ?>
                                </h3>
                                <p class="text-body text-white/90 leading-relaxed mb-8">
                                    <?php echo esc_html($slide['description']); ?>
                                </p>
                            </div>

                            <div class="flex flex-col gap-4 justify-center lg:justify-start mt-auto mb-2">
                                <?php 
                                $btn1 = $slide['button1'];
                                $btn2 = $slide['button2'];
                                ?>
                                <a href="<?php echo esc_url($btn1['link']); ?>" 
                                class="tp-btn <?php echo $btn1['type'] === 'primary' ? 'tp-btn--white' : 'tp-btn--outline-light'; ?>">
                                    <?php echo esc_html($btn1['text']); ?>
                                </a>
                                
                                <a href="<?php echo esc_url($btn2['link']); ?>" 
                                class="tp-btn <?php echo $btn2['type'] === 'primary' ? 'tp-btn--white' : 'tp-btn--outline-light'; ?>">
                                    <?php echo esc_html($btn2['text']); ?>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Правая часть с фото -->
                    <div class="lg:col-span-3 relative min-h-[300px] lg:min-h-full">
                        <?php if (!empty($slide['image'])) : ?>
                            <img src="<?php echo esc_url($slide['image']); ?>" 
                                 alt="<?php echo esc_attr($slide['title']); ?>"
                                 class="w-full h-full object-cover object-center lg:absolute lg:inset-0">
                        <?php else : ?>
                            <div class="w-full h-full bg-gradient-to-br from-gray-300 to-gray-400 flex items-center justify-center lg:absolute lg:inset-0">
                                <span class="text-white text-2xl">Фото слайда</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>

            <!-- Стрелки только на десктопе -->
            <button id="prev-slide" 
                    class="hidden lg:flex absolute left-1 top-1/2 -translate-y-1/2 z-30 w-12 h-12 items-center justify-center rounded-full transition-all duration-300 hover:scale-110"
                    style="outline: none !important; box-shadow: none !important; border: none; background: transparent;"
                    onfocus="this.blur()">
                <svg class="w-8 h-8" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" style="outline: none;">
                    <path stroke="white" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>

            <button id="next-slide" 
                    class="hidden lg:flex absolute right-1 top-1/2 -translate-y-1/2 z-30 w-12 h-12 items-center justify-center rounded-full transition-all duration-300 hover:scale-110"
                    style="outline: none !important; box-shadow: none !important; border: none; background: transparent;"
                    onfocus="this.blur()">
                <svg class="w-8 h-8" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" style="outline: none;">
                    <path stroke="white" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>

            <!-- Точки внизу -->
            <div class="absolute bottom-6 left-1/2 -translate-x-1/2 z-20 flex gap-2">
                <?php foreach ($slides as $index => $slide) : ?>
                    <button class="carousel-dot transition-all duration-300 cursor-pointer rounded-full h-1 <?php echo $index === 0 ? 'w-8 bg-white opacity-100' : 'w-4 bg-white opacity-50 hover:opacity-70'; ?>"
                            data-index="<?php echo $index; ?>"
                            style="outline: none !important; box-shadow: none !important; border: none;"
                            onfocus="this.blur()"></button>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const slides = document.querySelectorAll('.carousel-slide');
    const dots = document.querySelectorAll('.carousel-dot');
    const carousel = document.getElementById('solid-carousel');
    let current = 0;
    let touchStartX = 0;
    let touchEndX = 0;

    function showSlide(index) {
        slides.forEach(slide => slide.classList.add('opacity-0', 'pointer-events-none'));
        slides[index].classList.remove('opacity-0', 'pointer-events-none');
        
        dots.forEach(dot => {
            dot.classList.remove('w-8', 'bg-white', 'opacity-100');
            dot.classList.add('w-4', 'bg-white', 'opacity-50');
        });
        dots[index].classList.remove('w-4', 'opacity-50');
        dots[index].classList.add('w-8', 'opacity-100');
        
        current = index;
    }

    // Стрелки (если есть на устройстве)
    const prevBtn = document.getElementById('prev-slide');
    const nextBtn = document.getElementById('next-slide');
    
    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            let prev = current - 1;
            if (prev < 0) prev = slides.length - 1;
            showSlide(prev);
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            let next = current + 1;
            if (next >= slides.length) next = 0;
            showSlide(next);
        });
    }

    // Точки
    dots.forEach((dot, i) => {
        dot.addEventListener('click', () => showSlide(i));
    });

    // Свайпы для мобильных устройств
    if (carousel) {
        carousel.addEventListener('touchstart', (e) => {
            touchStartX = e.changedTouches[0].screenX;
        });

        carousel.addEventListener('touchend', (e) => {
            touchEndX = e.changedTouches[0].screenX;
            const swipeDistance = touchEndX - touchStartX;
            
            if (Math.abs(swipeDistance) > 50) { // минимальное расстояние для свайпа
                if (swipeDistance > 0) {
                    // Свайп вправо — предыдущий слайд
                    let prev = current - 1;
                    if (prev < 0) prev = slides.length - 1;
                    showSlide(prev);
                } else {
                    // Свайп влево — следующий слайд
                    let next = current + 1;
                    if (next >= slides.length) next = 0;
                    showSlide(next);
                }
            }
        });
    }
});
</script>