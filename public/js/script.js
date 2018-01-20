$(function () {
    if ($('#next-handlebars-container')[0]) {
        $.ajax({
            url: "/get/next",
            method: "GET",
            dataType: "json",
            success: function (data, textStatus, jqXHR) {
                console.warn(data);
                var source = $('#next-handlebars-template').html();
                console.warn(source);
                console.error(Handlebars)
                if (source) {
                    var template = Handlebars.compile(source);
                    console.warn({
                        races: data
                    });
                    console.warn($('#next-handlebars-container'));
                    $('#next-handlebars-container').html(template({
                        races: data
                    }));
                }
            }
        });
    }
});