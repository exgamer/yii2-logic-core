var InitHelper = function() {
    this.attribute  = 'data-state';
    this.value = 'init';
};

InitHelper.prototype.findElelements = function(selector) {
    var $el = $(selector + '[' + this.attribute + '!="' + this.value + '"]');
    if($el.length === 0) {
        return null;
    }

    return $el;
};

InitHelper.prototype.initElements = function(elements) {
    var self = this;

    if(typeof elements === 'string') {
        var $elements = $(elements);
    } else {
        var $elements = elements;
    }

    $elements.each(function(){
        $(this).attr(self.attribute, self.value);
    });
};

InitHelper.prototype.isInit = function (element) {
    var self = this;

    if(typeof element === 'string') {
        var $element = $(element);
    } else {
        var $element = element;
    }

    return $element.attr(self.attribute) === self.value;
};


/**
 * Для модалок
 */
var ModalHelper = function() {
    this.modalClass = 'js-modal-block';
    /**
     * Шаблон для модалки
     * если задается то внешнему блокунужно поставить класс this.modalClass и {content} для подстановки html
     * @type {string}
     */
    this.template = '<div class="' + this.modalClass + ' bs-modal"><div class="bs-modal-dialog" role="document"><div class="bs-modal-content" role="document">{content}</div></div></div>';
    this.setTemplate = function (tmp) {
        this.template = tmp;
    }
};

/**
 * show modal
 * @param html
 */
ModalHelper.prototype.show = function (html) {
    $('.' + this.modalClass).modal('hide');
    $( '.bs-modal-backdrop' ).remove();
    $('.' + this.modalClass).remove();
    $('body').append(this.template.replace('{content}', html));
    $('.' + this.modalClass).modal();
};

/**
 * update modal
 * @param html
 */
ModalHelper.prototype.update = function (html) {
    $('.' + this.modalClass).html(html);
};

/**
 * Confirm dialog modal
 *
 * @param {String} message
 * @param {function} confirmCallback
 * @param {function} cancelCallback
 */
ModalHelper.prototype.confirm = function (message, confirmCallback, cancelCallback) {
    let elem = $('<div>');
    let body = $('<div>', {'class': 'py-5 px-3 text-center', text: message});
    let confirmBtn = $('<button>', {
        'class': 'confirm-btn button button_main button_md px-md-4 px-3s',
        text: app.t('Confirm')
    });
    let cancelBtn = $('<button>', {
        'class': 'cancel-btn button button_bordered button_md px-md-4 px-3 mr-md-3 mr-2',
        'data-dismiss': 'bs-modal',
        text: app.t('Cancel')
    });
    let footer = $('<div>', {'class': 'bt p-3 text-center'})
        .append(cancelBtn)
        .append(confirmBtn);

    elem.append(body, footer);

    let html = elem[0].outerHTML;
    let tmpTemplate = this.template;
    this.template = '<div class="' + this.modalClass + ' bs-modal">' +
        '<div class="bs-modal-dialog bs-modal-dialog-centered confirm-modal" role="document">' +
        '<div class="bs-modal-content" role="document">{content}</div></div></div>';
    this.show(html);
    this.template = tmpTemplate;

    let $modal = $('.'+this.modalClass);
    confirmBtn = $modal.find('.confirm-btn');
    cancelBtn = $modal.find('.cancel-btn');
    confirmBtn.on('click', function () {
        $(this).attr('disabled', 'disabled');
        cancelBtn.attr('disabled', 'disabled');
        if (typeof confirmCallback === 'function') {
            confirmCallback();
        }
        $modal.modal('hide');
    });

    cancelBtn.on('click', function () {
        if (typeof cancelCallback === 'function') {
            cancelCallback();
        }
        $modal.modal('hide');
    });
};

var RequestHelper = function() {};


RequestHelper.prototype.getCsrfData = function () {
    let csrfParam = $('meta[name="csrf-param"]').attr("content");
    let csrfToken = $('meta[name="csrf-token"]').attr("content");
    let csrf = { };
    csrf[csrfParam] = csrfToken;

    return csrf;
};

RequestHelper.prototype.ajax = function (params) {
    return $.ajax(params);
};

RequestHelper.prototype.get = function (url, data, params) {
    let defaultParams = {
        url : url,
        type : "GET",
        data : data,
    };

    return this.ajax($.extend(defaultParams, params));
};

RequestHelper.prototype.post = function (url, data, params) {
    let defaultParams = {
        url : url,
        type : "POST",
        data :  data,
    };

    return this.ajax($.extend(defaultParams, params));
};

RequestHelper.prototype.put = function (url, data, params) {
    let defaultParams = {
        url : url,
        type : "PUT",
        data : data
    };

    return this.ajax($.extend(defaultParams, params));
};

RequestHelper.prototype.delete = function (url, data, params) {
    let defaultParams = {
        url : url,
        type : "DELETE",
        data : data,
    };

    return this.ajax($.extend(defaultParams, params));
};

var ResponseHelper = function() {
    this.statuses = {
        ok : 200,
        created: 201,
        badRequest : 400,
        unauthorized: 401,
        forbidden : 403,
        notFound : 404,
        methodNotAllowed: 405,
        unprocessableEntity: 422
    };
};

let signModalBusy = false;
ResponseHelper.prototype.parse = function(data, options) {
    var response;
    if(data === undefined) {
        return null;
    }

    if(typeof data === 'string') {
        try {
            response = $.parseJSON(data);
        } catch(e) {
            response = data;
        }
    }

    if(typeof data === 'object') {
        if(
            data.status !== undefined
            && data.status === this.statuses.forbidden
            && data.responseJSON === undefined
        ) {
            $(document).on('shown.bs.bs-modal', '#sign-modal', function () {
                signModalBusy = false;
            });
            if (signModalBusy === false) {
                signModalBusy = true;
                $('[app-auth-control]').click();
            }
            return null;
        }

        if(data.responseJSON !== undefined) {
            response = data.responseJSON;
        } else {
            response = data;
        }
    }

    if (typeof response.location !== 'undefined') {
        if (response.location === 'reload'){
            location.reload();

            return null;
        }

        location.replace(response.location);

        return null;
    }

    if (typeof response.notify !== 'undefined' && app.notification !== undefined && response.notify.delayed !== true) {
        app.notification.pNotify(response.notify.type, response.notify.message);
    }

    return response;
};

var StringHelper = function () {};

StringHelper.getRandomString = function(length) {
    var chars = '1234567890-ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz'.split('');

    if (! length) {
        length = Math.floor(Math.random() * chars.length);
    }

    var result = '';
    for (var i = 0; i < length; i++) {
        result += chars[Math.floor(Math.random() * chars.length)];
    }

    return result;
};

var HtmlHelper = function () {};

//todo arguments
HtmlHelper.updateList = function(html, listSelector, listPagerSelector, $wrapper = null) {
    var $html;

    if(typeof html === 'object') {
        $html = html;
    } else {
        $html = $(html);
    }

    if($wrapper === null) {
        $wrapper = $(document);
    }

    var $list = $html.find(listSelector),
        $pager = $html.find(listPagerSelector);

    if($list.length > 0) {
        $wrapper.find(listSelector).html($list.html());
    }

    if($pager.length > 0) {
        $wrapper.find(listPagerSelector).html($pager.html());
    } else {
        $wrapper.find(listPagerSelector).html('');
    }
};

var PushStateHelper = function () {};

PushStateHelper.back = false;

PushStateHelper.push = function(type, action, args, replace = false) {
    var state = {
            'type' : type
        },
        url = args[1] || null;

    if(! this.back) {
        if(! replace) {
            window.history.pushState(state, null, url);
        } else {
            window.history.replaceState(state, null, url);
        }
    }

    this.popstate(type, action, args);
};

PushStateHelper.popstate = function(type, action, args) {
    var context = args[0] || this,
        self = this;

    $(window).off('popstate');
    $(window).on('popstate', function(e) {
        if(
            e.originalEvent === undefined
            || e.originalEvent.state === null
            || e.originalEvent.state.length === 0
            || e.originalEvent.state.type !== type
        ) {
            return;
        }

        args[1] = e.originalEvent.target.location.href.replace(window.location.origin, '');

        (function() {
            self.back = true;
            action.apply(context, args);
        }());
    });
};

var UrlHelper = function() {};

UrlHelper.addParam = function(queryString, param, value) {
    var queryParameters = this.getQueryParameters(queryString);

    queryParameters[param] = value;

    return $.param(queryParameters);
};

UrlHelper.removeParam = function(queryString, param) {
    var queryParameters = this.getQueryParameters(queryString);

    delete queryParameters[param];

    return $.param(queryParameters);
};

UrlHelper.getQueryParameters = function(queryString) {
    var queryParameters = {},
        re = /([^&=]+)=([^&]*)/g,
        m;

    while (m = re.exec(queryString)) {
        queryParameters[decodeURIComponent(m[1])] = decodeURIComponent(m[2]);
    }

    return queryParameters;
};

var ArrayHelper = function() {};

var CookieHelper = function() {};
/*
Аргументы:

name
название cookie
value
значение cookie (строка)
props
Объект с дополнительными свойствами для установки cookie:
expires
Время истечения cookie. Интерпретируется по-разному, в зависимости от типа:
Если число - количество секунд до истечения.
Если объект типа Date - точная дата истечения.
Если expires в прошлом, то cookie будет удалено.
Если expires отсутствует или равно 0, то cookie будет установлено как сессионное и исчезнет при закрытии браузера.
path
Путь для cookie.
domain
Домен для cookie.
secure
Пересылать cookie только по защищенному соединению.
*/
CookieHelper.set = function(name, value, props) {
    props = props || {}
    let exp = props.expires
    if (typeof exp == "number" && exp) {
        let d = new Date()
        d.setTime(d.getTime() + exp*1000)
        exp = props.expires = d
    }

    if(exp && exp.toUTCString) {
        props.expires = exp.toUTCString()
    }

    value = encodeURIComponent(value)
    let updatedCookie = name + "=" + value
    for(let propName in props){
        updatedCookie += "; " + propName
        let propValue = props[propName]
        if(propValue !== true){ updatedCookie += "=" + propValue }
    }

    document.cookie = updatedCookie
};

CookieHelper.get = function(name) {
    let matches = document.cookie.match(new RegExp(
        "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
    ))
    return matches ? decodeURIComponent(matches[1]) : undefined
};

CookieHelper.delete = function(name) {
    this.set(name, null, { expires: -1 });
};

var UserHelper = function() {};

/**
 * Установка куки с отпечатком анонимного юзера
 * юзается библиотечка fingerprintjs2
 * @TODO если будут проблемы  поднастроить еще уникальнее
 * https://github.com/fingerprintjs/fingerprintjs2
 */
UserHelper.setFingerprint = function() {
    if (CookieHelper.get('rr_user') === undefined) {
        function setRRUser() {
            Fingerprint2.get({},function (components) {
                let values = components.map(function (component) {
                    return component.value
                })
                let murmur = Fingerprint2.x64hash128(values.join(''), 31);
                CookieHelper.set('rr_user', murmur, {'path': '/'});
            })
        }
        if (window.requestIdleCallback) {
            setRRUser();
        } else {
            setTimeout(function () {
                setRRUser();
            }, 500)
        }
    }
};

/**
 * Возвращает fingerprint браузера юзера
 */
UserHelper.getFingerprint = function() {
    if (CookieHelper.get('rr_user') === undefined) {
       return null;
    }

    return CookieHelper.get('rr_user');
};