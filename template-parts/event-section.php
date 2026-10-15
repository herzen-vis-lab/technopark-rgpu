<?php
// Годы для фильтра — по датам начала мероприятий, новые сверху
global $wpdb;
$event_years = $wpdb->get_col(
    "SELECT DISTINCT LEFT(pm.meta_value, 4) FROM {$wpdb->postmeta} pm
     JOIN {$wpdb->posts} p ON p.ID = pm.post_id
     WHERE p.post_type = 'meropriyatiya' AND p.post_status = 'publish'
       AND pm.meta_key = '_event_date_start' AND pm.meta_value <> ''
     ORDER BY 1 DESC"
);
?>
<section id="event-section" class="py-12">
    <div class="tp-event-filters" data-event-filters role="group" aria-label="Фильтр мероприятий">
        <button type="button" class="tp-btn tp-btn--primary text-body tp-filter-btn" data-filter-status="all" aria-pressed="true">Все</button>
        <button type="button" class="tp-btn tp-btn--outline text-body tp-filter-btn" data-filter-status="current" aria-pressed="false">Текущие</button>
        <button type="button" class="tp-btn tp-btn--outline text-body tp-filter-btn" data-filter-status="future" aria-pressed="false">Будущие</button>
        <button type="button" class="tp-btn tp-btn--outline text-body tp-filter-btn" data-filter-status="past" aria-pressed="false">Прошедшие</button>
        <?php if (count($event_years) > 1) : ?>
            <select class="tp-btn tp-btn--outline text-body tp-filter-year" data-filter-year aria-label="Год">
                <option value="">Все годы</option>
                <?php foreach ($event_years as $year) : ?>
                    <option value="<?php echo esc_attr($year); ?>"><?php echo esc_html($year); ?></option>
                <?php endforeach; ?>
            </select>
        <?php endif; ?>
    </div>
    <p class="tp-event-empty text-body" data-event-empty hidden>Мероприятий не найдено.</p>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" data-event-grid>
        <?php
        $events = new WP_Query([
            'post_type'      => 'meropriyatiya',
            'posts_per_page' => -1,
        ]);

        $events_array = [];
        if ($events->have_posts()) {
            while ($events->have_posts()) {
                $events->the_post();
                $start = get_post_meta(get_the_ID(), '_event_date_start', true);
                $end = get_post_meta(get_the_ID(), '_event_date_end', true);
                $today = date('Y-m-d');
                
                // Определяем статус мероприятия
                $status = 'ended'; // по умолчанию завершённое
                if ($start) {
                    if ($start > $today) {
                        $status = 'coming'; // скоро
                    } elseif ($start <= $today) {
                        if ($end && $end >= $today) {
                            $status = 'ongoing'; // уже идёт
                        } elseif (!$end && $start == $today) {
                            $status = 'ongoing'; // однодневное сегодня
                        } else {
                            $status = 'ended'; // завершённое
                        }
                    }
                }
                
                $events_array[] = [
                    'post_id' => get_the_ID(),
                    'sort_date' => $start,
                    'status' => $status,
                ];
            }
            
            // Сортировка: сначала "Скоро", потом "Уже идёт", потом "Завершённые"
            usort($events_array, function($a, $b) {
                $status_order = [
                    'coming' => 1,
                    'ongoing' => 2,
                    'ended' => 3,
                ];
                
                $order_a = $status_order[$a['status']];
                $order_b = $status_order[$b['status']];
                
                if ($order_a !== $order_b) {
                    return $order_a - $order_b;
                }
                
                // Внутри одинакового статуса сортируем по дате (сначала ближайшие)
                return strtotime($a['sort_date']) - strtotime($b['sort_date']);
            });
            
            foreach ($events_array as $event) {
                global $post;
                $post = get_post($event['post_id']);
                setup_postdata($post);
                get_template_part('template-parts/event-card');
            }
            wp_reset_postdata();
        }
        ?>
    </div>
</section>

<script>
// Фильтр мероприятий: текущие / будущие / прошедшие + год (карточки помечены data-status и data-year)
document.addEventListener('DOMContentLoaded', function () {
    var box = document.querySelector('[data-event-filters]');
    if (!box) return;
    var cards = document.querySelectorAll('[data-event-grid] .event-card');
    var buttons = box.querySelectorAll('[data-filter-status]');
    var yearSelect = box.querySelector('[data-filter-year]');
    var empty = document.querySelector('[data-event-empty]');
    var status = 'all';

    function apply() {
        var year = yearSelect ? yearSelect.value : '';
        var shown = 0;
        cards.forEach(function (card) {
            var ok = (status === 'all' || card.dataset.status === status) && (!year || card.dataset.year === year);
            card.style.display = ok ? '' : 'none';
            if (ok) shown++;
        });
        empty.hidden = shown > 0;
    }

    buttons.forEach(function (button) {
        button.addEventListener('click', function () {
            status = button.dataset.filterStatus;
            // Активная — залитая кнопка сайта, остальные — контурные
            buttons.forEach(function (b) {
                b.classList.toggle('tp-btn--primary', b === button);
                b.classList.toggle('tp-btn--outline', b !== button);
                b.setAttribute('aria-pressed', b === button ? 'true' : 'false');
            });
            apply();
        });
    });
    if (yearSelect) yearSelect.addEventListener('change', apply);
});
</script>
