/*global jCore*/
var PMActivityResizeBehavior = function () {
};

PMActivityResizeBehavior.prototype = new PMUI.behavior.RegularResizeBehavior();
PMActivityResizeBehavior.prototype.type = "PMActivityResizeBehavior";

/**
 * Sets a shape's container to a given container
 * @param container
 * @param shape
 */
PMActivityResizeBehavior.prototype.onResizeStart = function (shape) {
    return PMUI.behavior.RegularResizeBehavior
        .prototype.onResizeStart.call(this, shape);
};
/**
 * Removes shape from its current container
 * @param shape
 */
PMActivityResizeBehavior.prototype.onResize = function (shape) {
    return function (e, ui) {
        PMUI.behavior.RegularResizeBehavior
            .prototype.onResize.call(this, shape)(e, ui);
        shape.paint();
        shape.updateBoundaryPositions(false);
    };
};
