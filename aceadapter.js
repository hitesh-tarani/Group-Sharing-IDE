 //var bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; },
  var slice = [].slice;

 ACEAdapter = (function(global) {
  ACEAdapter.prototype.ignoreChanges = false;
  ACEAdapter.prototype.userchange = true;
  ACEAdapter.prototype.onload = false;
  ACEAdapter.prototype.grabDocumentState = function() {
    this.lastDocLines = this.aceDoc.getAllLines();
    return this.lastCursorRange = this.aceSession.selection.getRange();
  };
  function bind (obj, method) {
    var fn = obj[method];
    obj[method] = function () {
      fn.apply(obj, arguments);
    };
  }
  function ACEAdapter(aceInstance) {
    /*this.onCursorActivity = bind(this.onCursorActivity, this);
    this.onFocus = bind(this.onFocus, this);
    this.onBlur = bind(this.onBlur, this);
    this.onChange = bind(this.onChange, this);*/
    bind(this, 'onChange');
    bind(this, 'onCursorActivity');
    bind(this, 'onFocus');
    bind(this, 'onBlur');
    var ref;
    this.ace = aceInstance;
    this.aceSession = this.ace.getSession();
    this.aceDoc = this.aceSession.getDocument();
    var that=this;
    this.aceDoc.setNewLineMode('unix');
    this.grabDocumentState();
    
    this.ace.on('change', this.onChange);
    this.ace.on('blur', this.onBlur);
    this.ace.on('focus', this.onFocus);
    
    console.log(Object.getOwnPropertyNames(that));
    //console.log(this.aceDoc.getAllLines());
    this.aceSession.selection.on('changeCursor', this.onCursorActivity);
    if (this.aceRange == null) {
      this.aceRange = ((ref = ace.require) != null ? ref : require)("ace/range").Range;
    }
  }

  

  ACEAdapter.prototype.detach = function() {
    this.ace.removeListener('change', this.onChange);
    this.ace.removeListener('blur', this.onBlur);
    this.ace.removeListener('focus', this.onCursorActivity);
    return this.aceSession.selection.removeListener('changeCursor', this.onCursorActivity);
  };

  ACEAdapter.prototype.onChange = function(change) {
    var pair;
    //console.log(editor2.ignoreChanges);
    //console.log(change);
    if (/*!this.ignoreChanges &&*/!this.onload && this.userchange) {
      //this.ignoreChanges=true;
      //console.log(this.aceDoc);
      this.pair = this.operationFromACEChange(change);
      //this.trigger.apply(this, ['change'].concat(slice.call(this.pair)));
      return this.grabDocumentState();
    }
    this.userchange=true;
    //this.ignoreChanges=false;
    return this.grabDocumentState();
  };
  
  ACEAdapter.prototype.onBlur = function() {
    if (this.ace.selection.isEmpty()) {
      return this.trigger('blur');
    }
  };

  ACEAdapter.prototype.onFocus = function() {
    return this.trigger('focus');
  };

  ACEAdapter.prototype.onCursorActivity = function() {
    return setTimeout((function(_this) {
      return function() {
        return _this.trigger('cursorActivity');
      };
    })(this), 0);
  };

  ACEAdapter.prototype.operationFromACEChange = function(change) {
    var action, delete_op, delta, insert_op, ref, restLength, start, text;
    if (change.data) {
      delta = change.data;
      if ((ref = delta.action) === 'insertLines' || ref === 'removeLines') {
        text = delta.lines.join('\n') + '\n';
        action = delta.action.replace('Lines', '');
      } else {
        text = delta.text.replace(this.aceDoc.getNewLineCharacter(), '\n');
        action = delta.action.replace('Text', '');
      }
      start = this.indexFromPos(delta.range.start);
    } else {
      text = change.lines.join('\n');
      //console.log(text);
      start = this.indexFromPos(change.start);
    }
    restLength = this.lastDocLines.join('\n').length - start;
    if (change.action === 'remove') {
      restLength -= text.length;
    }
    insert_op = new ot.TextOperation().retain(start).insert(text).retain(restLength);
    delete_op = new ot.TextOperation().retain(start)["delete"](text).retain(restLength);
    if (change.action === 'remove') {
      comet.doRequest("E",lang+" "+fname+"."+text_lang_ext+" "+JSON.stringify(delete_op));
      return [delete_op, insert_op];
    } else {
      comet.doRequest("E",lang+" "+fname+"."+text_lang_ext+" "+JSON.stringify(insert_op));
      return [insert_op, delete_op];
    }
  };

  ACEAdapter.prototype.applyOperationToACE = function(operation) {
    this.userchange=false;
    /*if(!this.ignoreChanges)
    {*/
    var from, index, j, len, op, range, ref, to;
    index = 0;
    ref = operation.ops;
    for (j = 0, len = ref.length; j < len; j++) {
      op = ref[j];
      if (ot.TextOperation.isRetain(op)) {
        index += op;
        console.log(op);
      } else if (ot.TextOperation.isInsert(op)) {
        this.aceDoc.insert(this.posFromIndex(index), op);
        console.log(op);
        index += op.length;
      } else if (ot.TextOperation.isDelete(op)) {
        console.log(op);
        from = this.posFromIndex(index-op);
        to = this.posFromIndex(index);
        console.log(to);
        console.log(from);
        range = this.aceRange.fromPoints(to, from);
        console.log(range);
        this.aceDoc.remove(range);
      }
    }
    //}
    //this.ignoreChanges=true;
    return this.grabDocumentState();
  };

  ACEAdapter.prototype.posFromIndex = function(index) {
    var j, len, line, ref, row;
    ref = this.aceDoc.$lines;
    for (row = j = 0, len = ref.length; j < len; row = ++j) {
      line = ref[row];
      if (index <= line.length) {
        break;
      }
      index -= line.length + 1;
    }
    return {
      row: row,
      column: index
    };
  };

  ACEAdapter.prototype.indexFromPos = function(pos, lines) {
    var i, index, j, ref;
    if (lines == null) {
      lines = this.lastDocLines;
    }
    index = 0;
    for (i = j = 0, ref = pos.row; 0 <= ref ? j < ref : j > ref; i = 0 <= ref ? ++j : --j) {
      index += this.lastDocLines[i].length + 1;
    }
    return index += pos.column;
  };

  ACEAdapter.prototype.getValue = function() {
    return this.aceDoc.getValue();
  };

  ACEAdapter.prototype.getCursor = function() {
    var e, e2, end, error, error1, ref, ref1, start;
    try {
      start = this.indexFromPos(this.aceSession.selection.getRange().start, this.aceDoc.$lines);
      end = this.indexFromPos(this.aceSession.selection.getRange().end, this.aceDoc.$lines);
    } catch (error) {
      e = error;
      try {
        start = this.indexFromPos(this.lastCursorRange.start);
        end = this.indexFromPos(this.lastCursorRange.end);
      } catch (error1) {
        e2 = error1;
        console.log("Couldn't figure out the cursor range:", e2, "-- setting it to 0:0.");
        ref = [0, 0], start = ref[0], end = ref[1];
      }
    }
    if (start > end) {
      ref1 = [end, start], start = ref1[0], end = ref1[1];
    }
    return new ace.Cursor(start, end);
  };

  ACEAdapter.prototype.setCursor = function(cursor) {
    var end, ref, start;
    start = this.posFromIndex(cursor.position);
    end = this.posFromIndex(cursor.selectionEnd);
    if (cursor.position > cursor.selectionEnd) {
      ref = [end, start], start = ref[0], end = ref[1];
    }
    return this.aceSession.selection.setSelectionRange(new this.aceRange(start.row, start.column, end.row, end.column));
  };
/*
  ACEAdapter.prototype.setOtherCursor = function(cursor, color, clientId) {
    var clazz, css, cursorRange, end, justCursor, ref, start;
    if (this.otherCursors == null) {
      this.otherCursors = {};
    }
    cursorRange = this.otherCursors[clientId];
    if (cursorRange) {
      cursorRange.start.detach();
      cursorRange.end.detach();
      this.aceSession.removeMarker(cursorRange.id);
    }
    start = this.posFromIndex(cursor.position);
    end = this.posFromIndex(cursor.selectionEnd);
    if (cursor.selectionEnd < cursor.position) {
      ref = [end, start], start = ref[0], end = ref[1];
    }
    clazz = "other-client-selection-" + (color.replace('#', ''));
    justCursor = cursor.position === cursor.selectionEnd;
    if (justCursor) {
      clazz = clazz.replace('selection', 'cursor');
    }
    return css = "." + clazz + " {\n  position: absolute;\n  background-color: " + (justCursor ? 'transparent' : color) + ";\n  border-left: 2px solid " + color + ";\n}";
  };
  */
  ACEAdapter.prototype.registerCallbacks = function(callbacks) {
    this.callbacks = callbacks;
  };

  ACEAdapter.prototype.trigger = function() {
    var args, event, ref, ref1;
    event = arguments[0], args = 2 <= arguments.length ? slice.call(arguments, 1) : [];
    return (ref = this.callbacks) != null ? (ref1 = ref[event]) != null ? ref1.apply(this, args) : void 0 : void 0;
  };

  ACEAdapter.prototype.applyOperation = function(operation) {
    if (!operation.isNoop()) {
      //this.ignoreChanges = true;
    }
    this.applyOperationToACE(operation);
    return this.ignoreChanges = false;
  };

  ACEAdapter.prototype.registerUndo = function(undoFn) {
    return this.ace.undo = undoFn;
  };

  ACEAdapter.prototype.registerRedo = function(redoFn) {
    return this.ace.redo = redoFn;
  };

  ACEAdapter.prototype.invertOperation = function(operation) {
    return operation.invert(this.getValue());
  };

  return ACEAdapter;

})(this);
