@php
  $liquidacionTranslations = trans('v3/liquidacion.client');
  $liquidacionTranslations = is_array($liquidacionTranslations) ? $liquidacionTranslations : [];
@endphp
<script>
  (function () {
    var dictionary = {!! json_encode($liquidacionTranslations, JSON_UNESCAPED_UNICODE) !!};
    var ignoredTags = { SCRIPT: true, STYLE: true, CODE: true, PRE: true, TEXTAREA: true };

    function normalized(value) {
      return (value || '').replace(/\s+/g, ' ').replace(/^\s+|\s+$/g, '');
    }

    function translateText(node) {
      if (!node || !node.parentNode || ignoredTags[node.parentNode.nodeName]) {
        return;
      }

      var raw = node.nodeValue || '';
      var key = normalized(raw);
      var pagination = key.match(/^Mostrando\s+(\d+)\s*(?:a|–|-)\s*(\d+)\s+de\s+(\d+)\s+(registros|ítem(?:s)?|plantilla(?:s)?|medicina(?:s)?)$/);
      if (pagination) {
        var paginationKey = '__pagination__';
        if (pagination[4].indexOf('ítem') === 0) {
          paginationKey = pagination[3] === '1' ? '__item_pagination_singular__' : '__item_pagination_plural__';
        } else if (pagination[4].indexOf('plantilla') === 0) {
          paginationKey = pagination[3] === '1' ? '__template_pagination_singular__' : '__template_pagination_plural__';
        } else if (pagination[4].indexOf('medicina') === 0) {
          paginationKey = pagination[3] === '1' ? '__medicine_pagination_singular__' : '__medicine_pagination_plural__';
        }

        if (!dictionary[paginationKey]) {
          return;
        }

        var translatedPagination = dictionary[paginationKey]
          .replace(':from', pagination[1])
          .replace(':to', pagination[2])
          .replace(':total', pagination[3]);
        if (raw !== translatedPagination) {
          node.nodeValue = translatedPagination;
        }
        return;
      }

      var catalogCount = key.match(/^(\d+)\s+(ítem|ítems|plantilla|plantillas|medicina|medicinas)$/);
      if (catalogCount) {
        var isItem = catalogCount[2].indexOf('ítem') === 0;
        var isTemplate = catalogCount[2].indexOf('plantilla') === 0;
        var countKey = isItem
          ? (catalogCount[2] === 'ítem' ? '__item_count_singular__' : '__item_count_plural__')
          : (isTemplate
            ? (catalogCount[2] === 'plantilla' ? '__template_count_singular__' : '__template_count_plural__')
            : (catalogCount[2] === 'medicina' ? '__medicine_count_singular__' : '__medicine_count_plural__'));

        if (!dictionary[countKey]) {
          return;
        }

        var translatedCatalogCount = dictionary[countKey].replace(':count', catalogCount[1]);
        if (raw !== translatedCatalogCount) {
          node.nodeValue = translatedCatalogCount;
        }
        return;
      }

      var fileCount = key.match(/^(\d+)\s+archivo(s)?$/);
      if (fileCount && dictionary.__file_count_singular__ && dictionary.__file_count_plural__) {
        var translatedFileCount = (fileCount[2] ? dictionary.__file_count_plural__ : dictionary.__file_count_singular__)
          .replace(':count', fileCount[1]);
        if (raw !== translatedFileCount) {
          node.nodeValue = translatedFileCount;
        }
        return;
      }

      var recordCount = key.match(/^(\d+)\s+registro\(s\)$/);
      if (recordCount && dictionary.__record_count__) {
        var translatedRecordCount = dictionary.__record_count__.replace(':count', recordCount[1]);
        if (raw !== translatedRecordCount) {
          node.nodeValue = translatedRecordCount;
        }
        return;
      }

      var availableCount = key.match(/^(\d+)\s+disponibles$/);
      if (availableCount && dictionary.__available_count__) {
        var translatedAvailableCount = dictionary.__available_count__.replace(':count', availableCount[1]);
        if (raw !== translatedAvailableCount) {
          node.nodeValue = translatedAvailableCount;
        }
        return;
      }

      if (!key || !Object.prototype.hasOwnProperty.call(dictionary, key)) {
        return;
      }

      var leading = (raw.match(/^\s*/) || [''])[0];
      var trailing = (raw.match(/\s*$/) || [''])[0];
      var translatedText = leading + dictionary[key] + trailing;
      if (raw !== translatedText) {
        node.nodeValue = translatedText;
      }
    }

    function translateAttributes(element) {
      if (!element || element.nodeType !== 1 || ignoredTags[element.nodeName]) {
        return;
      }

      ['placeholder', 'title', 'aria-label', 'data-original-title'].forEach(function (attribute) {
        if (!element.hasAttribute(attribute)) {
          return;
        }

        var value = element.getAttribute(attribute);
        var key = normalized(value);
        if (key && Object.prototype.hasOwnProperty.call(dictionary, key)) {
          var translatedAttribute = dictionary[key];
          if (value !== translatedAttribute) {
            element.setAttribute(attribute, translatedAttribute);
          }
        }
      });
    }

    function translateTree(root) {
      if (!root) {
        return;
      }

      if (root.nodeType === 3) {
        translateText(root);
        return;
      }

      translateAttributes(root);
      if (!root.querySelectorAll) {
        return;
      }

      Array.prototype.forEach.call(root.querySelectorAll('[placeholder], [title], [aria-label], [data-original-title]'), translateAttributes);

      var walker = document.createTreeWalker(root, NodeFilter.SHOW_TEXT, null, false);
      var textNodes = [];
      var node;
      while ((node = walker.nextNode())) {
        textNodes.push(node);
      }
      textNodes.forEach(translateText);
    }

    function startTranslation() {
      if (!Object.keys(dictionary).length) {
        return;
      }

      translateTree(document.getElementById('contenido-principal') || document.body);

      // Varias modales heredadas se cargan ocultas o se completan por AJAX.
      // Traducirlas al mostrarse cubre ambos casos sin observar mutaciones de
      // texto (que causaban un ciclo de renderizado).
      if (window.jQuery) {
        jQuery(document)
          .off('shown.bs.modal.liquidacion-i18n')
          .on('shown.bs.modal.liquidacion-i18n', '.modal', function () {
            translateTree(this);
          });
      }

      var observer = new MutationObserver(function (changes) {
        changes.forEach(function (change) {
          Array.prototype.forEach.call(change.addedNodes, translateTree);
        });
      });

      // Solo se traduce contenido nuevo. Observar cambios de texto hacía que el
      // traductor se invocara nuevamente por sus propias sustituciones y podía
      // bloquear la interfaz al mostrar una alerta o una modal.
      observer.observe(document.body, { childList: true, subtree: true });
    }

    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', startTranslation);
    } else {
      startTranslation();
    }
  })();
</script>
