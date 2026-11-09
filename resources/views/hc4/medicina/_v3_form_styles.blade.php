<style>
  .v3-medicine-form { color: #163f5a; font-family: "Helvetica Neue", Arial, sans-serif; padding: 0; }
  .v3-medicine-form * { box-sizing: border-box; }
  .v3-medicine-form__card { background: #fff; border: 1px solid #cfe2ec; border-radius: 14px; box-shadow: 0 10px 28px rgba(18, 78, 111, .12); overflow: hidden; }
  .v3-medicine-form__header { align-items: center; background: linear-gradient(118deg, #086c98 0%, #118caf 58%, #18aaa7 100%); color: #fff; display: flex; justify-content: space-between; min-height: 88px; padding: 20px 26px; position: relative; }
  .v3-medicine-form__header:after { border: 28px solid rgba(255,255,255,.07); border-radius: 50%; content: ''; height: 120px; position: absolute; right: -28px; top: -55px; width: 120px; }
  .v3-medicine-form__heading { align-items: center; display: flex; min-width: 0; position: relative; z-index: 1; }
  .v3-medicine-form__icon { align-items: center; background: rgba(255,255,255,.15); border: 1px solid rgba(255,255,255,.28); border-radius: 12px; display: flex; flex: 0 0 50px; font-size: 22px; height: 50px; justify-content: center; margin-right: 15px; }
  .v3-medicine-form__eyebrow { color: rgba(255,255,255,.78); font-size: 10px; font-weight: 700; letter-spacing: 1.15px; margin-bottom: 3px; text-transform: uppercase; }
  .v3-medicine-form__header h4 { color: #fff; font-size: 22px; font-weight: 700; line-height: 1.15; margin: 0 0 4px; }
  .v3-medicine-form__header p { color: rgba(255,255,255,.84); font-size: 12px; margin: 0; }
  .v3-medicine-form__body { background: #f8fbfd; padding: 24px 26px 8px; }
  .v3-medicine-form__section { background: #fff; border: 1px solid #dbe9f0; border-radius: 11px; margin-bottom: 18px; padding: 20px; }
  .v3-medicine-form__section-title { align-items: center; color: #176f96; display: flex; font-size: 13px; font-weight: 700; margin: 0 0 17px; }
  .v3-medicine-form__section-title i { color: #16a2a4; margin-right: 8px; }
  .v3-medicine-form__grid { display: grid; gap: 18px 20px; grid-template-columns: repeat(2, minmax(0, 1fr)); }
  .v3-medicine-form__field { margin: 0; min-width: 0; }
  .v3-medicine-form__field--wide { grid-column: 1 / -1; }
  .v3-medicine-form__field label { color: #315b73; display: block; font-size: 11px; font-weight: 700; letter-spacing: .25px; margin: 0 0 7px; text-transform: uppercase; }
  .v3-medicine-form__field label .required { color: #e0524d; }
  .v3-medicine-form .form-control { background-color: #fff; border: 1px solid #cadde7; border-radius: 8px; box-shadow: none; color: #264f67; font-size: 13px; height: 42px; padding: 9px 12px; transition: border-color .18s, box-shadow .18s; width: 100%; }
  .v3-medicine-form textarea.form-control { min-height: 92px; resize: vertical; }
  .v3-medicine-form .form-control:focus { border-color: #28a4c5; box-shadow: 0 0 0 3px rgba(28, 151, 186, .12); outline: 0; }
  .v3-medicine-form .has-error .form-control, .v3-medicine-form .has-error .select2-selection { border-color: #df625d !important; }
  .v3-medicine-form .help-block { color: #c74642; display: block; font-size: 11px; margin: 5px 0 0; }
  .v3-medicine-form .select2-container { display: block; width: 100% !important; }
  .v3-medicine-form .select2-container--default .select2-selection--multiple { border: 1px solid #cadde7; border-radius: 8px; min-height: 42px; padding: 3px 6px; }
  .v3-medicine-form .select2-container--default.select2-container--focus .select2-selection--multiple { border-color: #28a4c5; box-shadow: 0 0 0 3px rgba(28, 151, 186, .12); }
  .v3-medicine-form .select2-container--default .select2-selection--multiple .select2-selection__choice { background: #e3f5f7; border: 0; border-radius: 13px; color: #08728e; font-size: 11px; font-weight: 700; margin-top: 5px; padding: 4px 9px; }
  .v3-medicine-form__hint { color: #718b9d; font-size: 11px; margin: 6px 0 0; }
  .v3-medicine-form__footer { align-items: center; background: #fff; border-top: 1px solid #dce9ef; display: flex; justify-content: flex-end; padding: 17px 26px; }
  .v3-medicine-form__submit { align-items: center; background: linear-gradient(110deg, #08729d, #149cac); border: 0; border-radius: 8px; box-shadow: 0 6px 14px rgba(8, 114, 157, .2); color: #fff !important; cursor: pointer; display: inline-flex; font-size: 13px; font-weight: 700; height: 42px; justify-content: center; min-width: 180px; padding: 0 22px; transition: transform .15s, box-shadow .15s; }
  .v3-medicine-form__submit:hover, .v3-medicine-form__submit:focus { box-shadow: 0 8px 18px rgba(8, 114, 157, .28); color: #fff; transform: translateY(-1px); }
  .v3-medicine-form__submit i { margin-right: 8px; }
  @media (max-width: 767px) { .v3-medicine-form__header, .v3-medicine-form__body, .v3-medicine-form__footer { padding-left: 17px; padding-right: 17px; } .v3-medicine-form__grid { grid-template-columns: 1fr; } .v3-medicine-form__field--wide { grid-column: auto; } .v3-medicine-form__submit { width: 100%; } }
</style>
