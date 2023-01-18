import {Controller} from "./controller";

jQuery($ => {

    const $root: JQuery = $('#nbdesigner-tabs-root');
    const $output: JQuery = $('input[name=_nbdf_data]') as JQuery<HTMLInputElement>;

    Controller.setRoot($root);

    $root.on('click', '.toolbar > button', function (e) {
        e.preventDefault();
        const fieldType = $(this).data('type');
        Controller.addField(fieldType);
    });

    $root.on('change', '.nbdt-data', function () {
        const fieldId: string = $(this).parent().data('fieldId');
        const dataKey: string = $(this).data('key');
        let updatedData: { [key: string]: any } = {};
        updatedData[dataKey] = $(this).val();
        Controller.updateField(fieldId, updatedData);
    });

    $root.on('field:remove', function (e, field, fieldId) {
        field.remove();
        Controller.removeField(fieldId);
    });

    $root.on('field:update', function () {
        $output.val(Controller.save());
        Controller.render('.fields');
    });

    if (typeof $output !== 'undefined') {
        const serializedData = $output.val() as string;
        Controller.load(serializedData);
    }


    console.log($root.children('.fields'));
    // @ts-ignore
    $root.children('.fields').sortable({
        items: '.nbdt-field',
        tolerance: "pointer",
        helper: 'clone',
        update: function () {
            let idOrderArr: string[] = [];
            $root.children('.fields').children('.nbdt-field').each(function () {
                idOrderArr.push($(this).data('fieldId').toString());
            });
            Controller.setOrder(idOrderArr);
        }
    });

});