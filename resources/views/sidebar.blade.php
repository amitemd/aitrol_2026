<?php
$imagen = Auth::user()->imagen_url;
if ($imagen == ' ') {
    $imagen = 'avatar.jpg';
}
$rolUsuario = Auth::user()->id_tipo_usuario;
$id_auth    = Auth::user()->id;
?>

<style type="text/css">
.sidebar, aside.main-sidebar {
    /*background: url({{asset('/imagenes')}}/index-top-bg.png) repeat-x scroll 0 0 transparent !important;*/
  }
</style>
@php
      $sidebar = $_SERVER["REQUEST_URI"];
@endphp
</style>

  <!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar" >

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

      <!-- Sidebar user panel (optional) -->
      <div class="user-panel">
        <div class="pull-left image">
          <img src="{{asset('/avatars').'/'.$imagen}}" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
          <p>{{ Auth::user()->name}}</p>
          <!-- Status -->
          <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
        </div>
      </div>

      <!-- search form (Optional) -->
      <!--form action="#" method="get" class="sidebar-form">
        <div class="input-group">
          <input type="text" name="q" class="form-control" placeholder="Search...">
              <span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
        </div>
      </form-->
      <!-- /.search form -->

      <!-- Sidebar Menu -->
      <ul class="sidebar-menu" >
        <!-- Optionally, you can add icons to the links -->
        <!--<li class="active"><a href="/"><i class="fa fa-link"></i> <span>Dashboard</span></a></li>-->


        @if(in_array($rolUsuario, array(1, 4, 5, 20)) == true)
        <li><a href="{{ route('disponibilidad.disponibilidad_menu') }}" ><i class="fa fa-calendar"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span>Disponibilidad</span></a></li>
        @endif
        
        @if(in_array($rolUsuario, array(1, 4, 12, 22)) == true)
        <li><a href="{{ route('produccion.produccion_estad') }}" ><i class="fa fa-user-md"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span>Producción de Médicos</span></a></li>
        @endif

        <li><a href="{{ route('paciente.historial_orden_lab_paciente') }}" ><i class="fa fa-user-md"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span>@if(in_array($rolUsuario, array(2)) == false) Tu @endif Historial de Examenes </span></a></li>

        @if(in_array($rolUsuario, array(2)) == false)
        <li><a href="{{ route('historial.rol') }}" ><i class="fa fa-file-text"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span>Tus Roles de Pago</span></a></li>
        @endif

        @if(in_array($rolUsuario, array(2)) == true)
        <li><a href="{{ route('paciente.historial_examenes') }}" ><i class="fa fa-book"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span>Historial de Ordenes </span></a></li>
        @endif

        @if(in_array($rolUsuario, array(2)) == true)
        <li><a href="{{ route('recetas_usuario') }}" ><i class="fa fa-file-text-o" aria-hidden="true"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span>Historial de Receta </span></a></li>
        @endif

        @if(in_array($rolUsuario, array(9, 1, 7)) == true)
        <li><a href="{{ route('enfermeria.index_insumos') }}" ><i class="glyphicon glyphicon-plus-sign"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span>Anestesiologos </span></a></li>
        @endif
        @if(in_array($rolUsuario, array(1,20, 22)) == true)
         <li class="treeview @if(strrpos($sidebar, 'contable')) active @endif">

          <a href="#"><i class="glyphicon glyphicon-book"></i> <span>Contable</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class="treeview @if(strrpos($sidebar, 'contable/empresa')) active @endif">
              <a href="#"> <i class="fa fa-building"> </i>Empresa
                <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-rigth"></i>
                </span>
              </a>
              <ul class="treeview-menu">
                <li class="treeview @if(strrpos($sidebar, 'empresa/seleccion')) active @endif">
                   <a href="{{ route('plan_cuentas.seleccion_empresa') }}"></i>Seleccion Empresa</a>
                </li>
              </ul>
            </li>

            <li class="treeview @if(strrpos($sidebar, 'contable/contabilidad')) active @endif">
              <a href="#"> <i class="fa fa-university"> </i>Contabilidad
                <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-rigth"></i>
                </span>
              </a>
              <ul class="treeview-menu">
                <li class="treeview @if(strrpos($sidebar, 'plan_cuentas')) active @endif">
                  <a href="{{ route('plan_cuentas.index') }}"></i>Plan de Cuentas</a>
                </li>
                <li class="treeview @if(strrpos($sidebar, 'libro_mayor')) active @endif">
                  <a href="{{ route('libro_mayor.index') }}"></i>Libro Mayor</a>
                </li>
                <li class="treeview @if(strrpos($sidebar, 'contabilidad/balance_general')) active @endif" ><a href="{{ route('balance_general.index') }}"></i>Balance General</a></li>
                <li class="treeview @if(strrpos($sidebar, 'contabilidad/estado/resultados')) active @endif"><a href="{{ route('estadoresultados.index') }}"></i>Estado de Resultado Integral</a></li>
                <li class="treeview @if(strrpos($sidebar, 'contabilidad/balance_comprobacion')) active @endif" ><a href="{{ route('balance_comprobacion.index') }}"></i>Balance de Comprobacion</a></li>
                <li class="treeview @if(strrpos($sidebar, 'contabilidad/libro')) active @endif" ><a href="{{ route('librodiario.index') }}"></i>Visualizar Asientos Diario</a></li>
                {{-- <li><a href="{{ route('compras.informe') }}"></i>Informe Compras</a></li> --}}
                <li class="treeview @if(strrpos($sidebar, 'contabilidad/flujo/efectivo_1')) active @endif" ><a href="{{ route('flujoefectivo.index') }}"></i>Flujo de efectivo</a></li>
                <li class="treeview @if(strrpos($sidebar, 'contabilidad/flujo/efectivo/comparativo_1')) active @endif" ><a href="{{ route('flujoefectivocomparativo.index') }}"></i>Flujo de efectivo comparativo</a></li>
                <li class="treeview @if(strrpos($sidebar, 'contabilidad/flujo/efectivo/comparativo/dos')) active @endif" ><a href="{{ route('flujoefectivocomparativo.index2') }}"></i>Flujo de efectivo comparativo II</a></li>
                <li class="treeview @if(strrpos($sidebar, 'contabilidad/ats')) active @endif" ><a href="{{ route('ats.index') }}"></i>SRI / ATS</a></li>
              </ul>
            </li>

            <li class="treeview @if(strrpos($sidebar, 'acreedores')) active @endif">
              <a href="#"> <i class="fa  fa-users"> </i>Acreedores
                <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-rigth"></i>
                </span>
              </a>
                <ul class="treeview-menu">
                  <li class="treeview @if(strrpos($sidebar, 'acreedores/mantenimiento')) active @endif">
                    <a href="#"> <i class="fa fa-bars"> </i>1 Mantenimiento
                      <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-rigth"></i>
                      </span>
                    </a>
                    <ul class="treeview-menu">
                      <li class="treeview @if(strrpos($sidebar, 'acreedores/mantenimiento/rubrosa')) active @endif"><a href="{{ route('rubrosa.index') }}"><i class="fa fa-gear"></i>Rubros Acreedores</a></li>
                      <li class="treeview @if(strrpos($sidebar, 'acreedores/mantenimiento/acreedor')) active @endif"><a href="{{ route('acreedores_index') }}"><i class="fa fa-gear"></i>Acreedores</a></li>
                      <li class="treeview @if(strrpos($sidebar, 'acreedores/mantenimiento/acreedor')) active @endif"><a href="{{ route('saldosinicialesp.index2') }}"> Saldos Iniciales</a></li>

                    </ul>

                  </li>
                </ul>
                <ul class="treeview-menu">
                  <li class="treeview @if(strrpos($sidebar, 'acreedores/documentos')) active @endif">
                    <a href="#"> <i class="fa  fa-file-text"> </i>2 Documentos
                      <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-rigth"></i>
                      </span>
                    </a>
                    <ul class="treeview-menu">
                      <li class="treeview">
                        <li class="treeview @if(strrpos($sidebar, 'acreedores/documentos/retenciones')) active @endif"><a href="{{ route('retenciones_index') }}"><i class="fa fa-calculator"></i>Retenciones</a></li>
                        <li class="treeview @if(strrpos($sidebar, 'acreedores/documentos/comprobante/egreso')) active @endif"><a href="{{ route('acreedores_cegreso') }}"><i class="fa  fa-file-text"></i>Comprobante Egreso</a></li>
                        <li class="treeview @if(strrpos($sidebar, 'acreedores/documentos/comprobante/index')) active @endif"><a href="{{ route('comp_egreso_masivo.index') }}"><i class="fa  fa-file-text"></i>Comprobante Egreso M</a></li>
                        <li class="treeview @if(strrpos($sidebar, 'acreedores/documentos/cuentas/egreso/varios')) active @endif"><a href="{{ route('egresosv.index') }}"><i class="fa  fa-file-text"></i> Comprobante Egresos V</a></li>
                        <li><a href="{{ route('cruce.index') }}"><i class="fa fa-exchange"></i>Cruce de Valores</a></li>
                        <li class="treeview @if(strrpos($sidebar, 'contable/cruce_cuentas/valores')) active @endif"><a href="{{ route('pr.cruce_cuentas') }}"><i class="fa  fa-file-text"></i> Cruce Cuentas</a></li>
                        <li><a href="{{ route('creditoacreedores.index') }}"><i class="fa fa-clone"></i>Nota de Crédito</a></li>
                        <li><a href="{{ route('debitoacreedores.index') }}"><i class="fa fa-file-o"></i>Nota de Débito</a></li>

                      </li>
                    </ul>

                  </li>
                </ul>
                <ul class="treeview-menu">
                  <li class="treeview @if(strrpos($sidebar, 'acreedores/informes')) active @endif">
                    <a href="#"> <i class="fa fa-pie-chart"> </i>3 Informes
                      <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-rigth"></i>
                      </span>
                    </a>
                    <ul class="treeview-menu">
                      <li class="treeview @if(strrpos($sidebar, 'acreedores/informes/cartera/pagar')) active @endif">
                        <a href="{{ route('carterap.index') }}"><i class="fa fa-file-excel-o"> </i>Carteras por Pagar</a>
                      </li>
                      <li class="treeview @if(strrpos($sidebar, 'acreedores/informes/informe/saldos')) active @endif">
                        <a href="{{ route('saldos_acreedor.index') }}"><i class="fa fa-file-excel-o"></i>Informe de Saldos</a>
                      </li>
                      <li><a href="{{ route('chequesa.index') }}"><i class="fa fa-file-excel-o"></i>Informe de Cheques Girados</a></li>
                      <li><a href="{{ route('informe_retenciones.index') }}"><i class="fa fa-file-excel-o"></i>Informe Retenciones</a></li>
                      <li><a href="{{ route('deudasvspagos.index') }}"><i class="fa fa-file-excel-o"></i>Deudas vs Pagos</a></li>
                      <li><a href="{{ route('deudas_pendientes.index') }}"><i class="fa fa-file-excel-o"></i>Deudas Pendientes</a></li>
                    </ul>

                  </li>
                </ul>
            </li>

            <li class="treeview">
              <a href="#"> <i class="fa  fa-users"> </i>Clientes
                <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-rigth"></i>
                </span>
              </a>
                <ul class="treeview-menu">
                  <li class="treeview">
                    <a href="#"> <i class="fa fa-bars"> </i>1 Mantenimiento
                      <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-rigth"></i>
                      </span>
                    </a>
                    <ul class="treeview-menu">
                      <li class="treeview">
                        <li><a href="{{ route('clientes.index') }}"></i>Crear Cliente</a></li>
                        <li><a href="{{ route('rubros_cliente.index') }}"></i>Rubros Cliente</a></li>
                        <li><a href="{{ route('saldosinicialesclientes.index2') }}"></i>Saldos Iniciales</a></li>
                      </li>
                    </ul>
                  </li>
                </ul>
                <ul class="treeview-menu">
                  <li class="treeview">
                    <a href="#"> <i class="fa  fa-file-text"> </i>2 Documentos
                      <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-rigth"></i>
                      </span>
                    </a>

                    <ul class="treeview-menu">
                      <li class="treeview">
                        <li><a href="{{route('retencion.cliente')}}"><i class="fa fa-calculator"></i>Retenciones</a></li>
                        <li><a href="{{route('chequespost.index')}}"><i class="fa fa-files-o"></i>Cheques PostFechados</a></li>
                        <li><a href="{{route('cr.cruce_cuentas')}}"><i class="fa fa-files-o"></i>Cruce Cuentas</a></li>
                        <li><a href="{{route('comprobante_ingreso.index')}}"><i class="fa  fa-file-text"></i>Comprobante Ingreso</a></li>
                        <li><a href="{{route('comprobante_ingreso_v.index')}}"><i class="fa  fa-file-text"></i> Comprobante Ingreso V</a></li>
                        <li><a href="{{route('cruce_clientes.index')}}"><i class="fa fa-exchange"></i>Cruce de Valores</a></li>
                        <li><a href="{{route('nota_credito_cliente.index')}}"><i class="fa fa-clone"></i>Nota de Crédito</a></li>
                        <li><a href="{{route('nota_cliente_debito.index')}}"><i class="fa fa-file-o"></i>Nota de Débito</a></li>

                      </li>
                    </ul>

                  </li>
                </ul>
                <ul class="treeview-menu">
                  <li class="treeview">
                    <a href="#"> <i class="fa fa-pie-chart"> </i>3 Informes
                      <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-rigth"></i>
                      </span>
                    </a>
                    <ul class="treeview-menu">
                      <li class="treeview">
                        <li><a href="{{route('clientes.deudas.pendientes')}}"><i class="fa fa-file-excel-o"></i>Deudas Pendientes</a></li>
                        <li><a href="{{route('clientes.saldo.cxc')}}"><i class="fa fa-file-excel-o"></i>Saldo de Cuentas por Cobrar</a></li>
                        <li><a href="{{route('cliente.informe.retenciones')}}"><i class="fa fa-file-excel-o"></i>Informe Retenciones</a></li>
                        {{-- <li><a href="#"><i class="fa fa-file-excel-o"> </i>Carteras por Pagar</a></li>
                        <li><a href="#"><i class="fa fa-file-excel-o"></i>Informe de Saldos</a></li>
                        <li><a href="#"><i class="fa fa-file-excel-o"></i>Informe de Cheques Girados</a></li>
                        <li><a href="#"><i class="fa fa-file-excel-o"></i>Informe Retenciones</a></li> --}}
                        <li><a href="{{route('deudasvspagos.cliente')}}"><i class="fa fa-file-excel-o"></i>Deudas vs Pagos</a></li>
                      </li>
                    </ul>

                  </li>
                </ul>
            </li>

            <li class="treeview @if(strrpos($sidebar, 'Banco')) active @endif">
              {{-- <a href="#"> <i class="fa fa-cubes"> </i>Caja y Bancos --}}
              <a href="#"> <i class="fa fa-money"> </i>Caja y Bancos
                <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-rigth"></i>
                </span>
              </a>
              <ul class="treeview-menu">
                <li class="treeview " ><a href="{{ route('caja_banco.index') }}"></i>Crear Caja Banco</a></li>
                <li><a href="{{ route('banco_clientes.index') }}"></i>Listado de Bancos</a></li>
                <li class="treeview @if(strrpos($sidebar, 'Banco/notadebito')) active @endif" ><a href="{{ route('notadebito.index') }}"></<i>Nota de Debito</a></li>
                <li class="treeview" ><a href="{{ route('notacredito.index') }}"></i>Nota de Credito</a></li>
                <li class="treeview" ><a href="{{ route('debitobancario.index') }}"></i>Nota de Debito Bancaria</a></li>
                <li class="treeview" ><a href="{{ route('depositobancario.index') }}"></i>Déposito Bancario</a></li>
                <li class="treeview" ><a href="{{ route('transferenciabancaria.index') }}"></i>Transferencia Bancaria</a></li>
                <li class="treeview" ><a href="{{ route('conciliacionbancaria.index') }}"></i>Conciliaci&oacute;n Bancaria </a></li>
                <li class="treeview" ><a href="{{ route('estadocuentabancos.index') }}"></i>Estado de Cuenta </a></li>
              </ul>
            </li>

            <li class="treeview">
              <a href="#">
                <i class="fa fa-share"></i> <span>Activos Fijos</span>
                <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
                </span>
              </a>
              <ul class="treeview-menu">
                <li class="treeview">
                  <a href="#"><i class="fa fa-circle-o"></i> Mantenimientos
                    <span class="pull-right-container">
                      <i class="fa fa-angle-left pull-right"></i>
                    </span>
                  </a>
                  <ul class="treeview-menu">
                    <li><a href="{{ route('afGrupo.index') }}"> Grupos</a></li>
                    <li><a href="{{ route('afTipo.index') }}"> Tipos</a></li>
                    <li><a href="{{ route('afActivo.index') }}"> Activos</a></li>
                  </ul>
                </li>
                <li class="treeview">
                  <a href="#"><i class="fa fa-circle-o"></i> Documentos
                    <span class="pull-right-container">
                      <i class="fa fa-angle-left pull-right"></i>
                    </span>
                  </a>
                  <ul class="treeview-menu">
                    <li><a href="{{ route('afDocumentoFactura.index') }}"> Facturas</a></li>
                    <li><a href="{{ route('afDepreciacion.index') }}"> Depreciaciones</a></li>
                  </ul>
                </li>
                <li class="treeview">
                  <a href="#"><i class="fa fa-circle-o"></i> Informes
                    <span class="pull-right-container">
                      <i class="fa fa-angle-left pull-right"></i>
                    </span>
                  </a>
                  <ul class="treeview-menu">
                    <li><a href="{{ route('afInformes.index') }}"> Saldos</a></li>
                    {{-- <li><a href="#"> Retenci&oacute;n</a></li> --}}
                    {{-- <li><a href="#"> Facturas</a></li> --}}
                  </ul>
                </li>
              </ul>
            </li>

            <li class="treeview">
              <a href="#"> <i class="fa fa-truck"> </i>Compras
                <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-rigth"></i>
                </span>
              </a>
              <ul class="treeview-menu">
                <li class="treeview">
                 <li><a href="{{ route('compras_index') }}"></i>Compras</a></li>
                 <li><a href="{{ route('saldosinicialesp.index2') }}"></i>Saldos Iniciales</a></li>
                 <li><a href="{{ route('compras.pedidos') }}"></i>Pedidos</a></li>
                 <li><a href="{{ route('fact_contable_index') }}"></i>Factura Contable</a></li>
                 <li><a href="{{ route('compras.informe') }}"><i class="fa fa-file-excel-o"></i>Informe Compras</a></li>
                 <!-- <li><a href="{{ route('kardex.index') }}"></i>Kardex</a></li>-->
                </li>
              </ul>
            </li>

            <li class="treeview">
              <a href="#"> <i class="fa fa-cubes"> </i>Inventario
                <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-rigth"></i>
                </span>
              </a>
              <ul class="treeview-menu">
                <li class="treeview">
                 <li><a href="{{ route('kardex.index') }}"></i>Kardex</a></li>
                 <li><a href="{{ route('notainventario.index') }}"></i>Nota de Ingreso Inventario</a></li>
                 <li><a href="{{ route('productos_servicios_index') }}"></i>Productos</a></li>
                </li>
              </ul>
            </li>

            <li class="treeview">
              <a href="#"> <i class="ion-person-stalker"> </i>Nomina
                <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-rigth"></i>
                </span>
              </a>
              <ul class="treeview-menu">
                <li class="treeview">
                 <li><a href="{{ route('config_valor.index') }}"></i>Configuracion</a></li>
                 <li><a href="{{ route('nomina.index') }}"></i>Crear Empleado / Rol</a></li>
                 <li><a href="{{ route('buscador_rol.index') }}"></i>Roles de Pago Individual</a></li>
                 <li><a href="{{ route('prestamos_empleado.index') }}"></i>Prestamos Empleados</a></li>
                 <!--<li><a href="{{ route('anticipos_empleado.index') }}"></i>Anticipo 1Ra Quinc Empl</a></li>-->
                 <li><a href="{{ route('nomina_anticipos_1eraquincena.index') }}"></i>Anticipo 1Ra Quinc Empl</a></li>
                </li>
              </ul>
              <ul class="treeview-menu">
                <li class="treeview">
                  <a href="#"> <i class="fa fa-file-excel-o"> </i>Informes
                    <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-rigth"></i>
                    </span>
                  </a>
                  <ul class="treeview-menu">
                    <li class="treeview">
                    <!--<li><a href="{{ route('nomina_anticipos.index') }}"><i class="fa fa-file-excel-o"> </i>Anticipo Quincena</a></li>-->
                    <li><a href="{{ route('reportes_empl.index') }}"><i class="fa fa-file-excel-o"> </i>Datos Empleados</a></li>
                    <li><a href="{{ route('reportes_rol.index') }}"><i class="fa fa-file-excel-o"> </i>Rol Pagos</a></li>
                    <li><a href="{{ route('reportes_banco.index') }}"><i class="fa fa-file-excel-o"> </i>Archivo Banco</a></li>
                    </li>
                  </ul>
                </li>
              </ul>
            </li>

            <li class="treeview">
              <a href="#"> <i class="fa fa-line-chart"> </i>Flujos de Caja
                <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-rigth"></i>
                </span>
              </a>
              <ul class="treeview-menu">
                <li class="treeview">
                   <li><a href="{{ route('flujoefectivo.index') }}"></i>Flujo Efectivo</a></li>
                   <li><a href="{{ route('flujoefectivocomparativo.index') }}"></i>Flujo Efectivo Comparativo</a></li>
                   <li><a href="{{ route('estructuraflujoefectivo.index') }}"></i>Estructura Flujo Efectivo</a></li>
                </li>
              </ul>
            </li>

            <li class="treeview">
              <a href="#"> <i class="fa  fa-shopping-cart"> </i>Ventas
                <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-rigth"></i>
                </span>
              </a>

              <ul class="treeview-menu">
                <li class="treeview">
                 <li><a href="{{ route('venta_index') }}"></i>Factura de Venta</a></li>
                 <li><a href="{{ route('entregas_mercaderia.index') }}"><i class="fa fa-truck"></i> Entregas de mercadería</a></li>
                </li>
              </ul>
              <ul class="treeview-menu">
                <li class="treeview">
                 <li><a href="{{ route('ventas.index_cierre') }}"></i>Factura de Caja</a></li>
                </li>
              </ul>
              
              <ul class="treeview-menu">
                <li class="treeview">
                 <li><a href="{{ route('orden_venta') }}"></i>Ordenes de Venta</a></li>
                </li>
              </ul>
              <ul class="treeview-menu">
                <li class="treeview">
                 <li><a href="{{ route('factura_convenios.index') }}"></i>Factura Convenios</a></li>
                </li>
              </ul>
              <ul class="treeview-menu">
                <li class="treeview">
                 <li><a href="{{ route('venta_index2') }}"></i>Facturas Conglomerada</a></li>
                </li>
              </ul>
              <ul class="treeview-menu">
                <li class="treeview">
                 <li><a href="{{ route('ventas.omni') }}"></i>Facturación Omni</a></li>
                </li>
              </ul>
              <ul class="treeview-menu">
                <li class="treeview">
                <li><a href="{{ route('venta.informe_ventas') }}"><i class="fa fa-file-excel-o"></i>Informe Ventas</a></li>
                </li>
              </ul>
            </li>
            <li class="treeview">
              <a href="#"> <i class="fa fa-cogs"> </i>Mantenimiento
                <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-rigth"></i>
                </span>
              </a>
              <ul class="treeview-menu">
                <li class="treeview">
                 <li><a href="{{ route('establecimiento.index') }}"></i>Sucursales</a></li>
                 <li><a href="{{ route('punto_emision.index') }}"></i>Punto de Emision</a></li>
                 <li><a href="{{ route('empleados.index') }}"></i>Asignar Recaudador P-E</a></li>
                 <li><a href="{{ route('divisas.index')}}"></i>Divisas</a></li>
                 <li><a href="{{ route('tipo_pago.index') }}"></i>Tipo de Pago</a></li>
                 <li><a href="{{ route('tipo_tarjeta.index') }}"></i>Tipo de Tarjeta</a></li>
                 <li><a href="{{ route('bodegas.index') }}"></i>Bodegas</a></li>
                 <!--<li><a href="#"></i>Bancos</a></li>
                 <li><a href="#"></i>Ciudad</a></li>-->
                 <!--<li><a href="{{ route('caja_banco.index') }}"></i>Cajas y Bancos</a></li>-->
                 <li><a href="{{ route('tipo_emision.index') }}"></i>Tipo de Emision</a></li>
                 <li><a href="{{ route('tipo_comprobante.index') }}"></i>Tipo de Comprobante</a></li>
                 <li><a href="{{ route('tipo_ambiente.index') }}"></i>Tipo de Ambiente</a></li>
                 <li><a href="{{ route('porcentaje_imp_renta.index') }}"></i>Porcentaje IR</a></li>
                 <li><a href="{{ route('retenciones.index') }}"></i>Retenciones</a></li>
                </li>
              </ul>
            </li>

            <li class="treeview">
              <a href="#"> <i class="fa fa-cog"> </i>Configuraciones
                <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-rigth"></i>
                </span>
              </a>
              <ul class="treeview-menu">
                <li class="treeview">
                  <li><a href="{{ route('configuraciones.index') }}"></i>Plan de Cuenta</a></li>
                  <li><a href="{{ route('productos.productos_tarifario') }}"></i>Producto Tarifario</a></li>
                  <li><a href="{{ route('configuraciones_pdf_index') }}"></i>Configuraciones Pdf</a></li>
                </li>
              </ul>
            </li>
          </ul>
         </li>
        @endif
        @if(in_array($rolUsuario, array(1)) == true)
        <li><a href="{{ route('user-management.index') }}"><i class="fa fa-user-md"></i> <span>Administración de Usuarios</span></a></li>
        @endif
        @if(in_array($rolUsuario, array(1, 4, 5, 20)) == true)
        <li><a href="{{ url('agenda') }}" ><i class="fa fa-calendar"></i> <span>Agenda</span></a></li>
        @endif
        @if(in_array($rolUsuario, array(1, 4, 5, 11, 20)) == true)
        <li><a href="{{ url('paciente') }}" ><i class="fa fa-fw fa-users"></i> <span>Pacientes</span></a></li>
        @endif
        @if(in_array($rolUsuario, array(1, 4, 5, 20)) == true)
        <li><a href="{{ route('biopsias_paciente.index') }}" ><i class="fa fa-file-text"></i> <span>Ingreso Masivo de Biopsias</span></a></li>
        @endif
        @if(in_array($rolUsuario, array(4, 5, 20)) == true )
        <li><a href="{{ route('orden.index') }}" ><i class="ionicons ion-ios-flask"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span>Laboratorio - Ordenes </span></a></li>
        @endif
        @if(in_array($rolUsuario, array(1, 4, 5, 20)) == true )
        <li>
          <a href="{{ route('observacion.index') }}" ><i class="glyphicon glyphicon-copy"></i>
            <span>Observaciones </span>
            <span class="pull-right-container">
              <small class="label pull-right bg-red" id="o_cantidad"></small>
            </span>
          </a>
        </li>
        @endif
        @if(in_array($rolUsuario, array(10)) == true)
        <li><a href="{{ route('orden.index_control') }}" ><i class="ionicons ion-ios-flask"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span>Laboratorio - Control </span></a></li>
        <li><a href="{{ route('agendalabs.agenda') }}" ><i class="fa fa-calendar"></i>&nbsp;&nbsp;<span>Agenda </span></a></li>
        @endif



        @if(in_array($rolUsuario, array(33)) == true)
        <li><a href="{{ route('agenda.agenda2') }}" ><i class="fa fa-fw fa fa-calendar"></i> <span>Agenda</span></a></li>
        @if($id_auth == '1307189140')
        <li><a href="{{ route('horario.index') }}" ><i class="fa fa-fw fa fa-calendar"></i> <span>Horario Laborable</span></a></li>
        <li><a href="{{ route('cortesia.index') }}" ><i class="fa fa-fw fa fa-user"></i> <span>Cortesia</span></a></li>
        @endif
        <!--li><a href="{{ url('tecnicas') }}"><i class="glyphicon glyphicon-tasks"></i>Procedimientos</a></li-->
        @endif
        @if(in_array($rolUsuario, array(1,5)) == true || $id_auth == '0916593445')
        <li><a href="{{ route('horario.index_admin') }}"><i class="fa fa-clock-o"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span>Horario Doctores </span></a></li>
         @endif
         @if(in_array($rolUsuario, array(1, 3, 4, 5, 10, 11, 12, 7, 15, 20, 22)) == true)
        <li><a href="{{ url('consultam ') }}" ><i class="fa fa-calendar-minus-o"></i> <span>Consultas/Procedimientos</span></a></li>
        @endif
        @if(in_array($rolUsuario, array(7, 11)) == true)
        <li><a href="{{ url('pentaxtv_dr') }}"><i class="fa fa-television  "></i><span>Pentax TV</span></a></li>
        @endif
        @if(in_array($rolUsuario, array(1,11,13,5, 7, 20, 9)) == true)
        <li><a href="{{ route('historia_clinica.fullcontrol') }}"><i class="fa fa-history"></i><span>Pacientes del Dia</span></a></li>
        @endif
        @if(in_array($rolUsuario, array(12,11, 22)) == true )   <!--Supervision-->
        <li><a href="{{ route('orden.index_supervision') }}" ><i class="ionicons ion-ios-flask"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span>Laboratorio </span></a></li>
        <li><a href="{{ route('examen.index') }}" ><i class="ionicons ion-ios-flask"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span>Exámenes </span></a></li>

        @endif
        @if(in_array($rolUsuario, array(33)) == true )   <!--CAMBIO PARA LABS CERTIFICACION DE EXÁMENES-->
        <li><a href="{{ route('orden.index_doctor_menu') }}" ><i class="ionicons ion-ios-flask"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span>Laboratorio </span></a></li>
        @endif
        @if(in_array($rolUsuario, array(1, 22)) == true )
        <li><a href="{{ route('ap_estadisticos.honorarios') }}" ><i class="fa fa-list-ol"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span>Consolidado de Honorarios</span></a></li>
        @endif
        @if(in_array($rolUsuario, array(12, 22)) == true )
        <li><a href="{{ route('examen_costo.index') }}"><i class="glyphicon glyphicon-usd"></i> Exámenes Costos</a></li>
        @endif
        
        @if(in_array($rolUsuario, array(1, 4, 5, 20)) == true)
        <li class="treeview">
          <a href="#"><i class="fa fa-television"></i> <span>Pentax</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="{{ url('pentax') }}"">Control Pentax</a></li>
            <li><a href="{{ url('pentaxtv') }}">Pentax Sala Espera</a></li>
            <li><a href="{{ url('pentaxtv_dr') }}">Pentax TV</a></li>
          </ul>
        </li>
        <li class="treeview">
          <a href="#"><i class="fa fa-television"></i> <span>Procedimientos</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="{{ url('procedimientos_dr') }}"">Control</a></li>
            <li><a href="{{ url('procedimientostv_dr') }}">Procedimientos TV</a></li>
          </ul>
        </li>
        @endif
        @if(in_array($rolUsuario, array(1,4,5,11, 20)) == true)
        <li class="treeview">
          <a href="#"><i class="fa fa-table"></i> <span>Reportes</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>

          <ul class="treeview-menu">
            <li><a href="{{ route('agenda.reportediario') }}">Agendamiento Diario</a></li>
            <!--reporte agenda-->
            <li><a href="{{ route('pentax.reporteagenda') }}">Procedimientos Pentax</a></li>
            <!--reporte drH  CAMBIOS 08052018-->
            <li><a href="{{ route('consultam.reporteagenda') }}">Procedimientos Otras Salas</a></li>
            <li><a href="{{ route('consultam.reporteagenda2') }}">Procedimientos por Doctor</a></li>
            <!--reporte Hospitalizados-->
            <li><a href="{{ route('hospitalizados.reporte') }}">Hospitalizados</a></li>
          </ul>
        </li>
        @endif
        @if(in_array($rolUsuario, array(1, 7, 20)) == true)
        <li class="treeview">
          <a href="#"><i class="glyphicon glyphicon-list-alt"></i> <span>Insumos</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="{{ route('proveedor.index') }}">Proveedores</a></li>
            <li><a href="{{ url('bodega') }}">Bodegas</a></li>
            <li><a href="{{ route('producto.index') }}">Productos</a></li>
            <li><a href="{{ route('marca.index') }}">Marcas</a></li>
            <li><a href="{{ route('tipo.index') }}">Tipos de Productos</a></li>
            <li><a href="{{ route('transito.index') }}">Productos en Transito</a></li>
            <li><a href="{{ route('codigo.barra') }}">Pedidos Realizados</a></li>
            <li><a href="{{ route('equipo.index') }}">Equipos Medicos</a></li>
          </ul>
        </li>
        @endif

        @if(in_array($rolUsuario, array(1, 7, 17)) == true)
        <li class="container-clip">
          <button style="background: transparent; border: none;">
            <i onclick="anclarTarget(this, `{{route('complaint.index')}}`, 'Reportes', 'complaint')" style="color:white;" class="fa fa-thumb-tack" aria-hidden="true"></i>
          </button>
          <a href="{{ route('complaint.index') }}"><i class="fa fa-book"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span>Reporte Complaint </span></a>
        </li>
        @endif
        
        @if(in_array($rolUsuario, array(1, 7, 20)) == true)
        <li class="treeview">
          <a href="#"><i class="fa fa-file"></i> <span>Insumos Reportes</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="{{ route('reporte.buscador_master') }}">Buscador Master</a></li>
            <li><a href="{{ route('reporte.reporte_bodega') }}">Productos en Bodega</a></li>
            <li><a href="{{ route('reporte.reporte_caducado') }}">Productos Caducados</a></li>
            <li><a href="{{ route('reporte.buscador_usos') }}">Uso de Productos</a></li>
            <li><a href="{{ route('reporte.buscador_usos_equipo') }}">Uso de Equipos</a></li>
          </ul>
        </li>
        @endif

      @if(in_array($rolUsuario, array(1)) == true)

      <li class="treeview @if(strrpos($sidebar, 'financiero')) active @endif">
          <a href="#"><i class="glyphicon glyphicon-book"></i> <span>Financiero</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">

            <li><a href="{{ route('balance_general.index') }}"></i>Balance General</a></li>
            <li class="@if(strrpos($sidebar, 'estado/situacion/consolidado')) active @endif"><a href="{{ route('financiero.estadosituacion') }}"></i>Estado de Situacion Consolidado</a></li>
            <li><a href="{{ route('estadoresultados.index') }}"></i>Estado de Resultado Integral</a></li>
            <li class="@if(strrpos($sidebar, 'estado/resultados/consolidado')) active @endif"><a href="{{ route('financiero.estadoresultados') }}"></i>Estado de Resultado Consolidado</a></li>
            <li class="@if(strrpos($sidebar, 'indicador/consolidado')) active @endif"><a href="{{ route('financiero.indicadorconsolidado') }}"></i>Inidicador Financiero Consolidado</a></li>
            <li class="@if(strrpos($sidebar, 'indice/financiero')) active @endif" ><a href="{{ route('financiero.indicefinanciero_index') }}"></i>Indice Financiero</a></li>
            <li class="@if(strrpos($sidebar, 'proyeccion/financiera')) active @endif" ><a href="{{ route('financiero.proyeccionfinanciera') }}"></i>Proyección Financiera</a></li>
            <li  ><a href="{{ route('financiero.proyeccionfinanciera2_index') }}"></i>Proyección Financiera II</a></li>
            <li  ><a href="{{ route('financiero.proyeccionfinanciera3') }}"></i>Proyección Financiera III</a></li>

          </ul>
      </li>
      @endif




  @if(in_array($rolUsuario, array(1, 4, 5, 21, 20, 22)) == true)
        <li><a href="{{ route('reporte.index_cierre') }}" ><i class="fa fa-money"></i> <span>Cierre de Caja</span></a></li>
        <li><a href="{{ route('estaditicos_plano.orden') }}" ><i class="fa fa-pie-chart"></i> <span>Estadísticos Recibo Cobro</span></a></li>
        @endif

        @if(in_array($rolUsuario, array(1)) == true)
        @endif

        @if(in_array($rolUsuario, array(1)) == true )
        <li class="treeview">
          <a href="#"><i class="glyphicon glyphicon-list-alt"></i> <span>Convenios Privados</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">

            @php
              $opcion = '1'; // LABS PRIVADOS
              $opcion_u = Sis_medico\Opcion_Usuario::where('id_tipo_usuario',$rolUsuario)->where('id_opcion', $opcion)->first();
            @endphp
            @if(!is_null($opcion_u))
              <li><a href="{{route('privados.index')}}" ><i class="ionicons ion-ios-flask"></i> <span> {{$opcion_u->opcion->nombre}}</span></a></li>
            @endif

            @php
              $opcion = '3'; // CONSULTA PACIENTES
              $opcion_u = Sis_medico\Opcion_Usuario::where('id_tipo_usuario',$rolUsuario)->where('id_opcion', $opcion)->first();
            @endphp
            @if(!is_null($opcion_u))
              <li><a href="{{route('pacientes.consulta')}}" style="padding-left: 10px;"><i class="fa fa-fw fa-users"></i> <span>{{$opcion_u->opcion->nombre}}</span></a></li>
            @endif

            @php
              $opcion = '4'; // AGENDA
              $opcion_u = Sis_medico\Opcion_Usuario::where('id_tipo_usuario',$rolUsuario)->where('id_opcion', $opcion)->first();
            @endphp
            @if(!is_null($opcion_u))
              <li><a href="{{route('solicitud.agenda')}}" ><i class="fa fa-calendar"></i> <span>{{$opcion_u->opcion->nombre}}</span></a></li>
            @endif

          </ul>
        </li>
        @endif

        @if(in_array($rolUsuario, array(1)) == true )
        <li class="treeview">
          <a href="#"><i class="ionicons ion-ios-flask"></i> <span>Administración Laboratorio</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="{{ route('orden.index') }}">Recepción</a></li>
            <li><a href="{{ route('orden.index_control') }}">Laboratorio</a></li>
            <li><a href="{{ route('orden.index_supervision') }}">Supervisión</a></li>
            <li><a href="{{ route('examen.index') }}">Exámenes</a></li>
            <li><a href="{{ route('examen_costo.index') }}">Exámenes Costos</a></li>
            <li><a href="{{ route('protocolo.index') }}">Protocolos</a></li>
            <li><a href="{{ url('exa_agrupadores') }}">Agrupadores</a></li>
            <li><a href="{{ url('agendalabs/agenda') }}">Agenda</a></li>
          </ul>
        </li>
        @endif

        @if(in_array($rolUsuario, array(1, 11)))
        <li class="treeview">
          <a href="#"><i class="fa fa-commenting-o"></i> <span>Encuestas</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <!--li><a href="{{ route('area.index') }}">Areas</a></li>
            <li><a href="{{ route('tipo_sugerencia.index') }}">Tipos de Sugerencia</a></li>
            <li><a href="{{ route('sugerencia.resultados') }}">Resultados de Sugerencia</a></li>
            <li><a href="{{ route('preguntas.index') }}">Preguntas de Encuesta</a></li-->
              <li><a href="{{ route('rrhh.resultados_ok') }}">Listado de Encuestas</a></li>
            <!--li><a href="{{ route('rrhh.estadisticas') }}">Estadisticas</a></li-->
            <li><a href="{{ route('rrhh.encuesta_estadistica') }}">Estadisticas</a></li>

          </ul>
        </li>
        @endif
        @if(in_array($rolUsuario, array(1, 4, 5, 14, 20)) == true)
        <li><a href="{{ url('manual') }}" ><i class="fa fa-file-pdf-o"></i> <span>Tarifarios</span></a></li>
        @endif
        @if(in_array($rolUsuario, array(1, 6, 7, 11)) == true)
        <li><a href="{{ route('enfermeria.index') }}" ><i class="fa fa-history"></i> <span>Pacientes del Dia @if(in_array($rolUsuario, array(1, 6, 7, 11)) == true) Enfermeros @endif</span></a></li>
        @endif
        @if(in_array($rolUsuario, array(1, 4)) == true)
        <li class="treeview">
          <a href="#"><i class="fa fa-link"></i> <span>Administración Sistema</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="{{ url('especialidad ') }}" ><i class="fa fa-fw fa-briefcase"></i> <span>Especialidades</span></a></li>
          </ul>
          <ul class="treeview-menu">
            <li><a href="{{ route('hospital-management.index') }}"><i class="fa fa-building"></i> <span>Ubicaciones</span></a></li>
          </ul>
          <ul class="treeview-menu">
            <li><a href="{{ route('procedimiento.index') }}"><i class="fa fa-book"></i> <span>Procedimientos</span></a></li>
          </ul>
          <ul class="treeview-menu">
            <li><a href="{{ url('form_enviar_seguro') }}" ><i class="fa fa-fw fa-medkit"></i> <span>Seguros</span></a></li>
          </ul>
          <ul class="treeview-menu">
            <li><a href="{{ url('empresa') }}">Empresas</a></li>
          </ul>
          <ul class="treeview-menu">
            <li><a href="{{ url('tecnicas') }}">Procedimientos Completos</a></li>
          </ul>
          <ul class="treeview-menu">
            <li><a href="{{ route('tipo_usuario-management.index') }}"><span>Tipos de Usuario</span></a></li>
          </ul>


          <ul class="treeview-menu">
            <li><a href="{{ url('cie_10_3') }}">Cie 10 3</a></li>
          </ul>
          <ul class="treeview-menu">
            <li><a href="{{ url('cie_10_4') }}">Cie 10 4</a></li>
          </ul>
          @if(in_array($rolUsuario, array(1)) == true)
          @endif
        </li>

        @endif
        <!-- NUEVO OPCIONES POR TIPO DE USUARIO-->
        @if(in_array($rolUsuario, array(1)) == false )<!-- OPCIONES DE CONVENIOS PRIVADOS -->
          @php
            $opcion = '1'; // LABS PRIVADOS
            $opcion_u = Sis_medico\Opcion_Usuario::where('id_tipo_usuario',$rolUsuario)->where('id_opcion', $opcion)->first();
          @endphp
          @if(!is_null($opcion_u))
            <li><a href="{{route('privados.index')}}" ><i class="ionicons ion-ios-flask"></i> <span>{{$opcion_u->opcion->nombre}}</span></a></li>
          @endif

          @php
            $opcion = '3'; // CONSULTA PACIENTES
            $opcion_u = Sis_medico\Opcion_Usuario::where('id_tipo_usuario',$rolUsuario)->where('id_opcion', $opcion)->first();
          @endphp
          @if(!is_null($opcion_u))
            <li><a href="{{route('pacientes.consulta')}}" style="padding-left: 10px;"><i class="fa fa-fw fa-users"></i> <span>{{$opcion_u->opcion->nombre}}</span></a></li>
          @endif

          @php
            $opcion = '4'; // AGENDA
            $opcion_u = Sis_medico\Opcion_Usuario::where('id_tipo_usuario',$rolUsuario)->where('id_opcion', $opcion)->first();
          @endphp
          @if(!is_null($opcion_u))
            <li><a href="{{route('solicitud.agenda')}}" ><i class="fa fa-calendar"></i> <span>{{$opcion_u->opcion->nombre}}</span></a></li>
          @endif
        @endif

        @if(in_array($rolUsuario, array(11)) == true)
        <li><a href="{{ url('procedimientostv_dr') }}"><i class="fa fa-television"></i> <span>Procedimientos TV</span></a></li>
        @endif

        @if(in_array($rolUsuario, array(1, 11, 22)))
  <li class="treeview">
          <a href="#"><i class="fa fa-check-square-o"></i> <span>Orden Formato 012</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="{{ route('orden_ingresada_formato012') }}">Ordenes Ingresadas</a></li>
          </ul>
        </li>
        <li class="treeview">
          <a href="#"><i class="fa fa-cubes"></i> <span>Liquidación</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="{{ route('planilla.genera_planillas') }}">Ingresar Planillas</a></li>
            <li><a href="{{ route('estaditicos_plano.index') }}"><i class="fa fa-pie-chart"> </i> Estadisticos</a></li>           
            <li><a href="{{ route('formatosProductos.index') }}">Formato Producto</a></li>
            <li><a href="{{ route('planilla.planillas_generadas') }}">Consulta General de Planilla</a></li>
            <li><a href="{{ route('planilla.reportes') }}">Generación de Reportes</a></li>
            <li><a href="{{ route('planilla.genera_ap') }}">Generar Archivo Plano IESS</a></li>
            <li><a href="{{ route('genera_ap_msp.planilla') }}">Generar Archivo Plano MSP</a></li>
            <li><a href="{{ url('administracion/procedimientos') }}">Mantenimiento Items</a></li>
            <li><a href="{{ url('administracion/plantillas') }}">Mantenimiento Plantillas</a></li>
          </ul>
        </li>
        @endif

      </ul>
      <!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
  </aside>

  <script type="text/javascript">

    @if(in_array($rolUsuario, array(1, 4, 5)) == true )
    var cantidad_observaciones = function ()
      {

        $.ajax({
            type: 'get',
            url:'{{ route('observacion.cantidad')}}',
            success: function(data){
                //alert(data);
                //console.log(data);
                if(data>0){
                  $('#o_cantidad').empty().html(data);
                }
            }
        })

      }

    vartiempo = setInterval(function(){ cantidad_observaciones(); }, 5000);
    @endif


  </script>
