<section id="event-section" class="py-12">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
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