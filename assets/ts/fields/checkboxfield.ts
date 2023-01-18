import {Field} from "./field";

export class CheckboxField extends Field {
    data = {
        label: '',
        required: true
    }

    constructor() {
        super();
    }

    useJQuery($this: JQuery): void {
        super.useJQuery($this);
    }

    render(): string {
        return `<div class="nbdt-${this.type} nbdt-field ui-sortable-handle" data-field-id="${this.id}" id="nbdt-${this.id}">
            <label>Checkbox - Tytuł</label>
            <input type="text" class="nbdt-data" data-key="label" value="${this.data.label}">
        </div>`;
    }

}