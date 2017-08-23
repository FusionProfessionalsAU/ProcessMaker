/**
 * @class Corona
 * Handle Shapes corona options
 *
 * @constructor
 * Creates a new instance of the class
 * @param {Object} options
 */
var Corona = function (options) {
    PMUI.draw.Shape.call(this, options);
    /**
     * Defines the positions of the markers
     * @type {Array}
     * @private
     */
    this.positions = ['left+2 top+2', 'center top+5', 'right top+5',
        'left+5 bottom-1', 'center bottom-2', 'right-5 bottom-1'];
    /**
     * Defines the offset of the markers
     * @type {Array}
     * @private
     */
    this.offset = ['5 5', '0 5', '0 5', '5 -1', '0 -1', '-5 -1'];
    /**
     * Define the marker type property
     * @type {null}
     */
    this.columns = [];
    this.items = PMUI.util.ArrayList();
    Corona.prototype.initObject.call(this, options);
};

Corona.prototype = new PMUI.draw.Shape();
/**
 * Defines the object type
 * @type {String}
 */
Corona.prototype.type = 'Corona';

/**
 * Initialize the object with the default values
 * @param {Object} options
 */
Corona.prototype.initObject = function (options) {
    var defaults = {
        parent: null,
        position: 2,
        items: null,
        width: 23,
        height: 66,
        items: [],
        columnsNumber: 3
    };
    $.extend(true, defaults, options);
    //this.setPosition(defaults.position)
    this.setParent(defaults.parent)
        .setHeight(defaults.height)
        .setItems(defaults.items)
        .setColumnsNumber(defaults.columnsNumber);

    if (defaults.items.length > defaults.columnsNumber) {
        this.setWidth(defaults.width * (Math.floor(defaults.items.length / defaults.columnsNumber) + 1));
    } else {
        this.setWidth(defaults.width);
    }

};
Corona.prototype.setWidth = function (newWidth) {
    var intPart;
    if (typeof newWidth === "number" && newWidth >= 0) {
        this.width = newWidth;
        if (this.canvas) {
            this.zoomWidth = this.width;
            intPart = Math.floor(this.zoomWidth);
            this.zoomWidth = (this.zoomWidth % 2 === 0) ? intPart + 1 : intPart;
        } else {
            this.zoomWidth = this.width;
        }
        if (this.html) {
            this.style.addProperties({width: this.zoomWidth});
        }
    }
    return this;
};

Corona.prototype.setHeight = function (newHeight) {
    var intPart;
    if (typeof newHeight === "number" && newHeight >= 0) {
        this.height = newHeight;
        if (this.canvas) {
            this.zoomHeight = this.height;
            intPart = Math.floor(this.zoomHeight);
            this.zoomHeight = (this.zoomHeight % 2 === 0) ? intPart + 1 : intPart;
        } else {
            this.zoomHeight = this.height;
        }
        if (this.html) {
            this.style.addProperties({height: this.zoomHeight});
        }
    }
    return this;
};
Corona.prototype.setColumnsNumber = function (number) {
    this.columnsNumber = number;
    return this;
};
/**
 * Create the HTML for the marker
 * @return {*}
 */
Corona.prototype.createHTML = function () {
    var columnn,
        i;
    PMUI.draw.Shape.prototype.createHTML.call(this);
    this.html.style.zIndex = '121';
    this.html.style.display = 'table';
    this.html.style.paddingLeft = '3px';
    for (i = 0; i < this.columnsNumber; i += 1) {
        columnn = PMUI.createHTMLElement("div");
        columnn.className = 'row';
        columnn.style.display = 'table-row';
        this.html.appendChild(columnn);

        this.columns.push(columnn);
    }

    this.parent.html.appendChild(this.html);
    return this.html;

};
Corona.prototype.paint = function (update) {
    var html,
        i,
        item,
        size = this.items.getSize();
    if (this.getHTML() === null || update) {
        this.createHTML();
    }

    for (i = 0; i < size; i += 1) {
        item = this.items.get(i);

        if (!item.html) {
            item.paint();
            item.attachListeners();
        }

    }
    $(this.html).position({
        of: $(this.parent.html),
        my: "left top",
        at: "right top",
        collision: 'none'
    });
    $(this.html).click(function (e) {
        e.preventDefault();
        e.stopPropagation();
    });
};
Corona.prototype.show = function () {
    var i,
        item,
        size = this.items.getSize();
    this.html.style.visibility = 'visible';
    //for (i = 0; i < size; i += 1) {
    //    item = this.items.get(i);
    //    item.html.style.display = 'table-cell';
    //}
    return this;
};
Corona.prototype.hide = function () {
    var i,
        item,
        size = this.items.getSize();
    this.html.style.visibility = 'hidden';
    //for (i = 0; i < size; i += 1) {
    //    item = this.items.get(i);
    //    item.html.style.display = 'none';
    //}
    return this;
};
Corona.prototype.setItems = function (items) {
    var size = items.length,
        i,
        item;
    for (i = 0; i < size; i += 1) {
        items[i].parent = this;
        items[i].canvas = this.canvas;
        item = new CoronaItem(items[i]);
        this.items.insert(item);
    }
    return this;
};
