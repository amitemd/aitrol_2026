(function () {
  'use strict';

  function closest(element, selector) {
    while (element && element.nodeType === 1) {
      if (element.matches(selector)) return element;
      element = element.parentElement;
    }
    return null;
  }

  document.addEventListener('click', function (event) {
    var toggle = closest(event.target, '.v3-menu-root > a[href="#"], .v3-menu-root .nav-item > a[href="#"]');
    if (toggle) {
      var managedMenu = closest(toggle, '#TourMenu[data-v3-accordion-ready="true"]');
      if (managedMenu) return;
      event.preventDefault();
      var item = closest(toggle, '.nav-item');
      if (!item) return;
      var isOpen = item.classList.toggle('menu-open');
      toggle.classList.toggle('active', isOpen);
      toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
      return;
    }

    var sidebarToggle = closest(event.target, '.v3-legacy-menu-toggle');
    if (sidebarToggle) {
      var mobile = window.matchMedia('(max-width: 767px)').matches;
      var bodyClass = mobile ? 'v3-legacy-sidebar-visible' : 'v3-legacy-sidebar-hidden';
      var visible = document.body.classList.toggle(bodyClass);
      sidebarToggle.setAttribute('aria-expanded', visible ? 'true' : 'false');
    }
  });

  var search = document.querySelector('.v3-menu-search input[type="search"]');
  if (search) {
    search.addEventListener('input', function () {
      var term = this.value.trim().toLocaleLowerCase();
      var leaves = document.querySelectorAll('.v3-sidebar-nav .nav-item');
      Array.prototype.forEach.call(leaves, function (item) {
        if (!term) {
          item.style.removeProperty('display');
          return;
        }
        var matches = item.textContent.toLocaleLowerCase().indexOf(term) !== -1;
        item.style.display = matches ? '' : 'none';
        if (matches) {
          var parent = item.parentElement;
          while (parent && !parent.classList.contains('v3-sidebar-nav')) {
            if (parent.classList && parent.classList.contains('nav-item')) {
              parent.style.display = '';
              parent.classList.add('menu-open');
            }
            parent = parent.parentElement;
          }
        }
      });
    });
  }
}());
