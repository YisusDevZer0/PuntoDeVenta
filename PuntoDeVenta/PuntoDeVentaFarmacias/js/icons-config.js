(function ($) {
    if (!$) return;

    var paginateIcons = {
        first: '<i class="fa-solid fa-angles-left" aria-hidden="true"></i>',
        last: '<i class="fa-solid fa-angles-right" aria-hidden="true"></i>',
        next: '<i class="fa-solid fa-chevron-right" aria-hidden="true"></i>',
        previous: '<i class="fa-solid fa-chevron-left" aria-hidden="true"></i>'
    };

    if ($.fn.dataTable) {
        $.extend(true, $.fn.dataTable.defaults, {
            language: { paginate: paginateIcons }
        });
    }

    var datePickerIcons = {
        time: 'fa-solid fa-clock',
        date: 'fa-solid fa-calendar-days',
        up: 'fa-solid fa-chevron-up',
        down: 'fa-solid fa-chevron-down',
        previous: 'fa-solid fa-chevron-left',
        next: 'fa-solid fa-chevron-right',
        today: 'fa-solid fa-calendar-check',
        clear: 'fa-solid fa-trash',
        close: 'fa-solid fa-xmark'
    };

    if ($.fn.datetimepicker && $.fn.datetimepicker.Constructor && $.fn.datetimepicker.Constructor.Default) {
        $.extend(true, $.fn.datetimepicker.Constructor.Default, { icons: datePickerIcons });
    }
})(jQuery);
