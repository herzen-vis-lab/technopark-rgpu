<?php
/**
 * Template Name: Мероприятия
 */

get_header();
?>

<main class="max-w-7xl mx-auto px-6 py-12">
    <h2 class="text-black text-center mb-10">Мероприятия</h2>
    
    <!-- Секция мероприятий -->
    <?php get_template_part('template-parts/event-section'); ?>
</main>

<?php get_footer(); ?>