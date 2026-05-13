<?php
// Получаем данные из CPT Hero
$hero_items = get_posts(array(
    'post_type' => 'hero',
    'posts_per_page' => 1,
));

if (!empty($hero_items)) {
    $hero = $hero_items[0];
    $hero_title = get_post_meta($hero->ID, '_hero_title', true) ?: 'Технопарк — место, где рождаются идеи';
    $hero_text = get_post_meta($hero->ID, '_hero_text', true) ?: 'Образовательная среда для подготовки педагогов...';
    $hero_image_id = get_post_meta($hero->ID, '_hero_image', true);
    $hero_image_url = $hero_image_id ? wp_get_attachment_url($hero_image_id) : '';
    
    $button_1_text = get_post_meta($hero->ID, '_hero_button_1_text', true);
    $button_1_link = get_post_meta($hero->ID, '_hero_button_1_link', true);
    $button_2_text = get_post_meta($hero->ID, '_hero_button_2_text', true);
    $button_2_link = get_post_meta($hero->ID, '_hero_button_2_link', true);
} else {
    $hero_title = 'Технопарк — место, где рождаются идеи';
    $hero_text = 'Образовательная среда для подготовки педагогов...';
    $hero_image_url = '';
    $button_1_text = '';
    $button_2_text = '';
}

$primary_button_text = $button_1_text ?: 'Посмотреть направления';
$primary_button_link = $button_1_link ?: home_url('/#directions');
$secondary_button_text = $button_2_text ?: 'Мероприятия';
$secondary_button_link = $button_2_link ?: home_url('/event/');
?>

<section class="py-4">
    <div class="relative mx-auto w-full max-w-7xl overflow-hidden rounded-[20px] shadow-custom">
        <?php if (!empty($hero_image_url)) : ?>
            <img
                src="<?php echo esc_url($hero_image_url); ?>"
                alt="<?php echo esc_attr($hero_title ?: 'Hero'); ?>"
                class="absolute inset-0 h-full w-full object-cover object-center"
            >
        <?php endif; ?>

        <div class="absolute inset-0 bg-black/70"></div>

        <div class="relative z-10 flex items-center justify-center md:justify-start p-6 md:p-10 lg:p-12 min-h-[500px] md:min-h-[550px] lg:min-h-[592px]">
            <div class="w-full max-w-[90%] md:max-w-[70%] lg:max-w-[520px] text-white text-center md:text-left">
                <?php if (!empty($hero_title)) : ?>
                    <h2 class="tp-hero-title break-words"><?php echo esc_html($hero_title); ?></h2>
                <?php endif; ?>

                <?php if (!empty($hero_text)) : ?>
                    <p class="mt-4 text-body tp-hero-subtitle break-words"><?php echo wp_kses_post(nl2br(esc_html($hero_text))); ?></p>
                <?php endif; ?>

                <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center md:justify-start">
                    <a href="<?php echo esc_url($primary_button_link); ?>" class="tp-btn tp-btn--primary text-body text-center">
                        <?php echo esc_html($primary_button_text); ?>
                    </a>

                    <a href="<?php echo esc_url($secondary_button_link); ?>" class="tp-btn tp-btn--outline-light text-body text-center">
                        <?php echo esc_html($secondary_button_text); ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>