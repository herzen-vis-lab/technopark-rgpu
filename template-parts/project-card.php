<?php
$directions = wp_get_post_terms(get_the_ID(), 'project_direction', ['fields' => 'slugs']);
$ages       = wp_get_post_terms(get_the_ID(), 'project_age', ['fields' => 'slugs']);
$direction_names = wp_get_post_terms(get_the_ID(), 'project_direction', ['fields' => 'names']);
$age_names = wp_get_post_terms(get_the_ID(), 'project_age', ['fields' => 'names']);

// Получаем данные из метабоксов 
$project_view = get_post_meta(get_the_ID(), '_project_view', true);
$short_description = get_the_excerpt() ?: wp_trim_words(strip_tags(get_the_content()), 20);

// Основное фото (первое из галереи)
$main_image_url = '';
$gallery_ids = get_post_meta(get_the_ID(), '_project_gallery', true);
if (!empty($gallery_ids)) {
    $ids = explode(',', $gallery_ids);
    $first_id = $ids[0];
    $main_image_url = wp_get_attachment_url($first_id);
}
?>

<article class="project-card bg-white rounded-[14px] shadow-custom overflow-hidden flex flex-col h-full hover:-translate-y-1 transition-all duration-300 cursor-pointer group"
         data-direction="<?php echo esc_attr(implode(',', $directions)); ?>"
         data-age="<?php echo esc_attr(implode(',', $ages)); ?>"
         onclick="window.location.href='<?php the_permalink(); ?>'">

    <!-- Фото -->
    <div class="relative aspect-[16/9] md:aspect-[5/4] overflow-hidden">
        <?php if ($main_image_url) : ?>
            <img src="<?php echo esc_url($main_image_url); ?>" 
                 alt="<?php the_title_attribute(); ?>"
                 class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
        <?php else : ?>
            <div class="w-full h-full bg-gray-200"></div>
        <?php endif; ?>

        <!-- Теги: направление в строку, возраст в строку -->
        <div class="absolute top-3 left-3 flex flex-col gap-1.5">
            <!-- Теги направлений (горизонтально) -->
            <div class="flex flex-wrap gap-1.5">
                <?php foreach ($direction_names as $dir) : ?>
                    <span class="bg-primary text-white border border-primary text-caption px-4 py-1 rounded-3xl font-medium shadow-custom whitespace-nowrap">
                        <?php echo esc_html($dir); ?>
                    </span>
                <?php endforeach; ?>
            </div>

            <!-- Теги возраста (горизонтально) -->
            <div class="flex flex-wrap gap-1.5">
                <?php foreach ($age_names as $age) : ?>
                    <span class="bg-black text-white text-caption px-4 py-1 rounded-3xl font-medium shadow-custom whitespace-nowrap">
                        <?php echo esc_html($age); ?>
                    </span>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Контент -->
    <div class="p-5 flex flex-col flex-1">
        <?php if ($project_view) : ?>
            <p class="text-small text-black/60 mb-3"><?php echo esc_html($project_view); ?></p>
        <?php endif; ?>

        <h3 class=" text-black leading-tight mb-3 mt-2 line-clamp-2">
            <?php the_title(); ?>
        </h3>

        <p class="text-small text-black/80 line-clamp-3"><?php echo esc_html($short_description); ?></p>
    </div>
</article>