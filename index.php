<?php
/**
 * Основной шаблон (fallback)
 * Если нет подходящего шаблона, используем этот
 */

// Проверяем, есть ли более специфичный шаблон
if (is_page()) {
    // Если это страница, используем page.php
    if (file_exists(get_template_directory() . '/page.php')) {
        include(get_template_directory() . '/page.php');
        return;
    }
}

get_header();
?>

<main class="max-w-7xl mx-auto px-6 py-12">
    <?php if (have_posts()) : ?>
        <?php while (have_posts()) : the_post(); ?>
            <article>
                <h2 class="mb-8"><?php the_title(); ?></h2>
                <div class="text-body">
                    <?php the_content(); ?>
                </div>
            </article>
        <?php endwhile; ?>
    <?php else : ?>
        <p>Страница не найдена.</p>
    <?php endif; ?>
</main>

<?php get_footer(); ?>