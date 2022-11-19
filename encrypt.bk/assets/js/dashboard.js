$(document).ready(function () {

    // Bar Graph Data initialize here, Change the detials if needed.
    var Bardata = {
        labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul"],
        datasets: [
            {
                fillColor: "#f59690",
                strokeColor: "#b51718",
                barStrokeWidth: 8,
                highlightFill: "rgba(220,220,220,0.75)",
                highlightStroke: "rgba(220,220,220,1)",
                data: [65, 59, 80, 81, 56, 55, 40]
            }]
    };


    //  Line chart Graph data initialize here.
    var Linedata = {
        labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul"],
        datasets: [
            {
                label: "My First dataset",
                fillColor: "#40cab0",
                strokeColor: "rgba(220,220,220,1)",
                pointColor: "#c8e4e1",
                pointStrokeColor: "#23a38a",
                pointHighlightFill: "#fff",
                pointHighlightStroke: "rgba(220,220,220,1)",
                bezierCurveTension: 0.8,
                data: [65, 59, 80, 81, 56, 55, 40]
            }]
    };

    /*
    $(window).on("load", () =>  {

        //  Line chart initialise and calling with various options
        var ctx = document.getElementById('lineChart').getContext('2d');
        var LineChart = new Chart(ctx).Line(Linedata, {
            responsive: false,
            scaleLineColor: 'transparent',
            scaleShowLabels: false,
            scaleShowGridLines: false
        });

        //  Bar chart initialise and calling with various options
        var ctx = document.getElementById('barChart').getContext('2d');
        var BarChart = new Chart(ctx).Bar(Bardata, {
            responsive: false,
            scaleShowGridLines: false,
            scaleLineColor: 'transparent',
            scaleShowLabels: false,
            scaleBeginAtZero: true,
            barValueSpacing: 15
        });
    });
    */

});





