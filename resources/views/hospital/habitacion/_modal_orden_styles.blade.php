<style>
    .clinical-order-modal {
        color: #08385f;
        font-family: inherit;
    }

    .clinical-order-modal .clinical-modal-header {
        align-items: center;
        background: linear-gradient(110deg, #075383 0%, #087fa5 54%, #13a8a0 100%);
        border: 0;
        display: flex;
        min-height: 94px;
        padding: 20px 24px;
    }

    .clinical-order-modal .clinical-modal-icon {
        align-items: center;
        background: rgba(255, 255, 255, .14);
        border: 1px solid rgba(255, 255, 255, .28);
        border-radius: 14px;
        color: #fff;
        display: flex;
        flex: 0 0 52px;
        font-size: 23px;
        height: 52px;
        justify-content: center;
        margin-right: 15px;
    }

    .clinical-order-modal .clinical-modal-heading {
        min-width: 0;
    }

    .clinical-order-modal .clinical-modal-eyebrow {
        color: #9ff5ed;
        display: block;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .09em;
        margin-bottom: 3px;
        text-transform: uppercase;
    }

    .clinical-order-modal .clinical-modal-title {
        color: #fff;
        font-size: 21px;
        font-weight: 700;
        line-height: 1.2;
        margin: 0;
    }

    .clinical-order-modal .clinical-modal-subtitle {
        color: rgba(255, 255, 255, .82);
        font-size: 12px;
        line-height: 1.45;
        margin: 4px 0 0;
    }

    .clinical-order-modal .clinical-modal-close {
        align-items: center;
        background: rgba(255, 255, 255, .94);
        border: 0;
        border-radius: 10px;
        color: #38627e;
        display: flex;
        font-size: 22px;
        height: 38px;
        justify-content: center;
        margin-left: auto;
        opacity: 1;
        padding: 0;
        position: static;
        text-shadow: none;
        width: 38px;
    }

    .clinical-order-modal .clinical-modal-close:hover,
    .clinical-order-modal .clinical-modal-close:focus {
        background: #fff;
        color: #e45555;
        outline: none;
    }

    .clinical-order-modal .clinical-modal-body {
        background: #f7fbfd;
        padding: 22px 24px 12px;
    }

    .clinical-order-modal .clinical-section {
        background: #fff;
        border: 1px solid #d5e6ef;
        border-radius: 13px;
        margin-bottom: 14px;
        padding: 17px;
    }

    .clinical-order-modal .clinical-section-title {
        align-items: center;
        color: #064c78;
        display: flex;
        font-size: 14px;
        font-weight: 700;
        margin: 0 0 14px;
    }

    .clinical-order-modal .clinical-section-title i {
        align-items: center;
        background: #e6f6fa;
        border-radius: 8px;
        color: #0789b6;
        display: flex;
        height: 30px;
        justify-content: center;
        margin-right: 9px;
        width: 30px;
    }

    .clinical-order-modal .form-group {
        margin-bottom: 15px;
    }

    .clinical-order-modal label {
        color: #244a65;
        display: block;
        font-size: 11px;
        font-weight: 700;
        margin-bottom: 7px;
    }

    .clinical-order-modal .required-mark {
        color: #e05252;
    }

    .clinical-order-modal .form-control,
    .clinical-order-modal .select2-container .select2-selection--single,
    .clinical-order-modal .select2-container .select2-selection--multiple {
        background: #fff;
        border: 1px solid #c7dce8 !important;
        border-radius: 9px !important;
        box-shadow: none !important;
        color: #244a65;
        min-height: 42px;
    }

    .clinical-order-modal textarea.form-control {
        min-height: 104px;
        padding: 11px 13px;
        resize: vertical;
    }

    .clinical-order-modal .select2-container {
        max-width: 100%;
        width: 100% !important;
    }

    .clinical-order-modal .select2-container .select2-selection--single .select2-selection__rendered {
        line-height: 40px;
        padding-left: 12px;
    }

    .clinical-order-modal .select2-container .select2-selection--single .select2-selection__arrow {
        height: 40px;
    }

    .clinical-order-modal .select2-container .select2-selection--multiple {
        padding: 4px 7px;
    }

    .clinical-order-modal .select2-container--default.select2-container--focus .select2-selection--multiple,
    .clinical-order-modal .form-control:focus {
        border-color: #0aa5b6 !important;
        box-shadow: 0 0 0 3px rgba(10, 165, 182, .1) !important;
    }

    .clinical-order-modal .clinical-help {
        color: #718b9d;
        display: block;
        font-size: 10px;
        line-height: 1.45;
        margin-top: 6px;
    }

    .clinical-order-modal .clinical-search-note {
        align-items: flex-start;
        background: #ecf8fb;
        border: 1px dashed #addbe8;
        border-radius: 9px;
        color: #3f667e;
        display: flex;
        font-size: 11px;
        line-height: 1.45;
        margin-top: 8px;
        padding: 9px 11px;
    }

    .clinical-order-modal .clinical-search-note i {
        color: #0789b6;
        margin: 2px 8px 0 0;
    }

    .clinical-order-modal .clinical-modal-footer {
        align-items: center;
        background: #f7fbfd;
        border-top: 1px solid #d5e6ef;
        display: flex;
        justify-content: flex-end;
        padding: 14px 24px 18px;
    }

    .clinical-order-modal .clinical-modal-footer .btn {
        border-radius: 9px;
        font-size: 12px;
        font-weight: 700;
        min-height: 40px;
        padding: 9px 17px;
    }

    .clinical-order-modal .clinical-modal-footer .btn + .btn {
        margin-left: 9px;
    }

    .clinical-order-modal .btn-clinical-save {
        background: #087fb8;
        border-color: #087fb8;
        color: #fff;
    }

    .clinical-order-modal .btn-clinical-save:hover,
    .clinical-order-modal .btn-clinical-save:focus {
        background: #066b9b;
        border-color: #066b9b;
        color: #fff;
    }

    .clinical-order-modal .btn-clinical-cancel {
        background: #fff;
        border: 1px solid #c7dce8;
        color: #516f83;
    }

    .clinical-order-dropdown .select2-results__option {
        font-size: 12px;
        line-height: 1.35;
        padding: 9px 12px;
    }

    .clinical-order-dropdown .select2-results__message,
    .clinical-order-dropdown .select2-search--dropdown {
        color: #56758a;
        font-size: 12px;
    }

    @media (max-width: 767px) {
        .clinical-order-modal .clinical-modal-header,
        .clinical-order-modal .clinical-modal-body,
        .clinical-order-modal .clinical-modal-footer {
            padding-left: 16px;
            padding-right: 16px;
        }

        .clinical-order-modal .clinical-modal-subtitle {
            display: none;
        }
    }
</style>
