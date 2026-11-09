<style>
    #area_trabajo .hc4-standard-module-container {
        overflow: hidden;
        border: 2px solid #c9dee8 !important;
        border-radius: 12px !important;
        background: #fff !important;
        box-shadow: 0 8px 20px rgba(6, 84, 126, .08);
    }

    #area_trabajo .hc4-standard-module-header {
        display: grid !important;
        grid-template-columns: minmax(0, 1fr) minmax(360px, 1.15fr) minmax(0, 1fr);
        align-items: center;
        gap: 20px;
        grid-template-rows: 50px;
        height: 70px;
        min-height: 70px;
        padding: 10px 20px !important;
        color: #fff !important;
        font-family: 'Helvetica general3', Arial, sans-serif;
        background: linear-gradient(105deg, #07547e 0%, #087eae 68%, #16aaa5 100%) !important;
        border: 0 !important;
        box-sizing: border-box;
    }

    #area_trabajo .hc4-standard-module-header__title,
    #area_trabajo .hc4-standard-module-header__patient,
    #area_trabajo .hc4-standard-module-header__actions {
        display: flex;
        grid-row: 1;
        align-items: center;
        align-self: stretch;
        height: 100%;
        min-width: 0;
        color: #fff !important;
        font-weight: 700 !important;
        background: transparent !important;
    }

    #area_trabajo .hc4-standard-module-header__title {
        grid-column: 1;
        justify-content: flex-start;
        gap: 10px;
        font-size: 15px;
        line-height: 1.25;
        text-align: left;
        text-transform: uppercase;
        overflow-wrap: anywhere;
    }

    #area_trabajo .hc4-standard-module-header__icon {
        width: 35px !important;
        height: 35px !important;
        flex: 0 0 35px;
        margin: 0 !important;
        object-fit: contain;
    }

    #area_trabajo .hc4-standard-module-header__patient {
        grid-column: 2;
        justify-content: center;
        font-size: 14px;
        line-height: 1.3;
        text-align: center;
        white-space: normal;
        overflow-wrap: anywhere;
    }

    #area_trabajo .hc4-standard-module-header__actions {
        grid-column: 3;
        justify-content: flex-end;
        gap: 10px;
    }

    #area_trabajo .hc4-standard-module-header__action {
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: auto !important;
        min-height: 38px;
        margin: 0 !important;
        padding: 8px 12px !important;
        color: #fff !important;
        font-size: 11px;
        font-weight: 700 !important;
        line-height: 1.1;
        white-space: nowrap;
        background: rgba(255, 255, 255, .12) !important;
        border: 1px solid rgba(255, 255, 255, .55) !important;
        border-radius: 9px !important;
        box-shadow: none !important;
    }

    #area_trabajo .hc4-standard-module-header__action img {
        width: 19px !important;
        height: 19px !important;
        margin: 0 !important;
        object-fit: contain;
    }

    @media (max-width: 900px) {
        #area_trabajo .hc4-standard-module-header {
            grid-template-columns: minmax(0, 1fr) auto;
            grid-template-rows: auto auto;
            height: auto;
            gap: 10px;
        }

        #area_trabajo .hc4-standard-module-header__patient {
            grid-column: 1 / -1;
            grid-row: 2;
            justify-content: flex-start;
            text-align: left;
        }

        #area_trabajo .hc4-standard-module-header__actions {
            grid-column: 2;
            grid-row: 1;
        }
    }
</style>
