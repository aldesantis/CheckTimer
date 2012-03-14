$(document).ready(function() {
    window['start_time'] = new Date();
    $("#timer").text("00:00:00.00");

    $("#timer").everyTime(10, function() {
        diff = (new Date()).getTime() - window['start_time'].getTime();

        seconds  = parseInt(diff / 1000);
        mseconds = diff % 1000;

        hours   = Math.floor(seconds / 3600);
        seconds = seconds % 3600;

        minutes = Math.floor(seconds / 60);
        seconds = seconds % 60;

        seconds  = seconds.toString();
        mseconds = mseconds.toString().substring(0, 2);
        hours    = hours.toString();
        minutes  = minutes.toString();

        if (seconds.length < 2) {
            seconds = "0" + seconds;
        }
        if (mseconds.length < 2) {
            mseconds = mseconds + "0";
        }
        if (hours.length < 2) {
            hours = "0" + hours;
        }
        if (minutes.length < 2) {
            minutes = "0" + minutes;
        }

        $(this).text(hours + ":" + minutes + ":" + seconds + "." + mseconds);
    });
});
