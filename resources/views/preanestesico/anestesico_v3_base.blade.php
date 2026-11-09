@extends('v3.layouts.view_plantilla')
@php
  $anesthesiaPageType = trim((string) View::getSection('anestesico-type', 'Anestesia'));
  config(['data' => array_merge(config('data') ?: [], [
    'active_controller' => 'anestesia',
    'controlador' => 'anestesia',
    'title' => $anesthesiaPageType . ' | Aitrol Hospitalario',
  ])]);
@endphp
@section('title', view('v3.layouts.view_title'))@show
@section('css', view('v3.layouts.view_css'))@show
@section('header', view('v3.layouts.view_header'))@show
@section('menu', view('v3.layouts.view_menu'))@show
@section('footer', view('v3.layouts.view_footer'))@show
@section('variables', view('v3.layouts.view_variables'))@show
@section('js')
  @include('v3.layouts.view_js')
  <script>
    (function ($) {
      'use strict';

      $(function () {
        var $anesthesiaSelects = $('.anestesico-v3-shell select');

        if (!$anesthesiaSelects.length || typeof $.fn.select2 !== 'function') return;

        $anesthesiaSelects.each(function () {
          var $select = $(this);

          if ($select.hasClass('select2-hidden-accessible')) return;

          $select.select2({
            theme: 'bootstrap4',
            width: '100%',
            minimumResultsForSearch: 0
          });
        });

        $anesthesiaSelects.on('select2:open.anesthesiaForms', function () {
          window.setTimeout(function () {
            $('.select2-container--open .select2-search__field')
              .attr('placeholder', 'Buscar...')
              .trigger('focus');
          }, 0);
        });
      });

      function normalizeRecordText(value) {
        return (value || '').toString().normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/\s+/g, ' ').trim().toUpperCase();
      }

      window.setAnesthesiaRecordStatus = function (state) {
        var status = document.querySelector('[data-anesthesia-save-status]');
        if (!status) return;

        var icon = status.querySelector('i');
        var label = status.querySelector('span');
        status.classList.remove('is-saving', 'is-saved', 'is-error');
        status.classList.add('is-' + state);

        if (state === 'saving') {
          icon.className = 'fa fa-circle-o-notch';
          label.textContent = 'Guardando...';
        } else if (state === 'error') {
          icon.className = 'fa fa-exclamation-circle';
          label.textContent = 'Error al guardar';
        } else {
          icon.className = 'fa fa-check-circle';
          label.textContent = 'Guardado';
        }
      };

      $(function () {
        document.querySelectorAll('[data-anesthesia-record]').forEach(function (section) {
          var configNode = section.querySelector('script.anesthesia-layout-config');
          var endMarker = section.querySelector('.anesthesia-layout-end');
          if (!configNode || !endMarker) return;

          var config;
          try { config = JSON.parse(configNode.textContent); } catch (error) { return; }

          var directTables = Array.prototype.filter.call(section.children, function (element) {
            return element.tagName === 'TABLE';
          });
          if (!directTables.length) return;

          directTables[0].classList.add('anesthesia-patient-source');

          function findTable(match) {
            var normalizedMatch = normalizeRecordText(match);
            return directTables.find(function (table) {
              return normalizeRecordText(table.textContent).indexOf(normalizedMatch) !== -1;
            });
          }

          var groups = (config.groups || []).map(function (group) {
            group.table = findTable(group.match);
            return group;
          }).filter(function (group) { return group.table; });

          groups.forEach(function (group, index) {
            var stopNode = groups[index + 1] ? groups[index + 1].table : endMarker;
            var details = document.createElement('details');
            details.className = 'anesthesia-record-block';
            details.id = group.id;
            if (group.open) details.open = true;

            var summary = document.createElement('summary');
            summary.innerHTML = '<i class="fa ' + (group.icon || 'fa-file-text-o') + '" aria-hidden="true"></i>' +
              '<span>' + group.title + '<small>' + (group.description || '') + '</small></span>' +
              '<i class="fa fa-chevron-down anesthesia-chevron" aria-hidden="true"></i>';
            details.appendChild(summary);

            var body = document.createElement('div');
            body.className = 'anesthesia-record-block__body';
            details.appendChild(body);
            section.insertBefore(details, group.table);

            var node = group.table;
            while (node && node !== stopNode && node !== endMarker) {
              var nextNode = node.nextSibling;
              body.appendChild(node);
              node = nextNode;
            }
          });

          var navButtons = section.querySelectorAll('.anesthesia-record-nav [data-scroll-target]');
          navButtons.forEach(function (button) {
            button.addEventListener('click', function () {
              var target = document.getElementById(button.getAttribute('data-scroll-target'));
              if (!target) return;
              if (target.tagName === 'DETAILS') target.open = true;
              navButtons.forEach(function (item) { item.classList.remove('is-active'); });
              button.classList.add('is-active');
              target.scrollIntoView({behavior:'smooth', block:'start'});
            });
          });

          if ('IntersectionObserver' in window) {
            var observer = new IntersectionObserver(function (entries) {
              entries.forEach(function (entry) {
                if (!entry.isIntersecting) return;
                navButtons.forEach(function (button) {
                  button.classList.toggle('is-active', button.getAttribute('data-scroll-target') === entry.target.id);
                });
              });
            }, {rootMargin:'-135px 0px -65% 0px', threshold:0});

            groups.forEach(function (group) {
              var target = document.getElementById(group.id);
              if (target) observer.observe(target);
            });
          }
        });
      });
    })(jQuery);
  </script>
@endsection
@section('contenido')
<style>
  .content-wrapper{background:#eef5f8}
  .anestesico-v3-shell{width:100%;max-width:none;margin:0;box-sizing:border-box;padding:clamp(12px,1.4vw,22px) clamp(12px,1.8vw,28px) 54px}
  .anestesico-v3-toolbar{display:flex;align-items:center;justify-content:space-between;gap:16px;margin-bottom:20px;padding:12px;border:1px solid #d4e5ed;border-radius:14px;background:rgba(255,255,255,.94);box-shadow:0 6px 20px rgba(21,74,103,.07)}
  .anestesico-v3-toolbar>div:first-child{display:flex;align-items:center;gap:8px;flex-wrap:nowrap;min-width:0}
  .anestesico-v3-tabs{display:flex;align-items:center;gap:8px;flex-wrap:nowrap;white-space:nowrap}
  .anestesico-v3-tabs a{display:inline-flex;align-items:center;gap:8px;padding:10px 15px;border:1px solid transparent;border-radius:9px;color:#315b71;background:#f2f7f9;font-weight:700;text-decoration:none;transition:.18s ease}
  .anestesico-v3-tabs a.active,.anestesico-v3-tabs a:hover{color:#fff;border-color:#087fbd;background:linear-gradient(125deg,#0876ad,#109cab);box-shadow:0 6px 14px rgba(8,127,189,.2);text-decoration:none;transform:translateY(-1px)}
  .anestesico-v3-back{display:inline-flex;align-items:center;gap:7px;padding:9px 13px;border:1px solid #c4dce8;border-radius:9px;color:#214b64;background:#fff;font-weight:700;text-decoration:none;white-space:nowrap}
  .anestesico-v3-back:hover{color:#0877be;border-color:#0877be;background:#f1f9fd;text-decoration:none}
  .anestesico-v3-pdf a{display:inline-flex;align-items:center;gap:8px;padding:10px 16px;border-radius:9px;color:#fff;background:#df4f5c;font-weight:700;text-decoration:none;box-shadow:0 6px 14px rgba(223,79,92,.18);transition:.18s ease}
  .anestesico-v3-pdf a:hover{color:#fff;background:#c9414d;transform:translateY(-1px)}
  .anestesico-v3-shell .content{width:100%;max-width:100%;padding:0;overflow-x:auto}
  .anestesico-v3-shell .content>table,.anestesico-v3-shell .content>form,.anestesico-v3-shell .content>.box,.anestesico-v3-shell .content>.box-body,.anestesico-v3-shell .content>div:not(.v3-hero):not(.v3-section){margin:18px 0;padding:20px;border:1px solid #cde1ed;border-radius:12px;background:#fff;box-shadow:0 5px 18px rgba(23,78,111,.07)}
  .anestesico-v3-shell table{width:100%;border-color:#d7e7ef;border-collapse:separate;border-spacing:0}
  .anestesico-v3-shell td{padding:7px!important;color:#123b5d}
  .anestesico-v3-shell .bg-blue,.anestesico-v3-shell .bg-purple,.anestesico-v3-shell .bg-green{color:#fff!important;background:linear-gradient(100deg,#0b4166,#20b7c7)!important}
  .anestesico-v3-shell .bg-blue b,.anestesico-v3-shell .bg-purple b,.anestesico-v3-shell .bg-green b{color:#fff!important}
  .anestesico-v3-shell input:not([type=hidden]),.anestesico-v3-shell select,.anestesico-v3-shell textarea{max-width:100%;border:1px solid #c9dce9;border-radius:8px}
  .anestesico-v3-shell .select2-container{width:100%!important;max-width:100%}
  .anestesico-v3-shell .select2-container--bootstrap4 .select2-selection{min-height:38px;border-color:#c9dce9;border-radius:8px;box-shadow:none}
  .anestesico-v3-shell .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered{padding-left:12px;line-height:36px;color:#123b5d}
  .anestesico-v3-shell .select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow{top:0!important;right:4px!important;width:32px!important;height:100%!important}
  .anestesico-v3-shell .select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow b{top:50%!important;left:50%!important;margin-top:-2px!important;margin-left:-4px!important}
  .anestesico-v3-shell .select2-container--bootstrap4.select2-container--focus .select2-selection,.anestesico-v3-shell .select2-container--bootstrap4.select2-container--open .select2-selection{border-color:#20b7c7;box-shadow:0 0 0 3px rgba(32,183,199,.13)}
  .select2-container--open .select2-dropdown{border-color:#c9dce9;border-radius:8px;box-shadow:0 12px 28px rgba(23,78,111,.16);overflow:hidden}
  .select2-container--open .select2-search--dropdown{padding:9px;background:#f7fbfd}
  .select2-container--open .select2-search__field{min-height:36px;padding:7px 10px!important;border:1px solid #9ebdcd!important;border-radius:6px;outline:0}
  .select2-container--open .select2-results__option{padding:8px 11px}
  .select2-container--open .select2-results__option--highlighted[aria-selected]{background:#087fbd}
  .dark-mode .content-wrapper{background:#10212a}
  .dark-mode .anestesico-v3-toolbar{border-color:#34515f;background:#172d38}
  .dark-mode .anestesico-v3-tabs a,.dark-mode .anestesico-v3-back{border-color:#34515f;color:#d7e8ef;background:#203a47}
  .dark-mode .anestesico-v3-shell .select2-container--bootstrap4 .select2-selection{border-color:#426171;background:#132934}
  .dark-mode .anestesico-v3-shell .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered{color:#e4f0f4}
  .anesthesia-record-v3{--record-navy:#0b4166;--record-blue:#087fbd;--record-cyan:#20b7c7;--record-ink:#123b5d;--record-muted:#67829a;color:var(--record-ink)}
  .anestesico-v3-shell .content.anesthesia-record-v3>.anesthesia-record-hero{display:flex;align-items:center;justify-content:space-between;gap:18px;margin:0 0 14px;padding:18px 22px;border:0;border-radius:13px;color:#fff;background:linear-gradient(110deg,var(--record-navy),#087ea6 68%,#159f91)!important;box-shadow:0 9px 22px rgba(11,65,102,.14)}
  .anesthesia-record-v3 .anesthesia-record-hero__title{display:flex;align-items:center;gap:13px;min-width:0}.anesthesia-record-v3 .anesthesia-record-hero__icon{display:inline-flex;align-items:center;justify-content:center;flex:0 0 44px;width:44px;height:44px;border:1px solid rgba(255,255,255,.35);border-radius:11px;background:rgba(255,255,255,.12);font-size:20px}.anesthesia-record-v3 .anesthesia-record-hero h1{margin:0 0 4px;color:#fff;font-size:21px;font-weight:800}.anesthesia-record-v3 .anesthesia-record-hero p{margin:0;color:rgba(255,255,255,.9);font-size:12px}
  .anesthesia-record-v3 .anesthesia-record-hero__meta{display:flex;align-items:center;gap:9px;flex-wrap:wrap;justify-content:flex-end}.anesthesia-record-v3 .anesthesia-record-chip{display:flex;flex-direction:column;min-width:110px;padding:8px 11px;border:1px solid rgba(255,255,255,.3);border-radius:9px;background:rgba(255,255,255,.1);font-size:10px;text-transform:uppercase}.anesthesia-record-v3 .anesthesia-record-chip strong{margin-top:2px;color:#fff;font-size:12px}.anesthesia-record-v3 .anesthesia-save-status{display:inline-flex;align-items:center;gap:6px;min-width:100px;padding:9px 11px;border:1px solid rgba(255,255,255,.3);border-radius:9px;background:rgba(255,255,255,.1);font-size:11px;font-weight:800}.anesthesia-record-v3 .anesthesia-save-status.is-saving i{animation:anesthesia-record-spin .8s linear infinite}.anesthesia-record-v3 .anesthesia-save-status.is-error{background:#c94652}@keyframes anesthesia-record-spin{to{transform:rotate(360deg)}}
  .anesthesia-record-v3 .anesthesia-record-nav{position:sticky;top:64px;z-index:20;display:flex;align-items:center;gap:6px;margin:0 0 14px;padding:8px;overflow-x:auto;border:1px solid #d5e5ed;border-radius:11px;background:rgba(255,255,255,.96);box-shadow:0 5px 16px rgba(23,78,111,.07);backdrop-filter:blur(8px)}.anesthesia-record-v3 .anesthesia-record-nav button{display:inline-flex;align-items:center;gap:6px;min-height:34px;padding:7px 10px;border:0;border-radius:7px;color:#315b71;background:transparent;font-size:11px;font-weight:800;white-space:nowrap}.anesthesia-record-v3 .anesthesia-record-nav button:hover,.anesthesia-record-v3 .anesthesia-record-nav button.is-active{color:#08799c;background:#e4f5f8}
  .anesthesia-record-v3>.anesthesia-patient-source{display:none!important}.anesthesia-record-v3 .anesthesia-record-block{margin:12px 0;border:1px solid #d3e5ed;border-radius:11px;background:#fff;box-shadow:0 4px 15px rgba(23,78,111,.06);overflow:hidden;scroll-margin-top:122px}.anesthesia-record-v3 .anesthesia-record-block>summary{display:flex;align-items:center;gap:11px;min-height:50px;padding:11px 15px;cursor:pointer;color:#174761;background:#f1f8fa;font-weight:800;list-style:none}.anesthesia-record-v3 .anesthesia-record-block>summary::-webkit-details-marker{display:none}.anesthesia-record-v3 .anesthesia-record-block>summary i:first-child{display:inline-flex;align-items:center;justify-content:center;width:31px;height:31px;border-radius:8px;color:#087fbd;background:#dff2f6}.anesthesia-record-v3 .anesthesia-record-block>summary span{display:flex;flex:1;flex-direction:column}.anesthesia-record-v3 .anesthesia-record-block>summary small{margin-top:2px;color:#71899a;font-size:10px;font-weight:500}.anesthesia-record-v3 .anesthesia-record-block>summary .anesthesia-chevron{flex:0 0 auto;color:#67829a;background:transparent;transition:transform .18s}.anesthesia-record-v3 .anesthesia-record-block[open]>summary{border-bottom:1px solid #dceaf0;background:#eaf6f8}.anesthesia-record-v3 .anesthesia-record-block[open]>summary .anesthesia-chevron{transform:rotate(180deg)}.anesthesia-record-v3 .anesthesia-record-block__body{padding:14px;overflow-x:auto}.anesthesia-record-v3 .anesthesia-record-block__body>br{display:none}
  .anesthesia-record-v3 .anesthesia-record-block table{width:100%!important;margin:0 0 12px;border-collapse:collapse}.anesthesia-record-v3 .anesthesia-record-block td{padding:7px!important;border-color:#d7e7ef!important;color:var(--record-ink)}.anesthesia-record-v3 .anesthesia-record-block td.bg-blue,.anesthesia-record-v3 .anesthesia-record-block td.bg-purple,.anesthesia-record-v3 .anesthesia-record-block td.bg-green{color:#fff!important;background:#0e6485!important}.anesthesia-record-v3 .anesthesia-record-block td.bg-light-green{color:#16455d;background:#dff3ed!important}.anesthesia-record-v3 .anesthesia-record-block input:not([type=checkbox]):not([type=radio]),.anesthesia-record-v3 .anesthesia-record-block select,.anesthesia-record-v3 .anesthesia-record-block textarea{min-height:36px;max-width:100%;border:1px solid #c9dce9;border-radius:7px;color:var(--record-ink);background:#fff;box-shadow:none}.anesthesia-record-v3 .anesthesia-record-block input:focus,.anesthesia-record-v3 .anesthesia-record-block select:focus,.anesthesia-record-v3 .anesthesia-record-block textarea:focus{border-color:var(--record-cyan);box-shadow:0 0 0 3px rgba(32,183,199,.13);outline:0}.anesthesia-record-v3 .anesthesia-record-block .alert{margin:8px 0 12px}.anesthesia-record-v3 .anesthesia-record-block .row{margin-right:-6px;margin-left:-6px}.anesthesia-record-v3 .anesthesia-record-block [class*=col-]{padding-right:6px;padding-left:6px}
  .dark-mode .anesthesia-record-v3 .anesthesia-record-nav,.dark-mode .anesthesia-record-v3 .anesthesia-record-block{border-color:#34515f;background:#172d38}.dark-mode .anesthesia-record-v3 .anesthesia-record-block>summary{color:#dcebf1;background:#203946}.dark-mode .anesthesia-record-v3 .anesthesia-record-block[open]>summary{border-color:#34515f;background:#254653}
  @media(max-width:767px){.anestesico-v3-shell{padding:12px 10px 36px}.anestesico-v3-toolbar{align-items:stretch;flex-direction:column}.anestesico-v3-toolbar>div:first-child{align-items:stretch;flex-direction:column}.anestesico-v3-tabs{overflow-x:auto;flex-wrap:nowrap}.anestesico-v3-tabs a{white-space:nowrap}.anestesico-v3-pdf{align-self:flex-start}.anestesico-v3-shell .content>table{min-width:1080px}}
  @media(max-width:767px){.anestesico-v3-shell .content.anesthesia-record-v3>.anesthesia-record-hero{align-items:flex-start;flex-direction:column;padding:16px}.anesthesia-record-v3 .anesthesia-record-hero__meta{justify-content:flex-start;width:100%}.anesthesia-record-v3 .anesthesia-record-chip{flex:1 1 130px}.anesthesia-record-v3 .anesthesia-record-nav{position:static}.anesthesia-record-v3 .anesthesia-record-block__body{padding:9px}.anesthesia-record-v3 .anesthesia-record-block table{min-width:900px}}
</style>
<div class="anestesico-v3-shell">
  <div class="anestesico-v3-toolbar">
    <div>
      <a class="anestesico-v3-back" href="javascript:history.back()"><i class="fas fa-arrow-left"></i> Volver a documentos</a>
      <nav class="anestesico-v3-tabs" aria-label="Formularios anestésicos">
        <a class="{{ View::getSection('anestesico-active') === 'pre' ? 'active' : '' }}" href="@yield('anestesico-pre-route')"><i class="fas fa-stethoscope"></i> Preanestésico</a>
        <a class="{{ View::getSection('anestesico-active') === 'trans' ? 'active' : '' }}" href="@yield('anestesico-trans-route')"><i class="fas fa-procedures"></i> Transanestésico</a>
        <a class="{{ View::getSection('anestesico-active') === 'post' ? 'active' : '' }}" href="@yield('anestesico-post-route')"><i class="fas fa-heartbeat"></i> Postanestésico</a>
      </nav>
    </div>
    <div class="anestesico-v3-pdf"><a href="@yield('anestesico-pdf-route')" target="_blank"><i class="fas fa-file-pdf"></i><span>PDF</span></a></div>
  </div>
  @yield('action-content')
</div>
@endsection
