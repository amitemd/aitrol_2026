function primer_paso(){
    //alert('data');
    //hospital.primerpaso
    var id_orden="1";
    console.log("aqui");
    $.ajax({
        type: "get",
        url: "{{route('hospital.primerpaso')}}",
        data: {
            'ep': id_orden,
        },
        datatype: "html",
        success: function(datahtml, data) {
            
            $("#content").html(datahtml);
        },
        error: function() {
            alert('error al cargar');
        }
    });
}
function segundo_paso(){
    var id_orden="1";
    $.ajax({
        type: "get",
        url: "{{route('retenciones_modal_retenciones')}}",
        data: {
            'ep': id_orden,
        },
        datatype: "html",
        success: function(datahtml, data) {
            console.log(data);
            $("#content").html(datahtml);
        },
        error: function() {
            alert('error al cargar');
        }
    });
}
function tercer_paso(){
    var id_orden="1";
    $.ajax({
        type: "get",
        url: "{{route('retenciones_modal_retenciones')}}",
        data: {
            'ep': id_orden,
        },
        datatype: "html",
        success: function(datahtml, data) {
            console.log(data);
            $("#content").html(datahtml);
        },
        error: function() {
            alert('error al cargar');
        }
    });
}