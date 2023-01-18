import {Field} from "./fields/field";
import {QuoteField} from "./fields/quotefield";
import {CheckboxField} from "./fields/checkboxfield";

let $fields: Field[] = [];
let $root: JQuery;

const setRoot = (rootElement: JQuery) => {
    $root = rootElement;
}

const load = (serialized: string) => {
    $fields = JSON.parse(serialized);
    $fields.map(field => {
        const loadedField = new (createField(field.type))();
        Object.setPrototypeOf(field, loadedField);
        return field;
    });
    $root.trigger('field:update');
}

const save = () => {
    return JSON.stringify($fields);
}

const createField = (type: string) => {
    switch (type) {
        case 'QuoteField': {
            return QuoteField;
        }
        case 'CheckboxField': {
            return CheckboxField;
        }
    }
    throw Error('Unknown field type');
}

const addField = (fieldType: string) => {
    const field = new (createField(fieldType))();
    $fields.push(field);
    $root.trigger('field:update');
}

const getFields = () => {
    return $fields;
}

const removeField = (id: string) => {
    $fields = $fields.filter(field => field.id !== id);
    $root.trigger('field:update');
}

const updateField = (id: string, data: Field['data']) => {
    $fields = $fields.map(field => {
        if (field.id === id) {
            field.data = {
                ...field.data,
                ...data
            }
        }
        return field;
    });
    $root.trigger('field:update');
}

const render = (fieldsSelector: string) => {
    const $rootElement = $root.children(fieldsSelector);
    $rootElement.empty();
    for (const field of getFields()) {
        const fieldDomElement = jQuery(field.render());
        field.useJQuery(fieldDomElement);
        $rootElement.append(fieldDomElement);
    }
}

const clearFields = () => {
    $fields = [];
    $root.trigger('field:update');
}

const setOrder = (idOrderArr: string[]) => {
    let tmpFields: { [key: string]: Field } = {};
    $fields.forEach(field => tmpFields[field.id] = field);
    $fields = idOrderArr.map(fieldId => tmpFields[fieldId]);
    $root.trigger('field:update');
}

export const Controller = {
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
}