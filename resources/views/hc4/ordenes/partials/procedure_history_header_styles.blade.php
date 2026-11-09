<style>
    #area_trabajo .hc4-procedure-orders {
        overflow: hidden;
        border: 1px solid #1aa4b0 !important;
        border-radius: 15px !important;
        background: #fff !important;
    }

    #area_trabajo .hc4-procedure-orders > .hc4-procedure-orders__header {
        display: grid !important;
        grid-template-areas: "title patient action";
        grid-template-columns: minmax(300px, 1fr) minmax(420px, 1.4fr) auto;
        align-items: center;
        gap: 18px;
        min-height: 86px;
        padding: 14px 20px !important;
        border: 0 !important;
        background: linear-gradient(105deg, #07547e 0%, #087eae 68%, #16aaa5 100%) !important;
        color: #fff !important;
    }

    #area_trabajo .hc4-procedure-orders__header::before,
    #area_trabajo .hc4-procedure-orders__header::after {
        display: none !important;
        content: none !important;
    }

    .hc4-procedure-orders__title {
        grid-area: title;
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
        font-size: 16px;
        font-weight: 700;
        line-height: 1.3;
    }

    .hc4-procedure-orders__icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 40px;
        width: 40px;
        height: 40px;
        border: 1px solid rgba(255,255,255,.35);
        border-radius: 10px;
        background: rgba(255,255,255,.12);
        font-size: 18px;
    }

    .hc4-procedure-orders__patient {
        grid-area: patient;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 18px;
        min-width: 0;
        font-size: 14px;
        line-height: 1.35;
    }

    .hc4-procedure-orders__patient-name {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .hc4-procedure-orders__age {
        flex: 0 0 auto;
        white-space: nowrap;
    }

    #area_trabajo .hc4-procedure-orders__action {
        grid-area: action;
        justify-self: end;
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: max-content !important;
        max-width: 290px;
        min-height: 40px;
        margin: 0 !important;
        padding: 8px 13px !important;
        border: 1px solid rgba(255,255,255,.38) !important;
        border-radius: 9px !important;
        background: rgba(255,255,255,.13) !important;
        color: #fff !important;
        box-shadow: none !important;
        font-size: 13px;
        line-height: 1.2;
        white-space: nowrap;
        cursor: pointer;
    }

    #area_trabajo .hc4-procedure-orders__action:hover,
    #area_trabajo .hc4-procedure-orders__action:focus {
        background: rgba(255,255,255,.23) !important;
        color: #fff !important;
        text-decoration: none;
    }

    #area_trabajo .hc4-procedure-orders__action img {
        position: static !important;
        width: 19px !important;
        height: 19px !important;
        margin: 0 !important;
    }

    #area_trabajo .hc4-procedure-orders > .box-body {
        background: #fff !important;
    }

    #area_trabajo .hc4-procedure-order-card {
        overflow: hidden;
        margin-bottom: 18px !important;
        border: 1px solid #1aa4b0 !important;
        border-radius: 15px !important;
        background: #fff !important;
        box-shadow: 0 8px 22px rgba(5,84,126,.09);
    }

    #area_trabajo .hc4-procedure-order-summary {
        padding: 12px 18px !important;
        border: 0 !important;
        background: linear-gradient(105deg, #07547e 0%, #087eae 68%, #16aaa5 100%) !important;
        color: #fff !important;
    }

    #area_trabajo .hc4-procedure-order-summary--grid {
        display: grid !important;
        grid-template-columns: minmax(0, 3fr) minmax(0, 3fr) minmax(0, 4fr) !important;
        align-items: center !important;
        gap: 0 !important;
        min-height: 72px;
    }

    .hc4-procedure-order-summary__date,
    .hc4-procedure-order-summary__doctor {
        min-width: 0;
        font-size: 13px;
        line-height: 1.35;
        white-space: nowrap;
    }

    .hc4-procedure-order-summary__doctor {
        text-align: center;
    }

    .hc4-procedure-order-summary__date {
        grid-column: 1;
        justify-self: start;
        text-align: left !important;
    }

    .hc4-procedure-order-summary__doctor {
        grid-column: 2;
        justify-self: center;
        text-align: center !important;
    }

    .hc4-procedure-order-summary__right {
        grid-column: 3;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
        width: 100%;
        min-width: 0;
    }

    .hc4-procedure-order-summary__procedures {
        min-width: 0;
        text-align: right;
    }

    .hc4-procedure-order-summary__procedures span {
        display: block;
        overflow: hidden;
        max-width: 390px;
        margin-top: 0;
        color: #fff;
        font-size: 15px;
        font-weight: 700;
        line-height: 1.25;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .hc4-procedure-order-summary__tools {
        position: static !important;
        display: flex;
        align-items: center;
        gap: 8px;
        flex: 0 0 auto;
        float: none !important;
        margin: 0 !important;
    }

    #area_trabajo .hc4-procedure-order-summary__delete {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 38px;
        height: 38px;
        min-height: 38px;
        padding: 0;
        border: 1px solid rgba(255,255,255,.42);
        border-radius: 9px;
        background: rgba(206,63,63,.78);
        color: #fff;
        font-size: 15px;
        white-space: nowrap;
    }

    #area_trabajo .hc4-procedure-order-summary__main {
        display: grid !important;
        grid-template-columns: minmax(260px, 1fr) minmax(240px, 1fr) auto auto;
        align-items: center;
        gap: 18px;
        min-height: 42px;
        margin: 0 !important;
    }

    #area_trabajo .hc4-procedure-order-summary__main::before,
    #area_trabajo .hc4-procedure-order-summary__main::after,
    #area_trabajo .hc4-procedure-order-detail__header-row::before,
    #area_trabajo .hc4-procedure-order-detail__header-row::after {
        display: none !important;
        content: none !important;
    }

    #area_trabajo .hc4-procedure-order-summary__main > div,
    #area_trabajo .hc4-procedure-order-detail__header-row > div {
        float: none !important;
        width: auto !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    #area_trabajo .hc4-procedure-order-summary__main > div:nth-child(2) {
        text-align: center;
    }

    .hc4-procedure-order-summary__id {
        padding: 7px 10px !important;
        border-radius: 8px;
        background: rgba(0,61,105,.25);
        font-weight: 700;
        white-space: nowrap;
    }

    #area_trabajo .hc4-procedure-order-summary__toggle {
        width: 38px !important;
        height: 38px !important;
        margin: 0 !important;
        padding: 0 !important;
        border: 1px solid rgba(255,255,255,.4) !important;
        border-radius: 9px !important;
        background: rgba(255,255,255,.13) !important;
        color: #fff !important;
    }

    #area_trabajo .hc4-procedure-order-summary__procedures-row {
        display: flex !important;
        align-items: center;
        gap: 10px;
        margin: 7px 0 0 !important;
    }

    #area_trabajo .hc4-procedure-order-summary__procedures-row > div {
        float: none !important;
        width: auto !important;
        padding: 0 !important;
    }

    #area_trabajo .hc4-procedure-order-summary__procedures-row .badge {
        display: block;
        overflow: hidden;
        max-width: min(850px, 65vw);
        color: #fff !important;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    #area_trabajo .hc4-procedure-order-detail {
        overflow: hidden;
        margin-bottom: 0 !important;
        border: 1px solid #25a7b4 !important;
        border-radius: 13px !important;
        background: #fff !important;
    }

    #area_trabajo .hc4-procedure-order-detail__header {
        padding: 10px 16px !important;
        border: 0 !important;
        background: linear-gradient(105deg, #07547e 0%, #087eae 68%, #16aaa5 100%) !important;
        color: #fff !important;
    }

    #area_trabajo .hc4-procedure-order-detail__header-row {
        position: relative !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 100% !important;
        min-height: 44px;
        margin: 0 !important;
    }

    #area_trabajo .hc4-procedure-order-detail__header-row > div {
        display: flex !important;
        align-items: center !important;
        width: auto !important;
        max-width: none !important;
        margin: 0 !important;
        padding-top: 0 !important;
    }

    #area_trabajo .hc4-procedure-order-detail__header-row > div:first-child {
        position: absolute !important;
        top: 50% !important;
        left: 0 !important;
        justify-content: flex-start !important;
        text-align: left !important;
        transform: translateY(-50%) !important;
    }

    #area_trabajo .hc4-procedure-order-detail__header-row > div:nth-child(2) {
        justify-content: center !important;
        text-align: center !important;
        font-size: 16px;
        font-weight: 700;
        white-space: nowrap;
    }

    #area_trabajo .hc4-procedure-order-detail__header-row > div:last-child {
        position: absolute !important;
        top: 50% !important;
        right: 0 !important;
        justify-content: flex-end !important;
        text-align: right !important;
        transform: translateY(-50%) !important;
    }

    #area_trabajo .hc4-procedure-order-detail__header-row .btn,
    #area_trabajo .hc4-procedure-order-detail__header-row a {
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        gap: 7px;
        width: auto !important;
        min-height: 40px;
        margin: 0 !important;
        padding: 8px 13px !important;
        border: 1px solid rgba(255,255,255,.42) !important;
        border-radius: 9px !important;
        background: rgba(255,255,255,.13) !important;
        color: #fff !important;
        box-shadow: none !important;
        font-size: 13px !important;
        line-height: 1.2;
        white-space: nowrap;
        cursor: pointer;
    }

    #area_trabajo .hc4-procedure-order-detail__header-row .btn > a {
        min-height: 0;
        padding: 0 !important;
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
    }

    #area_trabajo .hc4-procedure-section-title {
        min-height: 30px;
        padding: 6px 12px !important;
        border-radius: 5px 5px 0 0;
        background: linear-gradient(105deg, #07547e 0%, #087eae 68%, #16aaa5 100%) !important;
        color: #fff !important;
    }

    #area_trabajo .hc4-procedure-section-title .control-label {
        float: none !important;
        width: auto !important;
        margin: 0 !important;
        padding: 0 !important;
        color: #fff !important;
        font-size: 12px;
        font-weight: 700;
        line-height: 18px;
    }

    /* Los formularios antiguos cargados por AJAX aun usan estilos en linea. */
    #area_trabajo div[style*="background-color: #004AC1"][style*="color: white"]:has(> .control-label) {
        min-height: 30px;
        padding: 6px 12px !important;
        border-radius: 5px 5px 0 0;
        background: linear-gradient(105deg, #07547e 0%, #087eae 68%, #16aaa5 100%) !important;
        color: #fff !important;
    }

    #area_trabajo div[style*="background-color: #004AC1"][style*="color: white"] > .control-label {
        float: none !important;
        width: auto !important;
        margin: 0 !important;
        padding: 0 !important;
        color: #fff !important;
        font-size: 12px;
        font-weight: 700;
        line-height: 18px;
    }

    #area_trabajo .control-label[for="id_procedimiento"] {
        color: #fff !important;
        text-shadow: 0 1px 1px rgba(0,0,0,.12);
    }

    @media (max-width: 1100px) {
        #area_trabajo .hc4-procedure-orders > .hc4-procedure-orders__header {
            grid-template-areas: "title action" "patient patient";
            grid-template-columns: 1fr auto;
        }

        .hc4-procedure-orders__patient {
            grid-column: 1 / -1;
            justify-content: flex-start;
        }

        #area_trabajo .hc4-procedure-order-summary__main {
            grid-template-columns: 1fr 1fr auto auto;
        }

    }

    @media (max-width: 700px) {
        #area_trabajo .hc4-procedure-orders > .hc4-procedure-orders__header {
            display: flex !important;
            flex-direction: column;
            align-items: stretch;
        }

        .hc4-procedure-orders__patient {
            align-items: flex-start;
            flex-direction: column;
            gap: 5px;
        }

        .hc4-procedure-orders__patient-name {
            white-space: normal;
        }

        #area_trabajo .hc4-procedure-orders__action {
            width: 100% !important;
            max-width: none;
        }

    }
</style>
