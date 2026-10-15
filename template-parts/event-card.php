<?php
$start_raw = get_post_meta(get_the_ID(), '_event_date_start', true);
$end_raw = get_post_meta(get_the_ID(), '_event_date_end', true);
$short_description = get_post_meta(get_the_ID(), '_event_short_description', true) ?: wp_trim_words(get_the_content(), 20);

// Преобразуем даты
$start_date = '';
$end_date = '';
if ($start_raw) {
    $start_date = date('d.m.Y', strtotime($start_raw));
}
if ($end_raw) {
    $end_date = date('d.m.Y', strtotime($end_raw));
}

$date_text = $start_date;
if ($end_date && $end_date !== $start_date) {
    $date_text .= ' — ' . $end_date;
}

// Статус мероприятия (ИСПРАВЛЕННАЯ ЛОГИКА)
$status = '';
$status_text = '';
if ($start_raw) {
    $today = new DateTime('today');
    $start_dt = DateTime::createFromFormat('Y-m-d', $start_raw);
    
    if ($start_dt) {
        // Если есть дата окончания
        if ($end_raw) {
            $end_dt = DateTime::createFromFormat('Y-m-d', $end_raw);
            
            if ($start_dt > $today) {
                // Дата начала в будущем
                $status = 'coming';
                $status_text = 'Скоро';
            } elseif ($start_dt <= $today && $end_dt >= $today) {
                // Дата начала сегодня или раньше, но дата окончания сегодня или позже
                $status = 'ongoing';
                $status_text = 'Уже идёт';
            }
            // Если end_dt < today — мероприятие закончилось, плашку не показываем
        } else {
            // Нет даты окончания (однодневное мероприятие)
            if ($start_dt > $today) {
                // Дата начала в будущем
                $status = 'coming';
                $status_text = 'Скоро';
            } elseif ($start_dt == $today) {
                // Дата начала сегодня
                $status = 'ongoing';
                $status_text = 'Уже идёт';
            }
            // Если start_dt < today — мероприятие прошло, плашку не показываем
        }
    }
}

// Картинка карточки: «Картинка карточки» (featured image), иначе первое фото из галереи
$main_image_url = get_the_post_thumbnail_url(get_the_ID(), 'large') ?: '';
$gallery_ids = get_post_meta(get_the_ID(), '_event_gallery', true);
if (!$main_image_url && !empty($gallery_ids)) {
    $ids = explode(',', $gallery_ids);
    $first_id = $ids[0];
    $main_image_url = wp_get_attachment_url($first_id);
}
?>

<?php
// Для фильтров на странице мероприятий: current / future / past и год начала
$filter_status = $status === 'ongoing' ? 'current' : ($status === 'coming' ? 'future' : 'past');
$filter_year = $start_raw ? substr($start_raw, 0, 4) : '';
?>
<article class="event-card bg-white rounded-[14px] shadow-custom overflow-hidden flex flex-col h-full hover:-translate-y-1 transition-all duration-300 cursor-pointer group"
         data-status="<?php echo esc_attr($filter_status); ?>" data-year="<?php echo esc_attr($filter_year); ?>"
         onclick="window.location.href='<?php the_permalink(); ?>'">

    <div class="relative aspect-[16/9] md:aspect-[5/4] overflow-hidden">
        <?php if ($main_image_url) : ?>
            <img src="<?php echo esc_url($main_image_url); ?>" 
                 alt="<?php the_title_attribute(); ?>"
                 class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
        <?php else : ?>
            <div class="w-full h-full bg-gray-200"></div>
        <?php endif; ?>

        <?php if ($status === 'coming') : ?>
            <div class="absolute bottom-3 left-3 bg-primary text-white text-caption px-3.5 py-1 rounded-3xl font-medium shadow-sm">Скоро</div>
        <?php elseif ($status === 'ongoing') : ?>
            <div class="absolute bottom-3 left-3 bg-black text-white text-caption px-3.5 py-1 rounded-3xl font-medium shadow-sm">Уже идёт</div>
        <?php endif; ?>
    </div>

    <div class="p-5 flex flex-col flex-1">
        <?php if ($date_text) : ?>
            <p class="text-small text-black/60 mb-3"><?php echo esc_html($date_text); ?></p>
        <?php endif; ?>
        <h3 class="text-black leading-tight mb-3 mt-2 break-words line-clamp-2"><?php the_title(); ?></h3>
        <!-- line-clamp не должен растягиваться (flex-1), иначе из-под 3-й строки видна 4-я -->
        <p class="text-small text-black/80 line-clamp-3"><?php echo esc_html($short_description); ?></p>
    </div>
</article>