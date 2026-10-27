/**
 * Конструктор секций мероприятия в админке.
 * Форма строится по схеме из PHP (tpEventSections.schema, см. inc/event-sections.php),
 * состояние хранится в JSON в скрытом поле #tp-es-input и сохраняется вместе с записью.
 */
jQuery(function ($) {
    var cfg = window.tpEventSections;
    var $root = $('#tp-es-root');
    var $input = $('#tp-es-input');
    if (!cfg || !$root.length) return;

    var schema = cfg.schema;
    var images = $.extend({}, cfg.images);
    var iconsByName = {};
    cfg.icons.forEach(function (icon) { iconsByName[icon.name] = icon; });

    var state;
    try { state = JSON.parse($input.val() || '[]'); } catch (e) { state = []; }
    if (!Array.isArray(state)) state = [];

    var nextUid = 1;
    var openSections = {};
    var editorCounter = 0;
    var pendingEditors = [];   // textarea-заготовки, которые нужно превратить в TinyMCE после вставки в DOM
    var activeEditors = [];
    var openGroups = new WeakMap(); // объект → {ключ группы: раскрыта ли}; переживает перерисовку формы
    state.forEach(function (s) { s._uid = nextUid++; });

    // ---------- данные ----------

    function clone(v) { return JSON.parse(JSON.stringify(v)); }

    function emptyIcon() { return { source: 'none', name: '', image: 0, radius: 'none' }; }

    function defaultsFor(fields) {
        var o = {};
        fields.forEach(function (f) {
            if (f.type === 'group') {
                $.extend(o, defaultsFor(f.fields));
            } else if (f.type === 'repeater') {
                o[f.key] = (f['default'] || []).map(function (item) {
                    return $.extend(defaultsFor(f.fields), clone(item));
                });
            } else if (f.type === 'icon') {
                o[f.key] = emptyIcon();
            } else if (f.type === 'checkbox') {
                o[f.key] = !!f['default'];
            } else if (f['default'] !== undefined) {
                o[f.key] = f['default'];
            } else {
                o[f.key] = (f.type === 'image') ? 0 : '';
            }
        });
        return o;
    }

    function newSection(type) {
        var s = $.extend({ type: type }, defaultsFor(schema.common), defaultsFor(schema.types[type].fields));
        s._uid = nextUid++;
        return s;
    }

    function save() {
        var clean = state.map(function (s) {
            var copy = $.extend({}, s);
            delete copy._uid;
            return copy;
        });
        $input.val(JSON.stringify(clean));
        updateAnchorList();
    }

    function updateAnchorList() {
        var $list = $('#tp-es-anchors').empty();
        state.forEach(function (s) {
            if (s.slug) $list.append($('<option>').attr('value', s.slug).text(schema.types[s.type].label));
        });
    }

    function sectionSummary(s) {
        var parts = [];
        if (s.title) parts.push(s.title);
        else if (s.text) parts.push(s.text);
        if (s.slug) parts.push('#' + s.slug);
        return parts.join(' · ');
    }

    // ---------- поля ----------

    function makeField(f, obj, onChange) {
        var $wrap = $('<div class="tp-es-field">').addClass('tp-es-field--' + f.type);
        if (f.show_if && obj[f.show_if[0]] !== f.show_if[1]) return null;

        var label = f.label;
        var $label = $('<label class="tp-es-label">').text(label);
        var set = function (v) { obj[f.key] = v; save(); if (onChange) onChange(); };
        var $control;

        switch (f.type) {
            case 'text':
            case 'url':
            case 'slug':
            case 'anchor':
                $control = $('<input type="text" class="widefat">').val(obj[f.key] || '');
                if (f.type === 'url') $control.attr('placeholder', 'https://…');
                if (f.type === 'anchor') $control.attr({ list: 'tp-es-anchors', placeholder: 'например registration' });
                if (f.type === 'slug') $control.attr('placeholder', 'например registration');
                $control.on('input', function () { set(this.value); });
                break;

            case 'textarea':
            case 'embed':
                $control = $('<textarea class="widefat">').attr('rows', f.type === 'embed' ? 2 : 4).val(obj[f.key] || '');
                $control.on('input', function () { set(this.value); });
                break;

            case 'richtext':
                var editorId = 'tp-es-editor-' + (++editorCounter);
                $control = $('<textarea class="widefat">').attr({ id: editorId, rows: 14 }).val(obj[f.key] || '');
                $control.on('input', function () { set(this.value); }); // режим «Текст»
                pendingEditors.push({ id: editorId, set: set, height: f.small ? 160 : 320 });
                break;

            case 'number':
                $control = $('<input type="number" class="small-text">').attr({ min: f.min, max: f.max }).val(obj[f.key]);
                $control.on('input', function () { set(this.value === '' ? '' : parseInt(this.value, 10)); });
                break;

            case 'select':
                $control = $('<select>');
                $.each(f.options, function (value, text) {
                    $control.append($('<option>').val(value).text(text));
                });
                // у секций, созданных до появления поля, значения нет — показываем значение по умолчанию
                if (obj[f.key] === undefined || obj[f.key] === null || !(String(obj[f.key]) in f.options)) {
                    obj[f.key] = f['default'] !== undefined ? f['default'] : Object.keys(f.options)[0];
                }
                $control.val(String(obj[f.key])).on('change', function () { set(this.value); render(); });
                break;

            case 'checkbox':
                $control = $('<input type="checkbox">').prop('checked', !!obj[f.key]);
                // перерисовка — чтобы появились/скрылись поля с show_if по этой галочке
                $control.on('change', function () { set(this.checked); render(); });
                return $wrap.append($('<label class="tp-es-check">').append($control, ' ', label));

            case 'color':
                $control = colorControl(obj[f.key] || '', f['default'] || '', set);
                break;

            case 'image':
                $control = imageControl(obj[f.key], function (id) { set(id); });
                break;

            case 'icon':
                if (!obj[f.key] || typeof obj[f.key] !== 'object') obj[f.key] = emptyIcon();
                $control = iconControl(obj[f.key], function () { save(); });
                break;

            case 'group':
                return $wrap.append(groupControl(f, obj, onChange));

            case 'repeater':
                if (!Array.isArray(obj[f.key])) obj[f.key] = [];
                $control = repeaterControl(f, obj[f.key]);
                break;
        }

        $wrap.append($label, $control);
        if (f.help) $wrap.append($('<p class="description">').text(f.help));
        return $wrap;
    }

    function colorControl(value, def, set) {
        var $box = $('<div class="tp-es-color">');
        var $swatch = $('<input type="color">');
        var $text = $('<input type="text" class="tp-es-color-text" maxlength="7">').attr('placeholder', def ? def + ' (по умолчанию)' : 'нет');
        var $clear = $('<button type="button" class="button-link tp-es-color-clear" title="Сбросить">×</button>');

        function show(v) {
            $text.val(v);
            $swatch.val(/^#[0-9a-f]{6}$/i.test(v) ? v : (def || '#ffffff'));
            $box.toggleClass('is-empty', !v);
        }
        show(value);

        $swatch.on('input', function () { show(this.value.toUpperCase()); set(this.value.toUpperCase()); });
        $text.on('input', function () {
            var v = this.value.trim();
            if (v && v[0] !== '#') v = '#' + v;
            if (v === '' || /^#[0-9a-f]{6}$/i.test(v)) {
                $swatch.val(v || def || '#ffffff');
                $box.toggleClass('is-empty', !v);
                set(v.toUpperCase());
            }
        });
        $clear.on('click', function () { show(''); set(''); });
        return $box.append($swatch, $text, $clear);
    }

    function imageControl(id, set) {
        var $box = $('<div class="tp-es-image">');
        var $preview = $('<div class="tp-es-image-preview">');
        var $pick = $('<button type="button" class="button">');
        var $remove = $('<button type="button" class="button-link tp-es-danger">Убрать</button>');

        function show(newId) {
            $preview.empty();
            if (newId && images[newId]) $preview.append($('<img>').attr('src', images[newId]));
            $pick.text(newId ? 'Заменить' : 'Выбрать картинку');
            $remove.toggle(!!newId);
        }
        show(id);

        $pick.on('click', function () {
            var frame = wp.media({ title: 'Выберите картинку', multiple: false, library: { type: 'image' } });
            frame.on('select', function () {
                var a = frame.state().get('selection').first().toJSON();
                images[a.id] = (a.sizes && a.sizes.medium) ? a.sizes.medium.url : a.url;
                show(a.id);
                set(a.id);
            });
            frame.open();
        });
        $remove.on('click', function () { show(0); set(0); });
        return $box.append($preview, $('<div class="tp-es-image-actions">').append($pick, $remove));
    }

    function iconSvg(name) {
        var icon = iconsByName[name];
        if (!icon) return '';
        return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' + icon.svg + '</svg>';
    }

    function iconControl(icon, changed) {
        var $box = $('<div class="tp-es-icon-field">');
        var $source = $('<select>')
            .append('<option value="none">Без иконки</option>')
            .append('<option value="library">Из набора</option>')
            .append('<option value="upload">Своя картинка (SVG/PNG)</option>')
            .val(icon.source);
        $source.on('change', function () { icon.source = this.value; changed(); render(); });
        $box.append($source);

        if (icon.source === 'library') {
            var $current = $('<button type="button" class="button tp-es-icon-current">')
                .html(icon.name ? iconSvg(icon.name) + '<span>' + iconsByName[icon.name].label + '</span>' : 'Выбрать иконку');
            var $picker = $('<div class="tp-es-icon-picker">').hide();
            var $search = $('<input type="search" class="widefat" placeholder="Поиск: книга, звезда, code…">');
            var $grid = $('<div class="tp-es-icon-grid">');

            cfg.icons.forEach(function (item) {
                $('<button type="button" class="tp-es-icon-option">')
                    .attr({ title: item.label + ' (' + item.name + ')', 'data-search': (item.label + ' ' + item.name).toLowerCase() })
                    .toggleClass('is-active', item.name === icon.name)
                    .html(iconSvg(item.name))
                    .on('click', function () { icon.name = item.name; changed(); render(); })
                    .appendTo($grid);
            });
            $search.on('input', function () {
                var q = this.value.toLowerCase().trim();
                $grid.children().each(function () {
                    $(this).toggle(!q || $(this).attr('data-search').indexOf(q) !== -1);
                });
            });
            $current.on('click', function () { $picker.toggle(); if ($picker.is(':visible')) $search.trigger('focus'); });
            $box.append($current, $picker.append($search, $grid));
        }

        if (icon.source === 'upload') {
            $box.append(imageControl(icon.image, function (id) { icon.image = id; changed(); }));
            var $radius = $('<select>')
                .append('<option value="none">Без скругления</option>')
                .append('<option value="rounded">Скруглённые углы</option>')
                .append('<option value="circle">Круг</option>')
                .val(icon.radius || 'none')
                .on('change', function () { icon.radius = this.value; changed(); });
            $box.append($('<label class="tp-es-inline">').append('Скругление: ', $radius));
        }
        return $box;
    }

    // Сворачиваемый блок полей; значения хранятся прямо в obj, как у обычных полей.
    // Раскрыт, если его открыли руками, а по умолчанию — если 'open' в схеме или в нём что-то изменено.
    function groupControl(f, obj, onChange) {
        var opened = openGroups.get(obj) || {};
        openGroups.set(obj, opened);
        var isOpen = f.key in opened ? opened[f.key] : (!!f.open || f.fields.some(function (sub) {
            var def = sub['default'] !== undefined ? sub['default'] : '';
            return obj[sub.key] !== undefined && obj[sub.key] !== def;
        }));
        var $details = $('<details class="tp-es-group">').prop('open', isOpen)
            .on('toggle', function () { opened[f.key] = this.open; });
        $details.append($('<summary>').text(f.label));
        if (f.help) $details.append($('<p class="description">').text(f.help));
        return $details.append(fieldsGrid(f.fields, obj, onChange));
    }

    function moveItem(list, from, to) {
        if (to < 0 || to >= list.length) return;
        list.splice(to, 0, list.splice(from, 1)[0]);
    }

    function repeaterControl(f, items) {
        var $box = $('<div class="tp-es-repeater">');
        items.forEach(function (item, i) {
            var $item = $('<div class="tp-es-item">');
            var $head = $('<div class="tp-es-item-head">').append($('<strong>').text((f.item_label || 'Элемент') + ' ' + (i + 1)));
            var $actions = $('<span class="tp-es-actions">');
            $actions.append(
                $('<button type="button" class="button-link" title="Выше">↑</button>').prop('disabled', i === 0)
                    .on('click', function () { moveItem(items, i, i - 1); save(); render(); }),
                $('<button type="button" class="button-link" title="Ниже">↓</button>').prop('disabled', i === items.length - 1)
                    .on('click', function () { moveItem(items, i, i + 1); save(); render(); }),
                $('<button type="button" class="button-link tp-es-danger" title="Удалить">Удалить</button>')
                    .on('click', function () { items.splice(i, 1); save(); render(); })
            );
            $head.append($actions);
            $item.append($head, fieldsGrid(f.fields, item));
            $box.append($item);
        });
        var $buttons = $('<div class="tp-es-repeater-add">').append(
            $('<button type="button" class="button">').text('+ ' + (f.item_label || 'Добавить'))
                .on('click', function () { items.push(defaultsFor(f.fields)); save(); render(); })
        );
        if (f.bulk) {
            // Сразу несколько картинок из медиатеки → по элементу на каждую
            $buttons.append(
                $('<button type="button" class="button">+ Несколько из медиатеки</button>').on('click', function () {
                    var frame = wp.media({ title: 'Выберите картинки', multiple: 'add', library: { type: 'image' } });
                    frame.on('select', function () {
                        frame.state().get('selection').each(function (a) {
                            a = a.toJSON();
                            images[a.id] = (a.sizes && a.sizes.medium) ? a.sizes.medium.url : a.url;
                            var item = defaultsFor(f.fields);
                            item[f.bulk] = a.id;
                            items.push(item);
                        });
                        save();
                        render();
                    });
                    frame.open();
                })
            );
        }
        return $box.append($buttons);
    }

    function fieldsGrid(fields, obj, onChange) {
        var $grid = $('<div class="tp-es-fields">');
        fields.forEach(function (f) {
            var $f = makeField(f, obj, onChange);
            if ($f) $grid.append($f);
        });
        return $grid;
    }

    // ---------- секции ----------

    function destroyEditors() {
        activeEditors.forEach(function (ed) {
            if (window.tinymce && tinymce.get(ed.id)) tinymce.get(ed.id).save();
            ed.set(wp.editor.getContent(ed.id));
            wp.editor.remove(ed.id);
        });
        activeEditors = [];
    }

    function initEditors() {
        pendingEditors.forEach(function (ed) {
            wp.editor.initialize(ed.id, {
                tinymce: {
                    wpautop: true,
                    height: ed.height,
                    fontsize_formats: '12px 14px 16px 18px 20px 24px 28px 32px 40px 48px',
                    // всё в одной строке: вторую строку WordPress по умолчанию прячет
                    toolbar1: 'formatselect,fontsizeselect,bold,italic,underline,strikethrough,forecolor,backcolor,'
                        + 'alignleft,aligncenter,alignright,alignjustify,bullist,numlist,outdent,indent,blockquote,hr,'
                        + 'superscript,subscript,link,unlink,removeformat,pastetext,charmap,undo,redo,fullscreen',
                    toolbar2: '',
                    setup: function (editor) {
                        editor.on('change keyup NodeChange SetContent', function () {
                            editor.save();
                            ed.set(wp.editor.getContent(ed.id));
                        });
                    }
                },
                quicktags: true,
                mediaButtons: true
            });
            activeEditors.push(ed);
        });
        pendingEditors = [];
    }

    function render() {
        var scroll = window.scrollY;
        destroyEditors();
        $root.empty();

        var $list = $('<div class="tp-es-list">');
        if (!state.length) {
            $list.append('<p class="tp-es-empty">Секций пока нет — страница показывается в старом виде. Добавьте первую секцию ниже.</p>');
        }

        state.forEach(function (s, i) {
            var type = schema.types[s.type];
            if (!type) return;
            var isOpen = !!openSections[s._uid];

            var $section = $('<div class="tp-es-section">').attr('data-uid', s._uid)
                .toggleClass('is-open', isOpen).toggleClass('is-hidden', !!s.hidden);
            var $summary = $('<span class="tp-es-summary">').text(sectionSummary(s));
            var $head = $('<div class="tp-es-section-head">').append(
                $('<span class="tp-es-handle dashicons dashicons-move" title="Перетащите, чтобы поменять порядок"></span>'),
                $('<span class="tp-es-num">').text(i + 1),
                $('<strong class="tp-es-type">').text(type.label),
                $summary,
                s.hidden ? $('<span class="tp-es-badge">скрыта</span>') : null
            );
            var $actions = $('<span class="tp-es-actions">').append(
                $('<button type="button" class="button-link" title="Выше">↑</button>').prop('disabled', i === 0)
                    .on('click', function (e) { e.stopPropagation(); moveItem(state, i, i - 1); save(); render(); }),
                $('<button type="button" class="button-link" title="Ниже">↓</button>').prop('disabled', i === state.length - 1)
                    .on('click', function (e) { e.stopPropagation(); moveItem(state, i, i + 1); save(); render(); }),
                $('<button type="button" class="button-link" title="Дублировать">Копия</button>')
                    .on('click', function (e) {
                        e.stopPropagation();
                        var copy = clone(s);
                        copy._uid = nextUid++;
                        copy.slug = '';
                        state.splice(i + 1, 0, copy);
                        openSections[copy._uid] = true;
                        save(); render();
                    }),
                $('<button type="button" class="button-link tp-es-danger" title="Удалить секцию">Удалить</button>')
                    .on('click', function (e) {
                        e.stopPropagation();
                        if (!confirm('Удалить секцию «' + type.label + '»?')) return;
                        state.splice(i, 1);
                        save(); render();
                    }),
                $('<span class="tp-es-toggle dashicons">').addClass(isOpen ? 'dashicons-arrow-up-alt2' : 'dashicons-arrow-down-alt2')
            );
            $head.append($actions).on('click', function (e) {
                if ($(e.target).closest('button, .tp-es-handle').length) return;
                openSections[s._uid] = !isOpen;
                render();
            });
            $section.append($head);

            if (isOpen) {
                var refresh = function () { $summary.text(sectionSummary(s)); };
                var $body = $('<div class="tp-es-section-body">');
                $body.append(
                    $('<div class="tp-es-common">').append(fieldsGrid(schema.common, s, refresh)),
                    fieldsGrid(type.fields, s, refresh)
                );
                $section.append($body);
            }
            $list.append($section);
        });

        $list.sortable({
            handle: '.tp-es-handle',
            items: '> .tp-es-section',
            axis: 'y',
            update: function () {
                var order = $list.children('.tp-es-section').map(function () { return +$(this).attr('data-uid'); }).get();
                state.sort(function (a, b) { return order.indexOf(a._uid) - order.indexOf(b._uid); });
                save();
                render();
            }
        });

        var $type = $('<select>');
        $.each(schema.types, function (key, t) { $type.append($('<option>').val(key).text(t.label)); });
        var $add = $('<div class="tp-es-add">').append(
            $type,
            $('<button type="button" class="button button-primary">+ Добавить секцию</button>').on('click', function () {
                var s = newSection($type.val());
                state.push(s);
                openSections[s._uid] = true;
                save();
                render();
                $root.find('.tp-es-section').last()[0].scrollIntoView({ block: 'start', behavior: 'smooth' });
            })
        );

        $root.append($list, $add, '<datalist id="tp-es-anchors"></datalist>');
        initEditors();
        updateAnchorList();
        window.scrollTo(0, scroll);
    }

    render();
    save();
});
