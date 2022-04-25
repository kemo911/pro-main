$(document).ready(function() {
    var $scheduleCalendar = $('#schedule-calendar');
    var $scheduleCalendarDayFirstHalf = $('#schedule-calendar-day-1');
    var $scheduleCalendarDaySecondHalf = $('#schedule-calendar-day-2');

    if ( $scheduleCalendar.length ) {
        $scheduleCalendar.fullCalendar({
            locale: 'fr',
            editable: false,
            eventSources: [
                '/admin/ajax/ajax_get_no_appointment.php',
                '/admin/ajax/ajax_get_estimate_appointment.php',
                '/admin/ajax/ajax_get_repair_appointment.php',
                '/admin/ajax/ajax_get_combined_appointment.php',
            ],
            dayClick: function(date, jsEvent, view) {
                var clickedDate = date.format();
                window.location.href = '/admin/schedule_day.php?date=' + clickedDate;
            }
        });
    }

    var day = location.search.split('date=')[1] ? location.search.split('date=')[1] : moment().format('YYYY-MM-DD');

    if ( $scheduleCalendarDayFirstHalf.length ) {
        $scheduleCalendarDayFirstHalf.fullCalendar({
            locale: 'fr',
            header: false,
            defaultDate: day,
            defaultView: 'agendaDay',
            editable: false,
            minTime: "07:00:00",
            maxTime: "12:30:00",
            slotDuration: '00:15:00',
            height: 600,
            eventSources: [
                '/admin/ajax/ajax_get_agenda_day.php?date=' + day + '&agenda_type=available_schedule&shift=1',
                '/admin/ajax/ajax_get_agenda_day.php?date=' + day + '&agenda_type=estimate_schedule&shift=1',
                '/admin/ajax/ajax_get_agenda_day.php?date=' + day + '&agenda_type=repair_schedule&shift=1',
            ],
            eventRender: function(event, element, view ){
                if(event.rendering === "background"){
                    element.append('<span class="sc-label">'+ event.title +'</span>');
                }
            },
            eventAfterAllRender: function () {
                window.setTimeout(function () {
                    var top = 0;
                    $('#schedule-calendar-day-1 .available-schedule').each(function () {
                        $(this).css({"left": top + 'px'});
                        top += 100;
                    });

                    $('#schedule-calendar-day-1 .estimate-schedule').each(function () {
                        var $self = $(this);
                        var classList = $self.attr("class").split(/\s+/);
                        $.each(classList, function(index, item) {
                            if ( item.indexOf('s__') !== -1 ) {
                                var number = item.split('s__')[1];
                                if ( number > 1 ) {
                                    $self.css({
                                        "left": $('.a__' + number).position().left + 'px'
                                    });
                                }
                            }
                        });
                    });

                    $('#schedule-calendar-day-1 .repair-schedule').each(function () {
                        var $self = $(this);
                        var classList = $self.attr("class").split(/\s+/);
                        $.each(classList, function(index, item) {
                            if ( item.indexOf('s__') !== -1 ) {
                                var number = item.split('s__')[1];
                                if ( number > 1 ) {
                                    $self.css({
                                        "left": $('.a__' + number).position().left + 'px'
                                    });
                                }
                            }
                        });
                    });

                }, 500);
            },
            eventClick: function(calEvent, jsEvent, view) {
                var sc = calEvent.className[2];
                var id = sc.split('ap__')[1];
                window.location.href = "/admin/appointment_view.php?appointment_id="+id;
            }
        });
    }

    if ( $scheduleCalendarDaySecondHalf.length ) {
        $scheduleCalendarDaySecondHalf.fullCalendar({
            locale: 'fr',
            header: false,
            defaultDate: day,
            defaultView: 'agendaDay',
            editable: false,
            minTime: "13:00:00",
            maxTime: "18:30:00",
            slotDuration: '00:15:00',
            height: 600,
            eventSources: [
                '/admin/ajax/ajax_get_agenda_day.php?date=' + day + '&agenda_type=available_schedule&shift=2',
                '/admin/ajax/ajax_get_agenda_day.php?date=' + day + '&agenda_type=estimate_schedule&shift=2',
                '/admin/ajax/ajax_get_agenda_day.php?date=' + day + '&agenda_type=repair_schedule&shift=2',
            ],
            eventRender: function(event, element, view ){
                if(event.rendering === "background"){
                    element.append('<span class="sc-label">'+ event.title +'</span>');
                }
            },
            eventAfterAllRender: function () {
                window.setTimeout(function () {
                    var top = 0;
                    $('#schedule-calendar-day-2 .available-schedule').each(function () {
                        $(this).css({"left": top + 'px'});
                        top += 100;
                    });

                    $('#schedule-calendar-day-2 .estimate-schedule').each(function () {
                        var $self = $(this);
                        var classList = $self.attr("class").split(/\s+/);
                        $.each(classList, function(index, item) {
                            if ( item.indexOf('s__') !== -1 ) {
                                var number = item.split('s__')[1];
                                if ( number > 1 ) {
                                    $self.css({
                                        "left": $('.a__' + number).position().left + 'px'
                                    });
                                }
                            }
                        });
                    });

                    $('#schedule-calendar-day-2 .repair-schedule').each(function () {
                        var $self = $(this);
                        var classList = $self.attr("class").split(/\s+/);
                        $.each(classList, function(index, item) {
                            if ( item.indexOf('s__') !== -1 ) {
                                var number = item.split('s__')[1];
                                if ( number > 1 ) {
                                    $self.css({
                                        "left": $('.a__' + number).position().left + 'px'
                                    });
                                }
                            }
                        });
                    });

                }, 1000);
            },
            eventClick: function(calEvent, jsEvent, view) {

                var sc = calEvent.className[2];

                var id = sc.split('ap__')[1];

                window.location.href = "/admin/appointment_view.php?appointment_id="+id;

            }
        });
    }

    var $dvd = $('#day-view-datepicker');
    if ( $dvd.length ) {
        $dvd.datepicker({
            dateFormat: "yy-mm-dd",
            minDate: -7,
            maxDate: "+2M",
            onSelect: function(dateText) {
                window.location.href = "/admin/schedule_day.php?date=" + dateText;
            }
        });
    }
});