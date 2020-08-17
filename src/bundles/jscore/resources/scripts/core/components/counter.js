var BaseCounter = (function() {

    var Counter = function(selector) {
        if(typeof selector !== "undefined") {
            this.container = $(selector);
            this.value = this.container.text();
        } else {
            this.value = 0;
        }
    };

    Counter.prototype.set = function(value) {
        this.value = value;
        this.update();
    };

    Counter.prototype.increment = function() {
        this.value ++;
        this.update();
    };

    Counter.prototype.decrement = function() {
        this.value = ( this.value == 1 ? 0 : this.value - 1 );
        this.update();
    };

    Counter.prototype.update = function() {
        if(typeof this.container === "undefined") {
            return false;
        }

        this.container.text(this.value);
    };

    return Counter;
})();