@extends('hospital_admin.base')
@section('action-content')

<a type="button" href="{{ route('hospital_admin.crearmenu')}}" class="btn btn-sm btn-info my-2"><i class="fas fa-plus"></i> Crear Menu</a>

<div class="row">
    <div class="col-md-12">
        <!-- Collapsable Menú -->
        <div class="card shadow mb-4">
            <!-- Card Header - Accordion -->
            <a href="#listaplatos" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="listaplatos">
                <h6 class="m-0 font-weight-bold text-primary">Lista de Platos</h6>
            </a>
            <!-- Card Content - Collapse -->
            <div class="collapse show" id="listaplatos">
                <div class="card-body">
                    <table class="table table-bordered">
                        <tbody>
                        <tr class="text-dark">
                            <th>Id</th>
                            <th>Nombre</th>
                            <th>Costo</th>
                            <th>Ingredientes</th>
                            <th>Categoria</th>
                            <th>Acción</th>
                        </tr>
                       
                            @foreach($plato as $item)
                                <tr role="row" class="odd">
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->nombre }}</td>
                                    <td>${{ $item->costo }}</td>
                                    <!--<td>{{ $item->datos_item }}</td>-->
                                    <td>@foreach($item->datos_item as $ing)
                                    @if($ing->estado==1)
                                            @foreach($ing->items_plato as $plato)
                                                {{ $plato->item }}
                                            @endforeach
                                    @endif    
                                    @endforeach</td>
                                    <td>
                                        @if($item->tipo=="1") Desayuno 
                                        @elseif($item->tipo=="2") Almuerzo
                                        @elseif($item->tipo=="3") Merienda
                                        @elseif($item->tipo=="4") Refrigerio 
                                        @endif
                                    </td>
                                    <td><a type="button" href="{{ route('hospital_admin.editarplato', ['id' => $item->id])}}" class="btn btn-sm btn-warning"><i class="far fa-edit"></i> Editar</a></td>
                                </tr>
                            @endforeach
                        
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection