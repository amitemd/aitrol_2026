@extends('layouts.adminlte')

@section('title', 'Panel general')

@push('styles')
    <!-- jsvectormap -->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/jsvectormap@1.7.0/dist/jsvectormap.min.css"
      integrity="sha256-Zshqz6X+Rs2Rf5sPtiSba+rwLBL1ZTyLBppbYOstR9M="
      crossorigin="anonymous"
    />
@endpush

@section('content')

        <!--begin::App Content Header-->
        <div class="app-content-header">
          <!--begin::Container-->
          <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
              <div class="col-sm-6">
                <h1 class="mb-0 fs-3">Panel general</h1>
              </div>
              <div class="col-sm-6">
                <nav aria-label="breadcrumb">
                  <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Panel general</li>
                  </ol>
                </nav>
              </div>
            </div>
            <!--end::Row-->
          </div>
          <!--end::Container-->
        </div>
        <!--end::App Content Header-->
        <!--begin::App Content-->
        <div class="app-content">
          <!--begin::Container-->
          <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
              <!--begin::Col-->
              <div class="col-lg-3 col-6">
                <!--begin::Small Box Widget 1-->
                <div class="small-box text-bg-primary">
                  <div class="inner">
                    <h3>150</h3>

                    <p>New Orders</p>
                  </div>
                  <svg
                    class="small-box-icon"
                    fill="currentColor"
                    viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg"
                    aria-hidden="true"
                  >
                    <path
                      d="M2.25 2.25a.75.75 0 000 1.5h1.386c.17 0 .318.114.362.278l2.558 9.592a3.752 3.752 0 00-2.806 3.63c0 .414.336.75.75.75h15.75a.75.75 0 000-1.5H5.378A2.25 2.25 0 017.5 15h11.218a.75.75 0 00.674-.421 60.358 60.358 0 002.96-7.228.75.75 0 00-.525-.965A60.864 60.864 0 005.68 4.509l-.232-.867A1.875 1.875 0 003.636 2.25H2.25zM3.75 20.25a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0zM16.5 20.25a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0z"
                    ></path>
                  </svg>
                  <a
                    href="#"
                    class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover"
                  >
                    More info <i class="bi bi-link-45deg"></i>
                  </a>
                </div>
                <!--end::Small Box Widget 1-->
              </div>
              <!--end::Col-->
              <div class="col-lg-3 col-6">
                <!--begin::Small Box Widget 2-->
                <div class="small-box text-bg-success">
                  <div class="inner">
                    <h3>53<sup class="fs-5">%</sup></h3>

                    <p>Bounce Rate</p>
                  </div>
                  <svg
                    class="small-box-icon"
                    fill="currentColor"
                    viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg"
                    aria-hidden="true"
                  >
                    <path
                      d="M18.375 2.25c-1.035 0-1.875.84-1.875 1.875v15.75c0 1.035.84 1.875 1.875 1.875h.75c1.035 0 1.875-.84 1.875-1.875V4.125c0-1.036-.84-1.875-1.875-1.875h-.75zM9.75 8.625c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v11.25c0 1.035-.84 1.875-1.875 1.875h-.75a1.875 1.875 0 01-1.875-1.875V8.625zM3 13.125c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v6.75c0 1.035-.84 1.875-1.875 1.875h-.75A1.875 1.875 0 013 19.875v-6.75z"
                    ></path>
                  </svg>
                  <a
                    href="#"
                    class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover"
                  >
                    More info <i class="bi bi-link-45deg"></i>
                  </a>
                </div>
                <!--end::Small Box Widget 2-->
              </div>
              <!--end::Col-->
              <div class="col-lg-3 col-6">
                <!--begin::Small Box Widget 3-->
                <div class="small-box text-bg-warning">
                  <div class="inner">
                    <h3>44</h3>

                    <p>User Registrations</p>
                  </div>
                  <svg
                    class="small-box-icon"
                    fill="currentColor"
                    viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg"
                    aria-hidden="true"
                  >
                    <path
                      d="M6.25 6.375a4.125 4.125 0 118.25 0 4.125 4.125 0 01-8.25 0zM3.25 19.125a7.125 7.125 0 0114.25 0v.003l-.001.119a.75.75 0 01-.363.63 13.067 13.067 0 01-6.761 1.873c-2.472 0-4.786-.684-6.76-1.873a.75.75 0 01-.364-.63l-.001-.122zM19.75 7.5a.75.75 0 00-1.5 0v2.25H16a.75.75 0 000 1.5h2.25v2.25a.75.75 0 001.5 0v-2.25H22a.75.75 0 000-1.5h-2.25V7.5z"
                    ></path>
                  </svg>
                  <a
                    href="#"
                    class="small-box-footer link-dark link-underline-opacity-0 link-underline-opacity-50-hover"
                  >
                    More info <i class="bi bi-link-45deg"></i>
                  </a>
                </div>
                <!--end::Small Box Widget 3-->
              </div>
              <!--end::Col-->
              <div class="col-lg-3 col-6">
                <!--begin::Small Box Widget 4-->
                <div class="small-box text-bg-danger">
                  <div class="inner">
                    <h3>65</h3>

                    <p>Unique Visitors</p>
                  </div>
                  <svg
                    class="small-box-icon"
                    fill="currentColor"
                    viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg"
                    aria-hidden="true"
                  >
                    <path
                      clip-rule="evenodd"
                      fill-rule="evenodd"
                      d="M2.25 13.5a8.25 8.25 0 018.25-8.25.75.75 0 01.75.75v6.75H18a.75.75 0 01.75.75 8.25 8.25 0 01-16.5 0z"
                    ></path>
                    <path
                      clip-rule="evenodd"
                      fill-rule="evenodd"
                      d="M12.75 3a.75.75 0 01.75-.75 8.25 8.25 0 018.25 8.25.75.75 0 01-.75.75h-7.5a.75.75 0 01-.75-.75V3z"
                    ></path>
                  </svg>
                  <a
                    href="#"
                    class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover"
                  >
                    More info <i class="bi bi-link-45deg"></i>
                  </a>
                </div>
                <!--end::Small Box Widget 4-->
              </div>
              <!--end::Col-->
            </div>
            <!--end::Row-->
            <!--begin::Row-->
            <div class="row">
              <!-- Start col -->
              <div class="col-lg-7 connectedSortable">
                <div class="card mb-4">
                  <div class="card-header">
                    <h3 class="card-title">Sales Value</h3>
                  </div>

                  <div class="card-body">
                    <div class="position-relative" style="height: 300px">
                      <canvas
                        id="revenue-chart"
                        role="img"
                        aria-label="Area chart of digital goods and electronics sales, January to July 2023"
                      ></canvas>
                    </div>
                  </div>
                </div>
                <!-- /.card -->

                <!-- DIRECT CHAT -->
                <div class="card direct-chat direct-chat-primary mb-4">
                  <div class="card-header">
                    <h3 class="card-title">Direct Chat</h3>

                    <div class="card-tools">
                      <span title="3 New Messages" class="badge text-bg-primary"> 3 </span>
                      <button
                        type="button"
                        class="btn btn-tool"
                        data-lte-toggle="card-collapse"
                        aria-label="Collapse card"
                      >
                        <i data-lte-icon="expand" class="bi bi-plus-lg"></i>
                        <i data-lte-icon="collapse" class="bi bi-dash-lg"></i>
                      </button>
                      <button
                        type="button"
                        class="btn btn-tool"
                        title="Contacts"
                        data-lte-toggle="chat-pane"
                      >
                        <i class="bi bi-chat-text-fill"></i>
                      </button>
                      <button
                        type="button"
                        class="btn btn-tool"
                        data-lte-toggle="card-remove"
                        aria-label="Remove card"
                      >
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
                      aria-label="Chat messages"
                    >
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
                          alt="message user image"
                        />
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
                          <span class="direct-chat-timestamp float-start"> 23 Jan 2:05 pm </span>
                        </div>
                        <!-- /.direct-chat-infos -->
                        <img
                          class="direct-chat-img"
                          src="{{ asset('adminlte/assets/img/user3-128x128.jpg') }}"
                          alt="message user image"
                        />
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
                          alt="message user image"
                        />
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
                          <span class="direct-chat-timestamp float-start"> 23 Jan 6:10 pm </span>
                        </div>
                        <!-- /.direct-chat-infos -->
                        <img
                          class="direct-chat-img"
                          src="{{ asset('adminlte/assets/img/user3-128x128.jpg') }}"
                          alt="message user image"
                        />
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
                              alt="User Avatar"
                            />

                            <div class="contacts-list-info">
                              <span class="contacts-list-name">
                                Count Dracula
                                <small class="contacts-list-date float-end"> 2/28/2023 </small>
                              </span>
                              <span class="contacts-list-msg"> How have you been? I was... </span>
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
                              alt="User Avatar"
                            />

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
                              alt="User Avatar"
                            />

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
                              alt="User Avatar"
                            />

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
                              alt="User Avatar"
                            />

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
                              alt="User Avatar"
                            />

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
                  <div class="card-footer">
                    <form action="#" method="post">
                      <div class="input-group">
                        <input
                          type="text"
                          name="message"
                          placeholder="Type Message ..."
                          class="form-control"
                        />
                        <span class="input-group-append">
                          <button type="button" class="btn btn-primary">Send</button>
                        </span>
                      </div>
                    </form>
                  </div>
                  <!-- /.card-footer-->
                </div>
                <!-- /.direct-chat -->
              </div>
              <!-- /.Start col -->

              <!-- Start col -->
              <div class="col-lg-5 connectedSortable">
                <div class="card text-white bg-primary bg-gradient border-primary mb-4">
                  <div class="card-header border-0">
                    <h3 class="card-title">Sales Value</h3>
                    <div class="card-tools">
                      <button
                        type="button"
                        class="btn btn-primary btn-sm"
                        data-lte-toggle="card-collapse"
                        aria-label="Collapse card"
                      >
                        <i data-lte-icon="expand" class="bi bi-plus-lg"></i>
                        <i data-lte-icon="collapse" class="bi bi-dash-lg"></i>
                      </button>
                    </div>
                  </div>
                  <div class="card-body">
                    <div id="world-map" style="height: 220px"></div>
                  </div>
                  <div class="card-footer border-0">
                    <!--begin::Row-->
                    <div class="row">
                      <div class="col-4 text-center">
                        <div class="position-relative" style="height: 50px">
                          <canvas id="sparkline-1" role="img" aria-label="Visitors trend"></canvas>
                        </div>
                        <div class="text-white">Visitors</div>
                      </div>
                      <div class="col-4 text-center">
                        <div class="position-relative" style="height: 50px">
                          <canvas id="sparkline-2" role="img" aria-label="Online trend"></canvas>
                        </div>
                        <div class="text-white">Online</div>
                      </div>
                      <div class="col-4 text-center">
                        <div class="position-relative" style="height: 50px">
                          <canvas id="sparkline-3" role="img" aria-label="Sales trend"></canvas>
                        </div>
                        <div class="text-white">Sales</div>
                      </div>
                    </div>
                    <!--end::Row-->
                  </div>
                </div>
              </div>
              <!-- /.Start col -->
            </div>
            <!-- /.row (main row) -->
          </div>
          <!--end::Container-->
        </div>
        <!--end::App Content-->
@endsection

@push('scripts')
<!-- sortablejs -->
    <script
      src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.7/Sortable.min.js"
      crossorigin="anonymous"
    ></script>

    <!-- sortablejs -->
    <script>
      if (typeof Sortable !== 'undefined') {
        new Sortable(document.querySelector('.connectedSortable'), {
          group: 'shared',
          handle: '.card-header',
        });
      }

      const cardHeaders = document.querySelectorAll('.connectedSortable .card-header');
      cardHeaders.forEach((cardHeader) => {
        cardHeader.style.cursor = 'move';
      });
    </script>

    <!-- Chart.js + AdminLTE chart theme -->
    <!--begin::Third Party Plugin(Chart.js)-->
    <script
      src="https://cdn.jsdelivr.net/npm/chart.js@4.6.1/dist/chart.umd.min.js"
      integrity="sha256-hzZMwrKnamaEkp59a4Cxiol3lNs4QAyv8fzxeRxpJdI="
      crossorigin="anonymous"
    ></script>
    <!--end::Third Party Plugin(Chart.js)-->
    <!--begin::Chart.js theme preset-->
    <script>
      // Styles every Chart.js chart on the page from AdminLTE's CSS variables —
      // font, text, gridlines, tooltip and legend — and keeps them in step with the
      // colour mode (ColorMode, the OS in auto mode, or your own code) and with
      // `dir="rtl"`. Canvas can't read CSS, so the values are resolved here and
      // re-applied to every chart whenever <html data-bs-theme> or dir changes.
      (() => {
        'use strict';
        const { Chart } = globalThis;
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
            const { ctx, chartArea } = context.chart;
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
          d.set('scales.linear', { border: { display: false } });
          d.set('scales.category', { grid: { display: false } });

          d.elements.line.borderWidth = 2;
          d.elements.point.radius = 0;
          d.elements.point.hoverRadius = 5;
          d.elements.point.hitRadius = 8;
          d.elements.point.hoverBorderWidth = 2;
          d.elements.point.borderColor = surface;
          d.elements.bar.borderRadius = 4;
          d.elements.arc.borderColor = surface;
          d.elements.arc.borderWidth = 2;

          const { legend, tooltip } = d.plugins;
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
            titleFont: { weight: '600' },
            padding: 10,
            cornerRadius: 6,
            caretSize: 5,
            boxPadding: 4,
            usePointStyle: true,
          });

          // Line and bar charts share one tooltip per x value, like a dashboard should.
          for (const type of ['line', 'bar']) {
            Chart.overrides[type].interaction = { mode: 'index', intersect: false };
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
        globalThis.lteChartTheme = { cssVar, alpha, areaFill, refresh };
      })();
    </script>
    <!--end::Chart.js theme preset-->

    <!-- Chart.js -->
    <script>
      (() => {
        if (!globalThis.Chart || !globalThis.lteChartTheme) return;
        const { areaFill } = globalThis.lteChartTheme;

        new Chart(document.querySelector('#revenue-chart'), {
          type: 'line',
          data: {
            labels: ["Jan '23", "Feb '23", "Mar '23", "Apr '23", "May '23", "Jun '23", "Jul '23"],
            datasets: [
              {
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
      })();
    </script>

    <!-- jsvectormap -->
    <script
      src="https://cdn.jsdelivr.net/npm/jsvectormap@1.7.0/dist/jsvectormap.min.js"
      integrity="sha256-yjoBT8ZtgknE2gcA8yvWExzw8LsIAfj6/r/XAw889Qw="
      crossorigin="anonymous"
    ></script>
    <script
      src="https://cdn.jsdelivr.net/npm/jsvectormap@1.7.0/dist/maps/world.js"
      integrity="sha256-3jwsIc9jvdlaTPxHelZqSxiVedrrJhV+jssQEAAiTt0="
      crossorigin="anonymous"
    ></script>

    <!-- jsvectormap -->
    <script>
      // World map by jsVectorMap
      if (typeof jsVectorMap !== 'undefined') {
        new jsVectorMap({
          selector: '#world-map',
          map: 'world',
        });
      }

      // Sparkline charts
      if (typeof Chart !== 'undefined') {
        const sparkline = (selector, data) =>
          new Chart(document.querySelector(selector), {
            type: 'line',
            data: {
              labels: data.map((value, index) => index + 1),
              datasets: [
                {
                  data,
                  borderColor: '#DCE6EC',
                  backgroundColor: 'rgba(220, 230, 236, 0.3)',
                  pointBackgroundColor: '#DCE6EC',
                  fill: true,
                },
              ],
            },
            options: {
              layout: { padding: { top: 2 } },
              plugins: {
                legend: { display: false },
                tooltip: {
                  displayColors: false,
                  callbacks: { title: () => '' },
                },
              },
              scales: {
                x: { display: false },
                y: { display: false, min: 0 },
              },
            },
          });

        sparkline('#sparkline-1', [1000, 1200, 920, 927, 931, 1027, 819, 930, 1021]);
        sparkline('#sparkline-2', [515, 519, 520, 522, 652, 810, 370, 627, 319, 630, 921]);
        sparkline('#sparkline-3', [15, 19, 20, 22, 33, 27, 31, 27, 19, 30, 21]);
      }
    </script>
    <!--end::Script-->
@endpush
