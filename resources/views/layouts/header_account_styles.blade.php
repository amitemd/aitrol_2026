<style type="text/css">
  .ieced-language-trigger,
  .ieced-user-trigger {
    display: flex !important;
    align-items: center;
    gap: 7px;
    height: 40px;
    margin: 5px 2px;
    padding: 0 12px !important;
    border-radius: 8px;
    transition: background-color .18s ease, color .18s ease;
  }

  .ieced-language-trigger:hover,
  .ieced-language-trigger:focus,
  .ieced-language-dropdown.open > .ieced-language-trigger,
  .ieced-user-trigger:hover,
  .ieced-user-trigger:focus,
  .ieced-user-dropdown.open > .ieced-user-trigger {
    color: #124574 !important;
    background-color: #eef4f9 !important;
  }

  .ieced-language-menu {
    min-width: 215px;
    margin-top: 4px !important;
    padding: 8px !important;
    border: 1px solid #dce5ed !important;
    border-radius: 10px !important;
    box-shadow: 0 10px 28px rgba(18, 69, 116, .18) !important;
  }

  .ieced-language-menu > li > a {
    display: flex !important;
    align-items: center;
    justify-content: space-between;
    min-height: 40px;
    padding: 7px 9px !important;
    color: #34495e !important;
    border-radius: 7px;
    transition: background-color .16s ease, color .16s ease;
  }

  .ieced-language-menu > li > a:hover,
  .ieced-language-menu > li > a:focus {
    color: #124574 !important;
    background-color: #f2f7fb !important;
  }

  .ieced-language-menu > li.active > a,
  .ieced-language-menu > li.active > a:hover,
  .ieced-language-menu > li.active > a:focus {
    color: #124574 !important;
    background-color: #e5f1fa !important;
    font-weight: 600;
  }

  .ieced-language-option {
    display: flex;
    align-items: center;
    gap: 9px;
  }

  .ieced-language-code {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 24px;
    color: #124574;
    background-color: #edf3f7;
    border-radius: 6px;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: .4px;
  }

  .ieced-language-check {
    color: #2387c9;
    font-size: 12px;
  }

  .ieced-user-trigger > .user-image {
    float: none !important;
    margin: 0 !important;
  }

  .ieced-user-menu {
    width: 300px !important;
    max-width: calc(100vw - 16px);
    margin-top: 4px !important;
    padding: 0 !important;
    overflow: hidden;
    border: 1px solid #dce5ed !important;
    border-radius: 12px !important;
    box-shadow: 0 12px 32px rgba(18, 69, 116, .2) !important;
  }

  .ieced-user-menu > .ieced-user-card {
    height: auto !important;
    min-height: 142px;
    padding: 24px 20px 18px !important;
    color: #243746 !important;
    background: linear-gradient(180deg, #f8fbfd 0%, #eef5fa 100%) !important;
    text-align: center;
  }

  .ieced-user-menu > .ieced-user-card::before,
  .ieced-user-menu > .ieced-user-card::after {
    display: none;
  }

  .ieced-user-menu > .ieced-user-card > .ieced-user-avatar {
    width: 78px !important;
    height: 78px !important;
    margin: 0 auto 12px !important;
    object-fit: cover;
    border: 3px solid #fff !important;
    box-shadow: 0 4px 14px rgba(18, 69, 116, .2);
  }

  .ieced-user-menu > .ieced-user-card > .new-system-avatar-menu {
    width: 78px !important;
    height: 78px !important;
    margin: 0 auto 12px !important;
    color: #fff !important;
    background: linear-gradient(135deg, #2387c9 0%, #12558d 100%) !important;
    border: 3px solid #fff !important;
    box-shadow: 0 4px 14px rgba(18, 69, 116, .2);
  }

  .ieced-user-name {
    margin: 0 !important;
    color: #243746 !important;
    font-size: 15px;
    font-weight: 600;
    line-height: 1.35;
    white-space: normal;
  }

  .ieced-user-status {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin-top: 7px;
    padding: 4px 8px;
    color: #216b48;
    background-color: #e7f6ee;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 600;
    line-height: 1;
  }

  .ieced-user-status > .fa {
    color: #2ca56b;
    font-size: 8px;
  }

  .ieced-user-email,
  .ieced-user-menu > .ieced-user-card > small {
    display: block;
    margin-top: 8px;
    color: #647687 !important;
    font-size: 12px;
    line-height: 1.4;
    overflow-wrap: anywhere;
  }

  .ieced-user-menu > .ieced-user-footer {
    display: flex !important;
    flex-wrap: wrap;
    align-items: stretch;
    gap: 8px;
    padding: 12px !important;
    background-color: #fff !important;
    border-top: 1px solid #e5edf3;
  }

  .ieced-user-action {
    display: inline-flex !important;
    flex: 1 1 0;
    align-items: center;
    justify-content: center;
    gap: 7px;
    min-width: 0;
    min-height: 38px;
    margin: 0 !important;
    padding: 8px 10px !important;
    color: #124574 !important;
    background-color: #f3f7fa !important;
    border: 1px solid #dce5ed !important;
    border-radius: 7px !important;
    font-size: 13px;
    line-height: 1.25;
    white-space: nowrap;
    transition: background-color .16s ease, border-color .16s ease;
  }

  .ieced-user-action:hover,
  .ieced-user-action:focus {
    background-color: #e7f1f8 !important;
    border-color: #b9d2e4 !important;
  }

  .ieced-user-ticket {
    flex: 1 1 100%;
  }

  .ieced-user-signout {
    color: #b53b3b !important;
    background-color: #fff7f7 !important;
    border-color: #f0d2d2 !important;
  }

  .ieced-user-signout:hover,
  .ieced-user-signout:focus {
    background-color: #fdecec !important;
    border-color: #e7b9b9 !important;
  }

  @media (max-width: 767px) {
    .navbar-custom-menu > .navbar-nav > .ieced-user-dropdown > .ieced-user-menu {
      right: 8px !important;
      left: auto !important;
    }

    .ieced-user-menu > .ieced-user-card {
      min-height: 0;
      padding: 20px 16px 16px !important;
    }

    .ieced-user-menu > .ieced-user-footer {
      padding: 10px !important;
    }
  }
</style>
