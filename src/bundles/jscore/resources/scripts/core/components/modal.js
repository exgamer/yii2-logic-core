var BaseModal = (function() {

    var Modal = function() {
        this.init();
    };

    Modal.prototype.init = function(e) {
        let self = this;
        $(document).on('click', '.js-modal', function(event) {
            event.preventDefault();
            app.request.get(
                $(this).attr('data-href'),
                {},
                {
                    complete : function(xhr) {
                        let response = app.response.parse(xhr);
                        if(response === null) {
                            return;
                        }

                        if (typeof response.payload  === 'undefined' ) {
                            console.log("No payload");
                            return;
                        }

                        if (typeof response.payload.modalTemplate  !== 'undefined' ) {
                            app.modal.setTemplate(response.payload.modalTemplate);
                        }

                        if (typeof response.payload.html  !== 'undefined') {
                            if (typeof response.payload.update  !== 'undefined') {
                                app.modal.update(response.payload.html);
                            }else {
                                app.modal.show(response.payload.html);
                            }
                        }

                        self.onClickCallback(response);
                    }
                }
            );
        });
    };

    Modal.prototype.onClickCallback = function(response) {};

    return Modal;
})();