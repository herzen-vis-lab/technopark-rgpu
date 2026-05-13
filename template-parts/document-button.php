<?php
// Получаем все документы
$documents = get_posts(array(
    'post_type' => 'document',
    'posts_per_page' => -1,
    'orderby' => 'date',
    'order' => 'ASC',
));

if (empty($documents)) {
    $documents = array(
        (object) array('post_title' => 'Программа повышения квалификации «Современные практики IT-образования»', 'ID' => 0),
        (object) array('post_title' => 'Положение о Технопарке РГПУ им. А.И. Герцена', 'ID' => 0),
    );
}
?>

<section id="documents" class="py-8">
    <div class="max-w-7xl mx-auto">
        <h2 class="text-black text-center mb-12">Документы</h2>

        <div class="space-y-4">
            <?php foreach ($documents as $doc) : 
                $link = get_post_meta($doc->ID, '_document_link', true);
                if (empty($link) && $doc->ID != 0) continue;
            ?>
                <div class="bg-white rounded-[16px] p-4 flex flex-col sm:flex-row sm:items-center gap-4 sm:gap-6 shadow-custom hover:shadow-md transition-shadow">
                    <div class="flex-1">
                        <p class="text-body text-black leading-relaxed">
                            <?php echo esc_html($doc->post_title); ?>
                        </p>
                    </div>
                    <a href="<?php echo esc_url($link ?: '#'); ?>" 
                       target="_blank"
                       class="tp-btn tp-btn--primary whitespace-nowrap text-center sm:text-left">
                        Смотреть документ
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>