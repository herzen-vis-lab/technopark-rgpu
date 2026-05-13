<?php
$query = new WP_Query(array(
    'post_type' => 'faq',
    'posts_per_page' => -1,
    'orderby' => 'date',
    'order' => 'ASC',
));

if ($query->have_posts()):
    $counter = 1;
?>

<div id="accordion-card" class="tp-faq-list" data-accordion="collapse" class="py-4">
    <h2 class="text-black text-center py-2">Часто задаваемые вопросы</h2>
    <?php while ($query->have_posts()): $query->the_post(); ?>
        <div class="tp-faq-item shadow-custom <?php echo $counter > 1 ? 'mt-4' : ''; ?>">
            <h2 id="accordion-card-heading-<?php echo $counter; ?>">
                <button type="button" 
                    class="tp-faq-trigger" 
                    data-accordion-target="#accordion-card-body-<?php echo $counter; ?>" 
                    aria-expanded="false" 
                    aria-controls="accordion-card-body-<?php echo $counter; ?>">
                    <span class="text-h3"><?php the_title(); ?></span>
                    <svg data-accordion-icon class="tp-faq-icon" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 15 7-7 7 7"/>
                    </svg>
                </button>
            </h2>
            <div id="accordion-card-body-<?php echo $counter; ?>" 
                class="tp-faq-content" 
                aria-labelledby="accordion-card-heading-<?php echo $counter; ?>">
                <div class="tp-faq-content-inner">
                    <div class="text-body"><?php the_content(); ?></div>
                </div>
            </div>
        </div>
    <?php 
    $counter++;
    endwhile; 
    ?>
</div>

<?php 
endif; 
wp_reset_postdata(); 
?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const accordion = document.getElementById('accordion-card');
    if (!accordion) return;

    const triggers = accordion.querySelectorAll('.tp-faq-trigger');
    const contents = accordion.querySelectorAll('.tp-faq-content');
    contents.forEach((content) => {
        content.style.maxHeight = '0px';
        content.classList.remove('is-open');
    });

    triggers.forEach((trigger) => {
        trigger.addEventListener('click', function () {
            const targetSelector = trigger.getAttribute('data-accordion-target');
            const content = targetSelector ? document.querySelector(targetSelector) : null;
            if (!content) return;

            const isOpen = trigger.getAttribute('aria-expanded') === 'true';
            const icon = trigger.querySelector('[data-accordion-icon]');

            if (isOpen) {
                trigger.setAttribute('aria-expanded', 'false');
                content.style.maxHeight = '0px';
                content.classList.remove('is-open');
                if (icon) icon.classList.remove('is-open');
                return;
            }

            triggers.forEach((otherTrigger) => {
                const otherSelector = otherTrigger.getAttribute('data-accordion-target');
                const otherContent = otherSelector ? document.querySelector(otherSelector) : null;
                const otherIcon = otherTrigger.querySelector('[data-accordion-icon]');
                otherTrigger.setAttribute('aria-expanded', 'false');
                if (otherContent) {
                    otherContent.style.maxHeight = '0px';
                    otherContent.classList.remove('is-open');
                }
                if (otherIcon) otherIcon.classList.remove('is-open');
            });

            trigger.setAttribute('aria-expanded', 'true');
            content.classList.add('is-open');
            content.style.maxHeight = content.scrollHeight + 'px';
            if (icon) icon.classList.add('is-open');
        });
    });
});
</script>
