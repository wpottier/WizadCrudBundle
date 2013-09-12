$(document).ready(function () {

    $('tr[data-link]').each(function(i, el) {

        $(el).click(function(e)  {
            window.location = $(el).attr('data-link');
        });
    });

    $('#btn-filter').click(function () {

        var $form = $('#filter-form');

        $('.filter').each(function (i, el) {
            var $el = $(el);

            if($.isArray($el.val())) {
                $form.find('input[name="'+$el.attr('name')+'"]').remove();

                $($el.val()).each(function(i, item) {
                    var $input = $('<input type="hidden">');
                    $input.attr('name', $el.attr('name'));
                    $input.val(item);

                    $form.append($input);
                });
            }
            else {
                var $input = null;
                if($form.find('input[name="'+$el.attr('name')+'"]').length > 0) {
                    $input = $($form.find('input[name="'+$el.attr('name')+'"]')[0]);
                }
                else {
                    $input = $('<input type="hidden">');
                }

                $input.attr('name', $el.attr('name'));

                $input.val($el.val());
                $form.append($input);
            }
        });

        $form.submit();
    });

    $('#batch-form').submit(function(e) {

        $form = $(e.target);

        $('.batch-selected').each(function(i, el) {
            console.log(i, el);
            var $el = $(el);
            var id = 'hidden_' + $el.attr('id');

            if($el[0].checked) {
                // add or update field in batch form
                var $input = $('<input type="hidden" id="' + id + '">');

                if($form.find('#' + id).length > 0) {
                    $input = $($form.find('#' + id)[0]);
                }

                $input.attr('name', $el.attr('name'));
                $input.val($el.val());

                $form.append($input);
            }
            else {
                // remove field from batch form
                if($form.find('#'+id).length > 0) {
                    $input = $($form.find('#' + id)[0]);
                    $input.detach();
                }
            }
        });
    });

    /*
    $("select.filter").selectpicker({
        'countSelectedText': '{0} sur {1} sélectionnés'
    });

    $('input[name="batch-check-all"]').click(function(e) {
        $('.batch-selected').prop('checked', this.checked);
        $('input[name="batch-check-all"]').prop('checked', this.checked);
    });
    */

});