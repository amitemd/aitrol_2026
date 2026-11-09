<style>
  .v3-favorite-editor__body{background:#f7fbfd;padding:18px}
  .v3-favorite-editor__form{align-items:start;background:#fff;border:1px solid #d7e6ed;border-radius:10px;display:grid;gap:12px;grid-template-columns:minmax(280px,620px) 110px;justify-content:start;padding:16px}
  .v3-favorite-editor__hint{color:#78909e;font-size:11px;margin:6px 0 0}
  .v3-favorite-editor__save{align-items:center;background:#087ca7;border:0;border-radius:7px;color:#fff;display:inline-flex;font-size:11px;font-weight:700;height:36px;justify-content:center;margin-top:15px;padding:0 16px;width:110px}
  .v3-favorite-editor__save:hover,.v3-favorite-editor__save:focus{background:#066b90;color:#fff}.v3-favorite-editor__save i{margin-right:6px}
  .v3-favorite-editor__empty{border:1px dashed #bdd4df;border-radius:10px;color:#6f8998;margin-top:14px;padding:28px;text-align:center}
  .v3-favorite-editor__empty i{color:#8fb0c2;display:block;font-size:28px;margin-bottom:8px}
  .v3-favorite-editor .alerta_correcto{border:0;border-radius:8px;box-shadow:0 8px 24px rgba(22,77,107,.18);position:fixed;right:24px;top:90px;z-index:9999}
  .v3-favorite-editor__catalog{background:#fff;border:1px solid #d7e6ed;border-radius:10px;margin-top:14px;overflow:hidden}
  .v3-favorite-editor__filters{align-items:end;background:#f7fbfd;border-bottom:1px solid #dbe8ee;display:grid;gap:12px;grid-template-columns:1fr 1fr auto auto;padding:14px 16px}
  .v3-favorite-editor__selected{align-items:center;color:#41677d;display:flex;font-size:11px;font-weight:700;gap:7px;min-height:36px}
  .hc4-lab-catalog{align-items:start;display:grid;gap:10px;grid-auto-flow:dense;grid-template-columns:repeat(3,minmax(0,1fr));max-height:520px;overflow-x:hidden;overflow-y:auto;padding:10px;scrollbar-color:#24a6b4 #e6f3f5;scrollbar-width:thin}
  .hc4-lab-group{background:#fff;border:1px solid #dce7ec;border-radius:8px;min-width:0;overflow:hidden}
  .hc4-lab-group--wide{grid-column:1/-1}.hc4-lab-group--wide .hc4-lab-group__items{column-count:var(--group-columns,3);column-gap:0;column-rule:1px solid #e2e8ed}.hc4-lab-group--wide .hc4-lab-exam-row{break-inside:avoid;page-break-inside:avoid}
  .hc4-lab-group__title{align-items:center;background:var(--group-bg);color:var(--group-text);display:flex;font-size:12px;font-weight:700;gap:8px;justify-content:center;line-height:1.25;margin:0;padding:7px 10px;text-align:center;text-transform:uppercase}
  .hc4-lab-group__title small{color:inherit;font-size:10px;font-weight:600;opacity:.72;text-transform:none}
  .hc4-lab-exam-row{align-items:center;border-bottom:1px solid #edf1f3;color:#3f4652;cursor:pointer;display:flex;gap:8px;min-height:32px;margin:0;padding:5px 9px}.hc4-lab-exam-row:last-child{border-bottom:0}.hc4-lab-exam-row:hover,.hc4-lab-exam-row.is-selected{background:var(--group-selected)}
  .hc4-lab-exam-row--readonly{cursor:default}.hc4-lab-exam-row--readonly i{color:var(--group-text);font-size:10px}
  .hc4-lab-exam-row__check{flex:0 0 22px;text-align:center}.hc4-lab-exam-row__name{font-size:12px;font-weight:500;line-height:1.25;min-width:0;overflow-wrap:anywhere}
  .hc4-lab-catalog__empty{border:1px dashed #cfdde4;border-radius:8px;color:#647481;grid-column:1/-1;padding:24px;text-align:center}
  .v3-favorite-view__summary{align-items:center;background:#f7fbfd;border:1px solid #d7e6ed;border-radius:8px;color:#41677d;display:flex;justify-content:space-between;margin:0 10px 4px;padding:10px 14px}.v3-favorite-view__summary strong{color:#087ca7}
  @media(max-width:1050px){.hc4-lab-catalog{grid-template-columns:repeat(2,minmax(0,1fr))}.hc4-lab-group--wide .hc4-lab-group__items{column-count:2}}
  @media(max-width:767px){.v3-favorite-editor__form,.v3-favorite-editor__filters{grid-template-columns:1fr}.v3-favorite-editor__save{margin-top:0;width:100%}.hc4-lab-catalog{grid-template-columns:1fr;max-height:440px}.hc4-lab-group--wide .hc4-lab-group__items{column-count:1}}
</style>
