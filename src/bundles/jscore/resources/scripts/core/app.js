Function.prototype.extend = function (parent) {
    var self = this.prototype.constructor;

    self.prototype = Object.create(parent.prototype);
    self.prototype.constructor = parent;
    self.prototype.$parent = parent;
};

var App = function() {
    this.request = new RequestHelper();
    this.modal = new ModalHelper();
    this.response = new ResponseHelper();
    this.initHelper = new InitHelper();
    this.i18n = {
        dictionary : { },
        extend : function (object) {
            this.dictionary = _.extend(this.dictionary, object);
        }
    };
    UserHelper.setFingerprint();
};

App.prototype.t = function (key, params = {}) {
    if(typeof this.i18n.dictionary[key] === 'undefined') {
        return null;
    }

    var message = this.i18n.dictionary[key];
    if(Object.keys(params).length > 0) {
        _.each(params, function(value, index) {
            message = message.replace('{' + index + '}', value)
        })
    }

    return message;
};

var app = new App();