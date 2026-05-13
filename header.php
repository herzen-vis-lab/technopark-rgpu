<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<div style="background-color: #282828; height: 32px; width: 100%;">
    <div class="h-full flex items-center justify-center">
        <?php echo do_shortcode('[bvi]'); ?>
    </div>
</div>

<?php
// Получаем настройки шапки
$header_settings = get_posts(array(
    'post_type' => 'header_settings',
    'posts_per_page' => 1,
));
$header_id = !empty($header_settings) ? $header_settings[0]->ID : 0;

$header_logo_id = get_post_meta($header_id, '_header_logo', true);
$header_logo_url = $header_logo_id ? wp_get_attachment_url($header_logo_id) : '';
$header_menu_items = get_post_meta($header_id, '_header_menu_items', true) ?: array(
    array('text' => 'О нас', 'link' => home_url('/about/'), 'target' => '_self'),
    array('text' => 'Мероприятия', 'link' => home_url('/event/'), 'target' => '_self'),
    array('text' => 'Направления', 'link' => home_url('/#directions'), 'target' => '_self'),
    array('text' => 'Коробочные образовательные продукты', 'link' => home_url('/kop/'), 'target' => '_self'),
);

if (empty($header_logo_url)) {
    $header_logo_url = get_stylesheet_directory_uri() . '/assets/images/logo_black.svg';
}
?>

<header class="text-black sticky top-0 z-50 shadow-custom" style="background-color:white">
    <div class="max-w-7xl mx-auto px-6 py-5">
        <div class="flex items-center justify-between">

            <a href="<?php echo home_url('/'); ?>" class="flex items-center gap-3 flex-shrink-0">
                <img src="<?php echo esc_url($header_logo_url); ?>" 
                     alt="Технопарк РГПУ им. А.И. Герцена" 
                     class="h-12 w-auto">
            </a>

            <!-- Десктоп меню -->
            <nav class="hidden lg:flex items-center gap-x-9 text-body font-medium">
                <?php foreach ($header_menu_items as $item) : ?>
                    <?php if (!empty($item['children']) && empty($item['link'])) : ?>
                        <div class="relative group">
                            <button class="nav-link flex items-center gap-1 bg-transparent border-0 cursor-pointer p-0 text-body">
                                <?php echo esc_html($item['text']); ?>
                            </button>
                            <div class="absolute left-0 mt-2 w-56 bg-white rounded-md shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 border border-gray-100">
                                <?php foreach ($item['children'] as $child) : ?>
                                    <a href="<?php echo esc_url($child['link']); ?>" 
                                       class="block px-4 py-2 text-body text-black hover:bg-primary hover:text-white mb-1"  style="text-decoration: none;"
                                       target="<?php echo esc_attr($child['target']); ?>">
                                        <?php echo esc_html($child['text']); ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php else : ?>
                        <a href="<?php echo esc_url($item['link']); ?>" 
                           class="nav-link"
                           target="<?php echo esc_attr($item['target']); ?>">
                            <?php echo esc_html($item['text']); ?>
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </nav>

            <!-- Бургер меню -->
            <button id="burger-menu-btn" type="button" class="inline-flex items-center p-2 w-10 h-10 justify-center rounded-base lg:hidden" style="outline: none !important; box-shadow: none !important; background: transparent; border: none;" onfocus="this.blur()">
                <span class="sr-only">Open main menu</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="outline: none;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Мобильное меню -->
    <div id="mobile-menu" class="hidden lg:hidden bg-neutral-100 border-t border-gray-200">
        <nav class="flex flex-col px-6 py-4 gap-y-4 text-body font-medium">
            <?php foreach ($header_menu_items as $item) : ?>
                <?php if (!empty($item['children']) && empty($item['link'])) : ?>
                    <div>
                        <button type="button" 
                            class="mobile-faq-trigger w-full text-left nav-link py-2 px-3 rounded flex items-center justify-between bg-transparent border-0 cursor-pointer"
                            aria-expanded="false">
                            <span class="text-body"><?php echo esc_html($item['text']); ?></span>
                            <svg class="mobile-faq-icon w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 15 7-7 7 7"/>
                            </svg>
                        </button>
                        <div class="mobile-faq-content hidden pl-4 mt-1 space-y-1">
                            <?php foreach ($item['children'] as $child) : ?>
                                <a href="<?php echo esc_url($child['link']); ?>" 
                                   class="block py-2 px-3 text-body text-black hover:bg-neutral-200 rounded"
                                    style="text-decoration: none;"
                                   target="<?php echo esc_attr($child['target']); ?>">
                                    <?php echo esc_html($child['text']); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php else : ?>
                    <a href="<?php echo esc_url($item['link']); ?>" 
                       class="nav-link py-2 hover:bg-neutral-200 px-3 rounded"
                       target="<?php echo esc_attr($item['target']); ?>">
                        <?php echo esc_html($item['text']); ?>
                    </a>
                <?php endif; ?>
            <?php endforeach; ?>
        </nav>
    </div>
</header>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Бургер меню
        const burgerBtn = document.getElementById('burger-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        const mobileLinks = document.querySelectorAll('#mobile-menu a');

        if (burgerBtn && mobileMenu) {
            burgerBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                mobileMenu.classList.toggle('hidden');
            });

            mobileLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    mobileMenu.classList.add('hidden');
                    const href = this.getAttribute('href');
                    if (href && href.includes('#') && !href.includes('http')) {
                        e.preventDefault();
                        const [url, hash] = href.split('#');
                        const targetHash = '#' + hash;
                        if (url === '' || url === homeUrl) {
                            const targetElement = document.querySelector(targetHash);
                            if (targetElement) {
                                targetElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
                            }
                        } else {
                            window.location.href = href;
                        }
                    }
                });
            });
        }
        
        document.addEventListener('click', function(e) {
            if (mobileMenu && !mobileMenu.classList.contains('hidden')) {
                const isClickInsideMenu = mobileMenu.contains(e.target);
                const isClickOnBurger = burgerBtn.contains(e.target);
                if (!isClickInsideMenu && !isClickOnBurger) {
                    mobileMenu.classList.add('hidden');
                }
            }
        });
        
        if (window.location.hash) {
            const targetElement = document.querySelector(window.location.hash);
            if (targetElement) {
                setTimeout(() => {
                    targetElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }, 500);
            }
        }
        
        // АККОРДЕОН ДЛЯ МОБИЛЬНОГО МЕНЮ
        const mobileTriggers = document.querySelectorAll('.mobile-faq-trigger');

        mobileTriggers.forEach(trigger => {
            const icon = trigger.querySelector('.mobile-faq-icon');
            if (icon) {
                icon.style.transform = 'rotate(180deg)';
            }
            
            trigger.addEventListener('click', function(e) {
                e.preventDefault();
                const content = this.nextElementSibling;
                const isOpen = this.getAttribute('aria-expanded') === 'true';
                const icon = this.querySelector('.mobile-faq-icon');
                
                // Закрываем все остальные
                mobileTriggers.forEach(otherTrigger => {
                    if (otherTrigger !== this) {
                        const otherContent = otherTrigger.nextElementSibling;
                        const otherIcon = otherTrigger.querySelector('.mobile-faq-icon');
                        otherTrigger.setAttribute('aria-expanded', 'false');
                        if (otherContent) otherContent.classList.add('hidden');
                        if (otherIcon) {
                            otherIcon.style.transform = 'rotate(180deg)';
                        }
                    }
                });
                
                if (isOpen) {
                    this.setAttribute('aria-expanded', 'false');
                    content.classList.add('hidden');
                    if (icon) {
                        icon.style.transform = 'rotate(180deg)';
                    }
                } else {
                    this.setAttribute('aria-expanded', 'true');
                    content.classList.remove('hidden');
                    if (icon) {
                        icon.style.transform = 'rotate(0deg)';
                    }
                }
            });
        });
    });
</script>