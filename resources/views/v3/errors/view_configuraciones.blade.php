<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>412 Errores de configuraciones</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">412 Errores de configuraciones</li>
                </ol>
            </div>
        </div>
    </div>
</section>
<section class="content">
    <div class="error-page gif-container">
        <h2 class="headline text-warning"> 412</h2>
        <div class="error-content">
            <?php
            if (config('data.mensajesGenerales') != null && count(config('data.mensajesGenerales')) > 0) {
                echo '<h3>Los siguientes errores han ocurrido:</h3>';
                $count = 0;
                foreach (config('data.mensajesGenerales') as $mensaje) {
                    $count++;
                    echo '<p style="background-color:#E8F2F4;"><label class="label label-danger">No: ' . $count . ' ' . $mensaje['message'] . '</label></p>';
                }
            }
            ?>
            <form class="search-form">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Search">
                    <div class="input-group-append">
                        <button type="submit" name="submit" class="btn btn-warning"><i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>