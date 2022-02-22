$(document).ready(function(){
    var allActions = ['update', 'delete'];

    $('table.detail-table > tbody > tr > td input[type=checkbox][data-action]').on('click', function(e){
        updateTableRow(e.target);
    });
    $('table.detail-table > tbody > tr > td input[type=checkbox][data-action]:checked').each(function(i, e){
        updateTableRow(e);
    });

    $('table.detail-table > tbody > tr > td button').on('click', function(e){
        var $tr = $(e.target).closest('tr');
        var $checkbox = $tr.find('td input[type=checkbox]:checked');
        if ($checkbox.length) {
            if ($.inArray($checkbox.data('action'), allActions) !== -1) {
                var action = ['/', $checkbox.data('action'), '/', $tr.data('index')].join('');
                var $form = $(e.target).closest('form');
                $form.attr('action', $form.attr('action').replace(/\/add$/, action));
                return true;
            }
        }
        return false;
    });

    function updateTableRow(checkbox) {
        var $checkbox = $(checkbox);
        var $tr = $checkbox.closest('tr');
        if ($checkbox.is(':checked')) {
            $tr.removeClass('unchecked');
            $tr.find('td input[type=text], td button').prop('disabled', false);
            setupDatepicker($tr.find('td:first-child input[type=text]'));
            rest(allActions, $checkbox.data('action')).forEach(function(action){
                $tr.find('> td input[type=checkbox][data-action=' + action + ']').prop('checked', false);
            });
        } else {
            $tr.addClass('unchecked');
            $tr.find('td input[type=text], td button').prop('disabled', true);
        }
    }
});
function rest(oriArr, e) {
    var index = oriArr.indexOf(e);
    var copy = oriArr.slice();
    if (index !== -1) {
        copy.splice(index, 1);
    }
    return copy;
}
function setupDatepicker($target) {
    if ($target.is(':not(.hasDatepicker)')) {
        $target.datepicker({
            showOn: 'focus',
            buttonText: 'カレンダー',
            showButtonPanel: true
        });
    }
}
