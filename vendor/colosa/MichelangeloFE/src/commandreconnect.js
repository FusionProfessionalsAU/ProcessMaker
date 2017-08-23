var PMCommandReconnect = function (rec, opt) {
    var CmdReconnect = function (receiver) {
        PMUI.command.CommandReconnect.call(this, receiver);
        CmdReconnect.prototype.init.call(this, receiver, opt);
    };

    CmdReconnect.prototype = new PMUI.command.CommandReconnect(rec);

    CmdReconnect.prototype.init = function (receiver, opt) {
    };

    CmdReconnect.prototype.execute = function () {
    };

    CmdReconnect.prototype.undo = function () {
    };
    return new CmdReconnect(rec);
};
