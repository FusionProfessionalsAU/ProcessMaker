var PMConnectHandler = function (options) {
    PMUI.draw.Handler.call(this, options);
    /**
     * Category of this resize handler
     * @type {"resizable"/"nonresizable"}
     */
    this.category = null;

    /**
     * Denotes whether the resize handle is visible or not.
     * @property boolean
     */
    this.visible = false;

    /**
     * JSON used to create an instance of the class Style used when the object is resizable.
     * @property {Object}
     */
    this.resizableStyle = null;

    /**
     * JSON used to create an instance of the class Style used when the object is not resizable.
     * @property {Object}
     */
    this.nonResizableStyle = null;
    this.relativeShape = null;
    // set defaults
    PMConnectHandler.prototype.init.call(this, options);
};

PMConnectHandler.prototype = new PMUI.draw.Handler();

/**
 * The type of each instance of this class.
 * @property {String}
 */
PMConnectHandler.prototype.type = "PMConnectHandler";

/**
 * Instance initializer which uses options to extend the config options to initialize the instance
 * @param {Object} options The object that contains the config
 * @private
 */
PMConnectHandler.prototype.init = function (options) {
    var defaults = {
        width: 10,
        height: 10,
        parent: null,
        orientation: null,
        representation: null,
        resizableStyle: {},
        nonResizableStyle: {},
        zOrder: 2
    };

    // extend recursively the defaultOptions with the given options
    $.extend(true, defaults, options);

    // add default zIndex to this handler
    if (defaults.resizableStyle.cssProperties) {
        defaults.resizableStyle.cssProperties.zIndex = defaults.zOrder;
    }
    if (defaults.nonResizableStyle.cssProperties) {
        defaults.nonResizableStyle.cssProperties.zIndex = defaults.zOrder;
    }
    this.setParent(defaults.parent)
        .setWidth(defaults.width)
        .setHeight(defaults.height)
        .setOrientation(defaults.orientation)
        .setRepresentation(defaults.representation)
        .setResizableStyle(defaults.resizableStyle)
        .setNonResizableStyle(defaults.nonResizableStyle);
};

/**
 * Sets the parent of this handler
 * @param {PMUI.draw.Shape} newParent
 * @chainable
 */
PMConnectHandler.prototype.setParent = function (newParent) {
    this.parent = newParent;
    return this;
};

/**
 * Gets the parent of this handler.
 * @return {PMUI.draw.Shape}
 */
PMConnectHandler.prototype.getParent = function () {
    return this.parent;
};

/**
 * Paints this resize handler by calling it's parent's `paint` and setting
 * the visibility of this resize handler
 * @chainable
 */
PMConnectHandler.prototype.paint = function () {
    if (!this.html) {
        throw new Error("paint():  This handler has no html");
    }
    // this line paints the representation (by default a rectangle)
    PMUI.draw.Handler.prototype.paint.call(this);
    this.setVisible(this.visible);
    return this;
};

/**
 * Sets the category of the resizeHandler (also adds the needed class to
 * make the element resizable)
 * @param newCategory
 * @chainable
 */
PMConnectHandler.prototype.setCategory = function (newCategory) {
    if (typeof newCategory === "string") {
        this.category = newCategory;
    }
    this.style.addClasses([newCategory]);
    return this;
};
/**
 * Sets the resizable style of this shape by creating an instance of the class Style
 * @param {Object} style
 * @chainable
 */
PMConnectHandler.prototype.setResizableStyle = function (style) {
    this.resizableStyle = new PMUI.util.Style({
        belongsTo: this,
        cssProperties: style.cssProperties,
        cssClasses: style.cssClasses
    });
    return this;
};

/**
 * Sets the non resizable style for this shape by creating an instance of the class Style
 * @param {Object} style
 * @chainable
 */
PMConnectHandler.prototype.setNonResizableStyle = function (style) {
    this.nonResizableStyle = new PMUI.util.Style({
        belongsTo: this,
        cssProperties: style.cssProperties,
        cssClasses: style.cssClasses
    });
    return this;
};

PMConnectHandler.prototype.attachListeners = function () {
    var $handler = $('.dragConnectHandler');
    $handler.mousedown(this.onMouseDown(this));
    if (this.relativeShape) {
        dragOptions = {
            revert: true,
            helper: "clone",
            cursorAt: false,
            revertDuration: 0,
            grid: [1, 1],
            start: this.onDragStart(this.relativeShape),
            drag: this.onDrag(this.relativeShape),
            stop: this.onDragEnd(this.relativeShape),
            refreshPositions: true,
            cursor: "pointer"
        };
        $(this.html).draggable(dragOptions);
    }
    return this;
};
PMConnectHandler.prototype.attachDrop = function () {
    dropOptions = {
        accept: '.dragConnectHandler, .pmui-oval',
        hoverClass: "ui-state-hover",
        drop: this.onDrop(this.relativeShape, this),
        over: this.onDropOver(this.relativeShape, this)
    };
    $('.dropConnectHandler').droppable(dropOptions);
};

PMConnectHandler.prototype.onMouseDown = function (customShape) {
    return function (e, ui) {
        e.preventDefault();
        e.stopPropagation();
    }
};

PMConnectHandler.prototype.onMouseOver = function (customShape) {
    return function (e, ui) {
        e.preventDefault();
        e.stopPropagation();
    }
};
PMConnectHandler.prototype.onMouseOut = function (customShape) {
    return function (e, ui) {
        e.preventDefault();
        e.stopPropagation();
        PMUI.getActiveCanvas.isMouseOverHelper = false;
        if (PMUI.getActiveCanvas.hightLightShape) {
            PMUI.getActiveCanvas.hideDragConnectHandlers();
        }
    }
};

PMConnectHandler.prototype.onDragStart = function (customShape) {
    return function (e, ui) {
        if (!customShape.canvas.currentConnection) {
            customShape.canvas.isDraggingConnectHandler = true;
            var canvas = customShape.canvas,
                currentLabel = canvas.currentLabel,
                realPoint = canvas.relativePoint(e),
                startPortX = e.pageX - customShape.getAbsoluteX(),
                startPortY = e.pageY - customShape.getAbsoluteY();
            // empty the current selection so that the segment created by the
            // helper is always on top
            customShape.canvas.emptyCurrentSelection();

            if (currentLabel) {
                currentLabel.loseFocus();
                $(currentLabel.textField).focusout();
            }
            if (customShape.family !== "CustomShape") {
                return false;
            }
            customShape.setOldX(customShape.getX());
            customShape.setOldY(customShape.getY());
            customShape.startConnectionPoint.x = customShape.canvas.zoomFactor * realPoint.x;
            customShape.startConnectionPoint.y = customShape.canvas.zoomFactor * realPoint.y - canvas.getY();
        } else {
            customShape.canvas.currentConnection.disconnect();
        }
        return true;

    };
};
PMConnectHandler.prototype.onDragEnd = function (customShape) {
    var connect,
        sourceShape,
        destinyShape,
        oldParent;
    return function (e, ui) {
        if (!customShape.canvas.currentConnection) {
            customShape.canvas.isDraggingConnectHandler = false;
            if (customShape.canvas.connectionSegment) {
                //remove the connection segment left
                $(customShape.canvas.connectionSegment.getHTML()).remove();
            }
            customShape.setPosition(customShape.getOldX(), customShape.getOldY());
            customShape.dragging = false;
            customShape.canvas.hideDragConnectHandlers();
            customShape.canvas.hideDropConnectHandlers();
        } else {
            if (customShape.canvas.connectionSegment) {
                //fixed related shapes
                connect = customShape.connection;
                oldParent = customShape.oldParent;
                if(connect){
                    sourceShape = connect.getSrcPort().getParent();
                    destinyShape = connect.getDestPort().getParent();
                    sourceShape.addOutgoingElement(destinyShape);
                    destinyShape.addIncomingElement(sourceShape);
                    if (sourceShape instanceof PMGateway){
                        sourceShape.evaluateGatewayDirection();
                    }
                    if (destinyShape instanceof PMGateway){
                        destinyShape.evaluateGatewayDirection();
                    }
                    if (oldParent instanceof PMGateway){
                        oldParent.evaluateGatewayDirection();
                    }
                    if (PMDesigner.connectValidator){
                        PMDesigner.connectValidator.bpmnValidator();
                    }
                }
                //remove the connection segment left
                $(customShape.canvas.connectionSegment.getHTML()).remove();
                customShape.canvas.currentConnection.connect();
                customShape.canvas.currentConnection.setSegmentMoveHandlers();
                customShape.canvas.currentConnection.showPortsAndHandlers();
            }
        }

    };
};

PMConnectHandler.prototype.onDrag = function (customShape) {
    var sourceShape,
        destinyShape,
        startPoint;
    return function (e, ui) {
        if (customShape.canvas.currentConnection) {
            canvas = customShape.canvas;
            endPoint = new PMUI.util.Point();

            if (canvas.connectionSegment) {
                $(canvas.connectionSegment.getHTML()).remove();
            }

            endPoint.x = e.pageX - canvas.getX() + canvas.getLeftScroll() - canvas.getAbsoluteX();
            endPoint.y = e.pageY - canvas.getY() + canvas.getTopScroll() - canvas.getAbsoluteY();

            //make connection segment
            otherPort = customShape.connection.srcPort.getPoint(false)
                .equals(customShape.getPoint(false)) ? customShape.connection.destPort :
                customShape.connection.srcPort;

            //remove related shapes
            sourceShape = customShape.connection.srcPort.getParent();
            destinyShape = customShape.connection.destPort.getParent();

            if (sourceShape && sourceShape.outgoingElements.find("id", destinyShape.id)){
                sourceShape.outgoingElements.remove(destinyShape);
                destinyShape.incomingElements.remove(sourceShape);
            }

            if (destinyShape && destinyShape.outgoingElements.find("id", sourceShape.id)){
                destinyShape.outgoingElements.remove(sourceShape);
                sourceShape.incomingElements.remove(destinyShape);
            }

            startPoint = otherPort.getPoint(false);
            startPoint.x = startPoint.x - canvas.getAbsoluteX();
            startPoint.y = startPoint.y - canvas.getAbsoluteY();

            canvas.connectionSegment = new PMUI.draw.Segment({
                startPoint: startPoint,
                endPoint: endPoint,
                parent: canvas
            });
            canvas.connectionSegment.createHTML();
            canvas.connectionSegment.paint();
        } else {
            customShape.canvas.isDraggingConnectHandler = true;
            var canvas = customShape.getCanvas(),
                endPoint = new PMUI.util.Point(),
                realPoint = canvas.relativePoint(e);
            if (canvas.connectionSegment) {
                //remove the connection segment in order to create another one
                $(canvas.connectionSegment.getHTML()).remove();
            }
            //Determine the point where the mouse currently is
            endPoint.x = realPoint.x * customShape.canvas.zoomFactor - canvas.getX();
            endPoint.y = realPoint.y * customShape.canvas.zoomFactor - canvas.getY();
            //creates a new segment from where the helper was created to the
            // currently mouse location
            canvas.connectionSegment = new PMUI.draw.Segment({
                startPoint: customShape.startConnectionPoint,
                endPoint: endPoint,
                parent: canvas,
                zOrder: PMUI.util.Style.MAX_ZINDEX * 2
            });
            //We make the connection segment point to helper in order to get
            // information when the drop occurs
            canvas.connectionSegment.pointsTo = customShape;
            //create HTML and paint
            //canvas.connectionSegment.createHTML();
            canvas.connectionSegment.paint();
        }
    };
};

PMConnectHandler.prototype.onClick = function (obj) {
    return function (e, ui) {
        alert('clicked');
    };
};

/**
 * Drag enter hook for this drop behavior, marks that a shape is over a
 * droppable element
 * @param {PMUI.draw.Shape} shape
 * @return {Function}
 */
PMConnectHandler.prototype.onDropOver = function (shape, handler) {
    return function (e, ui) {
    };
};

/**
 * Drag leave hook for this drop behavior, marks that a shape has left a
 * droppable element
 * @param {PMUI.draw.Shape} shape
 * @return {Function}
 */
PMConnectHandler.prototype.onDropOut = function (shape, handler) {
    return function (e, ui) {
        shape.entered = false;
        handler.style.addClasses(['pmConnnectHandler']);
    };
};
/**
 * On drop handler for this drop behavior, creates a connection between the
 * droppable element and the dropped element, or move ports among those shapes
 * @param {PMUI.draw.Shape} shape
 * @return {Function}
 */
PMConnectHandler.prototype.onDrop = function (shape, handler) {
    var that = this;
    return function (e, ui) {
        if (!shape.canvas.currentConnection) {
            var canvas = shape.getCanvas(),
                id = ui.draggable.attr('id'),
                x,
                y,
                currLeft,
                currTop,
                startPoint,
                sourceShape,
                sourcePort,
                endPort,
                endPortXCoord,
                endPortYCoord,
                connection,
                currentConnection = canvas.currentConnection,
                srcPort,
                dstPort,
                port,
                prop,
                success = false,
                command,
                aux,
                segmentMap;
            shape.entered = false;
            //if its the helper then we need to create two ports and draw a
            // connection
            //we get the points and the corresponding shapes involved
            startPoint = shape.canvas.connectionSegment.startPoint;
            sourceShape = shape.canvas.connectionSegment.pointsTo;
            //determine the points where the helper was created
            if (sourceShape.parent && sourceShape.parent.id === shape.id) {
                return true;
            }
            if (!PMDesigner.connectValidator.isValid(sourceShape, shape).result) {
                //show invalid message
                PMDesigner.msgFlash(PMDesigner.connectValidator.isValid(sourceShape, shape).msg, document.body, 'info', 3000, 5);
                return false;
            }
            if (((sourceShape.extendedType === 'START') || (sourceShape.extendedType === 'START' && (sourceShape.evn_marker === 'MESSAGE' || sourceShape.evn_marker === 'TIMER'))) && !PMDesigner.connectValidator.oneToOneValidator(sourceShape).result) {
                //show invalid message
                PMDesigner.msgFlash(PMDesigner.connectValidator.oneToOneValidator(sourceShape, shape).msg, document.body, 'info', 3000, 5);
                return false;
            }
            sourceShape.setPosition(sourceShape.oldX, sourceShape.oldY);
            startPoint.x -= sourceShape.absoluteX - shape.canvas.getAbsoluteX();
            startPoint.y -= sourceShape.absoluteY - shape.canvas.getAbsoluteY();
            //create the ports
            sourcePort = new PMUI.draw.Port({
                width: 10,
                height: 10
            });
            endPort = new PMUI.draw.Port({
                width: 10,
                height: 10
            });

            //determine the position where the helper was dropped
            endPortXCoord = ui.offset.left - shape.canvas.getX() -
                shape.getAbsoluteX() + shape.canvas.getLeftScroll();
            endPortYCoord = ui.offset.top - shape.canvas.getY() -
                shape.getAbsoluteY() + shape.canvas.getTopScroll();
            // add ports to the corresponding shapes
            // addPort() determines the position of the ports
            sourceShape.addPort(sourcePort, startPoint.x, startPoint.y);
            shape.addPort(endPort, endPortXCoord, endPortYCoord,
                false, sourcePort);

            //add ports to the canvas array for regularShapes
            //shape.canvas.regularShapes.insert(sourcePort).insert(endPort);
            //create the connection
            connection = new PMFlow({
                srcPort: sourcePort,
                destPort: endPort,
                segmentColor: new PMUI.util.Color(0, 0, 0),
                name: " ",
                canvas: shape.canvas,
                segmentStyle: shape.connectionType.segmentStyle,
                flo_type: shape.connectionType.type
            });

            connection.setSrcDecorator(new PMUI.draw.ConnectionDecorator({
                width: 11,
                height: 11,
                canvas: canvas,
                decoratorPrefix: (typeof shape.connectionType.srcDecorator !== 'undefined'
                && shape.connectionType.srcDecorator !== null) ?
                    shape.connectionType.srcDecorator : "mafe-decorator",
                decoratorType: "source",
                parent: connection
            }));

            connection.setDestDecorator(new PMUI.draw.ConnectionDecorator({
                width: 11,
                height: 11,
                canvas: canvas,
                decoratorPrefix: (typeof shape.connectionType.destDecorator !== 'undefined'
                && shape.connectionType.destDecorator !== null) ?
                    shape.connectionType.destDecorator : "mafe-decorator",
                decoratorType: "target",
                parent: connection
            }));
            connection.canvas.commandStack.add(new PMUI.command.CommandConnect(connection));
            //connect the two ports
            connection.connect();
            connection.setSegmentMoveHandlers();
            //add the connection to the canvas, that means insert its html to
            // the DOM and adding it to the connections array
            canvas.addConnection(connection);
            // Filling PMFlow fields
            connection.setTargetShape(endPort.parent);
            connection.setOriginShape(sourcePort.parent);
            connection.savePoints();
            // now that the connection was drawn try to create the intersections
            connection.checkAndCreateIntersectionsWithAll();
            //attaching port listeners
            sourcePort.attachListeners(sourcePort);
            endPort.attachListeners(endPort);
            // finally trigger createEvent
            canvas.triggerCreateEvent(connection, []);
        } else {
            var connection = shape.canvas.currentConnection,
                id = ui.draggable.attr('id'),
                targetShape,
                sourceShape,
                originalType,
                canvas = shape.getCanvas();

            if (shape.canvas.dragConnectHandlers.get(0).id === id) {
                port = shape.canvas.dragConnectHandlers.get(0).relativeShape;
                targetShape = shape.canvas.dragConnectHandlers.get(1).relativeShape.parent;
                sourceShape = shape;

            } else if (shape.canvas.dragConnectHandlers.get(1).id === id) {
                port = shape.canvas.dragConnectHandlers.get(1).relativeShape;
                sourceShape = shape.canvas.dragConnectHandlers.get(0).relativeShape.parent;
                targetShape = shape;
            } else {
                port = null;
            }
            originalType = connection.flo_type;
            if (!PMDesigner.connectValidator.isValid(sourceShape, targetShape, true).result) {
                //show invalid message
                PMDesigner.msgFlash(PMDesigner.connectValidator.isValid(sourceShape, targetShape, true).msg, document.body, 'info', 3000, 5);
                return false;
            }
            if (originalType !== 'DEFAULT' && originalType !== targetShape.connectionType.type) {
                PMDesigner.msgFlash('Invalid connection type'.translate(), document.body, 'info', 3000, 5);
                targetShape.connectionType.type = originalType;
                return false;
            }
            if ((sourceShape.extendedType === 'START' && (sourceShape.evn_marker === 'MESSAGE' || sourceShape.evn_marker === 'TIMER')) && !PMDesigner.connectValidator.oneToOneValidator(sourceShape).result) {
                //show invalid message
                PMDesigner.msgFlash(PMDesigner.connectValidator.oneToOneValidator(sourceShape, shape).msg, document.body, 'info', 3000, 5);
                return false;
            }

            port.setOldParent(port.getParent());
            port.setOldX(port.getX());
            port.setOldY(port.getY());

            x = ui.position.left;
            y = ui.position.top;

            endPortXCoord = ui.offset.left - shape.canvas.getX() -
                shape.getAbsoluteX() + shape.canvas.getLeftScroll();
            endPortYCoord = ui.offset.top - shape.canvas.getY() -
                shape.getAbsoluteY() + shape.canvas.getTopScroll();
            port.setPosition(endPortXCoord, endPortYCoord);
            shape.dragging = false;
            if (shape.getID() !== port.parent.getID()) {
                port.parent.removePort(port);
                currLeft = ui.offset.left - canvas.getX() -
                    shape.absoluteX + shape.canvas.getLeftScroll();
                currTop = ui.offset.top - canvas.getY() - shape.absoluteY +
                    shape.canvas.getTopScroll();
                shape.addPort(port, currLeft, currTop, true);
                canvas.regularShapes.insert(port);
            } else {
                shape.definePortPosition(port, port.getPoint(true));
            }
            // LOGIC: when portChangeEvent is triggered it gathers the state
            // of the connection but since at this point there's only a segment
            // let's paint the connection, gather the state and then disconnect
            // it (the connection is later repainted on, I don't know how)
            aux = {
                before: {
                    condition: connection.flo_condition,
                    type: connection.flo_type,
                    segmentStyle: connection.segmentStyle,
                    srcDecorator: connection.srcDecorator.getDecoratorPrefix(),
                    destDecorator: connection.destDecorator.getDecoratorPrefix()
                },
                after: {
                    type: targetShape.connectionType.type,
                    segmentStyle: targetShape.connectionType.segmentStyle,
                    srcDecorator: connection.srcDecorator.getDecoratorPrefix(),
                    destDecorator: connection.destDecorator.getDecoratorPrefix()
                }
            };
            connection.connect();
            canvas.triggerPortChangeEvent(port);
            connection.disconnect();
            command = new PMCommandReconnect(port, aux);
            canvas.commandStack.add(command);
            canvas.hideDropConnectHandlers();
        }
        return false;
    };
};
