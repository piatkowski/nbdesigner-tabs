export abstract class Field {
    id = '';
    type = '';
    data = {};

    useJQuery($this: JQuery): void {
        const deleteBtn = jQuery(`<button type="button">Usuń</button>`, {
            className: 'button nbdf-delete-field'
        });
        deleteBtn.on('click', function () {
            if (confirm('Czy na pewno?')) {
                const fieldId = $this.data('fieldId').toString();
                $this.parent().trigger('field:remove', [$this, fieldId]);
            }
        });
        $this.append(deleteBtn);
    }

    render(): string {
        console.error('You need to override render() method');
        return '';
    }

    constructor() {
        this.id = Date.now().toString().slice(-3) + '_' + Math.floor(Math.random() * 100).toString();
        this.type = this.constructor.name;
    }

}