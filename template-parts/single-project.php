<?php get_header(); ?>

<div class="max-w-4xl mx-auto px-6 py-12">
    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
        
        <div class="mb-8">
            <?php the_post_thumbnail('large', ['class' => 'w-full rounded-2xl mb-8']); ?>
        </div>

        <h2 class="mb-4"><?php the_title(); ?></h2>

        <?php 
        $view = get_field('project_view');
        if ($view) : ?>
            <p class="text-small text-black/60 mb-6"><?php echo esc_html($view); ?></p>
        <?php endif; ?>

        <div class="prose max-w-none text-body leading-relaxed">
            <?php the_content(); ?>
        </div>

    <?php endwhile; endif; ?>
</div>

<?php get_footer(); ?>