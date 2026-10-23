<style type="text/css">
    .texto_derecha{
        text-align: right;
    }
</style>
@php
    use Sis_medico\Ct_Asientos_Cabecera;
    use Sis_medico\Ct_compras;
    $factura= Ct_compras::where('id',$id)->first();
    $asiento= Ct_Asientos_Cabecera::where('id',$factura->id_asiento_cabecera)->first();

    //dd($asiento);
@endphp

<div class="modal-content">
        <div class="modal-header">
                <label for="title" class="control-label col-md-11">DEVOLUCIÓN DE COMPRA</label>
                <button type="button" class="close texto_derecha" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
        </div>
        <form id="guardar_datos()" enctype="multipart/form-data" method="POST">
            {{ csrf_field() }} 
            <div class="modal-body">
                <div class="form-group col-md-12">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="total_factura">Factura N°</label>
                            <input class="form-control" type="text" name="factura" id="factura" value="@if($asiento!=null){{$asiento->fact_numero}} @endif"readonly >
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="total_egreso">Total Factura: </label>
                            <input class="form-control" type="text" name="total_c_egreso" id="total_c_egreso" value="@if($asiento!=null){{$asiento->valor}} @endif" readonly>
                            <input type="hidden" name="id_compra" id="id_compra">
                        </div>
                        <div class="form-group">
                            <label for="empresa">Empresa</label>
                            <input class="form-control" type="text" name="empresa" id="empresa" value="@if($factura!=null){{$factura->id_empresa}} @endif"readonly>
                        </div>
                        <div class="form-group">
                                <label class="control-label">Concepto: </label>
                                <input class="form-control" name="concepto" id="concepto" type="text" value="@if($asiento!=null){{$asiento->observacion}} @endif"readonly >
                        </div>
                        <div class="form-group">
                            <label for="" class="control-label">Nuevo concepto</label>
                            <textarea class="form-control" name="concepto_nuevo" id="concepto_nuevo" cols="3" rows="3"></textarea>
                        </div>
                        
                    </div>
                </div>
            </div>
            <div class="modal-footer">
            <a href="#" class="btn btn-danger">CREAR DEVOLUCIÓN</a>
            </div>
        </form>
</div>

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset ("/hospital/cleave/dist/cleave.min.js")}}"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>

<script type="text/javascript">
    function name() {
        //aqui va el ajax
    }
</script>