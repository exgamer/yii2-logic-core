/**
 * Фаил для хранения глобальных событий
 */
$(document).ready(function() {
    /**
     * Событие для возможности открытия ссылок путем навешивания класса js-link на элемент
     *
     * делает редирект
     * <div class="js-link" data-href="{{ path(['/review/view'], {'seo_name' : bookmaker.seo_name, 'user_id' : model.user_id}) }}">
     *
     * открывает в новом окне
     * <div class="js-link" data-href="{{ path(['/review/view'], {'seo_name' : bookmaker.seo_name, 'user_id' : model.user_id}) }}" data-target="_blank">
     */
    $(document).on("click",".js-link",function() {
        let self = $(this);
        let href = self.data('href');
        let target = self.data('target');
        if (target === undefined) {
            window.location.replace(href);
        }

        if(target === '_blank') {
            window.open(href);
        }
    });
});
