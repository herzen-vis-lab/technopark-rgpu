<section id="projects-section" class="py-12">
    <div class="max-w-7xl mx-auto">

        <!-- ФИЛЬТРЫ С ГОРИЗОНТАЛЬНЫМ СКРОЛЛОМ -->
        <div class="mb-8">
            <!-- Направления -->
            <div class="mb-2">
                <div class="flex gap-2 overflow-x-auto pb-4 pt-2 md:flex-wrap md:overflow-visible scrollbar-hide" id="direction-filters">
                    <button class="filter-btn active px-6 py-2.5 rounded-3xl bg-primary text-white border border-primary text-small font-medium shadow-custom transition-all duration-300 hover:-translate-y-0.5 hover:shadow-lg whitespace-nowrap my-1" data-filter="all">
                        Все направления
                    </button>
                    <?php
                    $directions = get_terms(['taxonomy' => 'project_direction', 'hide_empty' => true]);
                    foreach ($directions as $term) : ?>
                        <button class="filter-btn px-6 py-2.5 rounded-3xl bg-white text-black border border-primary text-small font-medium shadow-custom transition-all duration-300 hover:-translate-y-0.5 hover:shadow-lg whitespace-nowrap my-1" 
                                data-filter="<?php echo esc_attr($term->slug); ?>">
                            <?php echo esc_html($term->name); ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Возраст -->
            <div>
                <div class="flex gap-2 overflow-x-auto pb-4 pt-2 md:flex-wrap md:overflow-visible scrollbar-hide" id="age-filters">
                    <button class="age-filter-btn active px-6 py-2.5 rounded-3xl bg-black text-white text-small border border-transparent font-medium shadow-custom transition-all duration-300 hover:-translate-y-0.5 hover:shadow-lg whitespace-nowrap my-1" data-age="all">
                        Все возрасты
                    </button>
                    <?php
                    $ages = get_terms(['taxonomy' => 'project_age', 'hide_empty' => true]);
                    foreach ($ages as $term) : ?>
                        <button class="age-filter-btn px-6 py-2.5 rounded-3xl bg-white text-black text-small border border-transparent font-medium shadow-custom transition-all duration-300 hover:-translate-y-0.5 hover:shadow-lg whitespace-nowrap my-1" 
                                data-age="<?php echo esc_attr($term->slug); ?>">
                            <?php echo esc_html($term->name); ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Сетка карточек -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="projects-grid">
            <?php
            $projects = new WP_Query([
                'post_type'      => 'project',
                'posts_per_page' => -1,
                'orderby'        => 'date',
                'order'          => 'DESC'
            ]);

            if ($projects->have_posts()) :
                while ($projects->have_posts()) : $projects->the_post();
                    get_template_part('template-parts/project-card');
                endwhile;
            endif;
            wp_reset_postdata();
            ?>
        </div>
    </div>
</section>


<script>
document.addEventListener('DOMContentLoaded', function () {
    const cards = document.querySelectorAll('.project-card');
    const filterBtns = document.querySelectorAll('.filter-btn');
    const ageFilterBtns = document.querySelectorAll('.age-filter-btn');

    // Множественный выбор для направлений
    let selectedDirections = new Set(['all']);
    // Множественный выбор для возраста
    let selectedAges = new Set(['all']);

    function updateDirectionStyles() {
        const isAllSelected = selectedDirections.has('all');
        
        filterBtns.forEach(btn => {
            const btnValue = btn.dataset.filter;
            
            if (isAllSelected) {
                if (btnValue === 'all') {
                    btn.classList.remove('bg-white', 'text-black');
                    btn.classList.add('bg-primary', 'text-white');
                } else {
                    btn.classList.remove('bg-primary', 'text-white');
                    btn.classList.add('bg-white', 'text-black');
                }
            } else {
                if (selectedDirections.has(btnValue)) {
                    btn.classList.remove('bg-white', 'text-black');
                    btn.classList.add('bg-primary', 'text-white');
                } else {
                    btn.classList.remove('bg-primary', 'text-white');
                    btn.classList.add('bg-white', 'text-black');
                }
            }
        });
    }

    function updateAgeStyles() {
        const isAllSelected = selectedAges.has('all');
        
        ageFilterBtns.forEach(btn => {
            const btnValue = btn.dataset.age;
            
            if (isAllSelected) {
                if (btnValue === 'all') {
                    btn.classList.remove('bg-white', 'text-black');
                    btn.classList.add('bg-black', 'text-white');
                } else {
                    btn.classList.remove('bg-black', 'text-white');
                    btn.classList.add('bg-white', 'text-black');
                }
            } else {
                if (selectedAges.has(btnValue)) {
                    btn.classList.remove('bg-white', 'text-black');
                    btn.classList.add('bg-black', 'text-white');
                } else {
                    btn.classList.remove('bg-black', 'text-white');
                    btn.classList.add('bg-white', 'text-black');
                }
            }
        });
    }

    function filterProjects() {
        const activeDirs = selectedDirections.has('all') ? null : Array.from(selectedDirections);
        const activeAges = selectedAges.has('all') ? null : Array.from(selectedAges);

        cards.forEach(card => {
            const cardDirs = card.dataset.direction ? card.dataset.direction.split(',') : [];
            const cardAges = card.dataset.age ? card.dataset.age.split(',') : [];

            let dirMatch = false;
            if (!activeDirs) {
                dirMatch = true;
            } else {
                dirMatch = activeDirs.some(dir => cardDirs.includes(dir));
            }

            let ageMatch = false;
            if (!activeAges) {
                ageMatch = true;
            } else {
                ageMatch = activeAges.some(age => cardAges.includes(age));
            }

            card.style.display = (dirMatch && ageMatch) ? 'flex' : 'none';
        });
    }

    // Обработчики для направлений
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const value = this.dataset.filter;
            
            if (value === 'all') {
                selectedDirections.clear();
                selectedDirections.add('all');
            } else {
                if (selectedDirections.has('all')) {
                    selectedDirections.clear();
                    selectedDirections.add(value);
                } else {
                    if (selectedDirections.has(value)) {
                        selectedDirections.delete(value);
                        if (selectedDirections.size === 0) {
                            selectedDirections.add('all');
                        }
                    } else {
                        selectedDirections.add(value);
                    }
                }
            }
            
            updateDirectionStyles();
            filterProjects();
        });
    });

    // Обработчики для возраста
    ageFilterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const value = this.dataset.age;
            
            if (value === 'all') {
                selectedAges.clear();
                selectedAges.add('all');
            } else {
                if (selectedAges.has('all')) {
                    selectedAges.clear();
                    selectedAges.add(value);
                } else {
                    if (selectedAges.has(value)) {
                        selectedAges.delete(value);
                        if (selectedAges.size === 0) {
                            selectedAges.add('all');
                        }
                    } else {
                        selectedAges.add(value);
                    }
                }
            }
            
            updateAgeStyles();
            filterProjects();
        });
    });

    // Инициализация
    updateDirectionStyles();
    updateAgeStyles();
});
</script>