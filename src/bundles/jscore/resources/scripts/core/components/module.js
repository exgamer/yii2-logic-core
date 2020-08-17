var BaseModule = (function() {

    var Module = function() {
        this.forms = {};
        this.formsConfig = [];
    };

    /*
        {
            'selector' : '.search-form',
            'class' : SearchForm,
            'constructor' : ['search-list'],
            'initArgs' : [
                {
                    'a' : '1',
                    'b': null
                }
            ],
            'alias' : 'searchList'
        }
     */
    Module.prototype.initForms = function() {
        var self = this;
        self.formsConfig.forEach(function(element) {
            var $forms = app.initHelper.findElelements(element.selector);
            if($forms !== null && $forms.length !== 0) {
                _.each($forms, function() {
                    var instance = Object.create(element.class.prototype),
                        args = [];

                    if(Object.keys(element).includes('constructor')) {
                        args = element.constructor;
                    }

                    args = _.concat([element.selector], args);
                    element.class.apply(instance, args);
                    element.class.prototype.init.apply(instance, element.initArgs || []);
                    self.forms[element.alias] = instance;
                });
                app.initHelper.initElements($forms);
            }
        });
    };

    return Module;
})();