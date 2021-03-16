// firebase config for connecting to db
var firebaseConfig = {
    apiKey: "AIzaSyAqyOVfjaEZtejL81an0z2xECmBiHsuAQs",
    authDomain: "tina-project-fd72d.firebaseapp.com",
    databaseURL: "https://tina-project-fd72d-default-rtdb.firebaseio.com",
    projectId: "tina-project-fd72d",
    storageBucket: "tina-project-fd72d.appspot.com",
    messagingSenderId: "149757113177",
    appId: "1:149757113177:web:35a1a78a619dad200bd248",
    measurementId: "G-0W4SY5H786"
};
firebase.initializeApp(firebaseConfig);

$(document).ready(function (){
    // gauges get realtime data for firebase db in interval
    setInterval(function(){
        let illuminance, temperature, humidity;
        firebase.database().ref('realtime_data').on('value', function (snapshot){
            illuminance = snapshot.val().illuminance;
            temperature = snapshot.val().temperature;
            humidity = snapshot.val().humidity;
        });
        if (temperature !== undefined) {
            document.gauges[0].value = illuminance;
            if (Math.sign(temperature) === -1) {
                document.gauges[1].options.colorBarProgress = '#1EC8FF';
                document.gauges[1].options.colorBar = '#CAF2FF';
            } else {
                document.gauges[1].options.colorBarProgress = 'rgb(255,50,50)';
                document.gauges[1].options.colorBar = '#FFE8E8';
            }
            document.gauges[1].value = temperature;
            document.gauges[2].value = humidity;
        }
    }, 550);

    // realtime chart functions
    var illuminanceData = phpdata.map(value => value.illuminance);
    var temperatureData = phpdata.map(value => value.temperature);
    var humidityData = phpdata.map(value => value.humidity);
    var timestampData = phpdata.map(value => value.time_stamp);

    var trace1 = {
        x: timestampData,
        y: illuminanceData,
        type: 'scatter',
        name: 'Svietivosť',
        line: {
            color: '#FFDC1E'
        }
    };
    var trace2 = {
        x: timestampData,
        y: temperatureData,
        type: 'scatter',
        name: 'Teplota',
        line: {
            color: 'rgb(255,50,50)'
        }
    };
    var trace3 = {
        x: timestampData,
        y: humidityData,
        type: 'scatter',
        name: 'Vlhkosť',
        line: {
            color: 'rgba(4,197,246,.75)'
        }
    };
    var data = [trace1,trace2,trace3];
    Plotly.newPlot('chart', data);
    // realtime chart
    setInterval(function() {
        $.get( "database_php_operations/realtime_data.php", function( data ) {
            phpdata = $.parseJSON(data);
        });
        var illuminanceData = phpdata.map(value => value.illuminance);
        var temperatureData = phpdata.map(value => value.temperature);
        var humidityData = phpdata.map(value => value.humidity);
        var timestampData = phpdata.map(value => value.time_stamp);
        Plotly.extendTraces('chart', {
            x: [[timestampData[timestampData.length-1]], [timestampData[timestampData.length-1]], [timestampData[timestampData.length-1]]],
            y: [[illuminanceData[illuminanceData.length-1]], [temperatureData[temperatureData.length-1]], [humidityData[humidityData.length-1]]]
        }, [0, 1, 2]);
    }, 500);

    // insert sensors data to db
    $("#insert_to_db").prop('checked', settings.insert_to_db & true);
    $("#insert_to_db").click(function (){
        $.post( "database_php_operations/insert_db.php", { insert: this.checked } )
            .done(function( data ) {
                console.log( "Insert to db: " + data );
        });
    });

    // insert time to db
    console.log(settings);
    $("#interval_write").val(settings.insert_time_delay);
    $("#interval_write_help").text(sec2time(settings.insert_time_delay));
    $("#interval_write").on("change keyup",function (){
        if ($(this).val() >= 1) {
            $('#interval_write').removeClass('is-invalid').addClass('is-valid');
            $.post("database_php_operations/insert_delay.php", {delay: $(this).val()})
                .done(function (data) {
                    console.log("Insert to db: " + data);
                });
            $("#interval_write_help").text(sec2time($(this).val()));
        } else {
            $('#interval_write').removeClass('is-valid').addClass('is-invalid');
        }
    });
});

// Helper functions
function sec2time(timeInSeconds) {
    var pad = function(num, size) { return ('000' + num).slice(size * -1); },
        time = parseFloat(timeInSeconds).toFixed(3),
        hours = Math.floor(time / 60 / 60),
        minutes = Math.floor(time / 60) % 60,
        seconds = Math.floor(time - minutes * 60);

    return pad(hours, 2) + ':' + pad(minutes, 2) + ':' + pad(seconds, 2);
}