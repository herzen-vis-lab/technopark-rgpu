<?php
/** Секция «Кнопки»: ссылки или якоря по центру */
$buttons = array_filter(array_map('tp_es_button_html', $args['s']['items']));
if (!$buttons) return;
?>
<div class="tp-es-buttons tp-es-buttons--center"><?php echo implode('', $buttons); ?></div>
