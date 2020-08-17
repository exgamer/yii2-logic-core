var BaseSearch = (function() {

    var Search = function(selector, pStateName = null) {
        BaseForm.apply(this, arguments);
        this.attributes = {};
        this.pStateName = pStateName;
        this.config = {};
    };

    Search.extend(BaseForm);

    Search.prototype.init = function(attributes = {}) {
        var configDefault = {
            attributes : Object.keys(attributes),
            default : attributes,
        };

        this.config = _.merge(configDefault, this.config);

        Search.prototype.$parent.prototype.init.call(this);
        this.setAttributes();
        if(this.pStateName !== null) {
            var url = window.location.href.replace(window.location.origin, ''),
                args = [this, url];

            PushStateHelper.push(this.pStateName, this.handlePopstate, args, true);
        }
    };

    Search.prototype.setAttributes = function() {
        if(this.config.attributes.length === 0) {
            return;
        }

        var self = this,
            attributes = this.config.attributes;

        _.each(attributes, function(attribute) {
            var $element = self.form.find('[name="' + attribute + '"]');
            if($element.length === 1) {
                self.attributes[attribute] = $element;
                if(
                    self.config.default.length > 0
                    && typeof self.config.default[attribute] !== 'undefined'
                ) {
                    self.attributes[attribute].val(self.config.default[attribute]);
                }
            }
        });
    };

    Search.prototype.handlePopstate = function() {
        var url = arguments[1].replace('?', '&'),
            attributes = UrlHelper.getQueryParameters(url),
            self = this;

        if(Object.keys(attributes).length === 0) {
            var defaultValues = this.config.default;

            if(defaultValues.length === 0) {
                return;
            }

            attributes = defaultValues;
        }

        _.each(attributes, function(value, key) {
            self.attributes[key].val(value);
        });

        self.submit();
    };

    return Search;
})();