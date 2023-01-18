import {Field} from "./field";

export class QuoteField extends Field {
    data = {
        title: '',
        content: ''
    }

    constructor() {
        super();
    }

    useJQuery($this: JQuery): void {
        super.useJQuery($this);
    }

    render(): string {
        return `<div class="nbdt-${this.type} nbdt-field ui-sortable-handle" data-field-id="${this.id}" id="nbdt-${this.id}">
            <label>Pole tekstowe - Tytuł</label>
            <input type="text" class="nbdt-data" data-key="title" value="${this.data.title}">
            <label>Treść do wstawienia</label>
            <textarea class="nbdt-data" data-key="content">${this.data.content}</textarea>
        </div>`;
    }
}