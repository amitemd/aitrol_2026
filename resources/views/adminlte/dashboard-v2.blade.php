{{-- Adaptado del Dashboard v2 de AdminLTE 4, licencia MIT en public/adminlte/LICENSE. --}}
@extends('layouts.adminlte')

@section('title', __('Panel general'))

@section('content')
<!--begin::App Content Header-->
<div class="app-content-header">
  <div class="container-fluid">
    @php
    $accesosRapidos = collect($navegacion ?? [])->flatten(1)->keyBy('ruta')
    ->only(['clientes.index', 'configuracion.empresas.index', 'configuracion.usuarios.index'])
    ->filter(fn ($opcion) => Route::has($opcion->ruta));
    @endphp
    <div class="aitrol-overview">
      <div class="aitrol-overview-heading">
        <h1>{{ __('Panel general') }}</h1>
      </div>
      @if ($accesosRapidos->isNotEmpty())
      <nav class="aitrol-shortcuts" aria-label="Accesos rápidos">
        @foreach ($accesosRapidos as $opcion)
        <a href="{{ route($opcion->ruta) }}" data-workspace-link="{{ $opcion->ruta }}"
          data-workspace-label="{{ $opcion->nombre }}">
          <i class="bi {{ $opcion->icono }}" aria-hidden="true"></i>
          {{ $opcion->nombre }}
          <i class="bi bi-arrow-up-right" aria-hidden="true"></i>
        </a>
        @endforeach
      </nav>
      @endif
    </div>
  </div>
</div>
<div class="app-content">
  <!--begin::Container-->
  <div class="container-fluid">
    <!-- Info boxes -->
    <div class="row">
      <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box">
          <span class="info-box-icon text-bg-primary shadow-sm">
            <i class="bi bi-gear-fill"></i>
          </span>

          <div class="info-box-content">
            <span class="info-box-text">{{ __('Uso de CPU') }}</span>
            <span class="info-box-number">
              10
              <small>%</small>
            </span>
          </div>
          <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
      </div>
      <!-- /.col -->
      <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box">
          <span class="info-box-icon text-bg-danger shadow-sm">
            <i class="bi bi-hand-thumbs-up-fill"></i>
          </span>

          <div class="info-box-content">
            <span class="info-box-text">{{ __('Reacciones') }}</span>
            <span class="info-box-number">41,410</span>
          </div>
          <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
      </div>
      <!-- /.col -->

      <!-- fix for small devices only -->
      <!-- <div class="clearfix hidden-md-up"></div> -->

      <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box">
          <span class="info-box-icon text-bg-success shadow-sm">
            <i class="bi bi-cart-fill"></i>
          </span>

          <div class="info-box-content">
            <span class="info-box-text">{{ __('Ventas') }}</span>
            <span class="info-box-number">760</span>
          </div>
          <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
      </div>
      <!-- /.col -->
      <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box">
          <span class="info-box-icon text-bg-warning shadow-sm">
            <i class="bi bi-people-fill"></i>
          </span>

          <div class="info-box-content">
            <span class="info-box-text">{{ __('Nuevos miembros') }}</span>
            <span class="info-box-number">2,000</span>
          </div>
          <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->

    <!--begin::Row-->
    <div class="row">
      <div class="col-md-12">
        <div class="card mb-4">
          <div class="card-header">
            <h2 class="card-title">{{ __('Resumen mensual') }}</h2>

            <div class="card-tools">
              <button
                type="button"
                class="btn btn-tool"
                data-lte-toggle="card-collapse"
                aria-label="{{ __('Collapse card') }}">
                <i data-lte-icon="expand" class="bi bi-plus-lg"></i>
                <i data-lte-icon="collapse" class="bi bi-dash-lg"></i>
              </button>
              <div class="btn-group">
                <button
                  type="button"
                  class="btn btn-tool dropdown-toggle"
                  aria-label="More options"
                  data-bs-toggle="dropdown">
                  <i class="bi bi-wrench"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end" role="menu">
                  <a href="#" class="dropdown-item">Action</a>
                  <a href="#" class="dropdown-item">Another action</a>
                  <a href="#" class="dropdown-item"> Something else here </a>
                  <a class="dropdown-divider"></a>
                  <a href="#" class="dropdown-item">Separated link</a>
                </div>
              </div>
              <button
                type="button"
                class="btn btn-tool"
                data-lte-toggle="card-remove"
                aria-label="{{ __('Remove card') }}">
                <i class="bi bi-x-lg"></i>
              </button>
            </div>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <!--begin::Row-->
            <div class="row">
              <div class="col-md-8">
                <p class="text-center">
                  <strong>Sales: 1 Jan, 2023 - 30 Jul, 2023</strong>
                </p>

                <div class="position-relative" style="height: 180px">
                  <canvas
                    id="sales-chart"
                    role="img"
                    aria-label="Area chart of digital goods and electronics sales, January to July 2023"></canvas>
                </div>
              </div>
              <!-- /.col -->
              <div class="col-md-4">
                <p class="text-center">
                  <strong>Goal Completion</strong>
                </p>

                <div class="progress-group">
                  Add Products to Cart
                  <span class="float-end"><b>160</b>/200</span>
                  <div class="progress progress-sm">
                    <div class="progress-bar text-bg-primary" style="width: 80%"></div>
                  </div>
                </div>
                <!-- /.progress-group -->

                <div class="progress-group">
                  Complete Purchase
                  <span class="float-end"><b>310</b>/400</span>
                  <div class="progress progress-sm">
                    <div class="progress-bar text-bg-danger" style="width: 75%"></div>
                  </div>
                </div>

                <!-- /.progress-group -->
                <div class="progress-group">
                  <span class="progress-text">Visit Premium Page</span>
                  <span class="float-end"><b>480</b>/800</span>
                  <div class="progress progress-sm">
                    <div class="progress-bar text-bg-success" style="width: 60%"></div>
                  </div>
                </div>

                <!-- /.progress-group -->
                <div class="progress-group">
                  Send Inquiries
                  <span class="float-end"><b>250</b>/500</span>
                  <div class="progress progress-sm">
                    <div class="progress-bar text-bg-warning" style="width: 50%"></div>
                  </div>
                </div>
                <!-- /.progress-group -->
              </div>
              <!-- /.col -->
            </div>
            <!--end::Row-->
          </div>
          <!-- ./card-body -->
          <div class="card-footer bg-body">
            <!--begin::Row-->
            <div class="row">
              <div class="col-md-3 col-6">
                <div class="text-center border-end">
                  <span class="text-success-emphasis">
                    <i class="bi bi-caret-up-fill"></i> 17%
                  </span>
                  <h5 class="fw-bold mb-0">$35,210.43</h5>
                  <span class="text-uppercase">TOTAL REVENUE</span>
                </div>
              </div>

              <!-- /.col -->
              <div class="col-md-3 col-6">
                <div class="text-center border-end">
                  <span class="text-info-emphasis">
                    <i class="bi bi-caret-left-fill"></i> 0%
                  </span>
                  <h5 class="fw-bold mb-0">$10,390.90</h5>
                  <span class="text-uppercase">TOTAL COST</span>
                </div>
              </div>
              <!-- /.col -->
              <div class="col-md-3 col-6">
                <div class="text-center border-end">
                  <span class="text-success-emphasis">
                    <i class="bi bi-caret-up-fill"></i> 20%
                  </span>
                  <h5 class="fw-bold mb-0">$24,813.53</h5>
                  <span class="text-uppercase">TOTAL PROFIT</span>
                </div>
              </div>
              <!-- /.col -->
              <div class="col-md-3 col-6">
                <div class="text-center">
                  <span class="text-danger-emphasis">
                    <i class="bi bi-caret-down-fill"></i> 18%
                  </span>
                  <h5 class="fw-bold mb-0">1200</h5>
                  <span class="text-uppercase">GOAL COMPLETIONS</span>
                </div>
              </div>
            </div>
            <!--end::Row-->
          </div>
          <!-- /.card-footer -->
        </div>
        <!-- /.card -->
      </div>
      <!-- /.col -->
    </div>
    <!--end::Row-->

    <!--begin::Row-->
    <div class="row">
      <!-- Start col -->
      <div class="col-md-8">
        <!--begin::Row-->
        <div class="row g-4 mb-4">
          <div class="col-md-6">
            <!-- DIRECT CHAT -->
            <div class="card direct-chat direct-chat-warning">
              <div class="card-header">
                <h3 class="card-title">{{ __('Direct Chat') }}</h3>

                <div class="card-tools">
                  <span title="3 New Messages" class="badge text-bg-warning"> 3 </span>
                  <button
                    type="button"
                    class="btn btn-tool"
                    data-lte-toggle="card-collapse"
                    aria-label="{{ __('Collapse card') }}">
                    <i data-lte-icon="expand" class="bi bi-plus-lg"></i>
                    <i data-lte-icon="collapse" class="bi bi-dash-lg"></i>
                  </button>
                  <button
                    type="button"
                    class="btn btn-tool"
                    title="Contacts"
                    data-lte-toggle="chat-pane">
                    <i class="bi bi-chat-text-fill"></i>
                  </button>
                  <button
                    type="button"
                    class="btn btn-tool"
                    data-lte-toggle="card-remove"
                    aria-label="{{ __('Remove card') }}">
                    <i class="bi bi-x-lg"></i>
                  </button>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <!-- Conversations are loaded here -->
                <div
                  class="direct-chat-messages"
                  role="log"
                  tabindex="0"
                  aria-label="Chat messages">
                  <!-- Message. Default to the start -->
                  <div class="direct-chat-msg">
                    <div class="direct-chat-infos clearfix">
                      <span class="direct-chat-name float-start"> Alexander Pierce </span>
                      <span class="direct-chat-timestamp float-end"> 23 Jan 2:00 pm </span>
                    </div>
                    <!-- /.direct-chat-infos -->
                    <img
                      class="direct-chat-img"
                      src="{{ asset('adminlte/assets/img/user1-128x128.jpg') }}"
                      alt="message user image" />
                    <!-- /.direct-chat-img -->
                    <div class="direct-chat-text">
                      Is this template really for free? That's unbelievable!
                    </div>
                    <!-- /.direct-chat-text -->
                  </div>
                  <!-- /.direct-chat-msg -->

                  <!-- Message to the end -->
                  <div class="direct-chat-msg end">
                    <div class="direct-chat-infos clearfix">
                      <span class="direct-chat-name float-end"> Sarah Bullock </span>
                      <span class="direct-chat-timestamp float-start">
                        23 Jan 2:05 pm
                      </span>
                    </div>
                    <!-- /.direct-chat-infos -->
                    <img
                      class="direct-chat-img"
                      src="{{ asset('adminlte/assets/img/user3-128x128.jpg') }}"
                      alt="message user image" />
                    <!-- /.direct-chat-img -->
                    <div class="direct-chat-text">You better believe it!</div>
                    <!-- /.direct-chat-text -->
                  </div>
                  <!-- /.direct-chat-msg -->

                  <!-- Message. Default to the start -->
                  <div class="direct-chat-msg">
                    <div class="direct-chat-infos clearfix">
                      <span class="direct-chat-name float-start"> Alexander Pierce </span>
                      <span class="direct-chat-timestamp float-end"> 23 Jan 5:37 pm </span>
                    </div>
                    <!-- /.direct-chat-infos -->
                    <img
                      class="direct-chat-img"
                      src="{{ asset('adminlte/assets/img/user1-128x128.jpg') }}"
                      alt="message user image" />
                    <!-- /.direct-chat-img -->
                    <div class="direct-chat-text">
                      Working with AdminLTE on a great new app! Wanna join?
                    </div>
                    <!-- /.direct-chat-text -->
                  </div>
                  <!-- /.direct-chat-msg -->

                  <!-- Message to the end -->
                  <div class="direct-chat-msg end">
                    <div class="direct-chat-infos clearfix">
                      <span class="direct-chat-name float-end"> Sarah Bullock </span>
                      <span class="direct-chat-timestamp float-start">
                        23 Jan 6:10 pm
                      </span>
                    </div>
                    <!-- /.direct-chat-infos -->
                    <img
                      class="direct-chat-img"
                      src="{{ asset('adminlte/assets/img/user3-128x128.jpg') }}"
                      alt="message user image" />
                    <!-- /.direct-chat-img -->
                    <div class="direct-chat-text">I would love to.</div>
                    <!-- /.direct-chat-text -->
                  </div>
                  <!-- /.direct-chat-msg -->
                </div>
                <!-- /.direct-chat-messages-->

                <!-- Contacts are loaded here -->
                <div class="direct-chat-contacts">
                  <ul class="contacts-list">
                    <li>
                      <a href="#">
                        <img
                          class="contacts-list-img"
                          src="{{ asset('adminlte/assets/img/user1-128x128.jpg') }}"
                          alt="User Avatar" />

                        <div class="contacts-list-info">
                          <span class="contacts-list-name">
                            Count Dracula
                            <small class="contacts-list-date float-end"> 2/28/2023 </small>
                          </span>
                          <span class="contacts-list-msg">
                            How have you been? I was...
                          </span>
                        </div>
                        <!-- /.contacts-list-info -->
                      </a>
                    </li>
                    <!-- End Contact Item -->
                    <li>
                      <a href="#">
                        <img
                          class="contacts-list-img"
                          src="{{ asset('adminlte/assets/img/user7-128x128.jpg') }}"
                          alt="User Avatar" />

                        <div class="contacts-list-info">
                          <span class="contacts-list-name">
                            Sarah Doe
                            <small class="contacts-list-date float-end"> 2/23/2023 </small>
                          </span>
                          <span class="contacts-list-msg"> I will be waiting for... </span>
                        </div>
                        <!-- /.contacts-list-info -->
                      </a>
                    </li>
                    <!-- End Contact Item -->
                    <li>
                      <a href="#">
                        <img
                          class="contacts-list-img"
                          src="{{ asset('adminlte/assets/img/user3-128x128.jpg') }}"
                          alt="User Avatar" />

                        <div class="contacts-list-info">
                          <span class="contacts-list-name">
                            Nadia Jolie
                            <small class="contacts-list-date float-end"> 2/20/2023 </small>
                          </span>
                          <span class="contacts-list-msg"> I'll call you back at... </span>
                        </div>
                        <!-- /.contacts-list-info -->
                      </a>
                    </li>
                    <!-- End Contact Item -->
                    <li>
                      <a href="#">
                        <img
                          class="contacts-list-img"
                          src="{{ asset('adminlte/assets/img/user5-128x128.jpg') }}"
                          alt="User Avatar" />

                        <div class="contacts-list-info">
                          <span class="contacts-list-name">
                            Nora S. Vans
                            <small class="contacts-list-date float-end"> 2/10/2023 </small>
                          </span>
                          <span class="contacts-list-msg"> Where is your new... </span>
                        </div>
                        <!-- /.contacts-list-info -->
                      </a>
                    </li>
                    <!-- End Contact Item -->
                    <li>
                      <a href="#">
                        <img
                          class="contacts-list-img"
                          src="{{ asset('adminlte/assets/img/user6-128x128.jpg') }}"
                          alt="User Avatar" />

                        <div class="contacts-list-info">
                          <span class="contacts-list-name">
                            John K.
                            <small class="contacts-list-date float-end"> 1/27/2023 </small>
                          </span>
                          <span class="contacts-list-msg"> Can I take a look at... </span>
                        </div>
                        <!-- /.contacts-list-info -->
                      </a>
                    </li>
                    <!-- End Contact Item -->
                    <li>
                      <a href="#">
                        <img
                          class="contacts-list-img"
                          src="{{ asset('adminlte/assets/img/user8-128x128.jpg') }}"
                          alt="User Avatar" />

                        <div class="contacts-list-info">
                          <span class="contacts-list-name">
                            Kenneth M.
                            <small class="contacts-list-date float-end"> 1/4/2023 </small>
                          </span>
                          <span class="contacts-list-msg"> Never mind I found... </span>
                        </div>
                        <!-- /.contacts-list-info -->
                      </a>
                    </li>
                    <!-- End Contact Item -->
                  </ul>
                  <!-- /.contacts-list -->
                </div>
                <!-- /.direct-chat-pane -->
              </div>
              <!-- /.card-body -->
              <div class="card-footer bg-body">
                <div>
                  <div class="input-group">
                    <input
                      type="text"
                      aria-label="Mensaje de demostración" disabled
                      placeholder="Type Message ..."
                      class="form-control" />
                    <span class="input-group-append">
                      <button type="button" class="btn btn-warning" disabled>{{ __('Send') }}</button>
                    </span>
                  </div>
                </div>
              </div>
              <!-- /.card-footer-->
            </div>
            <!-- /.direct-chat -->
          </div>
          <!-- /.col -->

          <div class="col-md-6">
            <!-- USERS LIST -->
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">{{ __('Latest Members') }}</h3>

                <div class="card-tools">
                  <span class="badge text-bg-danger"> 8 New Members </span>
                  <button
                    type="button"
                    class="btn btn-tool"
                    data-lte-toggle="card-collapse"
                    aria-label="{{ __('Collapse card') }}">
                    <i data-lte-icon="expand" class="bi bi-plus-lg"></i>
                    <i data-lte-icon="collapse" class="bi bi-dash-lg"></i>
                  </button>
                  <button
                    type="button"
                    class="btn btn-tool"
                    data-lte-toggle="card-remove"
                    aria-label="{{ __('Remove card') }}">
                    <i class="bi bi-x-lg"></i>
                  </button>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body p-0">
                <div class="row text-center m-1">
                  <div class="col-3 p-2">
                    <img
                      class="img-fluid rounded-circle"
                      src="{{ asset('adminlte/assets/img/user1-128x128.jpg') }}"
                      alt="User Image" />
                    <a
                      class="btn fw-bold fs-7 text-body-secondary text-truncate w-100 p-0"
                      href="#">
                      Alexander Pierce
                    </a>
                    <div class="fs-8">Today</div>
                  </div>
                  <div class="col-3 p-2">
                    <img
                      class="img-fluid rounded-circle"
                      src="{{ asset('adminlte/assets/img/user1-128x128.jpg') }}"
                      alt="User Image" />
                    <a
                      class="btn fw-bold fs-7 text-body-secondary text-truncate w-100 p-0"
                      href="#">
                      Norman
                    </a>
                    <div class="fs-8">Yesterday</div>
                  </div>
                  <div class="col-3 p-2">
                    <img
                      class="img-fluid rounded-circle"
                      src="{{ asset('adminlte/assets/img/user7-128x128.jpg') }}"
                      alt="User Image" />
                    <a
                      class="btn fw-bold fs-7 text-body-secondary text-truncate w-100 p-0"
                      href="#">
                      Jane
                    </a>
                    <div class="fs-8">12 Jan</div>
                  </div>
                  <div class="col-3 p-2">
                    <img
                      class="img-fluid rounded-circle"
                      src="{{ asset('adminlte/assets/img/user6-128x128.jpg') }}"
                      alt="User Image" />
                    <a
                      class="btn fw-bold fs-7 text-body-secondary text-truncate w-100 p-0"
                      href="#">
                      John
                    </a>
                    <div class="fs-8">12 Jan</div>
                  </div>
                  <div class="col-3 p-2">
                    <img
                      class="img-fluid rounded-circle"
                      src="{{ asset('adminlte/assets/img/user2-160x160.jpg') }}"
                      alt="User Image" />
                    <a
                      class="btn fw-bold fs-7 text-body-secondary text-truncate w-100 p-0"
                      href="#">
                      Alexander
                    </a>
                    <div class="fs-8">13 Jan</div>
                  </div>
                  <div class="col-3 p-2">
                    <img
                      class="img-fluid rounded-circle"
                      src="{{ asset('adminlte/assets/img/user5-128x128.jpg') }}"
                      alt="User Image" />
                    <a
                      class="btn fw-bold fs-7 text-body-secondary text-truncate w-100 p-0"
                      href="#">
                      Sarah
                    </a>
                    <div class="fs-8">14 Jan</div>
                  </div>
                  <div class="col-3 p-2">
                    <img
                      class="img-fluid rounded-circle"
                      src="{{ asset('adminlte/assets/img/user4-128x128.jpg') }}"
                      alt="User Image" />
                    <a
                      class="btn fw-bold fs-7 text-body-secondary text-truncate w-100 p-0"
                      href="#">
                      Nora
                    </a>
                    <div class="fs-8">15 Jan</div>
                  </div>
                  <div class="col-3 p-2">
                    <img
                      class="img-fluid rounded-circle"
                      src="{{ asset('adminlte/assets/img/user3-128x128.jpg') }}"
                      alt="User Image" />
                    <a
                      class="btn fw-bold fs-7 text-body-secondary text-truncate w-100 p-0"
                      href="#">
                      Nadia
                    </a>
                    <div class="fs-8">15 Jan</div>
                  </div>
                </div>
                <!-- /.users-list -->
              </div>
              <!-- /.card-body -->
              <div class="card-footer bg-body text-center">
                <a
                  href="javascript:"
                  class="link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover">{{ __('View All Users') }}</a>
              </div>
              <!-- /.card-footer -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!--end::Row-->

        <!--begin::Latest Order Widget-->
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">{{ __('Latest Orders') }}</h3>

            <div class="card-tools">
              <button
                type="button"
                class="btn btn-tool"
                data-lte-toggle="card-collapse"
                aria-label="{{ __('Collapse card') }}">
                <i data-lte-icon="expand" class="bi bi-plus-lg"></i>
                <i data-lte-icon="collapse" class="bi bi-dash-lg"></i>
              </button>
              <button
                type="button"
                class="btn btn-tool"
                data-lte-toggle="card-remove"
                aria-label="{{ __('Remove card') }}">
                <i class="bi bi-x-lg"></i>
              </button>
            </div>
          </div>
          <!-- /.card-header -->
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table m-0">
                <thead>
                  <tr>
                    <th>{{ __('Order ID') }}</th>
                    <th>{{ __('Item') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Popularity') }}</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>
                      <a
                        href="pages/invoice.html"
                        class="link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover">OR9842</a>
                    </td>
                    <td>Call of Duty IV</td>
                    <td>
                      <span class="badge text-bg-success"> {{ __('Shipped') }} </span>
                    </td>
                    <td>
                      <div class="position-relative" style="width: 150px; height: 30px">
                        <canvas
                          id="table-sparkline-1"
                          role="img"
                          aria-label="Popularity trend"></canvas>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <a
                        href="pages/invoice.html"
                        class="link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover">OR1848</a>
                    </td>
                    <td>Samsung Smart TV</td>
                    <td>
                      <span class="badge text-bg-warning">{{ __('Pending') }}</span>
                    </td>
                    <td>
                      <div class="position-relative" style="width: 150px; height: 30px">
                        <canvas
                          id="table-sparkline-2"
                          role="img"
                          aria-label="Popularity trend"></canvas>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <a
                        href="pages/invoice.html"
                        class="link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover">OR7429</a>
                    </td>
                    <td>iPhone 6 Plus</td>
                    <td>
                      <span class="badge text-bg-danger"> {{ __('Delivered') }} </span>
                    </td>
                    <td>
                      <div class="position-relative" style="width: 150px; height: 30px">
                        <canvas
                          id="table-sparkline-3"
                          role="img"
                          aria-label="Popularity trend"></canvas>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <a
                        href="pages/invoice.html"
                        class="link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover">OR7429</a>
                    </td>
                    <td>Samsung Smart TV</td>
                    <td>
                      <span class="badge text-bg-info">{{ __('Processing') }}</span>
                    </td>
                    <td>
                      <div class="position-relative" style="width: 150px; height: 30px">
                        <canvas
                          id="table-sparkline-4"
                          role="img"
                          aria-label="Popularity trend"></canvas>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <a
                        href="pages/invoice.html"
                        class="link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover">OR1848</a>
                    </td>
                    <td>Samsung Smart TV</td>
                    <td>
                      <span class="badge text-bg-warning">{{ __('Pending') }}</span>
                    </td>
                    <td>
                      <div class="position-relative" style="width: 150px; height: 30px">
                        <canvas
                          id="table-sparkline-5"
                          role="img"
                          aria-label="Popularity trend"></canvas>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <a
                        href="pages/invoice.html"
                        class="link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover">OR7429</a>
                    </td>
                    <td>iPhone 6 Plus</td>
                    <td>
                      <span class="badge text-bg-danger"> {{ __('Delivered') }} </span>
                    </td>
                    <td>
                      <div class="position-relative" style="width: 150px; height: 30px">
                        <canvas
                          id="table-sparkline-6"
                          role="img"
                          aria-label="Popularity trend"></canvas>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <a
                        href="pages/invoice.html"
                        class="link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover">OR9842</a>
                    </td>
                    <td>Call of Duty IV</td>
                    <td>
                      <span class="badge text-bg-success">{{ __('Shipped') }}</span>
                    </td>
                    <td>
                      <div class="position-relative" style="width: 150px; height: 30px">
                        <canvas
                          id="table-sparkline-7"
                          role="img"
                          aria-label="Popularity trend"></canvas>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <!-- /.table-responsive -->
          </div>
          <!-- /.card-body -->
          <div class="card-footer bg-body clearfix">
            <a href="javascript:void(0)" class="btn btn-sm btn-primary float-start">
              {{ __('Place New Order') }}
            </a>
            <a href="javascript:void(0)" class="btn btn-sm btn-secondary float-end">
              {{ __('View All Orders') }}
            </a>
          </div>
          <!-- /.card-footer -->
        </div>
        <!-- /.card -->
      </div>
      <!-- /.col -->

      <div class="col-md-4">
        <!-- Info Boxes Style 2 -->
        <div class="info-box mb-3 text-bg-warning">
          <span class="info-box-icon">
            <i class="bi bi-tag-fill"></i>
          </span>

          <div class="info-box-content">
            <span class="info-box-text">{{ __('Inventory') }}</span>
            <span class="info-box-number">5,200</span>
          </div>
          <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
        <div class="info-box mb-3 text-bg-success">
          <span class="info-box-icon">
            <i class="bi bi-heart-fill"></i>
          </span>

          <div class="info-box-content">
            <span class="info-box-text">{{ __('Mentions') }}</span>
            <span class="info-box-number">92,050</span>
          </div>
          <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
        <div class="info-box mb-3 text-bg-danger">
          <span class="info-box-icon">
            <i class="bi bi-cloud-download"></i>
          </span>

          <div class="info-box-content">
            <span class="info-box-text">{{ __('Downloads') }}</span>
            <span class="info-box-number">114,381</span>
          </div>
          <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
        <div class="info-box mb-3 text-bg-info">
          <span class="info-box-icon">
            <i class="bi bi-chat-fill"></i>
          </span>

          <div class="info-box-content">
            <span class="info-box-text">{{ __('Direct Messages') }}</span>
            <span class="info-box-number">163,921</span>
          </div>
          <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->

        <div class="card mb-4">
          <div class="card-header">
            <h3 class="card-title">{{ __('Browser Usage') }}</h3>

            <div class="card-tools">
              <button
                type="button"
                class="btn btn-tool"
                data-lte-toggle="card-collapse"
                aria-label="{{ __('Collapse card') }}">
                <i data-lte-icon="expand" class="bi bi-plus-lg"></i>
                <i data-lte-icon="collapse" class="bi bi-dash-lg"></i>
              </button>
              <button
                type="button"
                class="btn btn-tool"
                data-lte-toggle="card-remove"
                aria-label="{{ __('Remove card') }}">
                <i class="bi bi-x-lg"></i>
              </button>
            </div>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <!--begin::Row-->
            <div class="row">
              <div class="col-12">
                <div class="position-relative" style="height: 350px">
                  <canvas
                    id="pie-chart"
                    role="img"
                    aria-label="Donut chart of browser usage"></canvas>
                </div>
              </div>
              <!-- /.col -->
            </div>
            <!--end::Row-->
          </div>
          <!-- /.card-body -->
          <div class="card-footer bg-body p-0">
            <ul class="nav nav-pills flex-column">
              <li class="nav-item">
                <a href="#" class="nav-link">
                  {{ __('United States of America') }}
                  <span class="float-end text-danger-emphasis">
                    <i class="bi bi-arrow-down fs-7"></i>
                    12%
                  </span>
                </a>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link">
                  {{ __('India') }}
                  <span class="float-end text-success-emphasis">
                    <i class="bi bi-arrow-up fs-7"></i> 4%
                  </span>
                </a>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link">
                  {{ __('China') }}
                  <span class="float-end text-info-emphasis">
                    <i class="bi bi-arrow-left fs-7"></i> 0%
                  </span>
                </a>
              </li>
            </ul>
          </div>
          <!-- /.footer -->
        </div>
        <!-- /.card -->

        @if ($productosRecientes !== null)
          @include('partials.listado-modal', ['iconoModal' => 'bi-box-seam'])
          <div class="card">
            <div class="card-header d-flex align-items-center">
              <h3 class="card-title">{{ __('Últimos productos registrados') }}</h3>
              <button type="button" class="btn btn-tool ms-auto" data-lte-toggle="card-collapse" aria-label="{{ __('Contraer tarjeta') }}">
                <i data-lte-icon="expand" class="bi bi-plus-lg" aria-hidden="true"></i>
                <i data-lte-icon="collapse" class="bi bi-dash-lg" aria-hidden="true"></i>
              </button>
            </div>
            <div class="card-body p-0">
              <ul class="list-group list-group-flush">
                @forelse ($productosRecientes as $producto)
                  <li class="list-group-item d-flex gap-3 align-items-center">
                    @if ($producto->id_imagen)
                      <img width="48" height="48" class="rounded object-fit-contain" loading="lazy" alt=""
                        src="{{ route('inventarios.productos.imagen', [$producto->id, $producto->id_imagen]) }}">
                    @else
                      <span class="rounded border p-2 fs-5 text-body-secondary" aria-hidden="true"><i class="bi bi-box-seam"></i></span>
                    @endif
                    <div class="flex-grow-1 overflow-hidden">
                      <a class="fw-semibold text-break" href="{{ route('inventarios.productos.show', $producto->id) }}"
                        data-ajax-open="{{ $producto->nombre }}">{{ $producto->nombre }}</a>
                      <div class="text-truncate text-body-secondary small">{{ $producto->descripcion ?: $producto->codigo }}</div>
                    </div>
                    @if ($producto->precio_venta !== null)
                      <span class="badge text-bg-secondary">USD {{ number_format($producto->precio_venta / 100, 2) }}</span>
                    @endif
                  </li>
                @empty
                  <li class="list-group-item text-body-secondary">{{ __('Todavía no hay productos registrados.') }}</li>
                @endforelse
              </ul>
            </div>
            <div class="card-footer text-center">
              <a href="{{ route('inventarios.productos.index') }}" data-workspace-link="inventarios.productos.index" data-workspace-label="{{ __('Productos') }}">{{ __('Ver todos los productos') }}</a>
            </div>
          </div>
        @endif
      </div>
      <!-- /.col -->
    </div>
    <!--end::Row-->
  </div>
  <!--end::Container-->
</div>
<!--end::App Content-->
@endsection

@push('scripts')
<!-- Chart.js + AdminLTE chart theme -->
<!--begin::Third Party Plugin(Chart.js)-->
<script nonce="{{ request()->attributes->get('csp_nonce') }}"
  src="https://cdn.jsdelivr.net/npm/chart.js@4.6.1/dist/chart.umd.min.js"
  integrity="sha256-hzZMwrKnamaEkp59a4Cxiol3lNs4QAyv8fzxeRxpJdI="
  crossorigin="anonymous"></script>
<!--end::Third Party Plugin(Chart.js)-->
<!--begin::Chart.js theme preset-->
<script nonce="{{ request()->attributes->get('csp_nonce') }}">
  // Styles every Chart.js chart on the page from AdminLTE's CSS variables —
  // font, text, gridlines, tooltip and legend — and keeps them in step with the
  // colour mode (ColorMode, the OS in auto mode, or your own code) and with
  // `dir="rtl"`. Canvas can't read CSS, so the values are resolved here and
  // re-applied to every chart whenever <html data-bs-theme> or dir changes.
  (() => {
    'use strict';
    const {
      Chart
    } = globalThis;
    if (!Chart) {
      return;
    }

    const root = document.documentElement;
    const cssVar = (name, fallback = '') =>
      getComputedStyle(root).getPropertyValue(name).trim() || fallback;

    // "#0d6efd" (or "#06f") + 0.3 -> "rgba(13, 110, 253, 0.3)"; anything else is returned as is.
    const alpha = (color, opacity) => {
      let hex = String(color).trim().replace(/^#/, '');
      if (!/^[\da-f]{3}([\da-f]{3})?$/i.test(hex)) {
        return color;
      }

      if (hex.length === 3) {
        hex = [...hex].map((c) => c + c).join('');
      }

      const [r, g, b] = [0, 2, 4].map((i) => Number.parseInt(hex.slice(i, i + 2), 16));
      return `rgba(${r}, ${g}, ${b}, ${opacity})`;
    };

    // Scriptable backgroundColor for area charts: the line colour fading to transparent.
    const areaFill =
      (color, from = 0.35, to = 0) =>
      (context) => {
        const {
          ctx,
          chartArea
        } = context.chart;
        if (!chartArea) {
          return alpha(color, from);
        }

        const gradient = ctx.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
        gradient.addColorStop(0, alpha(color, from));
        gradient.addColorStop(1, alpha(color, to));
        return gradient;
      };

    // The theme colours currently in Chart.defaults.
    let applied = [];

    const apply = () => {
      const text = cssVar('--bs-secondary-color', '#6c757d');
      const body = cssVar('--bs-body-color', '#212529');
      const surface = cssVar('--bs-body-bg', '#fff');
      const grid = `rgba(${cssVar('--bs-emphasis-color-rgb', '0, 0, 0')}, 0.08)`;
      const rtl = root.getAttribute('dir') === 'rtl';
      const d = Chart.defaults;
      applied = [text, grid];

      d.font.family = cssVar('--bs-body-font-family', d.font.family);
      d.font.size = 12;
      d.color = text;
      d.borderColor = grid;
      d.maintainAspectRatio = false;

      d.scale.grid.color = grid;
      d.scale.border.color = grid;
      d.scale.ticks.padding = 8;
      // Value axes float, category axes keep only their baseline.
      d.set('scales.linear', {
        border: {
          display: false
        }
      });
      d.set('scales.category', {
        grid: {
          display: false
        }
      });

      d.elements.line.borderWidth = 2;
      d.elements.point.radius = 0;
      d.elements.point.hoverRadius = 5;
      d.elements.point.hitRadius = 8;
      d.elements.point.hoverBorderWidth = 2;
      d.elements.point.borderColor = surface;
      d.elements.bar.borderRadius = 4;
      d.elements.arc.borderColor = surface;
      d.elements.arc.borderWidth = 2;

      const {
        legend,
        tooltip
      } = d.plugins;
      legend.position = 'bottom';
      legend.rtl = rtl;
      Object.assign(legend.labels, {
        color: body,
        usePointStyle: true,
        pointStyle: 'circle',
        boxWidth: 8,
        boxHeight: 8,
        padding: 16,
      });

      Object.assign(tooltip, {
        rtl,
        backgroundColor: surface,
        borderColor: cssVar('--bs-border-color', '#dee2e6'),
        borderWidth: 1,
        titleColor: cssVar('--bs-emphasis-color', '#000'),
        bodyColor: body,
        titleFont: {
          weight: '600'
        },
        padding: 10,
        cornerRadius: 6,
        caretSize: 5,
        boxPadding: 4,
        usePointStyle: true,
      });

      // Line and bar charts share one tooltip per x value, like a dashboard should.
      for (const type of ['line', 'bar']) {
        Chart.overrides[type].interaction = {
          mode: 'index',
          intersect: false
        };
      }
    };

    // Chart.js copies the scale defaults (tick, grid, border and title colours)
    // into each chart's config when the chart is created, so new defaults alone
    // never reach an existing chart's axes. Drop the copies that still hold the
    // previous theme's colours; update() then copies the new ones in. Colours a
    // chart set for itself are left alone.
    const forgetThemeColors = (options, stale) => {
      for (const [key, value] of Object.entries(options)) {
        if (value && typeof value === 'object' && !Array.isArray(value)) {
          forgetThemeColors(value, stale);
        } else if (/color$/i.test(key) && stale.includes(value)) {
          delete options[key];
        }
      }
    };

    const refresh = () => {
      const stale = applied;
      apply();
      for (const chart of Object.values(Chart.instances)) {
        forgetThemeColors(chart.config.options.scales || {}, stale);
        chart.update('none');
      }
    };

    apply();
    new MutationObserver(refresh).observe(root, {
      attributes: true,
      attributeFilter: ['data-bs-theme', 'dir'],
    });
    // The web font may arrive after the first draw; redraw so labels use it.
    document.fonts?.addEventListener('loadingdone', refresh);

    // Helpers for the page's own chart code.
    // eslint-disable-next-line unicorn/no-global-object-property-assignment
    globalThis.lteChartTheme = {
      cssVar,
      alpha,
      areaFill,
      refresh
    };
  })();
</script>
<!--end::Chart.js theme preset-->

<script nonce="{{ request()->attributes->get('csp_nonce') }}">
  (() => {
    if (!globalThis.lteChartTheme) {
      return;
    }

    const {
      cssVar,
      areaFill
    } = globalThis.lteChartTheme;

    //-----------------------
    // - MONTHLY SALES CHART -
    //-----------------------

    new Chart(document.querySelector('#sales-chart'), {
      type: 'line',
      data: {
        labels: ["Jan '23", "Feb '23", "Mar '23", "Apr '23", "May '23", "Jun '23", "Jul '23"],
        datasets: [{
            label: 'Digital Goods',
            data: [28, 48, 40, 19, 86, 27, 90],
            borderColor: '#0d6efd',
            backgroundColor: areaFill('#0d6efd'),
            pointBackgroundColor: '#0d6efd',
            fill: true,
            tension: 0.4,
          },
          {
            label: 'Electronics',
            data: [65, 59, 80, 81, 56, 55, 40],
            borderColor: '#20c997',
            backgroundColor: areaFill('#20c997'),
            pointBackgroundColor: '#20c997',
            fill: true,
            tension: 0.4,
          },
        ],
      },
      options: {
        plugins: {
          legend: {
            display: false,
          },
          tooltip: {
            callbacks: {
              title: ([item]) =>
                `${new Date(2023, item.dataIndex).toLocaleString('en', { month: 'long' })} 2023`,
            },
          },
        },
        scales: {
          y: {
            beginAtZero: true,
          },
        },
      },
    });

    //---------------------------
    // - END MONTHLY SALES CHART -
    //---------------------------

    function createSparklineChart(selector, data) {
      const primary = cssVar('--bs-primary', '#0d6efd');

      return new Chart(document.querySelector(selector), {
        type: 'line',
        data: {
          labels: data.map((value, index) => index + 1),
          datasets: [{
            data,
            borderColor: primary,
            backgroundColor: primary,
            pointHoverRadius: 3,
          }, ],
        },
        options: {
          layout: {
            padding: 3,
          },
          plugins: {
            legend: {
              display: false,
            },
            tooltip: {
              displayColors: false,
              padding: 6,
              callbacks: {
                title: () => '',
              },
            },
          },
          scales: {
            x: {
              display: false,
            },
            y: {
              display: false,
            },
          },
        },
      });
    }

    const table_sparkline_1_data = [25, 66, 41, 89, 63, 25, 44, 12, 36, 9, 54];
    const table_sparkline_2_data = [12, 56, 21, 39, 73, 45, 64, 52, 36, 59, 44];
    const table_sparkline_3_data = [15, 46, 21, 59, 33, 15, 34, 42, 56, 19, 64];
    const table_sparkline_4_data = [30, 56, 31, 69, 43, 35, 24, 32, 46, 29, 64];
    const table_sparkline_5_data = [20, 76, 51, 79, 53, 35, 54, 22, 36, 49, 64];
    const table_sparkline_6_data = [5, 36, 11, 69, 23, 15, 14, 42, 26, 19, 44];
    const table_sparkline_7_data = [12, 56, 21, 39, 73, 45, 64, 52, 36, 59, 74];

    createSparklineChart('#table-sparkline-1', table_sparkline_1_data);
    createSparklineChart('#table-sparkline-2', table_sparkline_2_data);
    createSparklineChart('#table-sparkline-3', table_sparkline_3_data);
    createSparklineChart('#table-sparkline-4', table_sparkline_4_data);
    createSparklineChart('#table-sparkline-5', table_sparkline_5_data);
    createSparklineChart('#table-sparkline-6', table_sparkline_6_data);
    createSparklineChart('#table-sparkline-7', table_sparkline_7_data);

    //-------------
    // - PIE CHART -
    //-------------

    new Chart(document.querySelector('#pie-chart'), {
      type: 'doughnut',
      data: {
        labels: ['Chrome', 'Edge', 'FireFox', 'Safari', 'Opera', 'IE'],
        datasets: [{
          label: 'Users',
          data: [700, 500, 400, 600, 300, 100],
          backgroundColor: ['#0d6efd', '#20c997', '#ffc107', '#d63384', '#6f42c1', '#adb5bd'],
        }, ],
      },
      options: {
        cutout: '65%',
        plugins: {
          legend: {
            position: 'right',
          },
        },
      },
    });

    //-----------------
    // - END PIE CHART -
    //-----------------
  })();
</script>
@endpush
