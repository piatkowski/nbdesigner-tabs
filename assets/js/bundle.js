"use strict";
(() => {
  var __defProp = Object.defineProperty;
  var __getOwnPropSymbols = Object.getOwnPropertySymbols;
  var __hasOwnProp = Object.prototype.hasOwnProperty;
  var __propIsEnum = Object.prototype.propertyIsEnumerable;
  var __defNormalProp = (obj, key, value) => key in obj ? __defProp(obj, key, { enumerable: true, configurable: true, writable: true, value }) : obj[key] = value;
  var __spreadValues = (a, b) => {
    for (var prop in b || (b = {}))
      if (__hasOwnProp.call(b, prop))
        __defNormalProp(a, prop, b[prop]);
    if (__getOwnPropSymbols)
      for (var prop of __getOwnPropSymbols(b)) {
        if (__propIsEnum.call(b, prop))
          __defNormalProp(a, prop, b[prop]);
      }
    return a;
  };

  // assets/ts/fields/field.ts
  var Field = class {
    constructor() {
      this.id = "";
      this.type = "";
      this.data = {};
      this.id = Date.now().toString().slice(-3) + "_" + Math.floor(Math.random() * 100).toString();
      this.type = this.constructor.name;
    }
    useJQuery($this) {
      const deleteBtn = jQuery(`<button type="button">Usu\u0144</button>`, {
        className: "button nbdf-delete-field"
      });
      deleteBtn.on("click", function() {
        if (confirm("Czy na pewno?")) {
          const fieldId = $this.data("fieldId").toString();
          $this.parent().trigger("field:remove", [$this, fieldId]);
        }
      });
      $this.append(deleteBtn);
    }
    render() {
      console.error("You need to override render() method");
      return "";
    }
  };

  // assets/ts/fields/quotefield.ts
  var QuoteField = class extends Field {
    constructor() {
      super();
      this.data = {
        title: "",
        content: ""
      };
    }
    useJQuery($this) {
      super.useJQuery($this);
    }
    render() {
      return `<div class="nbdt-${this.type} nbdt-field ui-sortable-handle" data-field-id="${this.id}" id="nbdt-${this.id}">
            <label>Pole tekstowe - Tytu\u0142</label>
            <input type="text" class="nbdt-data" data-key="title" value="${this.data.title}">
            <label>Tre\u015B\u0107 do wstawienia</label>
            <textarea class="nbdt-data" data-key="content">${this.data.content}</textarea>
        </div>`;
    }
  };

  // assets/ts/fields/checkboxfield.ts
  var CheckboxField = class extends Field {
    constructor() {
      super();
      this.data = {
        label: "",
        required: true
      };
    }
    useJQuery($this) {
      super.useJQuery($this);
    }
    render() {
      return `<div class="nbdt-${this.type} nbdt-field ui-sortable-handle" data-field-id="${this.id}" id="nbdt-${this.id}">
            <label>Checkbox - Tytu\u0142</label>
            <input type="text" class="nbdt-data" data-key="label" value="${this.data.label}">
        </div>`;
    }
  };

  // assets/ts/controller.ts
  var $fields = [];
  var $root;
  var setRoot = (rootElement) => {
    $root = rootElement;
  };
  var load = (serialized) => {
    $fields = JSON.parse(serialized);
    $fields.map((field) => {
      const loadedField = new (createField(field.type))();
      Object.setPrototypeOf(field, loadedField);
      return field;
    });
    $root.trigger("field:update");
  };
  var save = () => {
    return JSON.stringify($fields);
  };
  var createField = (type) => {
    switch (type) {
      case "QuoteField": {
        return QuoteField;
      }
      case "CheckboxField": {
        return CheckboxField;
      }
    }
    throw Error("Unknown field type");
  };
  var addField = (fieldType) => {
    const field = new (createField(fieldType))();
    $fields.push(field);
    $root.trigger("field:update");
  };
  var getFields = () => {
    return $fields;
  };
  var removeField = (id) => {
    $fields = $fields.filter((field) => field.id !== id);
    $root.trigger("field:update");
  };
  var updateField = (id, data) => {
    $fields = $fields.map((field) => {
      if (field.id === id) {
        field.data = __spreadValues(__spreadValues({}, field.data), data);
      }
      return field;
    });
    $root.trigger("field:update");
  };
  var render = (fieldsSelector) => {
    const $rootElement = $root.children(fieldsSelector);
    $rootElement.empty();
    for (const field of getFields()) {
      const fieldDomElement = jQuery(field.render());
      field.useJQuery(fieldDomElement);
      $rootElement.append(fieldDomElement);
    }
  };
  var clearFields = () => {
    $fields = [];
    $root.trigger("field:update");
  };
  var setOrder = (idOrderArr) => {
    let tmpFields = {};
    $fields.forEach((field) => tmpFields[field.id] = field);
    $fields = idOrderArr.map((fieldId) => tmpFields[fieldId]);
    $root.trigger("field:update");
  };
  var Controller = {
    setRoot,
    addField,
    getFields,
    removeField,
    updateField,
    render,
    load,
    save,
    clearFields,
    setOrder
  };

  // assets/ts/app.ts
  jQuery(($) => {
    const $root2 = $("#nbdesigner-tabs-root");
    const $output = $("input[name=_nbdf_data]");
    Controller.setRoot($root2);
    $root2.on("click", ".toolbar > button", function(e) {
      e.preventDefault();
      const fieldType = $(this).data("type");
      Controller.addField(fieldType);
    });
    $root2.on("change", ".nbdt-data", function(e) {
      const fieldId = $(this).parent().data("fieldId");
      const dataKey = $(this).data("key");
      let updatedData = {};
      updatedData[dataKey] = $(this).val();
      Controller.updateField(fieldId, updatedData);
    });
    $root2.on("field:remove", function(e, field, fieldId) {
      field.remove();
      Controller.removeField(fieldId);
    });
    $root2.on("field:update", function(e) {
      $output.val(Controller.save());
      Controller.render(".fields");
    });
    if (typeof $output !== "undefined") {
      const serializedData = $output.val();
      Controller.load(serializedData);
    }
    console.log($root2.children(".fields"));
    $root2.children(".fields").sortable({
      items: ".nbdt-field",
      tolerance: "pointer",
      helper: "clone",
      update: function() {
        let idOrderArr = [];
        $root2.children(".fields").children(".nbdt-field").each(function() {
          idOrderArr.push($(this).data("fieldId").toString());
        });
        Controller.setOrder(idOrderArr);
      }
    });
  });
})();
