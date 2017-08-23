var CoronaItem = function (options) {
    PMUI.draw.Shape.call(this, options);

    CoronaItem.prototype.initObject.call(this, options);
};

CoronaItem.prototype = new PMUI.draw.Shape();
/**
 * Defines the object type
 * @type {String}
 */
CoronaItem.prototype.type = 'CoronaItem';

/**
 * Initialize the object with the default values
 * @param {Object} options
 */
CoronaItem.prototype.initObject = function (options) {
    var defaults = {
        className: '',
        width: 22,
        height: 22,
        parent: null,
        name: '',
        column: 0
    };
    $.extend(true, defaults, options);
    this.setParent(defaults.parent)
        .setHeight(defaults.height)
        .setWidth(defaults.width)
        .setClassName(defaults.className)
        .setName(defaults.name)
        .setColumn(defaults.column);
    this.onClickCallback = defaults.onClick;
    this.onMouseDownCallback = defaults.onMouseDown;
};
CoronaItem.prototype.setColumn = function (column) {
    this.column = column;
    return this;
};
CoronaItem.prototype.setName = function (name) {
    this.name = name;
    return this;
};

CoronaItem.prototype.setClassName = function (name) {
    this.className = name;
    return this;
};
/**
 * Create the HTML for the corona item
 * @return {*}
 */
CoronaItem.prototype.createHTML = function () {

    PMUI.draw.Shape.prototype.createHTML.call(this);
    this.html.className = this.className;
    this.html.style.position = 'relative';
    this.html.title = this.name;
    this.html.style.display = 'table-cell';
    this.parent.columns[this.column].appendChild(this.html);
    //this.parent.html.appendChild(this.html);
    return this.html;
};
CoronaItem.prototype.paint = function (update) {
    if (this.getHTML() === null || update) {
        this.createHTML();
    }
};
CoronaItem.prototype.attachListeners = function () {
    $(this.html).click(this.onClick(this));
    $(this.html).mousedown(this.onMouseDown(this));
    $(this.html).mouseup(this.onMouseUp(this));
    return this;
};
CoronaItem.prototype.onClick = function () {
    var self = this;
    return function (e, ui) {
        e.stopPropagation();
        e.preventDefault();
        if (self.onClickCallback) {
            self.onClickCallback(self);
        }

    };
};
CoronaItem.prototype.onMouseDown = function () {
    var self = this;
    return function (e, ui) {
        if (self.onMouseDownCallback) {
            self.onMouseDownCallback(self);
        }
        e.stopPropagation();
        e.preventDefault();
    };
};
CoronaItem.prototype.onMouseUp = function () {

    return function (e, ui) {
        e.stopPropagation();
        e.preventDefault();
    };
};
CoronaItem.prototype.setCallback = function (fn) {
    this.callback = fn;
    return this;
};