<?php
/** Секция «Вопросы и ответы»: аккордеон */
$s = $args['s'];
$items = array_filter($s['items'], function ($item) { return $item['question'] !== ''; });
if (!$items) return;
$uid = 'tp-es-faq-' . (int) $args['index'];
// цвета — переменными, а не прямым background-color: иначе инлайн-стиль перебивает :hover
$style = '--tp-es-faq-bg:' . ($s['item_bg'] ?: '#FFFFFF') . ';--tp-es-faq-color:' . ($s['item_color'] ?: '#000000') . ';';
if (!empty($s['item_hover_bg'])) $style .= '--tp-es-faq-hover-bg:' . $s['item_hover_bg'] . ';';
if (!empty($s['item_hover_color'])) $style .= '--tp-es-faq-hover-color:' . $s['item_hover_color'] . ';';
?>
<?php if ($s['title']) : ?>
    <h2 class="tp-es-title tp-es-title--center"><?php echo esc_html($s['title']); ?></h2>
<?php endif; ?>
<div class="tp-faq-list tp-es-faq" data-tp-accordion style="<?php echo esc_attr($style); ?>">
    <?php foreach (array_values($items) as $i => $item) : ?>
        <div class="tp-faq-item shadow-custom">
            <h3 class="tp-es-faq-heading">
                <button type="button" class="tp-faq-trigger" aria-expanded="false" aria-controls="<?php echo $uid . '-' . $i; ?>">
                    <span><?php echo esc_html($item['question']); ?></span>
                    <svg class="tp-faq-icon" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 15 7-7 7 7"/>
                    </svg>
                </button>
            </h3>
            <div id="<?php echo $uid . '-' . $i; ?>" class="tp-faq-content" style="max-height: 0px">
                <div class="tp-faq-content-inner text-body"><?php echo tp_es_text_html($item['answer']); ?></div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
