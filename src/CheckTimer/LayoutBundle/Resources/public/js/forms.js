$(document).ready(function() {
    $("td[colspan=2]").each(function() {
        if ($(this).text() != "") {
            $(this).parent().next().children(".error").first().text($(this).text());
            $(this).hide();
        }
    });

    var labels = [];
    $("label").each(function() {
        text = $(this).text();

        if (labels.indexOf($(this).text()) != -1) {
            $(this).text(text.substr(0, text.length - 1) + " (ripeti):");
        } else {
            labels.push(text);
        }
    });
});
