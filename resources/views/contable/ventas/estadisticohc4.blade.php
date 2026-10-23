<style>
    g shapering-rendering {
        display: none !important;
    }
</style>
<script src="https://cdn.amcharts.com/lib/4/core.js"></script>
<script src="https://cdn.amcharts.com/lib/4/charts.js"></script>
<script src="https://cdn.amcharts.com/lib/4/themes/animated.js"></script>
<section class="content">
    <div class="box">
        <div class="box header">
            <h3> Estadisticos Factura de Venta</h3>
        </div>
        <!-- <div class="col-md-12" style="margin-top: 10px;">
            <label>SELECCIONE EMPRESA</label>
        </div> -->
        @php
        if(is_null($id_empresa)){
        $id_empresa= "0992704152001";
        }

        @endphp
        <!-- <div class="col-md-12" style="margin-top: 10px;">
            <div class="row">
                <div class="col-md-6">
                    <select class="form-control " name="id_empresa" onchange="changeGraphics2(this.value)" id="empresa">
                        <option value="">Seleccione</option>
                        @foreach($empresas as $value)
                        @if($value->id=='0992704152001' || $value->id=='0993075000001')
                        <option @if($value->id==$id_empresa) selected='selected' @endif value="{{$value->id}}">{{$value->nombrecomercial}}</option>
                        @endif
                        @endforeach
                    </select>
                </div>
            </div>
        </div> -->
        <div id="graphics">
        </div>
    </div>
    </form>
    </div>
    <script>
        $(document).ready(function() {


            changeGraphics2('{{$id_empresa}}');

        });

        function changeGraphics2(id_empresa) {
            $.ajax({
                type: "get",
                url: "{{route('venta.graphics2')}}",
                data: {
                    'id_empresa': id_empresa,
                },
                datatype: "html",
                success: function(datahtml, data) {
                    console.log(data);
                    $("#graphics").html(datahtml);
                },
                error: function() {
                    alert('error al cargar');
                }
            });
        }
    </script>
</section>