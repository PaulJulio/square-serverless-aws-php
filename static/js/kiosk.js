$(document).ready(function(){
    console.log('kiosk starting');
    $('#menuitems').html('<p>Loading menu items...</p>');
    $.ajax({
        type: "GET",
        url: "./inventory",
        success: function(data)
        {
            console.log(data);
        }
    });
});