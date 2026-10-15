// Аккордеон для секций «Вопросы и ответы» на странице мероприятия
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-tp-accordion]').forEach(function (accordion) {
        var triggers = accordion.querySelectorAll('.tp-faq-trigger');

        function setOpen(trigger, open) {
            var content = document.getElementById(trigger.getAttribute('aria-controls'));
            var icon = trigger.querySelector('.tp-faq-icon');
            trigger.setAttribute('aria-expanded', open ? 'true' : 'false');
            if (content) content.style.maxHeight = open ? content.scrollHeight + 'px' : '0px';
            if (icon) icon.classList.toggle('is-open', open);
        }

        triggers.forEach(function (trigger) {
            trigger.addEventListener('click', function () {
                var wasOpen = trigger.getAttribute('aria-expanded') === 'true';
                triggers.forEach(function (other) { setOpen(other, false); });
                if (!wasOpen) setOpen(trigger, true);
            });
        });
    });
});

// Слайдер фото/видео: нативная прокрутка со snap, стрелки и точки
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-tp-slider]').forEach(function (slider) {
        var track = slider.querySelector('[data-tp-slider-track]');
        var slides = track.children;
        var prev = slider.querySelector('[data-tp-slider-prev]');
        var next = slider.querySelector('[data-tp-slider-next]');
        var dotsBox = slider.querySelector('[data-tp-slider-dots]');
        if (!prev || !next) return;

        function step() {
            return slides.length > 1 ? slides[1].offsetLeft - slides[0].offsetLeft : track.clientWidth;
        }
        function current() {
            return Math.round(track.scrollLeft / step());
        }
        function perView() {
            return Math.max(1, Math.round(track.clientWidth / step()));
        }
        function pages() {
            return Math.max(1, slides.length - perView() + 1);
        }
        function goTo(i) {
            track.scrollTo({ left: Math.max(0, Math.min(i, pages() - 1)) * step() });
        }

        function update() {
            var i = current();
            prev.disabled = i <= 0;
            next.disabled = i >= pages() - 1;
            if (dotsBox.children.length !== pages()) {
                dotsBox.innerHTML = '';
                for (var d = 0; d < pages(); d++) {
                    var dot = document.createElement('button');
                    dot.type = 'button';
                    dot.className = 'tp-es-slider-dot';
                    dot.setAttribute('aria-label', 'Слайд ' + (d + 1));
                    dot.addEventListener('click', goTo.bind(null, d));
                    dotsBox.appendChild(dot);
                }
            }
            Array.prototype.forEach.call(dotsBox.children, function (dot, d) {
                dot.classList.toggle('is-active', d === i);
            });
        }

        prev.addEventListener('click', function () { goTo(current() - 1); });
        next.addEventListener('click', function () { goTo(current() + 1); });
        track.addEventListener('scroll', function () { window.requestAnimationFrame(update); }, { passive: true });
        window.addEventListener('resize', update);
        update();
    });
});
