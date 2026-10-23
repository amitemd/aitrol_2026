function calcularEdad(fechaNacimiento)
{
    var valor = String(fechaNacimiento || '').trim();
    if (!valor) {
        return NaN;
    }

    var partes = valor.split(/[-\/]/);
    if (partes.length !== 3) {
        return NaN;
    }

    var anio;
    var mes;
    var dia;
    if (partes[0].length === 4) {
        anio = parseInt(partes[0], 10);
        mes = parseInt(partes[1], 10);
        dia = parseInt(partes[2], 10);
    } else {
        dia = parseInt(partes[0], 10);
        mes = parseInt(partes[1], 10);
        anio = parseInt(partes[2], 10);
    }

    if (!anio || !mes || !dia || mes < 1 || mes > 12 || dia < 1 || dia > 31) {
        return NaN;
    }

    var nacimiento = new Date(anio, mes - 1, dia);
    if (nacimiento.getFullYear() !== anio || nacimiento.getMonth() !== mes - 1 || nacimiento.getDate() !== dia) {
        return NaN;
    }

    var hoy = new Date();
    if (nacimiento > hoy) {
        return NaN;
    }

    var edadCalculada = hoy.getFullYear() - nacimiento.getFullYear();
    var aunNoCumple = hoy.getMonth() < nacimiento.getMonth() ||
        (hoy.getMonth() === nacimiento.getMonth() && hoy.getDate() < nacimiento.getDate());

    return aunNoCumple ? edadCalculada - 1 : edadCalculada;
}


function pais()
{
    
    var relacion = document.getElementById("parentesco").value;
    if (relacion == "Principal") {  
        var valor = document.getElementById("id_pais").value;
        $("#id_pais2").val(valor)}
      
}
function fecha(e)
{
    alert("hola");
    var relacion = document.getElementById("parentesco").value;
    if (relacion == "Principal") {  
        var valor = document.getElementById("fecha_nacimiento").value;
        $("#fecha_nacimiento2").val(valor)
        edad();
    }
      
}

function edad()
{
    
    var nacimiento = document.getElementById("fecha_nacimiento2").value;
    var edad = calcularEdad(nacimiento);
    if(isNaN(edad))
    {
        $("#Xedad").val('0');
    }
    else
    {

        $("#Xedad").val(edad);
    }
    $("#terceraedad").val("NO");
    if (edad>=18){
            if(edad>=65){
                $("#terceraedad").val("SI");
            }
       $("#tmenoredad").val("NO");
       $("#menoredad").val("0");
    }
    else{
        $("#tmenoredad").val("SI");
        $("#menoredad").val("1");
    }
       
          
}



function datos_edad()
{
    var nacimiento = document.getElementById("fecha_nacimiento").value;
    var edad = calcularEdad(nacimiento);
    
    if(isNaN(edad))
    {
        $("#Xedad").val('0');
    }
    else
    {

        $("#Xedad").val(edad);
    }
}

function edad2()
{
    
    var nacimiento = document.getElementById("fecha_nacimiento").value;
    var edad = calcularEdad(nacimiento);
    
    if(isNaN(edad))
    {
        $("#Xedad").val('');
        $("#tmenoredad").val('');
        $("#menoredad").val('0');
        return;
    }
    else
    {

        $("#Xedad").val(edad);
    }


    if (edad>=18){

       $("#tmenoredad").val("NO");
       $("#menoredad").val("0");
    }
    else{
        $("#tmenoredad").val("SI");
        $("#menoredad").val("1");
    }
       
          
}

function teclaEnter(e)

{

    tecla = (document.all) ? e.keyCode : e.which; 
    if (tecla==13) //13: enter
    {
        
        vcedula = document.getElementById("id").value;
        //pulsar boton
        //location.href ="{{ route('agenda.paciente2', ['id' => $id])}}/"+document.getElementById("id").value;
        

        if (vcedula != ""){

            location.href ="{{ route('agenda.paciente2', ['id' => $id])}}/"+vcedula;
        }


    }
}


