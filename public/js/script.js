/**
 * @package Next
 * @author Yury Ostapenko
 */

Handlebars.registerHelper('formateDate', function (date) {
    return moment(new Date(date + ' UTC')).format('MMMM Do YYYY, h:mm:ss a');
});

$(function () {
    var isRenderingNext = false;

    function renderNext() {
        if (isRenderingNext) {
            return;
        }
        isRenderingNext = true;
        if ($('#next-handlebars-container')[0]) {
            $.ajax({
                url: "/get/next",
                method: "GET",
                dataType: "json",
                success: function (data, textStatus, jqXHR) {
                    if (data.races) {
                        var source = $('#next-handlebars-template').html();
                        if (source) {
                            // Populate Handlebars template
                            var template = Handlebars.compile(source);
                            $('#next-handlebars-container').html(template(data));
                            $('#next-handlebars-container .nav-item:first .nav-link').click();

                            $('.js-countdown').each(function () {
                                var $this = $(this),
                                    finalDate = moment(new Date($this.text() + ' UTC')).format("YYYY/MM/DD HH:mm:ss");

                                $this.countdown(finalDate, {
                                    elapse: false
                                }).on('update.countdown', function (event) {
                                    $this.html(event.strftime('%-Nm %-Ss').replace(new RegExp("^0m"), ""));
                                }).on('finish.countdown', function () {
                                    // Re-render, when finished
                                    renderNext();
                                });
                            });
                            isRenderingNext = false;

                            if (!data.races.length) {
                                var interval = window.setInterval(function () {
                                    var seconds = $('.js-redirect-countdown').text();
                                    if (seconds == 1) {
                                        clearInterval(interval);
                                        window.location.href = $('.js-redirect-countdown').data("href");
                                    } else {
                                        $('.js-redirect-countdown').text(seconds - 1);
                                    }
                                }, 1000);
                            }
                        }
                    }
                }
            });
        }
    }

    // Render on load
    renderNext();

    $('#next-handlebars-container').on('click', '.nav-item .nav-link', function () {
        var $this = $(this),
            href = $this.attr('href'),
            race_id = href.substring(5)

        $.ajax({
            url: "/get/race/" + race_id,
            method: "GET",
            dataType: "json",
            success: function (data, textStatus, jqXHR) {
                var source = $('#race-handlebars-template').html();
                if (source) {
                    var template = Handlebars.compile(source);
                    $(href).html(template(data));
                }
            }
        });

        $this.tab('show');
        return false;
    });


});