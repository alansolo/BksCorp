$(function() {
    console.log("ready!");
});

function GetInformacion(){
    console.log("Boton");

    $.ajax({
        type: "POST",
        url: "apiDolibarr.php",
        dataType: 'json',
        data: {functionname: 'add'},
        success: function(data)
        {
            alert(data);
        },
        error: function(error)
        {
            alert(error)
        }
    });
    //.done(function( msg ) {
    //    alert( "Data Saved: " + msg );
    //});
}