var app = new App();

$(document).ready(function () {
    var $document = $(document),
        $flashAlert = $document.find('[data-flash-message]');

    if($flashAlert.length > 0) {
        var data = $flashAlert.data();
        if(
            typeof data.flashMessage !== "undefined"
            && typeof data.flashType !== "undefined"
        ) {
            app.notification.pNotify(data.flashType, data.flashMessage);
        }
    }

    // закрывает уведомления при любом клике в теле
    $(document).on('click', function () {
        var $notify = $(this).find('.ui-pnotify.alert');
        if($notify.length > 0) {
            PNotify.removeAll();
        }
    });
});