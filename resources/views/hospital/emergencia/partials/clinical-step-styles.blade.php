<style>
    .clinical-step-card {
        overflow: hidden;
        margin: 0;
        border: 1px solid #d7e6ee;
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 8px 22px rgba(24, 74, 103, .07);
    }
    .clinical-step-card .clinical-step-header {
        display: flex;
        align-items: center;
        gap: 13px;
        padding: 16px 20px;
        border-bottom: 1px solid #d7e6ee;
        background: #eef8fb;
    }
    .clinical-step-card .clinical-step-primary-header {
        display: flex;
        align-items: center;
        min-height: 58px;
        padding: 14px 18px;
        border: 0;
        background-color: #0b4f7d !important;
        background-image: linear-gradient(105deg, #0b4f7d 0%, #0877a9 58%, #19aec0 100%) !important;
    }
    .clinical-step-card .clinical-step-primary-title {
        margin: 0;
        color: #fff !important;
        font-size: 17px !important;
        font-weight: 800 !important;
        line-height: 1.25;
    }
    .clinical-step-card .clinical-discharge-header {
        justify-content: space-between;
        gap: 14px;
        flex-wrap: wrap;
    }
    .clinical-step-card .clinical-discharge-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        flex-wrap: wrap;
        gap: 6px;
        margin-left: auto;
    }
    .clinical-step-card .clinical-discharge-actions .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        width: auto;
        min-height: 32px;
        margin: 0;
        padding: 6px 9px;
        border-radius: 8px;
        font-size: 11px;
        font-weight: 700;
        line-height: 1.15;
        white-space: nowrap;
    }
    .clinical-step-card .clinical-discharge-actions .clinical-access-button {
        border: 1px solid rgba(255, 255, 255, .9) !important;
        background: #fff !important;
        color: #0b4f7d !important;
        box-shadow: 0 4px 10px rgba(11, 79, 125, .16);
    }
    .clinical-step-card .clinical-discharge-actions .clinical-access-button:hover,
    .clinical-step-card .clinical-discharge-actions .clinical-access-button:focus {
        border-color: #d8f1f5 !important;
        background: #eaf8fa !important;
        color: #073b5d !important;
    }
    .clinical-step-card .clinical-step-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 40px;
        width: 40px;
        height: 40px;
        border-radius: 11px;
        background: #d9f2f5;
        color: #087fab;
        font-size: 17px;
    }
    .clinical-step-card .clinical-step-title {
        margin: 0 0 2px;
        color: #0b3a5b !important;
        font-size: 17px !important;
        font-weight: 800 !important;
    }
    .clinical-step-card .clinical-step-subtitle {
        margin: 0;
        color: #688093;
        font-size: 12px;
    }
    .clinical-step-card .clinical-step-body { padding: 20px; }
    .clinical-step-card .clinical-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }
    .clinical-step-card .clinical-form-grid--three {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }
    .clinical-step-card .clinical-field { min-width: 0; }
    .clinical-step-card .clinical-field--full { grid-column: 1 / -1; }
    .clinical-step-card .clinical-field label {
        display: block;
        margin-bottom: 6px;
        color: #284c63 !important;
        font-size: 12px !important;
        font-weight: 750 !important;
    }
    .clinical-step-card .clinical-field .field-number {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 23px;
        height: 23px;
        margin-right: 5px;
        border-radius: 7px;
        background: #e5f4f8;
        color: #087fab;
        font-size: 11px;
        font-weight: 800;
    }
    .clinical-step-card .form-control,
    .clinical-step-card textarea,
    .clinical-step-card .select2-container .select2-selection {
        width: 100%;
        min-height: 40px;
        border: 1px solid #c9dbe5 !important;
        border-radius: 9px !important;
        background: #fff;
        color: #24485f;
        box-shadow: none !important;
    }
    .clinical-step-card .select2-container--default .select2-selection--multiple {
        padding: 4px 7px;
    }
    .clinical-step-card .select2-container--default .select2-selection--multiple .select2-selection__choice {
        margin: 2px 5px 2px 0;
        padding: 4px 9px;
        border: 1px solid #087fab;
        border-radius: 7px;
        background: #087fab;
        color: #fff;
        font-weight: 700;
    }
    .clinical-step-card .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        margin-right: 5px;
        border: 0;
        background: transparent;
        color: #fff;
    }
    .clinical-step-card textarea.form-control {
        min-height: 130px;
        padding: 11px 13px;
        resize: vertical;
        line-height: 1.5;
    }
    .clinical-step-card .form-control:focus,
    .clinical-step-card textarea:focus {
        border-color: #1597b7 !important;
        box-shadow: 0 0 0 3px rgba(21, 151, 183, .11) !important;
        outline: 0;
    }
    .clinical-step-card .input-group-addon {
        border-color: #c9dbe5;
        border-radius: 0 9px 9px 0;
        background: #f4f9fb;
        color: #087fab;
    }
    .clinical-step-card .input-group.dropdown {
        display: flex;
        align-items: stretch;
        width: 100%;
    }
    .clinical-step-card .input-group.dropdown .form-control {
        flex: 1 1 auto;
        min-width: 0;
        border-radius: 9px 0 0 9px !important;
    }
    .clinical-step-card .input-group.dropdown .input-group-addon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 40px;
        width: 40px;
        border-left: 0;
    }
    .clinical-step-card .input-group.dropdown .dropdown-menu {
        display: none;
        z-index: 1050;
        width: 100%;
        margin-top: 4px;
        padding: 5px;
        border: 1px solid #c9dbe5;
        border-radius: 8px;
        background: #fff;
        box-shadow: 0 8px 18px rgba(24, 74, 103, .12);
        list-style: none;
    }
    .clinical-step-card .input-group.dropdown.open .dropdown-menu { display: block; }
    .clinical-step-card .input-group.dropdown .dropdown-menu a {
        display: block;
        padding: 7px 9px;
        border-radius: 6px;
        color: #284c63;
        cursor: pointer;
    }
    .clinical-step-card .input-group.dropdown .dropdown-menu a:hover { background: #eef8fb; }
    .clinical-step-card .clinical-section-label {
        grid-column: 1 / -1;
        padding-bottom: 9px;
        border-bottom: 1px solid #e2edf2;
        color: #0b3a5b;
        font-size: 14px;
        font-weight: 800;
    }
    .clinical-step-card .clinical-exam-grid {
        display: grid;
        grid-template-columns: minmax(180px, 230px) minmax(0, 1fr);
        gap: 10px 14px;
        align-items: center;
    }
    .clinical-step-card .clinical-exam-grid > [class*="col-md-"] {
        width: auto;
        max-width: none;
        padding: 0;
    }
    .clinical-step-card .clinical-exam-grid > [class*="col-md-"]:nth-child(odd):not(.clinical-form-actions) {
        align-self: stretch;
        display: flex;
        align-items: center;
        padding: 9px 11px;
        border-radius: 8px;
        background: #f2f8fa;
        color: #284c63;
    }
    .clinical-step-card .clinical-exam-grid > [class*="col-md-"] b {
        color: #284c63 !important;
        font-size: 12px !important;
        font-weight: 750 !important;
    }
    .clinical-step-card .clinical-form-actions {
        display: flex;
        justify-content: center;
        grid-column: 1 / -1;
        padding-top: 4px;
    }
    .clinical-step-card .clinical-save-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-width: 150px;
        min-height: 40px;
        padding: 9px 17px;
        border: 0;
        border-radius: 10px;
        background: #087fab !important;
        color: #fff !important;
        font-size: 13px;
        font-weight: 750;
        box-shadow: 0 6px 14px rgba(8, 127, 171, .18);
    }
    .clinical-step-card .clinical-save-button:hover,
    .clinical-step-card .clinical-save-button:focus {
        background: #076e95 !important;
        color: #fff !important;
    }
    @media (max-width: 900px) {
        .clinical-step-card .clinical-form-grid,
        .clinical-step-card .clinical-form-grid--three,
        .clinical-step-card .clinical-exam-grid { grid-template-columns: 1fr; }
        .clinical-step-card .clinical-exam-grid > [class*="col-md-"]:nth-child(odd):not(.clinical-form-actions) {
            margin-top: 4px;
        }
        .clinical-step-card .clinical-discharge-actions {
            width: 100%;
            justify-content: flex-start;
            margin-left: 0;
        }
    }
</style>
