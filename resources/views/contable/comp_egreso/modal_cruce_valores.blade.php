<style type="text/css">
    .texto_derecha{
        text-align: right;
    }
</style>
@php
    use Sis_medico\Ct_Asientos_Cabecera;
    use Sis_medico\Ct_Comprobante_Egreso;
    $factura= Ct_Asientos_Cabecera::where('id',$id)->first();
    $comprobante= Ct_Comprobante_Egreso::where('id_cabecera',$id)->first();
@endphp

<div class="modal-content">
        <div class="modal-header">
                <label for="title" class="control-label col-md-11">CRUCE DE VALORES</label>
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
                            <label for="total_factura">Total Factura</label>
                            <input class="form-control" type="text" name="total_factura" id="total_factura" value="{{$factura->valor_nuevo}}" >
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="total_egreso">Total Comprobante Egresos</label>
                            <input class="form-control" type="text" name="total_c_egreso" id="total_c_egreso" value="@if(!is_null($comprobante)){{$comprobante->valor_pago}} @else No se ha creado Comprobante de Egresos @endif " >
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <label class="control-label">Valor contable</label>
                                <input class="form-control" name="valor_contable" id="valor_contable" type="text">
                            </div>

                        </div>
                        <div class="form-group" id="para_desabilitar" style="display: none;">
                           
                        </div>
                        <div class="form-group" id="para_caja" style="display: none;">
                        
                        </div>
                        <div class="form-group">
                           
                        </div>
                        <div class="form-group">
                            
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
            <a href="#" class="btn btn-danger">Crear</a>
            </div>
        </form>
</div>

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset ("/hospital/cleave/dist/cleave.min.js")}}"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>

<script type="text/javascript">

</script>