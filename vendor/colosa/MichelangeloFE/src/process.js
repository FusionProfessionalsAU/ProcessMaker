var PMProcess = function (options) {
    /**
     * @property {Object}
     * Object to stores the functionalities from {@link PMUI.proxy.RestProxy RestProxy}
     * @private
     */
    this.remoteProxy = null;
    /**
     * @property {Object}
     * Object to stores the functionalities from any local Proxy
     * @private
     */
    this.localProxy = null;
    /**
     *
     * @private
     */
    this.tokens = null;
    /**
     *
     * @private
     */
    this.keys = null;
    /**
     * @property {String}
     * Represents the process identifier
     */
    this.id = null;
    /** Defines the identifier of the diagram
     * @type {String}
     */
    this.diagramId = null;
    /**
     * Represents the diagram name
     * @type {String}
     */
    this.diagramName = null;
    /**
     * Stores if the project has elements without save
     * @type {Boolean}
     */
    this.dirty = false;
    /**
     * Property related to project name
     * @type {String}
     */
    this.name = null;
    /**
     * Stores id the  project is waiting for a response
     * @type {Boolean}
     */
    this.waitingResponse = false;
    /**
     * Stores the interval of time for auto save feature
     * @type {Number}
     */
    this.saveInterval = 20000;
    /**
     * Stores the current canvas
     * @type {Object}
     */
    this.canvas = null;
    /**
     * Represents an object joint related to elements inside of canvas
     * @tyle {Object}
     */
    this.projectId = null;
    /**
     * Represents the project name
     * @type {String}
     */
    this.projectName = null;

    this.loadingProcess = false;
    this.dirtyElements = [
        {
            laneset: {},
            lanes: {},
            activities: {},
            events: {},
            gateways: {},
            flows: {},
            artifacts: {},
            lines: {},
            data: {},
            participants: {}
        },
        {
            laneset: {},
            lanes: {},
            activities: {},
            events: {},
            gateways: {},
            flows: {},
            artifacts: {},
            lines: {},
            data: {},
            participants: {}
        }
    ];
    /**
     * Grid that contains the current selected element's properties
     * @type {Object} This object is a used from the jquery propi plugin
     */
    this.propertiesGrid = null;
    this.identifiers = {};
    this.listeners = {
        create: function () {
        },
        remove: function () {
        },
        update: function () {
        },
        success: function () {
        },
        failure: function () {
        }
    };
    PMProcess.prototype.init.call(this, options);
};
/**
 * Defines the object type
 * @type {String}
 */
PMProcess.prototype.type = 'PMProcess';
/**
 * Defines the object family
 * @type {String}
 */
PMProcess.prototype.family = 'PMProcess';

PMProcess.prototype.init = function (options) {
    var defaults = {
        diagramId: "",
        diagramName: "",
        projectId: "",
        projectName: "",
        description: "",
        canvas: null,
        remoteProxy: null,
        localProxy: null,
        keys: {
            access_token: null,
            expires_in: null,
            token_type: null,
            scope: null,
            refresh_token: null,
            client_id: null,
            client_secret: null
        },
        listeners: {
            create: function () {
            },
            remove: function () {
            },
            update: function () {
            },
            success: function () {
            },
            failure: function () {
            }
        }
    };
    jQuery.extend(true, defaults, options);
    this.setId(defaults.id)
        .setName(defaults.name)
        .setDiagramId(defaults.diagramId)
        .setDiagramName(defaults.diagramName)
        .setProjectId(defaults.projectId)
        .setProjectName(defaults.projectName)
        .setDescription(defaults.description)
        .setCanvas(defaults.canvas)
        .setRemoteProxy(defaults.remoteProxy)
        .setLocalProxy(defaults.localProxy)
        .setKeysClient(defaults.keys)
        .setTokens(defaults.keys)
        .setListeners(defaults.listeners);


    this.remoteProxy = new PMUI.proxy.RestProxy();
};
/**
 * Sets the identifier for the process
 * @param {String} id
 */
PMProcess.prototype.setId = function (id) {
    this.id = id;
    return this;
};
/**
 * Sets the diagram identifier from the project
 * @param {String} id [description]
 */
PMProcess.prototype.setDiagramId = function (id) {
    if (typeof id === "string") {
        this.diagramId = id;
    }
    return this;
};
/**
 * [setDiagramName description]
 * @param {[type]} name [description]
 */
PMProcess.prototype.setDiagramName = function (name) {
    if (typeof name === "string") {
        this.diagramName = name;
    }
    return this;
};
/**
 * [setProjectId description]
 * @param {[type]} id [description]
 */
PMProcess.prototype.setProjectId = function (id) {
    if (typeof id === "string") {
        this.projectId = id;
    }
    return this;
};
/**
 * [setProjectName description]
 * @param {[type]} name [description]
 */
PMProcess.prototype.setProjectName = function (name) {
    if (typeof name === "string") {
        this.projectName = name;
        //jQuery(".navBar div").text(name);
        jQuery(".navBar div").remove();
        if ($(".navBar h2").length > 0) {
            $(".navBar h2").text(name);
        } else {
            jQuery(".navBar").append("<h2>" + name + "</h2>");
        }

    }
    return this;
};
/**
 * Sets the project name
 * @param {String} value
 */
PMProcess.prototype.setName = function (name) {
    var titleProject;
    this.name = name;
    titleProject = jQuery(".mafe-designer-title-name");
    jQuery(titleProject).html = name;
    return this;
};
/**
 * Sets the project description
 * @param  {String} description [description]
 */
PMProcess.prototype.setDescription = function (description) {
    this.description = description;
    return this;
};
PMProcess.prototype.setDirty = function (dirty) {
    if (typeof dirty === "boolean") {
        this.dirty = dirty;
    }
    return this;
};
/**
 * Sets the time interval used to save automatically
 * @param {Number} interval Expressed in miliseconds
 * @return {*}
 */
PMProcess.prototype.setSaveInterval = function (interval) {
    this.saveInterval = interval;
    return this;
};
/**
 *
 * @param {Object} canvas
 */
PMProcess.prototype.setCanvas = function (canvas) {
    this.canvas = canvas;
    return this;
};

PMProcess.prototype.setKeysClient = function (keys) {
    if (typeof keys === "object") {
        this.keys = keys;
    }
    return this;
};
PMProcess.prototype.setListeners = function (listeners) {
    if (typeof listeners === "object") {
        this.listeners = listeners;
    }
    return this;
};
PMProcess.prototype.getKeysClient = function () {
    var keys = this.keys;
    return {
        access_token: keys.access_token,
        expires_in: keys.expires_in,
        token_type: keys.token_type,
        scope: keys.scope,
        refresh_token: keys.refresh_token,
        client_id: keys.client_id,
        client_secret: keys.client_secret
    };
};
/**
 *
 * @param {Object} proxy
 */
PMProcess.prototype.setRemoteProxy = function (proxy) {
    this.remoteProxy = proxy;
    return this;
};
/**
 *
 * @param {Object} proxy
 */
PMProcess.prototype.setLocalProxy = function (proxy) {
    this.localProxy = proxy;
    return this;
};
/**
 * Represents a flag if the project was saved or not
 */
PMProcess.prototype.isDirty = function () {
    return this.dirty;
};
/**
 * Represents if the proxy is waiting any response from the server
 */
PMProcess.prototype.isWaitingResponse = function () {
    return this.waitingResponse;
};

PMProcess.prototype.setWaitingResponse = function (value) {
    this.waitingResponse = value;
    return this;
};

PMProcess.prototype.formatProperty = function (type, property) {
    var prefixes = {
            "PMActivity": "act",
            "PMGateway": "gat",
            "PMEvent": "evn",
            "PMArtifact": "art",
            "PMData": "dat",
            "PMParticipant": "par",
            "PMPool": "swl",
            "PMLane": "lan"
        },
        map = {
            x: "bou_x",
            y: "bou_y",
            width: "bou_width",
            height: "bou_height"
        },
        out;

    if (type === "PMFlow" || type === 'Connection') {
        out = "flo_" + property;
    } else if (map[property]) {
        out = map[property];
    } else {
        out = prefixes[type] + '_' + property;
    }
    return out;
};

PMProcess.prototype.getUpdateList = function (type) {
    var listName = {
            "PMActivity": "activities",
            "PMGateway": "gateways",
            "PMEvent": "events",
            "PMFlow": "flows",
            "PMArtifact": "artifacts",
            "PMLabel": "artifacts",
            "Connection": "flows",
            "PMData": "data",
            "PMParticipant": "participants",
            "PMPool": "laneset",
            "PMLane": "lanes"
        },
        dirtyArray;
    dirtyArray = (this.isWaitingResponse()) ? 1 : 0;
    return this.dirtyElements[dirtyArray][listName[type]];
};

PMProcess.prototype.objectToArray = function (object) {
    var newArray = [];
    jQuery.each(object, function (index, val) {
        newArray.push(val);
    });
    return newArray;
};
PMProcess.prototype.getDataObject = function () {
    var object, i, elements, shapes;
    elements = this.canvas.items.asArray();

    shapes = {
        activities: [],
        gateways: [],
        events: [],
        flows: [],
        artifacts: [],
        laneset: [],
        lanes: [],
        data: [],
        participants: [],
        pools: []
    };
    if (this.canvas.items.getSize() > 0) {
        for (i = 0; i < elements.length; i += 1) {
            if (typeof elements[i].relatedObject.getDataObject === "undefined") {
                object = elements[i].relatedObject;
            } else {
                object = elements[i].relatedObject.getDataObject();
            }
            switch (elements[i].type) {
                case "PMActivity":
                    shapes.activities.push(object);
                    break;
                case "PMGateway":
                    shapes.gateways.push(object);
                    break;
                case "PMEvent":
                    shapes.events.push(object);
                    break;
                case "PMFlow":
                case "Connection":
                    shapes.flows.push(object);
                    break;
                case "PMArtifact":
                    shapes.artifacts.push(object);
                    break;
                case "PMData":
                    shapes.data.push(object);
                    break;
                case "PMParticipant":
                    shapes.participants.push(object);
                    break;
                case "PMPool":
                    shapes.laneset.push(object);
                    break;
                case "PMLane":
                    shapes.lanes.push(object);
                    break;
            }
        }
    }
    return shapes;
};

PMProcess.prototype.getDirtyObject = function () {
    var that = this,
        shape = this.getDataObject();
    return {
        prj_uid: that.id,
        prj_name: that.projectName,
        prj_description: that.description,
        diagrams: [
            {
                dia_uid: that.diagramId || PMUI.generateUniqueId(),
                pro_uid: that.id,
                laneset: shape.laneset,
                lanes: shape.lanes,
                activities: shape.activities,
                events: shape.events,
                gateways: shape.gateways,
                flows: shape.flows,
                artifacts: shape.artifacts,
                data: shape.data,
                participants: shape.participants
            }
        ]
    };
};

PMProcess.prototype.load = function () {
    var keys = this.getKeysClient(),
        that = this;
    this.remoteProxy.setDataType("json");
    this.remoteProxy.setAuthorizationType('oauth2', keys);

    this.remoteProxy.get({
        url: that.remoteProxy.url,
        authorizationOAuth: true,
        success: function (xhr, response) {
            that.dirty = false;
            that.loadProject(response);
        },
        failure: function (xhr, response) {
            that.listeners.failure(that, xhr, response);
        }
    });

    return this;
};

PMProcess.prototype.loadProject = function (project) {
    var that = this,
        i,
        shapes,
        result,
        activities,
        gateways,
        events,
        artifacts,
        connections,
        approved,
        diagram,
        laneset;

    if (typeof project !== 'object') {
        project = JSON.parse(project);
    }
    if (project) {
        this.loadingProcess = true;
        this.setProjectId(project.prj_uid);
        this.setProjectName(project.prj_name);
        this.setDescription(project.prj_description);
        diagram = project.diagrams[0];
        this.setDiagramId(diagram.dia_uid);
        this.setDiagramName(diagram.dia_name);

        jQuery.each(diagram.laneset, function (index, val) {
            laneset = diagram.laneset[index];
            if (that.propertiesReview("laneset", laneset)) {
                that.loadShape('POOL', laneset);
            }
        });

        jQuery.each(diagram.activities, function (index, val) {
            activities = diagram.activities[index];
            if (that.propertiesReview("activities", activities)) {
                that.loadShape(activities.act_type, activities);
            }
        });
        jQuery.each(diagram.events, function (index, val) {
            events = diagram.events[index];
            if (that.propertiesReview("events", events)) {
                that.loadShape(events.evn_type, events);
            }
        });
        jQuery.each(diagram.gateways, function (index, val) {
            gateways = diagram.gateways[index];
            if (that.propertiesReview("gateways", gateways)) {
                that.loadShape(gateways.gat_type, gateways);
            }
        });
        jQuery.each(diagram.artifacts, function (index, val) {
            artifacts = diagram.artifacts[index];
            if (that.propertiesReview("artifacts", artifacts)) {
                that.loadShape(artifacts.art_type, artifacts);
            }
        });
        jQuery.each(diagram.data, function (index, val) {
            data = diagram.data[index];
            if (that.propertiesReview("data", data)) {
                that.loadShape(data.dat_type, data);
            }
        });
        jQuery.each(diagram.participants, function (index, val) {
            participants = diagram.participants[index];
            if (that.propertiesReview("participants", participants)) {
                that.loadShape('PARTICIPANT', participants);
            }
        });
        jQuery.each(diagram.flows, function (index, val) {
            connections = diagram.flows[index];
            if (that.propertiesReview("flows", connections)) {
                that.loadFlow(connections);
            }
        });
        this.loadingProcess = false;
        this.loaded = true;
    } else {
        this.loaded = false;
    }
    this.setDirty(false);
    return this;
};
/**
 * Loads the AdamShape into the diagram
 * @param {Object} shape
 * @param {String} type
 */
PMProcess.prototype.loadShape = function (type, shape) {
    var customShape,
        command,
        transformShape;
    transformShape = this.setShapeValues(type, shape);
    customShape = this.canvas.shapeFactory(type, transformShape);
    if (customShape) {
        this.canvas.addElement(customShape, parseInt(shape.bou_x, 10), parseInt(shape.bou_y, 10), true);
        this.canvas.updatedElement = customShape;
        command = new PMUI.command.CommandCreate(customShape);
        command.execute();
    }
};
/**
 * Loads the connection into the diagram
 * @param {Object} conn
 */
PMProcess.prototype.loadFlow = function (conn) {
    var sourceObj,
        targetObj,
        startPoint,
        endPoint,
        sourcePort,
        targetPort,
        connection,
        positionX,
        positionY,
        segmentMap = {
            'SEQUENCE': 'regular',
            'MESSAGE': 'segmented',
            'DATAASSOCIATION': 'dotted',
            'ASSOCIATION': 'dotted',
            'DEFAULT': 'regular',
            'CONDITIONAL': 'regular'
        },
        srcDecorator = {
            'SEQUENCE': 'mafe-decorator',
            'MESSAGE': 'mafe-message',
            'DATAASSOCIATION': 'mafe-association',
            'ASSOCIATION': 'mafe-decorator',
            'DEFAULT': 'mafe-default',
            'CONDITIONAL': 'mafe-decorator_conditional'
        },
        destDecorator = {
            'SEQUENCE': 'mafe-sequence',
            'MESSAGE': 'mafe-message',
            'DATAASSOCIATION': 'mafe-association',
            'ASSOCIATION': 'mafe-decorator_association',
            'DEFAULT': 'mafe-sequence',
            'CONDITIONAL': 'mafe-sequence'
        },
        positionSourceX,
        positionSourceY,
        positionTargetX,
        positionTargetY;

    sourceObj = this.getElementByUid(conn.flo_element_origin);
    targetObj = this.getElementByUid(conn.flo_element_dest);

    if (typeof sourceObj === "object" && typeof targetObj === "object") {
        startPoint = new PMUI.util.Point(conn.flo_x1, conn.flo_y1);
        endPoint = new PMUI.util.Point(conn.flo_x2, conn.flo_y2);

        sourcePort = new PMUI.draw.Port({
            width: 10,
            height: 10
        });

        targetPort = new PMUI.draw.Port({
            width: 10,
            height: 10
        });

        positionSourceX = startPoint.x - sourceObj.absoluteX + this.canvas.absoluteX;
        positionSourceY = startPoint.y - sourceObj.absoluteY + this.canvas.absoluteY;

        positionTargetX = endPoint.x - targetObj.absoluteX + this.canvas.absoluteX;
        positionTargetY = endPoint.y - targetObj.absoluteY + this.canvas.absoluteY;

        sourceObj.addPort(sourcePort, positionSourceX, positionSourceY);
        targetObj.addPort(targetPort, positionTargetX, positionTargetY, false, sourcePort);

        connection = new PMFlow({
            id: conn.flo_uid,
            srcPort: sourcePort,
            destPort: targetPort,
            canvas: this.canvas,
            segmentStyle: segmentMap[conn.flo_type],
            segmentColor: new PMUI.util.Color(0, 0, 0),
            flo_type: conn.flo_type,
            name: conn.flo_name,
            flo_condition: conn.flo_condition,
            flo_state: conn.flo_state
        });

        connection.setSrcDecorator(new PMUI.draw.ConnectionDecorator({
            decoratorPrefix: srcDecorator[conn.flo_type],
            decoratorType: "source",
            style: {
                cssClasses: []
            },
            width: 11,
            height: 11,
            canvas: this.canvas,
            parent: connection
        }));

        connection.setDestDecorator(new PMUI.draw.ConnectionDecorator({
            decoratorPrefix: destDecorator[conn.flo_type],
            decoratorType: "target",
            style: {
                cssClasses: []
            },
            width: 11,
            height: 11,
            canvas: this.canvas,
            parent: connection
        }));
        connection.setSegmentMoveHandlers();
        //add the connection to the canvas, that means insert its html to
        // the DOM and adding it to the connections array
        this.canvas.addConnection(connection);

        // Filling mafeFlow fields
        connection.setTargetShape(targetPort.parent);
        connection.setOriginShape(sourcePort.parent);
        connection.savePoints();
        // now that the connection was drawn try to create the intersections
        connection.checkAndCreateIntersectionsWithAll();
        //attaching port listeners
        sourcePort.attachListeners(sourcePort);
        targetPort.attachListeners(targetPort);
        this.canvas.triggerCreateEvent(connection, []);
    } else {
        throw new Error("No elements found to connect.".translate());
    }
};
/**
 * Returns the shape into the diagram
 * @param {String} uid
 * @return {Object|undefined}
 */

PMProcess.prototype.getElementByUid = function (uid) {
    var element;
    element = this.canvas.items.find('id', uid);
    if (!element) {
        element = this.canvas.getCustomShapes().find('id', uid);
    }
    return element.relatedObject;
};

PMProcess.prototype.addElement = function (element) {
    var object,
        pk_name,
        list,
        i,
        pasteElement,
        elementUndo,
        shape,
        contDivergent = 0,
        contConvergent = 0;
    if (element.relatedElements.length > 0) {
        for (i = element.relatedElements.length - 1; i >= 0; i -= 1) {
            pasteElement = element.relatedElements[i];
            list = this.getUpdateList(pasteElement.type);
            if (list === undefined) {
                return;
            }

            list[pasteElement.id] = object;
            elementUndo = {
                id: pasteElement.id,
                relatedElements: [],
                relatedObject: pasteElement,
                type: pasteElement.type || pasteElement.extendedType
            };
            this.canvas.items.insert(elementUndo);
            if (!(pasteElement instanceof PMUI.draw.MultipleSelectionContainer)
                && !(pasteElement instanceof PMLine)
                && !(pasteElement instanceof PMLabel)) {
                pasteElement.createBpmn(pasteElement.getBpmnElementType());
            }
        }
    } else {
        switch (element.type) {
            case "Connection":
                pk_name = this.formatProperty(element.type, 'uid');
                list = this.getUpdateList(element.type);
                element.relatedObject[pk_name] = element.id;

                if (typeof element.relatedObject.getDataObject === "undefined") {
                    object = element.relatedObject;
                }
                list[element.id] = object;
                break;
            default:
                pk_name = this.formatProperty(element.type, 'uid');
                list = this.getUpdateList(element.type);
                element.relatedObject[pk_name] = element.id;
                list[element.id] = object;
                break;
        }
        this.canvas.items.insert(element);
        shape = element.relatedObject;
        if (!(shape instanceof PMUI.draw.MultipleSelectionContainer)
            && !(shape instanceof PMLine)
            && !(shape instanceof PMLabel)) {
            shape.createBpmn(shape.getBpmnElementType());
        }
    }
    this.listeners.create(this, element);
};

PMProcess.prototype.updateElement = function (updateElement) {
    var element,
        i,
        shape,
        object,
        list,
        item;
    for (i = 0; i < updateElement.length; i += 1) {
        element = updateElement[i];
        shape = element.relatedObject;

        object = this.formatObject(element);
        list = this.getUpdateList(element.type);

        if (list[element.id]) {
            jQuery.extend(true, list[element.id], object);
            if (element.type === 'Connection') {
                list[element.id].flo_state = object.flo_state;
                item = this.canvas.items.find("id", element.id);
                item.relatedObject.flo_state = object.flo_state;
            }
        } else {
            list[element.id] = object;
        }
        if (shape) {
            if (shape instanceof PMUI.draw.Port) {
                shape.connection.updateBpmn();
            } else {
                if (!(shape instanceof PMUI.draw.MultipleSelectionContainer)
                    && !(shape instanceof PMLine)
                    && !(shape instanceof PMLabel)) {
                    shape.updateBpmn();
                }
            }
        }
    }
    this.dirty = true;
    this.listeners.update(this, updateElement);
};

PMProcess.prototype.removeElement = function (updateElement) {
    var object,
        dirtyEmptyCounter,
        element,
        i,
        pk_name,
        list,
        emptyObject = {},
        currentItem;

    for (i = 0; i < updateElement.length; i += 1) {
        element = updateElement[i];
        currentItem = this.canvas.items.find("id", updateElement[i].id);
        this.canvas.items.remove(currentItem);

        list = this.getUpdateList(element.type);
        if (list) {
            pk_name = this.formatProperty(element.type, 'uid');
            if (list[element.id]) {
                delete list[element.id];
            } else {
                pk_name = this.formatProperty(element.type, 'uid');
                object = {};
                object[pk_name] = element.id;
                list[element.id] = object;
            }
        }
        // to remove BpmnModdle in de exported xml
        if (!(element instanceof PMUI.draw.MultipleSelectionContainer)
            && !(element instanceof PMLine)
            && !(element instanceof PMLabel)) {
            element.removeBpmn();
        }
    }

    if (!this.isWaitingResponse()) {
        dirtyEmptyCounter = true;
        dirtyEmptyCounter = dirtyEmptyCounter && (this.dirtyElements[0].activities === emptyObject);
        dirtyEmptyCounter = dirtyEmptyCounter && (this.dirtyElements[0].gateways === emptyObject);
        dirtyEmptyCounter = dirtyEmptyCounter && (this.dirtyElements[0].events === emptyObject);
        dirtyEmptyCounter = dirtyEmptyCounter && (this.dirtyElements[0].artifacts === emptyObject);
        dirtyEmptyCounter = dirtyEmptyCounter && (this.dirtyElements[0].flows === emptyObject);
        if (dirtyEmptyCounter) {
            this.dirty = false;
        }
    }
    this.dirty = true;
    this.listeners.remove(this, updateElement);
};
/**
 * Gets the changed fields from an object
 * @param {Object} element
 * @return {Object}
 */
PMProcess.prototype.formatObject = function (element) {
    var i,
        field,
        formattedElement = {},
        property;
    formattedElement[this.formatProperty(element.type, 'uid')] = element.id;
    if (element.adam) {
        for (i = 0; i < element.fields.length; i += 1) {
            field = element.fields[i];
            formattedElement[field.field] = field.newVal;
        }
    } else if (element.fields) {
        for (i = 0; i < element.fields.length; i += 1) {
            field = element.fields[i];
            property = this.formatProperty(element.type, field.field);
            if (property === "element_uid") {
                field.newVal = field.newVal.id;
            }
            formattedElement[property] = field.newVal;
        }
    }
    return formattedElement;
};
PMProcess.prototype.setRefreshToken = function () {
    var keys = this.getKeysClient(),
        that = this;
    this.remoteProxy.setDataType("json");
    this.remoteProxy.setAuthorizationType('basic', {client: keys.client_id, secret: keys.client_secret});

    this.remoteProxy.post({
        url: that.remoteProxy.url,
        data: {
            grant_type: "refresh_token",
            refresh_token: keys.refresh_token
        },
        success: function (xhr, response) {
            that.setKeysClient(response);
            that.setTokens(response);
            that.remoteProxy.setAuthorizationType('oauth2', that.tokens);
        },
        failure: function (xhr, response) {
            throw new Error("There are problems getting tokens from the server...".translate());
        }
    });

};

PMProcess.prototype.setTokens = function (response) {
    this.tokens = response;
    return this;
};

PMProcess.prototype.getTokens = function (type) {
    var token;
    if (typeof type === "string") {
        token = this.tokens[type];
    } else if (type === undefined) {
        token = this.tokens;
    }
    return token;
};

PMProcess.prototype.save = function (options) {
    var keys = this.getKeysClient(),
        that = this;
    if (this.isDirty()) {
        this.remoteProxy.setDataType("json");
        this.remoteProxy.setAuthorizationType('oauth2', keys);
        this.remoteProxy.update({
            url: that.remoteProxy.url,
            authorizationOAuth: true,
            data: that.getDirtyObject(),
            success: function (xhr, response) {
                that.listeners.success(that, xhr, response);
            },
            failure: function (xhr, response) {
                that.listeners.failure(that, xhr, response);
            }
        });
    }
    return this;
};
PMProcess.prototype.updateIdentifiers = function (response) {
    var i, shape, that = this, connection, shapeCanvas;
    if (typeof response === "object") {
        for (i = 0; i < response.length; i += 1) {
            shape = that.canvas.items.find("id", response[i].old_uid);
            shapeCanvas = that.canvas.children.find("id", response[i].old_uid);
            connection = that.canvas.connections.find("flo_uid", response[i].old_uid);
            this.identifiers[response[i].old_uid] = response[i].new_uid;
            if (shape) {
                shape.id = response[i].new_uid;
                shape.relatedObject.id = response[i].new_uid;
                switch (shape.type) {
                    case "Connection":
                        shape.relatedObject.flo_uid = response[i].new_uid;
                        break;
                    case "PMActivity":
                        shape.relatedObject.act_uid = response[i].new_uid;
                        break;
                    case "PMEvent":
                        shape.relatedObject.evn_uid = response[i].new_uid;
                        break;
                    case "PMGateway":
                        shape.relatedObject.gat_uid = response[i].new_uid;
                        break;
                    case "PMArtifact":
                        shape.relatedObject.art_uid = response[i].new_uid;
                        break;
                    case "PMData":
                        shape.relatedObject.dat_uid = response[i].new_uid;
                        break;
                    case "PMParticipant":
                        shape.relatedObject.par_uid = response[i].new_uid;
                        break;
                    case "PMPool":
                        shape.relatedObject.lns_uid = response[i].new_uid;
                        shape.relatedObject.participantObject.id = 'el_' + response[i].new_uid;
                        break;
                    case "PMLane":
                        shape.relatedObject.lan_uid = response[i].new_uid;
                        break;
                }
            }
            if (shapeCanvas) {
                shapeCanvas.id = response[i].new_uid;
            }
            if (connection) {
                connection.flo_uid = response[i].new_uid;
                connection.id = response[i].new_uid;
            }
        }
    }
};

PMProcess.prototype.setShapeValues = function (type, options) {
    var newShape;
    switch (type) {
        case "TASK":
        case "SUB_PROCESS":
            options.width = parseInt(options.bou_width, 10);
            options.height = parseInt(options.bou_height, 10);
            options.id = options.act_uid;
            options.labels = [
                {
                    message: options.act_name
                }
            ];
            break;
        case "START":
        case "END":
        case "INTERMEDIATE":
            options.id = options.evn_uid;
            options.labels = [
                {
                    message: options.evn_name
                }
            ];
            break;

        case "TEXT_ANNOTATION":
        case "GROUP":
            options.width = parseInt(options.bou_width, 10);
            options.height = parseInt(options.bou_height, 10);
            options.id = options.art_uid;
            options.labels = [
                {
                    message: options.art_name
                }
            ];
            break;
        case "COMPLEX":
        case "EXCLUSIVE":
        case "PARALLEL":
        case "INCLUSIVE":
            options.id = options.gat_uid;
            options.labels = [
                {
                    message: options.gat_name
                }
            ];
            break;
        case "DATAOBJECT":
        case "DATASTORE":
        case "DATAINPUT":
        case "DATAOUTPUT":
            options.id = options.dat_uid;
            options.labels = [
                {
                    message: options.dat_name
                }
            ];
            break;
        case "PARTICIPANT":
            options.id = options.par_uid;
            options.width = parseInt(options.bou_width, 10);
            options.height = parseInt(options.bou_height, 10);
            options.labels = [
                {
                    message: options.par_name
                }
            ];
            break;
        case "POOL":
            options.id = options.lns_uid;
            options.width = parseInt(options.bou_width, 10);
            options.height = parseInt(options.bou_height, 10);
            options.labels = [
                {
                    message: options.par_name
                }
            ];
            break;
        case "LANE":
            options.id = options.lan_uid;
            options.width = parseInt(options.bou_width, 10);
            options.height = parseInt(options.bou_height, 10);
            options.labels = [
                {
                    message: options.par_name
                }
            ];
            break;
    }
    return options;
};

PMProcess.prototype.propertiesReview = function (type, currenShape) {
    var passed = true, shape, i;
    shape = {
        laneset: [],
        lanes: [],
        activities: [
            "act_uid",
            "act_name",
            "act_type"
        ],
        events: [
            "evn_uid",
            "evn_name",
            "evn_type"
        ],
        gateways: [
            "gat_uid",
            "gat_name",
            "gat_type"
        ],
        flows: [
            "flo_uid",
            "flo_type",
            "flo_element_dest",
            "flo_element_origin",
            "flo_x1",
            "flo_x2",
            "flo_y1",
            "flo_y2"
        ],
        artifacts: [],
        startMessageEvent: [
            "evn_uid",
            "evn_name",
            "evn_type"
        ],
        startTimerEvent: [
            "evn_uid",
            "evn_name",
            "evn_type"
        ]

    };
    if (shape[type]) {
        for (i = 0; i < shape[type].length; i += 1) {
            if (currenShape[shape[type][i]]) {
                if (currenShape[shape[type][i]] === null && currenShape[shape[type][i]] === "") {
                    currenShape[shape[type][i]] = " ";
                }
            }
        }
    }
    return true;
};