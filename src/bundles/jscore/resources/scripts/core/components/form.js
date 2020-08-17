var BaseForm = (function() {
    var Form = function(selector) {
        this.formSelector = selector;
        this.form = $(this.formSelector);
    };

    Form.prototype.init = function() {
        var self = this;

        this.form.on('submit', function(event) {
            self.submit(self);

            return false;
        });
    };

    Form.prototype.submit = function(context, event) {
        if(context === undefined) {
            self = this;
        } else {
            self = context;
        }

        var $button = self.form.find('[type="submit"]'),
            method = self.form.attr('method').toLowerCase(),
            response = null,
            request = app.request[method] (
            self.form.attr('action'),
            self.form.serialize(),
            {
                dataType: 'json',
                beforeSend: function() {
                    $button.attr('disabled', 'disabled');
                    self.form.find("[error-form-attribute]").html('');
                },
                complete : function(xhr) {
                    response = app.response.parse(xhr);

                    if(response === null) {
                        return;
                    }

                    if (typeof response.validation !== 'undefined') {
                        _.each(response.validation, function(message, attribute) {
                            $error = self.form.find("[error-form-attribute='" + attribute + "']");
                            if($error.length !== 0) {
                                $error.html(message);
                            }
                        });
                    }

                    self.submitCallback(response);
                },
            }
        );

        request.always(function(xhr) {
            response = app.response.parse(xhr);

            $button.removeAttr('disabled');
            self.alwaysCallback(response);
        });
    };

    Form.prototype.submitCallback = function(response) {};
    Form.prototype.alwaysCallback = function(response) {};

    return Form;
})();