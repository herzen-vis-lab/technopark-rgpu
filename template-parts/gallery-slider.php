<?php
/**
 * Универсальный шаблон галереи-слайдера
 * 
 * Использование:
 * get_template_part('template-parts/gallery-slider', null, array('gallery_id' => 123));
 * 
 * ИЛИ напрямую с IDs:
 * get_template_part('template-parts/gallery-slider', null, array('ids' => '1,2,3'));
 */

$args = isset($args) ? $args : array();

// Поддержка двух вариантов: передача ID галереи или прямых IDs
if (!empty($args['gallery_id'])) {
    $gallery_ids = get_post_meta($args['gallery_id'], '_gallery_images', true);
} else {
    $gallery_ids = isset($args['ids']) ? $args['ids'] : '';
}

$slider_height = isset($args['height']) ? $args['height'] : 'h-[400px] md:h-[500px]';
$show_arrows = isset($args['show_arrows']) ? $args['show_arrows'] : true;
$show_dots = isset($args['show_dots']) ? $args['show_dots'] : true;

// Собираем изображения
$images = [];
if (!empty($gallery_ids)) {
    $ids = explode(',', $gallery_ids);
    foreach ($ids as $img_id) {
        $img_url = wp_get_attachment_url($img_id);
        if ($img_url) {
            $images[] = array(
                'url' => $img_url,
                'alt' => get_post_meta($img_id, '_wp_attachment_image_alt', true)
            );
        }
    }
}
$has_multiple = count($images) > 1;

if (empty($images)) return;
?>

<div class="relative mb-12 gallery-slider-container" data-show-arrows="<?php echo $show_arrows ? 'true' : 'false'; ?>">
    <div class="rounded-[20px] overflow-hidden shadow-custom leading-[0]">
        <?php if ($has_multiple) : ?>
            <?php foreach ($images as $index => $image) : ?>
                <div class="gallery-slide" style="<?php echo $index === 0 ? 'display:block' : 'display:none'; ?>">
                    <img src="<?php echo esc_url($image['url']); ?>" 
                         alt="<?php echo esc_attr($image['alt']); ?>" 
                         class="w-full <?php echo $slider_height; ?> object-cover">
                </div>
            <?php endforeach; ?>
            
            <?php if ($show_arrows) : ?>
            <button class="gallery-prev hidden lg:flex absolute left-4 top-1/2 -translate-y-1/2 z-30 w-12 h-12 items-center justify-center rounded-full transition-all duration-300 hover:scale-110"
                    style="outline: none !important; box-shadow: none !important; border: none; background: transparent;"
                    onfocus="this.blur()">
                <svg class="w-8 h-8" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" style="filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));">
                    <path stroke="white" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
            
            <button class="gallery-next hidden lg:flex absolute right-4 top-1/2 -translate-y-1/2 z-30 w-12 h-12 items-center justify-center rounded-full transition-all duration-300 hover:scale-110"
                    style="outline: none !important; box-shadow: none !important; border: none; background: transparent;"
                    onfocus="this.blur()">
                <svg class="w-8 h-8" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" style="filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));">
                    <path stroke="white" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
            <?php endif; ?>
            
            <?php if ($show_dots) : ?>
            <div class="absolute bottom-6 left-1/2 -translate-x-1/2 z-20 flex gap-2">
                <?php foreach ($images as $index => $image) : ?>
                    <button class="gallery-dot transition-all duration-300 cursor-pointer rounded-full h-1 <?php echo $index === 0 ? 'w-8 bg-white opacity-100' : 'w-4 bg-white opacity-50 hover:opacity-70'; ?>"
                            data-index="<?php echo $index; ?>"
                            style="outline: none !important; box-shadow: none !important; border: none;"
                            onfocus="this.blur()"></button>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        <?php else : ?>
            <img src="<?php echo esc_url($images[0]['url']); ?>" 
                 alt="<?php echo esc_attr($images[0]['alt']); ?>" 
                 class="w-full <?php echo $slider_height; ?> object-cover">
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const galleryContainers = document.querySelectorAll('.gallery-slider-container');
    
    galleryContainers.forEach(galleryContainer => {
        const slides = galleryContainer.querySelectorAll('.gallery-slide');
        const prevBtn = galleryContainer.querySelector('.gallery-prev');
        const nextBtn = galleryContainer.querySelector('.gallery-next');
        const dots = galleryContainer.querySelectorAll('.gallery-dot');
        
        if (slides.length === 0) return;
        
        let currentIndex = 0;
        
        function showSlide(index) {
            if (index < 0) index = slides.length - 1;
            if (index >= slides.length) index = 0;
            
            slides.forEach((slide, i) => {
                slide.style.display = i === index ? 'block' : 'none';
            });
            
            if (dots.length) {
                dots.forEach((dot, i) => {
                    dot.classList.remove('w-8', 'bg-white', 'opacity-100');
                    dot.classList.add('w-4', 'bg-white', 'opacity-50');
                    if (i === index) {
                        dot.classList.remove('w-4', 'opacity-50');
                        dot.classList.add('w-8', 'opacity-100');
                    }
                });
            }
            
            currentIndex = index;
        }
        
        if (prevBtn && nextBtn && slides.length > 1) {
            prevBtn.addEventListener('click', (e) => { 
                e.preventDefault(); 
                showSlide(currentIndex - 1); 
            });
            
            nextBtn.addEventListener('click', (e) => { 
                e.preventDefault(); 
                showSlide(currentIndex + 1); 
            });
        }
        
        if (dots.length) {
            dots.forEach((dot, i) => {
                dot.addEventListener('click', (e) => { 
                    e.preventDefault(); 
                    showSlide(i); 
                });
            });
        }
        
        let touchStartX = 0, touchEndX = 0;
        galleryContainer.addEventListener('touchstart', (e) => { touchStartX = e.changedTouches[0].screenX; });
        galleryContainer.addEventListener('touchend', (e) => {
            touchEndX = e.changedTouches[0].screenX;
            const swipeDistance = touchEndX - touchStartX;
            if (Math.abs(swipeDistance) > 50 && slides.length > 1) {
                if (swipeDistance > 0) showSlide(currentIndex - 1);
                else showSlide(currentIndex + 1);
            }
        });
        
        // Показываем первый слайд
        showSlide(0);
    });
});
</script>