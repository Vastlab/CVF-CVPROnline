/**
 * This file is responsible for calendar template rendering.
 **/

var cal_bgColor = event_api.ect_cal_bgColor;
var cal_textColor = event_api.ect_cal_textColor;
const ect_COMMON_CUSTOM_THEME = {
    'common.border': '1px solid #e5e5e5',
    'common.backgroundColor': cal_bgColor == '' ? '#fcfcfc' : cal_bgColor,
    'common.holiday.color': '#ff4040',
    'common.saturday.color': '#333',
    'common.dayname.color': '#333',
    'common.today.color': '#333',

    // creation guide style
    'common.creationGuide.backgroundColor': 'rgba(81, 92, 230, 0.05)',
    'common.creationGuide.border': '1px solid #515ce6',

    // month header 'dayname'
    'month.dayname.height': '31px',
    'month.dayname.borderLeft': '2px solid #e5e5e5',
    'month.dayname.paddingLeft': '10px',
    'month.dayname.paddingRight': '10px',
    'month.dayname.backgroundColor': 'inherit',
    'month.dayname.fontSize': '12px',
    'month.dayname.fontWeight': 'normal',
    'month.dayname.textAlign': 'left',

    // month day grid cell 'day'
    'month.holidayExceptThisMonth.color': 'rgba(255, 64, 64, 0.4)',
    'month.dayExceptThisMonth.color': 'rgba(51, 51, 51, 0.4)',
    'month.weekend.backgroundColor': 'inherit',
    'month.day.fontSize': '14px',

    // month schedule style
    'month.schedule.borderRadius': '2px',
    'month.schedule.height': '24px',
    'month.schedule.marginTop': '2px',
    'month.schedule.marginLeft': '8px',
    'month.schedule.marginRight': '8px',

    // month more view
    'month.moreView.border': '1px solid #d5d5d5',
    'month.moreView.boxShadow': '0 2px 6px 0 rgba(0, 0, 0, 0.1)',
    'month.moreView.backgroundColor': 'white',
    'month.moreView.paddingBottom': '17px',
    'month.moreViewTitle.height': '44px',
    'month.moreViewTitle.marginBottom': '12px',
    'month.moreViewTitle.backgroundColor': 'inherit',
    'month.moreViewTitle.borderBottom': 'none',
    'month.moreViewTitle.padding': '12px 17px 0 17px',
    'month.moreViewList.padding': '0 17px',

    // week header 'dayname'
    'week.dayname.height': '42px',
    'week.dayname.borderTop': '1px solid #e5e5e5',
    'week.dayname.borderBottom': '1px solid #e5e5e5',
    'week.dayname.borderLeft': 'inherit',
    'week.dayname.paddingLeft': '0',
    'week.dayname.backgroundColor': 'inherit',
    'week.dayname.textAlign': 'left',
    'week.today.color': '#333',
    'week.pastDay.color': '#bbb',

    // week vertical panel 'vpanel'
    'week.vpanelSplitter.border': '1px solid #e5e5e5',
    'week.vpanelSplitter.height': '3px',

    // week daygrid 'daygrid'
    'week.daygrid.borderRight': '1px solid #e5e5e5',
    'week.daygrid.backgroundColor': 'inherit',

    'week.daygridLeft.width': '72px',
    'week.daygridLeft.backgroundColor': 'inherit',
    'week.daygridLeft.paddingRight': '8px',
    'week.daygridLeft.borderRight': '1px solid #e5e5e5',

    'week.today.backgroundColor': 'rgba(81, 92, 230, 0.05)',
    'week.weekend.backgroundColor': 'inherit',

    // week timegrid 'timegrid'
    'week.timegridLeft.width': '72px',
    'week.timegridLeft.backgroundColor': 'inherit',
    'week.timegridLeft.borderRight': '1px solid #e5e5e5',
    'week.timegridLeft.fontSize': '11px',
    'week.timegridLeftTimezoneLabel.height': '40px',
    'week.timegridLeftAdditionalTimezone.backgroundColor': 'white',

    'week.timegridOneHour.height': '52px',
    'week.timegridHalfHour.height': '26px',
    'week.timegridHalfHour.borderBottom': 'none',
    'week.timegridHorizontalLine.borderBottom': '1px solid #e5e5e5',

    'week.timegrid.paddingRight': '8px',
    'week.timegrid.borderRight': '1px solid #e5e5e5',
    'week.timegridSchedule.borderRadius': '2px',
    'week.timegridSchedule.paddingLeft': '2px',

    'week.currentTime.color': '#515ce6',
    'week.currentTime.fontSize': '11px',
    'week.currentTime.fontWeight': 'normal',

    'week.pastTime.color': '#bbb',
    'week.pastTime.fontWeight': 'normal',

    'week.futureTime.color': '#333',
    'week.futureTime.fontWeight': 'normal',

    'week.currentTimeLinePast.border': '1px dashed #515ce6',
    'week.currentTimeLineBullet.backgroundColor': '#515ce6',
    'week.currentTimeLineToday.border': '1px solid #515ce6',
    'week.currentTimeLineFuture.border': 'none',

    // week creation guide style
    'week.creationGuide.color': '#515ce6',
    'week.creationGuide.fontSize': '11px',
    'week.creationGuide.fontWeight': 'bold',

    // week daygrid schedule style
    'week.dayGridSchedule.borderRadius': '2px',
    'week.dayGridSchedule.height': '24px',
    'week.dayGridSchedule.marginTop': '2px',
    'week.dayGridSchedule.marginLeft': '8px',
    'week.dayGridSchedule.marginRight': '8px'
};
/**
 * Custom template values.
 */
const ect_templates = {
    popupIsAllDay: function() {
        return 'All Day';
    },
    popupStateFree: function() {
        return 'Free';
    },
    popupStateBusy: function() {
        return 'Busy';
    },
    titlePlaceholder: function() {
        return 'Subject';
    },
    locationPlaceholder: function() {
        return 'Location';
    },
    startDatePlaceholder: function() {
        return 'Start date';
    },
    endDatePlaceholder: function() {
        return 'End date';
    },
    popupSave: function() {
        return 'Save';
    },
    popupUpdate: function() {
        return 'Update';
    },
    popupDetailDate: function(isAllDay, start, end) {
        var start = new Date(start).getTime();
        var end = new Date(end).getTime();
        var date_format = jQuery('.ect-custom-calendar').attr('data-date-format');
        var time_format = jQuery('.ect-custom-calendar').attr('data-time-format');
        var isSameDate = false;
        if (new Date(start).getDate() == new Date(end).getDate()) {
            isSameDate = true;
        }
        var endFormat = (isSameDate ? '' : date_format) + ' ' + time_format;

        if (isAllDay) {
            //      return moment(start).format(date_format) + (isSameDate ? '' : ' - ' + moment(end).format(date_format));
        }

        return (moment(start).format(date_format + ' ' + time_format) + ' - ' + moment(end).format(endFormat));
    },
    popupDetailLocation: function(schedule) {
        return 'Location : ' + schedule.location;
    },
    popupDetailUser: function(schedule) {
        return 'User : ' + (schedule.attendees || []).join(', ');
    },
    popupDetailState: function(schedule) {
        return 'State : ' + schedule.state || 'Busy';
    },
    popupDetailRaw: function(schedule) {
        return 'Raw : ' + schedule.state || 'Busy';
    },
    popupDetailRepeat: function(schedule) {
        return 'Repeat : ' + schedule.recurrenceRule;
    },
    popupDetailBody: function(schedule) {
        var desc = unescape(jQuery(schedule.body).text()); // remove all HTML
        if (desc.length < 3) {
            return;
        }
        if (desc.length > 255) {
            return '' + (desc).substring(0, 250) + '...';
        }
        return '' + desc;
    },
    popupEdit: function() {
        return 'Edit';
    },
    popupDelete: function() {
        return 'Delete';
    }
};

(function($) {
    var calendar_container = '.ect-custom-calendar';

    $(calendar_container).each(function(key, value) {

            var $this = $(this);
            var ID = $this.attr('data-calendar-id');
            var calendar_div = '#ect_calendar-' + ID;
            var daysName = JSON.parse($this.attr('data-days-name'));
            var calendar = [];
            // initializing Calendar for all available DOM in the page
            calendar[ID] = new tui.Calendar(document.querySelector(calendar_div), {
                isReadOnly: true, // Read Only calendar for users
                usageStatistics: true,
                defaultView: 'month',
                taskView: ['task'],
                scheduleView: true,
                theme: ect_COMMON_CUSTOM_THEME, // set theme
                template: ect_templates,
                useCreationPopup: true,
                useDetailPopup: true,
                month: {
                    daynames: daysName,
                    startDayOfWeek: 0,
                    narrowWeekend: false,
                    isAlways6Week: false,
                },
                week: {
                    daynames: daysName,
                    startDayOfWeek: 0,
                    narrowWeekend: false
                }

            });

            var date = calendar[ID].getDate();

            var NameOfTheMonths = JSON.parse($('.ect_renderRange[data-calendar-id="' + ID + '"]').attr('data-months'));
            $('.ect_renderRange[data-calendar-id="' + ID + '"]').text(NameOfTheMonths[date.getMonth()] + ' - ' + date.getFullYear());

            function ect_render_events() {
                var dateStart = calendar[ID].getDateRangeStart();
                var cal = $('.ect-calendar-select[data-calendar-id="' + ID + '"]');
                var view = $(cal).find(":selected").val();
                if (view != 'month') {
                    dateStart = dateStart.getFullYear() + "-" + (dateStart.getMonth()) + "-1";
                } else {
                    dateStart = dateStart.getFullYear() + "-" + (dateStart.getMonth()) + "-" + dateStart.getDate();
                }

                var dateEnd = calendar[ID].getDateRangeEnd();
                if (view != 'month') {
                    dateEnd = dateEnd.getFullYear() + "-" + (dateEnd.getMonth() + 1) + "-31";
                } else {
                    dateEnd = dateEnd.getFullYear() + "-" + (dateEnd.getMonth() + 1) + "-" + dateEnd.getDate();
                }
                var limit = $this.attr('data-events-limit')
                    // Send an ajax request to fetch all available events via event calendar (Tribe) REST api
                $.ajax({
                    'url': wpApiSettings.root + 'tribe/events/v1/events?start_date=' + dateStart + '&per_page=' + limit, //event_api.ajax_url,
                    'method': 'GET',
                    beforeSend: function() {
                        // initialize or setup anything before sending event request
                        $this.find('#ect_calendar-' + ID + ', .ect-calendar-menu').css({ "opacity": ".2" });
                        $this.find('.ect_calendar_events_spinner').show();
                        $('.ect-calendar_btn[data-calendar-id="' + ID + '"]').each(function() {
                            var action = $(this).attr('data-action');
                            $(this).attr('data-action', action + '-disable');
                            $(this).css({ "opacity": ".2" });
                        });
                    },
                    success: function(res) {
                        var data = res;
                        var events = data['events'];
                        var Schedule = [];
                        var category = ['ECTNotInUsed'];
                        category.push("Uncategorized");
                        var ev_category, evt_bgColor, evt_textColor;
                        for (var i = 0; i < events.length; i++) {
                            if (events[i]['categories'].length != 0) {
                                var evt_end_date = new Date(events[i]['end_date'].replace(/-/g, "/"));
                                var evt_start_date = new Date(events[i]['start_date'].replace(/-/g, "/"));
                                var dateStart = calendar[ID].getDateRangeStart();
                                var dateEnd = calendar[ID].getDateRangeEnd();
                                var calendar_start_date = dateStart.getFullYear() + "-" + (dateStart.getMonth() + 1) + "-" + dateStart.getDate();
                                var calendar_end_date = dateEnd.getFullYear() + "-" + (dateEnd.getMonth() + 1) + "-" + dateEnd.getDate();
                                ev_category = events[i]['categories'][0]['name'];
                                // category.push(ev_category);
                                if (ev_category != '' && category.indexOf(ev_category) == -1) {
                                    category.push(ev_category);
                                }
                            } else {
                                ev_category = 'Uncategorized';
                            }

                            // if the event is featured
                            if (events[i]['featured'] === true) {
                                evt_textColor = $this.attr('data-featured-textcolor');
                                evt_bgColor = $this.attr('data-featured-bgcolor');
                            } else {
                                if (events[i]['event_bgcolor'] != '#' && events[i]['event_bgcolor'] != events[i]['event_text_color']) {
                                    evt_bgColor = events[i]['event_bgcolor'];
                                } else {
                                    evt_bgColor = $this.attr('data-skin-color');
                                }

                                if (events[i]['event_text_color'] != '#' && events[i]['event_bgcolor'] != events[i]['event_text_color']) {
                                    evt_textColor = events[i]['event_text_color'];
                                } else {
                                    evt_textColor = $this.attr('data-alt-skin-color');
                                }
                            }

                            var venue = '';
                            var featured_image = events[i]['image']['url'];
                            if (typeof events[i]['venue']['venue'] != 'undefined') {
                                venue = events[i]['venue']['venue'];

                                if (typeof events[i]['venue']['address'] != 'undefined') {
                                    venue += ', ' + events[i]['venue']['address'];
                                }

                                if (typeof events[i]['venue']['city'] != 'undefined') {
                                    venue += ', ' + events[i]['venue']['city'];
                                }

                                if (typeof events[i]['venue']['country'] != 'undefined') {
                                    venue += ", " + events[i]['venue']['country'];
                                }

                                if (typeof events[i]['venue']['zip'] != 'undefined') {
                                    venue += ", Zip: " + events[i]['venue']['zip'];
                                }
                                if (typeof events[i]['venue']['phone'] != 'undefined') {
                                    venue += ", Contact: " + events[i]['venue']['phone'];
                                }
                            }
                            var custom_css = 'background-color:' + evt_bgColor + ';color:' + evt_textColor + ';'
                            Schedule.push({
                                id: i + 1,
                                calendarId: category.indexOf(ev_category),
                                title: unescape(events[i]['title']),
                                body: unescape(events[i]['description']),
                                isAllDay: events[i]['all_day'],
                                category: 'time',
                                customStyle: custom_css, // custom CSS can be added here
                                raw: [events[i]['url'], featured_image, events[i]['start_date'], events[i]['end_date'], events[i]['all_day']],
                                bgColor: evt_bgColor,
                                isFocused: events[i]['featured'],
                                location: venue,
                                start: new Date(events[i]['start_date'].replace(/-/g, "/")),
                                end: new Date(events[i]['end_date'].replace(/-/g, "/")),
                            });
                        }

                        // create an array of all availabel categories
                        if (Schedule.length != 0 && $('.ect-calendar-cat-filter[data-calendar-id="' + ID + '"]').length > 0) {
                            category.forEach(function(item, index, arr) {
                                if (index == 0) {
                                    $('.ect-calendar-cat-filter[data-calendar-id="' + ID + '"]').html('<label class="cat-label">Select All<input type="checkbox" checked="checked" class="select_allCat" /> <span class="cat-checkmark"></span></label>');
                                    return;
                                }
                                var previous = $('.ect-calendar-cat-filter[data-calendar-id="' + ID + '"]').html();
                                var new_html = previous.concat('<label class="cat-label">' + item + '<input type="checkbox" checked="checked" data-event-cat="' + index + '" class="' + (calendar_div).replace('.', '') + '_' + item + ' ect_cat_filter_option"/>  <span class="cat-checkmark"></span></label>');
                                $('.ect-calendar-cat-filter[data-calendar-id="' + ID + '"]').html(new_html);
                            });
                        } else {
                            $('.ect-calendar-cat-filter[data-calendar-id="' + ID + '"]').html('');
                        }

                        calendar[ID].createSchedules(Schedule, true);
                        $this.find('.ect_calendar_events_spinner').hide();
                        calendar[ID].render();
                        $this.find('#ect_calendar-' + ID + ', .ect-calendar-menu').css({ "opacity": "1" });
                        $('.ect-calendar_btn[data-calendar-id="' + ID + '"]').each(function() {
                            var action = $(this).attr('data-action');
                            $(this).attr('data-action', action.replace('-disable', ''));
                            $(this).css({ "opacity": "1" });
                        });



                    }
                });

            } // end of ect_render_events()

            // change calendar view option
            $('.ect-calendar-select[data-calendar-id="' + ID + '"]').on('change', function() {
                var view = $(this).find(":selected").val();
                //calendar[ID].render();
                calendar[ID].clear(true);
                ect_render_events();
                calendar[ID].changeView(view);
            });

            // Select All (category option)
            $(document).on('click', ".ect-calendar-cat-filter[data-calendar-id='" + ID + "'] input.select_allCat", function(e) {

                if ($(this).is(":checked")) {
                    $this.find('input.ect_cat_filter_option').removeAttr('checked');
                } else {
                    $this.find('input.ect_cat_filter_option').attr('checked', 'checked');
                }
                $this.find('input.ect_cat_filter_option').trigger('click');
            });

            // individual category option to hide and show
            $(document).on('click', ".ect-calendar-cat-filter[data-calendar-id='" + ID + "'] input[type='checkbox']", function() {
                var cat_name = $(this).attr('data-event-cat');
                var option = $(this).is(":checked") ? false : true;
                if (option == true) {
                    $this.find('.select_allCat').removeAttr('checked');
                    $(this).removeAttr('checked');
                } else {
                    $(this).attr('checked', 'checked');
                    if ($this.find('.ect-calendar-cat-filter input[checked].ect_cat_filter_option').length == $this.find('.ect-calendar-cat-filter input.ect_cat_filter_option').length) {
                        $this.find('.select_allCat').attr('checked', 'checked');
                    }
                }
                calendar[ID].toggleSchedules(parseInt(cat_name), option, true);
            });

            // Read More button on calendar brief info
            calendar[ID].on('clickSchedule', function(event) {
                var event_url = event.schedule.raw;
                var start = new Date(event.schedule.raw[2].replace(/-/g, "/"));

                var end = new Date(event.schedule.raw[3].replace(/-/g, "/"));
                var all_day_event = event.schedule.raw[4];
                var date_format = jQuery('.ect-custom-calendar').attr('data-date-format');
                var time_format = jQuery('.ect-custom-calendar').attr('data-time-format');
                var isSameDate = false;
                if (new Date(start).getDate() == new Date(end).getDate()) {
                    isSameDate = true;
                }
                var endFormat = (isSameDate ? '' : date_format) + ' ' + time_format;
                var date_time_data = '';
                if (all_day_event) {
                    date_time_data = moment(start).format(date_format) + (isSameDate ? ' All Day' : ' - ' + moment(end).format(date_format));
                } else {
                    date_time_data = moment(start).format(date_format + ' ' + time_format) + ' - ' + moment(end).format(endFormat);
                }
                $(".tui-full-calendar-popup-detail-date").text(date_time_data);

                // get title of the popup window and push again after unescaping special characters      
                $('.ect-calendar-container [class*="-schedule-title"]').each(function() {
                    var title = $(this).attr('data-title');
                    if (typeof title == 'undefined') {
                        return;
                    }
                    title = unescape(title);
                    $(this).attr('data-title', title.replace('&#038;', '&'));
                    $(this).attr('title', title.replace('&#038;', '&'));
                    $(this).text(title.replace('&#038;', '&'));
                })

                var popup_title = $('.tui-full-calendar-popup-detail .tui-full-calendar-schedule-title').text();
                popup_title = unescape(popup_title);
                $('.tui-full-calendar-popup-detail .tui-full-calendar-schedule-title').html('<a class="ect-title-link" href="#" target="_new">' + popup_title.replace('&#038;', '&') + '</a>');

                if (event_url[0] != '' && (typeof event_url[0] != 'undefined')) {
                    $('.tui-full-calendar-section-detail').append('<a class="ect_readMore" target="_new" href="' + event_url[0] + '">Read More</a>');
                    $('.tui-full-calendar-popup-detail .tui-full-calendar-schedule-title a.ect-title-link').attr('href', event_url[0]);
                }
                if (event_url[1] != '' && (typeof event_url[1] != 'undefined')) {
                    $('span.tui-full-calendar-content:first').append('<img onError="this.src=\'\'" class="feature_image" src="' + event_url[1] + '">');
                }
            });

            // Navigation button. Today/Prev/Next
            $('.ect-calendar_btn[data-calendar-id="' + ID + '"]').on('click', function() {
                var action = $(this).attr('data-action');
                var cal_id = $(this).attr('data-calendar-id');
                switch (action) {
                    case 'move-today':
                        calendar[cal_id].clear(true);
                        calendar[cal_id].today();
                        ect_render_events();
                        break;
                    case 'move-next':
                        calendar[cal_id].clear(true);
                        calendar[cal_id].next();
                        ect_render_events();
                        break;
                    case 'move-prev':
                        calendar[cal_id].clear(true);
                        calendar[cal_id].prev();
                        ect_render_events();
                        break;
                }
                var date = calendar[cal_id].getDate();
                $('.ect_renderRange[data-calendar-id="' + cal_id + '"]').text(NameOfTheMonths[date.getMonth()] + ' - ' + date.getFullYear());
            });
            //calendar[ID].clear(true);
            // There is no milestone, task, so hide those view panel
            calendar[ID].toggleTaskView(false);
            calendar[ID].hideMoreView(false);

            ect_render_events();

        }) // end of for-each;
})(jQuery);