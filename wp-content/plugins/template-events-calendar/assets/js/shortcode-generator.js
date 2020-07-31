 (function() {
    var $ = jQuery;
    const catsEndPoint = ectRestUrl + 'categories' + '/?per_page=50';
    let categoryList = [];
    categoryList.push({ text: "Select a Category", value: 'all' });
    $.getJSON(catsEndPoint, function(data) {
        $.each(data.categories, function(key, val) {
            categoryList.push({ text: val.name, value: val.slug });
        });
    });
    // var ect_cats = JSON.parse(ect_cat_obj.category);
    // var categories = [];

    // for (var cat in ect_cats) {
    //     categories.push({ "text": ect_cats[cat], "value": cat });
    // }
    var date_formats = {
        "formats": [
            { "text": "Default (01 January 2019)", "value": "default" },
            { "text": "Md,Y (Jan 01, 2019)", "value": "MD,Y" },
            { "text": "Fd,Y (January 01, 2019)", "value": "FD,Y" },
            { "text": "dM (01 Jan)", "value": "DM" },
            { "text": "dMl (01 Jan Monday)", "value": "DML" },
            { "text": "dF (01 January)", "value": "DF" },
            { "text": "Md (Jan 01)", "value": "MD" },
            { "text": "Fd (January 01)", "value": "FD" },
            { "text": "Md,YT (Jan 01, 2019 8:00am-5:00pm)", "value": "MD,YT" },
            { "text": "Full (01 January 2019 8:00am-5:00pm)", "value": "full" },
            { "text": "jMl (1 Jan Monday)", "value": "jMl" },
            { "text": "d.FY (01. January 2019)", "value": "d.FY" },
            { "text": "d.F (01. January)", "value": "d.F" },
            { "text": "ldF (Monday 01 January)", "value": "ldF" },
            { "text": "Mdl (Jan 01 Monday)", "value": "Mdl" },
            { "text": "d.Ml (01. Jan Monday)", "value": "d.Ml" },
            { "text": "dFT (01 January 8:00am-5:00pm)", "value": "dFT" }
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
                        title: 'The Events Calendar Template - Shortcode Generator',
                        body: [
                            {
                                type: 'listbox',
                                name: 'time',
                                label: 'Show Events',
                                values: [
                                    { text: 'Upcoming Events', value: 'future' },
                                    { text: 'Past Events', value: 'past' },
                                    { text: 'All (Upcoming + Past)', value: 'all' },
                                ]
                            },
                            {
                                type: 'listbox',
                                name: 'category',
                                label: 'Select Category',
                                values: categoryList
                            },
                            {
                                type: 'listbox',
                                name: 'template',
                                label: 'Select Template',
                                values: [
                                    { text: 'Default List Layout', value: 'default' },
                                    { text: 'Timeline Layout', value: 'timeline-view' },
                                ]
                            },
                            {
                                type: 'listbox',
                                name: 'style',
                                label: 'Template Style',
                                values: [
                                    { text: 'Style 1', value: 'style-1' },
                                    { text: 'Style 2', value: 'style-2' },
                                    { text: 'Style 3', value: 'style-3' }
                                ]
                            },
                            {
                                type: 'listbox',
                                name: 'date_formats',
                                label: 'Date Format',
                                values: date_formats.formats
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
                                label: 'Hide Venue?',
                                values: [
                                    { text: 'NO', value: 'no' },
                                    { text: 'YES', value: 'yes' },
                                ]
                            },
                            {
                                type: 'listbox',
                                name: 'sharebutton',
                                label: 'Enable Social Share Buttons?',
                                values: [
                                    { text: 'NO', value: 'no' },
                                    { text: 'YES', value: 'yes' },
                                ]
                            },
                            {
                                type: 'textbox',
                                name: 'limit',
                                label: 'Limit the events',
                                value: "10"
                            },
                            {
                                type: 'container',
                                name: 'container',
                                label: 'Note',
                                html: '<span class="ect-note">Show events in between a date range e.g( 2020-01-01 to 2020-02-15).</span>'
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
                            },
                        ],
                        onsubmit: function(e) {
                            editor.insertContent(
                                '[events-calendar-templates category="' + e.data.category + '" template="' + e.data.template + '" style="' + e.data.style + '" date_format="' + e.data.date_formats + '" start_date="' + e.data.start_date + '"  end_date="' + e.data.end_date + '" limit="' + e.data.limit + '" order="' + e.data.order + '" hide-venue="' + e.data.venue + '" socialshare="' + e.data.sharebutton + '" time="' + e.data.time + '"]'
                            );
                        }
                    });
                }
            }]
        });
    });
})();