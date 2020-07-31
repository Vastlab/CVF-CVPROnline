jQuery(document).ready(function($) {
    const catsEndPoint = ectRestUrl + 'categories' + '/?per_page=50';
    const tagsEndPoint = ectRestUrl + 'tags' + '/?per_page=50';
    const venuesEndPoint = ectRestUrl + 'venues' + '/?per_page=50&status=publish';
    const organizersEndPoint = ectRestUrl + 'organizers' + '/?per_page=50&status=publish';
    let categoryList = [];
    let tagList = [];
    let venueList = [];
    let organizerList = [];

    categoryList.push({ text: "Select a Category", value: 'all' });
    $.getJSON(catsEndPoint, function(data) {
        $.each(data.categories, function(key, val) {
            categoryList.push({ text: val.name, value: val.slug });
        });
    });

    tagList.push({ text: "Select a Tag", value: '' });
    $.getJSON(tagsEndPoint, function(data) {
        $.each(data.tags, function(key, val) {
            tagList.push({ text: val.name, value: val.slug });
        });
    });

    venueList.push({ text: "Select a Venue", value: '' });
    $.getJSON(venuesEndPoint, function(data) {
        $.each(data.venues, function(key, val) {
            venueList.push({ text: val.venue, value: val.id });
        });
    });

    organizerList.push({ text: "Select a Organizer", value: '' });
    $.getJSON(organizersEndPoint, function(data) {
        $.each(data.organizers, function(key, val) {
            organizerList.push({ text: val.organizer, value: val.id });
        });
    });

    var date_formats = {
        "formats": [
            { "text": "Default (01 January 2019)", "value": "default" },
            { "text": "Md,Y (Jan 01, 2019)", "value": "MD,Y" },
            { "text": "Fd,Y (January 01, 2019)", "value": "FD,Y" },
            { "text": "dM (01 Jan)", "value": "DM" },
            { "text": "dF (01 January)", "value": "DF" },
            { "text": "Md (Jan 01)", "value": "MD" },
            { "text": "Fd (January 01)", "value": "FD" },
            { "text": "Md,YT (Jan 01, 2019 8:00am-5:00pm)", "value": "MD,YT" },
            { "text": "Full (01 January 2019 8:00am-5:00pm)", "value": "full" },
            { "text": "jMl (1 Jan Monday)", "value": "jMl" },
            { "text": "d.FY (01. January 2019)", "value": "d.FY" },
            { "text": "d.F (01. January)", "value": "d.F" },
            { "text": "d.Ml (01. Jan Monday)", "value": "d.Ml" },
            { "text": "ldF (Monday 01 January)", "value": "ldF" },
            { "text": "Mdl (Jan 01 Monday)", "value": "Mdl" },
            { "text": "dFT (01 January 8:00am-5:00pm)", "value": "dFT" },
            // {"text":"Custom(Please check The event calender settings)","value":"custom"},
        ]
    };

    tinymce.PluginManager.add('ect_tc_button', function(editor, url) {
        editor.addButton('ect_tc_button', {
            title: 'Events Calendar Templates',
            type: 'menubutton',
            icon: 'icon ect-own-icon',
            menu: [{
                    text: 'Events Calendar Templates',
                    value: 'Events Calendar Templates',
                    onclick: function() {
                        editor.windowManager.open({
                            title: 'The Events Calendar Templates - Shortcode Generator',
                            body: [{
                                    type: 'listbox',
                                    name: 'category',
                                    label: 'Events Categories (* Don\'t select for all.)',
                                    values: categoryList
                                },
                                {
                                    type: 'listbox',
                                    name: 'template',
                                    label: 'Select Template',
                                    values: [
                                        { text: 'List (default)', value: 'default' },
                                        { text: 'Grid (grid-view)', value: 'grid-view' },
                                        { text: 'Carousel (carousel-view)', value: 'carousel-view' },
                                        { text: 'Slider (slider-view)', value: 'slider-view' },
                                        { text: 'Timeline (timeline-view)', value: 'timeline-view' },
                                        { text: 'Masonry Layout(Categories Filters)', value: 'masonry-view' },
                                        { text: 'Toggle List(accordion-view)', value: 'accordion-view' }
                                    ]
                                },
                                {
                                    type: 'listbox',
                                    name: 'style',
                                    label: 'Template Style',
                                    values: [
                                        { text: 'Style 1', value: 'style-1' },
                                        { text: 'Style 2', value: 'style-2' },
                                        { text: 'Style 3', value: 'style-3' },
                                    ]
                                },
                                {
                                    type: 'listbox',
                                    name: 'date_formats',
                                    label: 'Date formats',
                                    values: date_formats.formats
                                },
                                {
                                    type: 'listbox',
                                    name: 'time',
                                    label: 'Events Time (* Show past or future events.)',
                                    values: [
                                        { text: 'Future', value: 'future' },
                                        { text: 'Past', value: 'past' },
                                        { text: 'All', value: 'all' },
                                    ]
                                },
                                {
                                    type: 'listbox',
                                    name: 'order',
                                    label: 'Events Order',
                                    values: [
                                        { text: 'ASC', value: 'ASC' },
                                        { text: 'DESC', value: 'DESC' },
                                    ]
                                },
                                {
                                    type: 'listbox',
                                    name: 'venue',
                                    label: 'Hide Events Venue',
                                    values: [
                                        { text: 'NO', value: 'no' },
                                        { text: 'YES', value: 'yes' },
                                    ]
                                },
                                {
                                    type: 'listbox',
                                    name: 'autoplay',
                                    label: 'AutoPlay (* For slide function only.)',
                                    values: [

                                        { text: 'True', value: 'true' },
                                        { text: 'False', value: 'false' },
                                    ]
                                },
                                {
                                    type: 'textbox',
                                    name: 'limit',
                                    label: 'Limit the events',
                                    value: "10"
                                },
                                {
                                    type: 'listbox',
                                    name: 'number_of_columns',
                                    label: 'Select Columns (* For carousel / grid templates.)',
                                    values: [
                                        { text: '2', value: '2' },
                                        { text: '3', value: '3' },
                                        { text: '4', value: '4' },
                                        { text: '6', value: '6' },
                                    ]
                                },
                                {
                                    type: 'listbox',
                                    name: 'featuredonly',
                                    label: 'Show Only Featured Events',
                                    values: [
                                        { text: 'No', value: 'false' },
                                        { text: 'Yes', value: 'true' },
                                    ]
                                },
                                {
                                    type: 'listbox',
                                    name: 'tags',
                                    label: 'Select Tag (* Events by tag.)',
                                    values: tagList
                                },
                                {
                                    type: 'listbox',
                                    name: 'venues',
                                    label: 'Select Venue (* Events by venue.)',
                                    values: venueList
                                },
                                {
                                    type: 'listbox',
                                    name: 'organizers',
                                    label: 'Select Organizer (* Events by organizer.)',
                                    values: organizerList
                                },
                                {
                                    type: 'listbox',
                                    name: 'socialshare',
                                    label: 'Enable Social Share Buttons?',
                                    values: [
                                        { text: 'NO', value: 'no' },
                                        { text: 'YES', value: 'yes' }
                                    ]
                                },
                                {
                                    type: 'listbox',
                                    name: 'show_description',
                                    label: 'Show Description?',
                                    values: [
                                        { text: 'YES', value: 'yes' },
                                        { text: 'NO', value: 'no' },
                                       // { text: 'YES', value: 'yes' }
                                    ]
                                },
                                {
                                    type: 'container',
                                    name: 'container',
                                    label: 'Note:-',
                                    html: '<small style="color:red;">Show events in a date range e.g( 2019-01-25 to 2019-04-15).</small>'
                                },
                                {
                                    type: 'textbox',
                                    name: 'start_date',
                                    label: 'Start Date | format(YY-MM-DD)',
                                    value: ""
                                },
                                {
                                    type: 'textbox',
                                    name: 'end_date',
                                    label: 'End Date | format(YY-MM-DD)',
                                    value: ""
                                }
                            ],
                            onsubmit: function(e) {
                                editor.insertContent(
                                    '[events-calendar-templates  template="' + e.data.template + '" style="' + e.data.style + '" category="' + e.data.category + '" date_format="' + e.data.date_formats + '" start_date="' + e.data.start_date + '"  end_date="' + e.data.end_date + '" limit="' + e.data.limit + '" order="' + e.data.order + '" hide-venue="' + e.data.venue + '"   time="' + e.data.time + '" featured-only="' +
                                    e.data.featuredonly + '" columns="' + e.data.number_of_columns +
                                    '" autoplay="' + e.data.autoplay + '" tags="' + e.data.tags + '" venues="' + e.data.venues + '"  organizers="' + e.data.organizers + '" show-description="' + e.data.show_description + '" socialshare="' + e.data.socialshare + '"]'
                                );
                            }
                        });
                    }
                },
                {
                    text: 'Events Calendar Layout',
                    value: 'Events Calendar Layout',
                    onclick: function() {
                        editor.windowManager.open({
                            title: 'The Events Calendar Layout - Shortcode Generator',
                            body: [{
                                    type: 'listbox',
                                    name: 'dateFormat',
                                    label: 'Date Format',
                                    values: [
                                        { "text": "Default (01 January 2019)", "value": "d F Y" },
                                        { "text": "Md,Y (Jan 01, 2019)", "value": "M D, Y" },
                                        { "text": "Fd,Y (January 01, 2019)", "value": "F D, Y" },
                                        { "text": "dM (01 Jan)", "value": "D M" },
                                        { "text": "dF (01 January)", "value": "D F" },
                                        { "text": "Md (Jan 01)", "value": "M D" },
                                        { "text": "Fd (January 01)", "value": "F D" },
                                        { "text": "jMl (1 Jan Monday)", "value": "j M l" },
                                        { "text": "d.FY (01. January 2019)", "value": "d. F Y" },
                                        { "text": "d.F (01. January)", "value": "d. F" },
                                        { "text": "d.Ml (01. Jan Monday)", "value": "d. M l" },
                                        { "text": "ldF (Monday 01 January)", "value": "l d F" },
                                        { "text": "Mdl (Jan 01 Monday)", "value": "M d l" },
                                    ]
                                },
                                {
                                    type: 'listbox',
                                    name: 'catFilter',
                                    label: 'Show Category Filter',
                                    values: [
                                        { text: 'Yes', value: 'true' },
                                        { text: 'No', value: 'false' },
                                    ]
                                 
                                },
                               
                            ],
                            onsubmit: function(e) {
                                editor.insertContent(
                                    '[ect-calendar-layout date-format="' + e.data.dateFormat + '" show-category-filter="' + e.data.catFilter + '"]'
                                );
                            }
                        });
                    }
                }
            ]
        });
    });


});