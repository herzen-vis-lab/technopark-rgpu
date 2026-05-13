<?php
$settings = get_posts(['post_type' => 'footer_settings', 'posts_per_page' => 1]);
$settings_id = $settings ? $settings[0]->ID : 0;

$docs = get_post_meta($settings_id, '_footer_docs', true) ?: [];
$contacts = get_post_meta($settings_id, '_footer_contacts', true) ?: [];
$socials = get_post_meta($settings_id, '_footer_socials', true) ?: [];
?>

<footer class="text-white" style="--color-dark:#282828;background-color:var(--color-dark);">
    <div class="max-w-7xl mx-auto px-6 py-10">
        <div class="grid gap-8 md:grid-cols-3 md:items-center">
            <h1 class="sr-only">Технопарк РГПУ</h1>
            <!-- Логотип -->
            <div class="flex justify-center md:justify-start">
                <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/assets/images/logo_white.svg'); ?>" alt="Технопарк" class="h-14 w-auto">
            </div>

            <!-- Документы -->
            <nav class="text-center text-body">
                <ul class="list-none space-y-2 pl-0">
                    <?php foreach ($docs as $doc) : ?>
                        <li><a href="<?php echo esc_url($doc['link']); ?>" class="footer-link"><?php echo esc_html($doc['title']); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </nav>

            <!-- Контакты + Соцсети -->
            <div class="text-body text-center md:text-right">
                <?php foreach ($contacts as $contact) : ?>
                    <a href="<?php echo esc_url($contact['link']); ?>" class="footer-link mt-2 inline-block"><?php echo esc_html($contact['text']); ?></a>
                <?php endforeach; ?>

                <div class="mt-4 flex items-center justify-center gap-4 md:justify-end">
                    <?php foreach ($socials as $social) : 
                        $social_name = '';
                        if (strpos($social['link'], 'vk.com') !== false) {
                            $social_name = 'ВКонтакте';
                        } elseif (strpos($social['link'], 'telegram') !== false) {
                            $social_name = 'Telegram';
                        } elseif (strpos($social['link'], 'max') !== false) {
                            $social_name = 'Max';    
                        } elseif (strpos($social['link'], 'youtube.com') !== false) {
                            $social_name = 'YouTube';
                        } elseif (strpos($social['link'], 'ok.ru') !== false) {
                            $social_name = 'Одноклассники';
                        } else {
                            $social_name = $social['title'] ?? 'Социальная сеть';
                        }
                        
                        $svg = $social['icon'];
                        $svg = preg_replace('/width="[^"]+"/', 'width="30"', $svg);
                        $svg = preg_replace('/height="[^"]+"/', 'height="30"', $svg);
                        $svg = str_replace('fill="#000000"', 'fill="currentColor"', $svg);
                        $svg = str_replace('fill="#000"', 'fill="currentColor"', $svg);
                        if (strpos($svg, 'fill=') === false) {
                            $svg = str_replace('<svg', '<svg fill="currentColor"', $svg);
                        }
                    ?>
                        <a href="<?php echo esc_url($social['link']); ?>" 
                        target="_blank" 
                        rel="noopener noreferrer" 
                        aria-label="<?php echo esc_attr($social_name); ?>"
                        class="inline-flex h-9 w-9 items-center justify-center rounded-full transition-colors duration-300 text-white hover:text-primary">
                            <?php echo $svg; ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <div style="clear: both;"></div>
</footer>

<?php wp_footer(); ?>

<!-- Кнопка "Наверх" -->
<button id="scrollToTop" 
        aria-label="Наверх"
        class="fixed bottom-8 right-8 z-50 w-12 h-12 rounded-full bg-primary/80 hover:bg-primary text-white flex items-center justify-center transition-all duration-300 opacity-0 invisible hover:scale-110 focus:outline-none"
        style="outline: none !important; box-shadow: none !important; border: none;"
        onfocus="this.blur()">
    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
    </svg>
</button>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const scrollBtn = document.getElementById('scrollToTop');
    
    if (scrollBtn) {
        // Показываем кнопку при прокрутке
        window.addEventListener('scroll', function() {
            if (window.scrollY > 300) {
                scrollBtn.classList.remove('opacity-0', 'invisible');
                scrollBtn.classList.add('opacity-100', 'visible');
            } else {
                scrollBtn.classList.remove('opacity-100', 'visible');
                scrollBtn.classList.add('opacity-0', 'invisible');
            }
        });
        
        // Плавный скролл наверх
        scrollBtn.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const main = document.querySelector('main');
    const footer = document.querySelector('footer');
    if (main && footer) {
        const minHeight = window.innerHeight - footer.offsetHeight;
        main.style.minHeight = minHeight + 'px';
    }
});
</script>
</body>
</html>