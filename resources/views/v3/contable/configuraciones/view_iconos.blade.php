<style>
    div.scroll {
        width: 100%;
        height: 450px;
        overflow-x: hidden;
        overflow-y: auto;
        padding: 20px;
    }

    .icon-box {
        border: 1px solid #000;
        /* Color del borde */
        padding: 5px;
        /* Espacio interno */
        margin: 5px;
        /* Espacio externo */
        display: inline-block;
        /* Para que los bordes se ajusten al contenido */
        text-align: center;
        /* Centra el contenido dentro del cuadro */
        transition: box-shadow 0.3s ease-in-out;
        /* Transición suave para el efecto de sombra */
    }

    .icon-box i {
        display: block;
        /* Hace que el ícono se muestre como un bloque */
        margin-bottom: 5px;
        /* Espacio entre el ícono y el texto */
    }

    .icon-box span {
        display: block;
        /* Hace que el texto se muestre como un bloque debajo del ícono */
    }

    .icon-box:hover {
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
        /* Efecto de sombra al pasar el ratón */
    }
</style>

<div>
    <div class="modal-header">
        <h5 class="modal-title">Busca tu Icono</h5>
        <button type="button" class="close_icon" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="modal-body">
        <div class="row mb-2">
            <div class="input-group">
                <div class="input-group">
                    <input type="search" id="buscador" class="form-control" placeholder="Buscar tu icono" aria-label="buscador" aria-describedby="basic-addon1">
                </div>
            </div>
        </div>
        <div class="scroll" id="results">
            <div class="row results">
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-address-book" aria-hidden="true"></i>address-book
                </div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-address-book-o" aria-hidden="true"></i>
                    address-book-o
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-address-card" aria-hidden="true"></i>
                    address-card
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-address-card-o" aria-hidden="true"></i>
                    address-card-o
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-bandcamp" aria-hidden="true"></i>
                    bandcamp
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-bath" aria-hidden="true"></i>
                    bath
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-bathtub" aria-hidden="true"></i>
                    bathtub (alias)
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-drivers-license" aria-hidden="true"></i>
                    drivers-license (alias)
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-drivers-license-o" aria-hidden="true"></i>
                    drivers-license-o (alias)
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-eercast" aria-hidden="true"></i>
                    eercast
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-envelope-open" aria-hidden="true"></i>
                    envelope-open
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-envelope-open-o" aria-hidden="true"></i>
                    envelope-open-o
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-etsy" aria-hidden="true"></i>
                    etsy
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-free-code-camp" aria-hidden="true"></i>
                    free-code-camp
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-grav" aria-hidden="true"></i>
                    grav
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-handshake-o" aria-hidden="true"></i>
                    handshake-o
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-id-badge" aria-hidden="true"></i>
                    id-badge
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-id-card" aria-hidden="true"></i>
                    id-card
                </div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-id-card-o" aria-hidden="true"></i>
                    id-card-o
                </div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-imdb" aria-hidden="true"></i>
                    Imdb
                </div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-linode" aria-hidden="true"></i>
                    Linode
                </div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-meetup" aria-hidden="true"></i>
                    meetup
                </div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-microchip"></i>
                    microchip
                </div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-microchip"></i>
                    <span>microchip</span>
                </div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-wallet"></i>
                    <span>wallet</span>
                </div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-quora"></i>
                    <span>quora</span>
                </div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-ravelry"></i>
                    <span>ravelry</span>
                </div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-s15"></i>
                    <span>s15 (alias)</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-shower"></i>
                    <span>shower</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-snowflake-o"></i>
                    <span>snowflake-o</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-superpowers"></i>
                    <span>superpowers</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-telegram"></i>
                    <span>telegram</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-thermometer"></i>
                    <span>thermometer (alias)</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-thermometer-0"></i>
                    <span>thermometer-0 (alias)</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box">
                    <i class="fa fa-thermometer-1"></i>
                    <span>thermometer-1 (alias)</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box">
                    <i class="fa fa-thermometer-2"></i>
                    <spans>thermometer-2 (alias)</spans>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box">
                    <i class="fa fa-thermometer-2"></i>
                    <span>thermometer-2 (alias)</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box">
                    <i class="fa fa-thermometer-4"></i>
                    <span>thermometer-4 (alias)</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box">
                    <i class="fa fa-thermometer-empty"></i>
                    <span>thermometer-empty</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box">
                    <i class="fa fa-thermometer-full"></i>
                    <span>thermometer-full</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box">
                    <i class="fa fa-thermometer-half"></i>
                    <span>thermometer-half</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box">
                    <i class="fa fa-thermometer-quarter"></i>
                    <span>thermometer-quarter</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box">
                    <i class="fa fa-thermometer-three-quarters"></i>
                    <span>thermometer-three-quarters</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box">
                    <i class="fa fa-times-rectangle"></i>
                    <span>times-rectangle (alias)</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box">
                    <i class="fa fa-times-rectangle-o"></i>
                    <span>times-rectangle-o (alias)</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box">
                    <i class="fa fa-user-circle"></i>
                    <span>user-circle</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box">
                    <i class="fa fa-user-circle-o"></i>
                    <span>user-circle-o</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box">
                    <i class="fa fa-user-o"></i>
                    <span>user-o</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box">
                    <i class="fa fa-vcard"></i>
                    <span>vcard (alias)</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box">
                    <i class="fa fa-vcard-o"></i>
                    <span>vcard-o (alias)</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box">
                    <i class="fa fa-window-close"></i>
                    <span>window-close</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box">
                    <i class="fa fa-window-close-o"></i>
                    <span>window-close-o</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box">
                    <i class="fa fa-window-maximize"></i>
                    <span>window-maximize</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box">
                    <i class="fa fa-window-minimize"></i>
                    <span>window-minimize</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box">
                    <i class="fa fa-window-restore"></i>
                    <span>window-restore</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box">
                    <i class="fa fa-wpexplorer"></i>
                    <span>wpexplorer</span>
                </div>







                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-address-book"></i>
                    <span>address-book</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-address-book-o"></i>
                    <span>address-book-o</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-address-card"></i>
                    <span>address-card</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-address-card-o"></i>
                    <span>address-card-o</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-adjust"></i>
                    <span>adjust</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-american-sign-language-interpreting"></i>
                    <span>american-sign-language-interpreting</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-anchor"></i>
                    <span>anchor</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-archive"></i>
                    <span>archive</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-area-chart"></i>
                    <span>area-chart</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-arrows"></i>
                    <span>arrows</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-arrows-h"></i>
                    <span>arrows-h</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-arrows-v"></i>
                    <span>arrows-v</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-asl-interpreting"></i>
                    <span>asl-interpreting (alias)</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-assistive-listening-systems"></i>
                    <span>assistive-listening-systems</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-asterisk"></i>
                    <span>asterisk</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-at"></i>
                    <span>at</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-audio-description"></i>
                    <span>audio-description</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-automobile"></i>
                    <span>automobile (alias)</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-balance-scale"></i>
                    <span>balance-scale</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-ban"></i>
                    <span>ban</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-bank"></i>
                    <span>bank (alias)</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-bar-chart"></i>
                    <span>bar-chart</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-bar-chart-o"></i>
                    <span>bar-chart-o (alias)</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-barcode"></i>
                    <span>barcode</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-bars"></i>
                    <span>bars</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-bath"></i>
                    <span>bath</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-bathtub"></i>
                    <span>bathtub (alias)</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-battery"></i>
                    <span>battery (alias)</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-battery-0"></i>
                    <span>battery-0 (alias)</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-battery-1"></i>
                    <span>battery-1 (alias)</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-battery-2"></i>
                    <span>battery-2 (alias)</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-battery-4"></i>
                    <span>battery-4 (alias)</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-battery-4"></i>
                    <span>battery-4 (alias)</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-battery-empty"></i>
                    <span>battery-empty</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-battery-full"></i>
                    <span>battery-full</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-battery-half"></i>
                    <span>battery-half</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-battery-quarter"></i>
                    <span>battery-quarter</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-battery-three-quarters"></i>
                    <span>battery-three-quarters</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-bed"></i>
                    <span>bed</span>
                </div>


                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-beer"></i>
                    <span>beer</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-bell"></i>
                    <span>bell</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-bell-o"></i>
                    <span>bell-o</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-bell-slash"></i>
                    <span>bell-slash</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-bell-slash-o"></i>
                    <span>bell-slash-o</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-bicycle"></i>
                    <span>bicycle</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-binoculars"></i>
                    <span>binoculars</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-birthday-cake"></i>
                    <span>birthday-cake</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-blind"></i>
                    <span>blind</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-bluetooth"></i>
                    <span>bluetooth</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-bluetooth-b"></i>
                    <span>bluetooth-b</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-bolt"></i>
                    <span>bolt</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-bomb"></i>
                    <span>bomb</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-book"></i>
                    <span>book</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-bookmark"></i>
                    <span>bookmark</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-bookmark-o"></i>
                    <span>bookmark-o</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-braille"></i>
                    <span>braille</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-briefcase"></i>
                    <span>briefcase</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-bug"></i>
                    <span>bug</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-building"></i>
                    <span>building</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-building-o"></i>
                    <span>building-o</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-bullhorn"></i>
                    <span>bullhorn</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-bullseye"></i>
                    <span>bullseye</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-bus"></i>
                    <span>bus</span>
                </div>




                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-cab"></i>
                    <span>cab</span>
                    <span class="text-muted">(alias)</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-calculator"></i>
                    <span>calculator</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-calendar"></i>
                    <span>calendar</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-calendar-check-o"></i>
                    <span>calendar-check-o</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-calendar-minus-o"></i>
                    <span>calendar-minus-o</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-calendar-o"></i>
                    <span>calendar-o</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-calendar-plus-o"></i>
                    <span>calendar-plus-o</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-calendar-times-o"></i>
                    <span>calendar-times-o</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-camera"></i>
                    <span>camera</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-camera-retro"></i>
                    <span>camera-retro</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-car"></i>
                    <span>car</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-caret-square-o-down"></i>
                    <span>caret-square-o-down</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-caret-square-o-left"></i>
                    <span>caret-square-o-left</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-caret-square-o-right"></i>
                    <span>caret-square-o-right</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-caret-square-o-up"></i>
                    <span>caret-square-o-up</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-cart-arrow-down"></i>
                    <span>cart-arrow-down</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-cart-plus"></i>
                    <span>cart-plus</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-cc"></i>
                    <span>cc</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-certificate"></i>
                    <span>certificate</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-check"></i>
                    <span>check</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-check-circle"></i>
                    <span>check-circle</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-check-circle-o"></i>
                    <span>check-circle-o</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-check-square"></i>
                    <span>check-square</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-check-square-o"></i>
                    <span>check-square-o</span>
                </div>



                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-child"></i>
                    <span>child</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-circle"></i>
                    <span>circle</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-circle-o"></i>
                    <span>circle-o</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-circle-o-notch"></i>
                    <span>circle-o-notch</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-circle-thin"></i>
                    <span>circle-thin</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-clock-o"></i>
                    <span>clock-o</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-clone"></i>
                    <span>clone</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-close"></i>
                    <span>close</span>
                    <span class="text-muted">(alias)</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-cloud"></i>
                    <span>cloud</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-cloud-download"></i>
                    <span>cloud-download</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-cloud-upload"></i>
                    <span>cloud-upload</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-code"></i>
                    <span>code</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-code-fork"></i>
                    <span>code-fork</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-coffee"></i>
                    <span>coffee</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-cog"></i>
                    <span>cog</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-cogs"></i>
                    <span>cogs</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-comment"></i>
                    <span>comment</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-comment-o"></i>
                    <span>comment-o</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-commenting"></i>
                    <span>commenting</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-commenting-o"></i>
                    <span>commenting-o</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-comments"></i>
                    <span>comments</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-comments-o"></i>
                    <span>comments-o</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-compass"></i>
                    <span>compass</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-copyright"></i>
                    <span>copyright</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-creative-commons"></i>
                    <span>creative-commons</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-credit-card"></i>
                    <span>credit-card</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-credit-card-alt"></i>
                    <span>credit-card-alt</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-crop"></i>
                    <span>crop</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-crosshairs"></i>
                    <span>crosshairs</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-cube"></i>
                    <span>cube</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-cubes"></i>
                    <span>cubes</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-child"></i>
                    <span>child</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-circle"></i>
                    <span>circle</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-circle-o"></i>
                    <span>circle-o</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-circle-o-notch"></i>
                    <span>circle-o-notch</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-circle-thin"></i>
                    <span>circle-thin</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-clock-o"></i>
                    <span>clock-o</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-clone"></i>
                    <span>clone</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-close"></i>
                    <span>close</span>
                    <span class="text-muted">(alias)</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-cloud"></i>
                    <span>cloud</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-cloud-download"></i>
                    <span>cloud-download</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-cloud-upload"></i>
                    <span>cloud-upload</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-code"></i>
                    <span>code</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-code-fork"></i>
                    <span>code-fork</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-coffee"></i>
                    <span>coffee</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-cog"></i>
                    <span>cog</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-cogs"></i>
                    <span>cogs</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-comment"></i>
                    <span>comment</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-comment-o"></i>
                    <span>comment-o</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-commenting"></i>
                    <span>commenting</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-commenting-o"></i>
                    <span>commenting-o</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-comments"></i>
                    <span>comments</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-comments-o"></i>
                    <span>comments-o</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-compass"></i>
                    <span>compass</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-copyright"></i>
                    <span>copyright</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-creative-commons"></i>
                    <span>creative-commons</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-credit-card"></i>
                    <span>credit-card</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-credit-card-alt"></i>
                    <span>credit-card-alt</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-crop"></i>
                    <span>crop</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-crosshairs"></i>
                    <span>crosshairs</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-cube"></i>
                    <span>cube</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-cubes"></i>
                    <span>cubes</span>
                </div>


                <div class="fa-hover col-md-2 col-sm-1 icon-box">
                    <i class="fa fa-exchange"></i>
                    <span>exchange</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box">
                    <i class="fa fa-exclamation"></i>
                    <span>exclamation</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box">
                    <i class="fa fa-exclamation-circle"></i>
                    <span>exclamation-circle</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box">
                    <i class="fa fa-exclamation-triangle"></i>
                    <span>exclamation-triangle</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box">
                    <i class="fa fa-external-link"></i>
                    <span>external-link</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box">
                    <i class="fa fa-external-link-square"></i>
                    <span>external-link-square</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-eye"></i>
                    <span>eye</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-eye-slash"></i>
                    <span>eye-slash</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-eyedropper"></i>
                    <span>eyedropper</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-fax"></i>
                    <span>fax</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-feed"></i>
                    <span>feed</span>
                    <span class="text-muted">(alias)</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-female"></i>
                    <span>female</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-fighter-jet"></i>
                    <span>fighter-jet</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-file-archive-o"></i>
                    <span>file-archive-o</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-file-audio-o"></i>
                    <span>file-audio-o</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-file-code-o"></i>
                    <span>file-code-o</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-file-excel-o"></i>
                    <span>file-excel-o</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-file-image-o"></i>
                    <span>file-image-o</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-file-movie-o"></i>
                    <span>file-movie-o</span>
                    <span class="text-muted">(alias)</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-file-pdf-o"></i>
                    <span>file-pdf-o</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-file-photo-o"></i>
                    <span>file-photo-o</span>
                    <span class="text-muted">(alias)</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-file-picture-o"></i>
                    <span>file-picture-o</span>
                    <span class="text-muted">(alias)</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-file-powerpoint-o"></i>
                    <span>file-powerpoint-o</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-file-sound-o"></i>
                    <span>file-sound-o</span>
                    <span class="text-muted">(alias)</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-file-video-o"></i>
                    <span>file-video-o</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-file-word-o"></i>
                    <span>file-word-o</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-file-zip-o"></i>
                    <span>file-zip-o</span>
                    <span class="text-muted">(alias)</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-film"></i>
                    <span>film</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-filter"></i>
                    <span>filter</span>
                </div>


                <div class="fa-hover col-md-2 col-sm-1 icon-box">
                    <i class="fa fa-exchange"></i>
                    <span>exchange</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box">
                    <i class="fa fa-exclamation"></i>
                    <span>exclamation</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box">
                    <i class="fa fa-exclamation-circle"></i>
                    <span>exclamation-circle</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box">
                    <i class="fa fa-exclamation-triangle"></i>
                    <span>exclamation-triangle</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box">
                    <i class="fa fa-external-link"></i>
                    <span>external-link</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box">
                    <i class="fa fa-external-link-square"></i>
                    <span>external-link-square</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-eye"></i>
                    <span>eye</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-eye-slash"></i>
                    <span>eye-slash</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-eyedropper"></i>
                    <span>eyedropper</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-fax"></i>
                    <span>fax</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-feed"></i>
                    <span>feed</span>
                    <span class="text-muted">(alias)</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-female"></i>
                    <span>female</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-fighter-jet"></i>
                    <span>fighter-jet</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-file-archive-o"></i>
                    <span>file-archive-o</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-file-audio-o"></i>
                    <span>file-audio-o</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-file-code-o"></i>
                    <span>file-code-o</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-file-excel-o"></i>
                    <span>file-excel-o</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-file-image-o"></i>
                    <span>file-image-o</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-file-movie-o"></i>
                    <span>file-movie-o</span>
                    <span class="text-muted">(alias)</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-file-pdf-o"></i>
                    <span>file-pdf-o</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-file-photo-o"></i>
                    <span>file-photo-o</span>
                    <span class="text-muted">(alias)</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-file-picture-o"></i>
                    <span>file-picture-o</span>
                    <span class="text-muted">(alias)</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-file-powerpoint-o"></i>
                    <span>file-powerpoint-o</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-file-sound-o"></i>
                    <span>file-sound-o</span>
                    <span class="text-muted">(alias)</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-file-video-o"></i>
                    <span>file-video-o</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-file-word-o"></i>
                    <span>file-word-o</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-file-zip-o"></i>
                    <span>file-zip-o</span>
                    <span class="text-muted">(alias)</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-film"></i>
                    <span>film</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-filter"></i>
                    <span>filter</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-newspaper-o"></i> Example of newspaper-o
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-object-group"></i> Example of object-group
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-object-ungroup"></i> Example of object-ungroup
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-paint-brush"></i> Example of paint-brush
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-paper-plane"></i> Example of paper-plane
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-paper-plane-o"></i> Example of paper-plane-o
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-paw"></i> Example of paw
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-pencil"></i> Example of pencil
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-pencil-square"></i> Example of pencil-square
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-pencil-square-o"></i> Example of pencil-square-o
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-percent"></i> Example of percent
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-phone"></i> Example of phone
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-phone-square"></i> Example of phone-square
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-photo"></i> Example of photo <span class="text-muted">(alias)</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-picture-o"></i> Example of picture-o
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-pie-chart"></i> Example of pie-chart
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-plane"></i> Example of plane
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-plug"></i> Example of plug
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-plus"></i> Example of plus
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-plus-circle"></i> Example of plus-circle
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-plus-square"></i> Example of plus-square
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;">
                    <i class="fa fa-plus-square-o"></i> Example of plus-square-o
                </div>


                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-podcast" aria-hidden="true"></i> podcast
                </div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-power-off" aria-hidden="true"></i> power-off
                </div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-print" aria-hidden="true"></i> print</div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-puzzle-piece" aria-hidden="true"></i>
                    puzzle-piece</div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-qrcode" aria-hidden="true"></i> qrcode</div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-question" aria-hidden="true"></i> question
                </div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-question-circle" aria-hidden="true"></i>
                    question-circle</div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-question-circle-o" aria-hidden="true"></i>
                    question-circle-o</div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-quote-left" aria-hidden="true"></i>
                    quote-left
                </div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-quote-right" aria-hidden="true"></i>
                    quote-right
                </div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-random" aria-hidden="true"></i> random</div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-recycle" aria-hidden="true"></i> recycle
                </div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-refresh" aria-hidden="true"></i> refresh
                </div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-registered" aria-hidden="true"></i>
                    registered
                </div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-remove" aria-hidden="true"></i> remove <span class="text-muted">(alias)</span></div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-reorder" aria-hidden="true"></i> reorder
                    <span class="text-muted">(alias)</span>
                </div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-reply" aria-hidden="true"></i> reply</div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-reply-all" aria-hidden="true"></i> reply-all
                </div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-retweet" aria-hidden="true"></i> retweet
                </div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-road" aria-hidden="true"></i> road</div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class "fa fa-rocket" aria-hidden="true"></i> rocket</div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-rss" aria-hidden="true"></i> rss</div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-rss-square" aria-hidden="true"></i>
                    rss-square
                </div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-s15" aria-hidden="true"></i> s15 <span class="text-muted">(alias)</span></div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-search" aria-hidden="true"></i> search</div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-search-minus" aria-hidden="true"></i>
                    search-minus</div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-search-plus" aria-hidden="true"></i>
                    search-plus
                </div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-send" aria-hidden="true"></i> send <span class="text-muted">(alias)</span></div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-send-o" aria-hidden="true"></i> send-o <span class="text-muted">(alias)</span></div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-server" aria-hidden="true"></i> server</div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-share" aria-hidden="true"></i> share</div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-share-alt" aria-hidden="true"></i> share-alt
                </div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-share-alt-square" aria-hidden="true"></i>
                    share-alt-square</div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-share-square" aria-hidden="true"></i>
                    share-square</div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-share-square-o" aria-hidden="true"></i>
                    share-square-o</div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-shield" aria-hidden="true"></i> shield</div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-ship" aria-hidden="true"></i> ship</div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-shopping-bag" aria-hidden="true"></i>
                    shopping-bag</div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-shopping-basket" aria-hidden="true"></i>
                    shopping-basket</div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-shopping-cart" aria-hidden="true"></i>
                    shopping-cart</div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-shower" aria-hidden="true"></i> shower</div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-sign-in" aria-hidden="true"></i> sign-in
                </div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-sign-language" aria-hidden="true"></i>
                    sign-language</div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-sign-out" aria-hidden="true"></i> sign-out
                </div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-signal" aria-hidden="true"></i> signal</div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-signing" aria-hidden="true"></i> signing
                    <span class="text-muted">(alias)</span>
                </div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-sitemap" aria-hidden="true"></i> sitemap
                </div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-sliders" aria-hidden="true"></i> sliders
                </div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-smile-o" aria-hidden="true"></i> smile-o
                </div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-snowflake-o" aria-hidden="true"></i>
                    snowflake-o
                </div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-soccer-ball-o" aria-hidden="true"></i>
                    soccer-ball-o <span class="text-muted">(alias)</span></div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-sort" aria-hidden="true"></i> sort</div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-sort-alpha-asc" aria-hidden="true"></i>
                    sort-alpha-asc</div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-sort-alpha-desc" aria-hidden="true"></i>
                    sort-alpha-desc</div>


                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-sort-amount-asc" aria-hidden="true"></i>
                    sort-amount-asc</div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-sort-amount-desc" aria-hidden="true"></i>
                    sort-amount-desc</div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-sort-asc" aria-hidden="true"></i> sort-asc
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-sort-desc" aria-hidden="true"></i> sort-desc
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-sort-down" aria-hidden="true"></i> sort-down
                    <span class="text-muted">(alias)</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-sort-numeric-asc" aria-hidden="true"></i>
                    sort-numeric-asc</div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-sort-numeric-desc" aria-hidden="true"></i>
                    sort-numeric-desc</div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-sort-up" aria-hidden="true"></i> sort-up
                    <span class="text-muted">(alias)</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-space-shuttle" aria-hidden="true"></i>
                    space-shuttle</div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-spinner" aria-hidden="true"></i> spinner
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-spoon" aria-hidden="true"></i> spoon</div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-square" aria-hidden="true"></i> square</div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-square-o" aria-hidden="true"></i> square-o
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-star" aria-hidden="true"></i> star</div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-star-half" aria-hidden="true"></i> star-half
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-star-half-empty" aria-hidden="true"></i>
                    star-half-empty <span class="text-muted">(alias)</span></div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-star-half-full" aria-hidden="true"></i>
                    star-half-full <span class="text-muted">(alias)</span></div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-star-half-o" aria-hidden="true"></i>
                    star-half-o
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-star-o" aria-hidden="true"></i> star-o</div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-sticky-note" aria-hidden="true"></i>
                    sticky-note
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-sticky-note-o" aria-hidden="true"></i>
                    sticky-note-o</div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-street-view" aria-hidden="true"></i>
                    street-view
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-suitcase" aria-hidden="true"></i> suitcase
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-sun-o" aria-hidden="true"></i> sun-o</div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-support" aria-hidden="true"></i> support
                    <span class="text-muted">(alias)</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-tablet" aria-hidden="true"></i> tablet</div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-tachometer" aria-hidden="true"></i>
                    tachometer
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-tag" aria-hidden="true"></i> tag</div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-tags" aria-hidden="true"></i> tags</div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-tasks" aria-hidden="true"></i> tasks</div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-taxi" aria-hidden="true"></i> taxi</div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-television" aria-hidden="true"></i>
                    television
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-terminal" aria-hidden="true"></i> terminal
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-thermometer" aria-hidden="true"></i>
                    thermometer
                    <span class="text-muted">(alias)</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-thermometer-0" aria-hidden="true"></i>
                    thermometer-0 <span class="text-muted">(alias)</span></div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-thermometer-1" aria-hidden="true"></i>
                    thermometer-1 <span class="text-muted">(alias)</span></div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-thermometer-2" aria-hidden="true"></i>
                    thermometer-2 <span class="text-muted">(alias)</span></div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-thermometer-3" aria-hidden="true"></i>
                    thermometer-3 <span class="text-muted">(alias)</span></div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-thermometer-4" aria-hidden="true"></i>
                    thermometer-4 <span class="text-muted">(alias)</span></div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-thermometer-empty" aria-hidden="true"></i>
                    thermometer-empty</div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-thermometer-full" aria-hidden="true"></i>
                    thermometer-full</div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-thermometer-half" aria-hidden="true"></i>
                    thermometer-half</div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-thermometer-quarter" aria-hidden="true"></i>
                    thermometer-quarter</div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-thermometer-three-quarters" aria-hidden="true"></i> thermometer-three-quarters</div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-thumb-tack" aria-hidden="true"></i>
                    thumb-tack
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-thumbs-down" aria-hidden="true"></i>
                    thumbs-down
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-thumbs-o-down" aria-hidden="true"></i>
                    thumbs-o-down</div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-thumbs-o-up" aria-hidden="true"></i>
                    thumbs-o-up
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-thumbs-up" aria-hidden="true"></i> thumbs-up
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-ticket" aria-hidden="true"></i> ticket</div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-times" aria-hidden="true"></i> times</div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-times-circle" aria-hidden="true"></i>
                    times-circle</div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-times-circle-o" aria-hidden="true"></i>
                    times-circle-o</div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-times-rectangle" aria-hidden="true"></i>
                    times-rectangle <span class="text-muted">(alias)</span></div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-times-rectangle-o" aria-hidden="true"></i>
                    times-rectangle-o <span class="text-muted">(alias)</span></div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-tint" aria-hidden="true"></i> tint</div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-toggle-down" aria-hidden="true"></i>
                    toggle-down
                    <span class="text-muted">(alias)</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-toggle-left" aria-hidden="true"></i>
                    toggle-left
                    <span class="text-muted">(alias)</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-toggle-off" aria-hidden="true"></i>
                    toggle-off
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-toggle-on" aria-hidden="true"></i> toggle-on
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-toggle-right" aria-hidden="true"></i>
                    toggle-right <span class="text-muted">(alias)</span></div>


                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-toggle-up" aria-hidden="true"></i> toggle-up
                    <span class="text-muted">(alias)</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-trademark" aria-hidden="true"></i> trademark
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-trash" aria-hidden="true"></i> trash</div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-trash-o" aria-hidden="true"></i> trash-o
                </div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-tree" aria-hidden="true"></i> tree</div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-trophy" aria-hidden="true"></i> trophy
                </div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-truck" aria-hidden="true"></i> truck</div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-tty" aria-hidden="true"></i> tty</div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-tv" aria-hidden="true"></i> tv <span class="text-muted">(alias)</span></div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-umbrella" aria-hidden="true"></i> umbrella
                </div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-universal-access" aria-hidden="true"></i>
                    universal-access</div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-university" aria-hidden="true"></i>
                    university
                </div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-unlock" aria-hidden="true"></i> unlock
                </div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-unlock-alt" aria-hidden="true"></i>
                    unlock-alt
                </div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-unsorted" aria-hidden="true"></i> unsorted
                    <span class="text-muted">(alias)</span>
                </div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-upload" aria-hidden="true"></i> upload
                </div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-user" aria-hidden="true"></i> user</div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-user-circle" aria-hidden="true"></i>
                    user-circle</div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-user-circle-o" aria-hidden="true"></i>
                    user-circle-o</div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-user-o" aria-hidden="true"></i> user-o
                </div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-user-plus" aria-hidden="true"></i>
                    user-plus
                </div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-user-secret" aria-hidden="true"></i>
                    user-secret</div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-user-times" aria-hidden="true"></i>
                    user-times
                </div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-users" aria-hidden="true"></i> users</div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-vcard" aria-hidden="true"></i> vcard <span class="text-muted">(alias)</span></div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-vcard-o" aria-hidden="true"></i> vcard-o
                    <span class="text-muted">(alias)</span>
                </div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-vcard-o" aria-hidden="true"></i> vcard-o
                    <span class="text-muted">(alias)</span>
                </div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-video-camera" aria-hidden="true"></i>
                    video-camera</div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-volume-control-phone" aria-hidden="true"></i>
                    volume-control-phone</div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-volume-down" aria-hidden="true"></i>
                    volume-down</div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-volume-off" aria-hidden="true"></i>
                    volume-off
                </div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-volume-up" aria-hidden="true"></i>
                    volume-up
                </div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-warning" aria-hidden="true"></i> warning
                    <span class="text-muted">(alias)</span>
                </div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-wheelchair" aria-hidden="true"></i>
                    wheelchair
                </div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-wheelchair-alt" aria-hidden="true"></i>
                    wheelchair-alt</div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-wifi" aria-hidden="true"></i> wifi</div>

                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-window-close" aria-hidden="true"></i>
                    window-close</div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-window-close-o" aria-hidden="true"></i>
                    window-close-o</div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-window-maximize" aria-hidden="true"></i>
                    window-maximize</div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-window-minimize" aria-hidden="true"></i>
                    window-minimize</div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-window-restore" aria-hidden="true"></i>
                    window-restore</div>
                <div class="fa-hover col-md-2 col-sm-1 icon-box" style="cursor: pointer;"><i class="fa fa-wrench" aria-hidden="true"></i> wrench
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $("#buscador").keyup(function() {
            var filter = $(this).val(),
                count = 0;
            $('#results div').each(function() {
                if ($(this).text().search(new RegExp(filter, "i")) < 0) {
                    $(this).hide();
                } else {
                    $(this).show();
                    count++;
                }
            });

        });

        $(".fa-hover").each(function() {

            $(this).on("click", function() {

                $("#icon_menu").val(`<i class="nav-icon ${$(this).children().attr('class')}"></i>`);
                $(".close_icon").click();
            });
        });
    </script>