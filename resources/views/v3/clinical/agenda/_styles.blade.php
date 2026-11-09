<style>
.ca-alert{margin-bottom:16px;border-radius:11px}.ca-access-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px;padding:18px}.ca-access{display:flex;flex-direction:column;gap:11px;padding:18px;border:1px solid #d5e5ec;border-top:4px solid #1ba58c;border-radius:13px;background:#fbfdfe}.ca-access--emergency{border-top-color:#e05e65}.ca-access--procedure{border-top-color:#5b8fd6}.ca-access__icon{display:grid;place-items:center;width:43px;height:43px;border-radius:12px;color:#087b98;background:#e1f6f8;font-size:19px}.ca-access h3{margin:0;color:#123e5b;font-size:17px;font-weight:800}.ca-access p{min-height:36px;margin:2px 0 0;color:#6f8798;font-size:12px}.ca-room-links{display:flex;flex-direction:column;gap:7px;margin-top:auto}.ca-room-links a{display:grid;grid-template-columns:1fr auto;gap:1px 8px;padding:9px 10px;border:1px solid #d6e5ec;border-radius:9px;background:#fff;color:#15506e}.ca-room-links a:hover{border-color:#75c5d6;background:#f0fbfd;text-decoration:none}.ca-room-links a span{font-size:11px;font-weight:800}.ca-room-links a small{grid-column:1;color:#78919f;font-size:9px}.ca-room-links a i{grid-column:2;grid-row:1/3;align-self:center;font-size:9px}.ca-unconfigured{padding:10px;border:1px dashed #c8dce5;border-radius:8px;color:#8399a5;font-size:11px;text-align:center}.ca-filters{display:grid;grid-template-columns:minmax(220px,1.2fr) minmax(220px,1fr);gap:12px;padding:18px 20px;border-bottom:1px solid #e0eaee;transition:opacity .15s ease}.ca-filters.is-filtering{opacity:.55;pointer-events:none}.ca-doctor-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px;padding:18px}.ca-doctor{display:flex;flex-direction:column;padding:17px;border:1px solid #d7e5eb;border-radius:13px;background:#fff;box-shadow:0 5px 14px rgba(20,73,97,.04)}.ca-doctor__top{display:flex;align-items:center;gap:12px}.ca-avatar{display:grid;place-items:center;flex:0 0 50px;height:50px;border-radius:14px;color:#fff;background:linear-gradient(135deg,#087aa7,#18a7a3);font-size:16px;font-weight:900}.ca-doctor__identity{min-width:0}.ca-doctor__identity>span{color:#000000;font-size:9px;font-weight:800;letter-spacing:.8px}.ca-doctor h3{overflow:hidden;margin:2px 0;color:#0b3d5c;font-size:14px;font-weight:800;text-overflow:ellipsis;white-space:nowrap}.ca-doctor__identity small{color:#7b94a3}.ca-specialties{display:flex;flex-wrap:wrap;gap:5px;min-height:45px;padding:12px 0}.ca-specialties span{align-self:flex-start;padding:4px 7px;border-radius:12px;color:#347188;background:#e8f5f8;font-size:9px;font-weight:700}.ca-capacity{display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:12px}.ca-capacity div{padding:9px;border-radius:9px;background:#f3f8fa}.ca-capacity strong,.ca-capacity span{display:block}.ca-capacity strong{color:#0a5f83;font-size:16px}.ca-capacity span{color:#718997;font-size:9px}.ca-actions{display:flex;flex-wrap:wrap;gap:6px;margin-top:auto}.ca-actions .btn{padding:7px 9px;border-radius:8px;font-size:10px;font-weight:800}.ca-empty{grid-column:1/-1}.ca-modal{overflow:hidden;border:0;border-radius:14px}.ca-modal .modal-header{color:#fff;background:linear-gradient(110deg,#0b668f,#14a3a5)}.ca-modal .modal-header small{font-size:9px;letter-spacing:1px}.ca-modal .modal-title{margin:2px 0 0;font-weight:800}.ca-modal .close{color:#fff;text-shadow:none}.ca-modal label{color:#254b63;font-size:11px;font-weight:800}.ca-modal-note{margin:5px 0 0;padding:10px;border-radius:8px;color:#4c7186;background:#edf7f9;font-size:11px}
.ca-avatar{position:relative;z-index:1;overflow:hidden;flex:0 0 136px;width:136px;height:136px;border-radius:20px;box-shadow:0 5px 14px rgba(14,76,104,.16);transition:transform .22s ease,box-shadow .22s ease}.ca-avatar:hover{z-index:2;transform:scale(1.07);box-shadow:0 10px 24px rgba(14,76,104,.28)}.ca-avatar img{display:block;width:100%;height:100%;object-fit:cover;object-position:center;transition:transform .22s ease}.ca-avatar:hover img{transform:scale(1.035)}.ca-avatar__fallback{display:grid;width:100%;height:100%;place-items:center;font-size:24px}.ca-avatar--photo .ca-avatar__fallback{display:none}.ca-doctor__identity>span{font-size:12px}.ca-doctor h3{font-size:18px}.ca-doctor__identity small{font-size:12px}.ca-specialties span{padding:5px 9px;font-size:11px}.ca-capacity strong{font-size:20px}.ca-capacity span{font-size:11px}.ca-actions .btn{padding:8px 11px;font-size:13px}@media(prefers-reduced-motion:reduce){.ca-avatar,.ca-avatar img{transition:none}}
@media(max-width:1100px){.ca-access-grid,.ca-doctor-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:700px){.ca-access-grid,.ca-doctor-grid,.ca-filters{grid-template-columns:1fr}.ca-access p{min-height:0}}
/* Directorio médico: tarjeta de profesional */
.ca-doctor-grid {
    align-items:stretch;
    gap:18px;
    padding:20px;
}
.ca-doctor {
    position:relative;
    min-height:310px;
    padding:18px;
    overflow:hidden;
    border:1px solid #d8e6ed;
    border-top:4px solid #0e9ea5;
    border-radius:16px;
    background:linear-gradient(145deg,#ffffff 0%,#fbfeff 100%);
    box-shadow:0 8px 22px rgba(17,65,89,.08);
    transition:transform .2s ease,box-shadow .2s ease,border-color .2s ease;
}
.ca-doctor::after {
    position:absolute;
    z-index:0;
    top:-50px;
    right:-52px;
    width:155px;
    height:155px;
    border:26px solid rgba(21,174,169,.06);
    border-radius:50%;
    content:"";
    pointer-events:none;
}
.ca-doctor:hover {
    border-color:#8bcfd4;
    transform:translateY(-4px);
    box-shadow:0 15px 30px rgba(17,65,89,.15);
}
.ca-doctor__top {
    position:relative;
    z-index:1;
    align-items:flex-start;
    gap:14px;
}
.ca-avatar {
    flex:0 0 108px;
    width:108px;
    height:108px;
    border:3px solid #ffffff;
    border-radius:15px;
    box-shadow:0 7px 18px rgba(14,76,104,.16);
}
.ca-avatar:hover {
    transform:scale(1.045);
    box-shadow:0 11px 24px rgba(14,76,104,.25);
}
.ca-avatar__fallback {
    font-size:22px;
}
.ca-doctor__identity {
    display:flex;
    min-width:0;
    flex:1 1 auto;
    flex-direction:column;
    padding-top:4px;
}
.ca-doctor__identity > span {
    display:inline-flex;
    align-self:flex-start;
    margin-bottom:5px;
    padding:4px 7px;
    border-radius:999px;
    color:#000000;
    background:#e8f7f7;
    font-size:10px;
    font-weight:800;
    letter-spacing:.08em;
    line-height:1;
    text-transform:uppercase;
}
.ca-doctor h3 {
    display:-webkit-box;
    overflow:hidden;
    margin:0;
    color:#0d3852;
    font-size:17px;
    font-weight:850;
    line-height:1.28;
    text-overflow:ellipsis;
    white-space:normal;
    -webkit-box-orient:vertical;
    -webkit-line-clamp:2;
}
.ca-doctor__identity small {
    display:inline-flex;
    align-self:flex-start;
    margin-top:7px;
    padding:3px 7px;
    border:1px solid #dbe8ee;
    border-radius:6px;
    color:#526c7b;
    background:#ffffff;
    font-family:monospace;
    font-size:10px;
    line-height:1;
}
.ca-specialties {
    position:relative;
    z-index:1;
    min-height:46px;
    margin-top:11px;
    padding:0;
}
.ca-specialties span {
    padding:5px 9px;
    border:1px solid #cce8eb;
    border-radius:999px;
    color:#12677a;
    background:#ecfafa;
    font-size:10px;
    font-weight:800;
    letter-spacing:.015em;
}
.ca-capacity {
    position:relative;
    z-index:1;
    gap:9px;
    margin:13px 0;
}
.ca-capacity .ca-capacity__item {
    display:flex;
    align-items:center;
    gap:9px;
    min-height:64px;
    padding:10px;
    border:1px solid #e0ebf0;
    border-radius:11px;
    background:#f6fafc;
}
.ca-capacity__item > i {
    display:grid;
    flex:0 0 30px;
    width:30px;
    height:30px;
    place-items:center;
    border-radius:8px;
    color:#087e9d;
    background:#dff4f6;
    font-size:13px;
}
.ca-capacity__item > div {
    display:flex;
    min-width:0;
    flex-direction:column;
}
.ca-capacity span {
    order:1;
    color:#566f7d;
    font-size:10px;
    font-weight:650;
    line-height:1.2;
}
.ca-capacity strong {
    order:0;
    margin-bottom:2px;
    color:#0a4f70;
    font-size:22px;
    font-weight:850;
    line-height:1;
}
.ca-actions {
    position:relative;
    z-index:1;
    display:flex;
    flex-wrap:wrap;
    gap:7px;
    margin-top:auto;
}
.ca-actions .btn {
    display:inline-flex;
    flex:1 1 0;
    align-items:center;
    justify-content:center;
    gap:5px;
    min-width:0;
    min-height:37px;
    padding:8px 7px;
    border-radius:9px;
    font-size:11px;
    font-weight:800;
    line-height:1.1;
    white-space:nowrap;
}
.ca-actions .btn i {
    font-size:12px;
}
.ca-actions .btn-outline-primary {
    color:#087ca5;
    border-color:#75c6d4;
    background:#ffffff;
}
.ca-actions .btn-outline-primary:hover {
    color:#ffffff;
    background:#087ca5;
    border-color:#087ca5;
}
@media (max-width:700px) {
    .ca-doctor { min-height:0; }
    .ca-avatar { flex-basis:92px; width:92px; height:92px; }
    .ca-doctor h3 { font-size:16px; }
}
@media (max-width:390px) {
    .ca-doctor__top { gap:10px; }
    .ca-avatar { flex-basis:78px; width:78px; height:78px; }
    .ca-actions .btn { flex-basis:calc(50% - 4px); }
}

/* Tarjetas del directorio: distribución horizontal y uso completo del ancho. */
.ca-doctor-grid {
    grid-template-columns:minmax(0,1fr);
    gap:12px;
    padding:16px;
}
.ca-doctor {
    display:grid;
    grid-template-columns:minmax(320px,1.15fr) minmax(190px,.7fr) minmax(330px,.9fr);
    grid-template-areas:
        "profile specialties capacity"
        "profile specialties actions";
    align-items:center;
    gap:12px 22px;
    min-height:0;
    padding:18px 20px;
    border-top-width:3px;
}
.ca-doctor__top {
    grid-area:profile;
    align-items:center;
}
.ca-avatar {
    flex:0 0 92px;
    width:92px;
    height:92px;
    border-radius:14px;
}
.ca-avatar__fallback { font-size:20px; }
.ca-doctor__identity > span {
    margin-bottom:6px;
    padding:4px 8px;
    font-size:9px;
}
.ca-doctor h3 {
    display:block;
    margin:0;
    overflow:visible;
    color:#0d3852;
    font-size:18px;
    line-height:1.25;
    white-space:normal;
}
.ca-doctor__identity small {
    gap:5px;
    align-items:center;
    margin-top:8px;
    padding:5px 8px;
    font-size:11px;
}
.ca-specialties {
    grid-area:specialties;
    display:flex;
    min-height:0;
    margin:0;
    padding:0;
    flex-direction:column;
    justify-content:center;
    gap:7px;
}
.ca-section-label {
    color:#607d8b;
    font-size:9px;
    font-weight:800;
    letter-spacing:.08em;
    text-transform:uppercase;
}
.ca-specialties > div {
    display:flex;
    flex-wrap:wrap;
    gap:6px;
}
.ca-specialties span {
    padding:6px 10px;
    font-size:11px;
    line-height:1.15;
}
.ca-capacity {
    grid-area:capacity;
    width:100%;
    margin:0;
}
.ca-capacity .ca-capacity__item {
    min-height:58px;
    padding:9px 11px;
}
.ca-capacity strong { font-size:21px; }
.ca-capacity span { font-size:10px; }
.ca-actions {
    grid-area:actions;
    width:100%;
    margin:0;
}
.ca-actions .btn {
    min-height:40px;
    padding:9px 11px;
    font-size:12px;
}
.ca-actions .btn-primary {
    box-shadow:0 4px 10px rgba(8,124,165,.16);
}
@media (max-width:1050px) {
    .ca-doctor {
        grid-template-columns:minmax(0,1fr) minmax(300px,.9fr);
        grid-template-areas:
            "profile profile"
            "specialties specialties"
            "capacity actions";
    }
}
@media (max-width:740px) {
    .ca-doctor-grid { padding:12px; }
    .ca-doctor {
        grid-template-columns:minmax(0,1fr);
        grid-template-areas:"profile" "specialties" "capacity" "actions";
        gap:14px;
        padding:16px;
    }
    .ca-avatar { flex-basis:80px; width:80px; height:80px; }
    .ca-doctor h3 { font-size:16px; }
    .ca-actions .btn { flex-basis:calc(50% - 4px); }
}
@media (max-width:420px) {
    .ca-capacity { grid-template-columns:1fr; }
    .ca-actions .btn { flex-basis:100%; }
}

/* Vista final del directorio: tarjetas compactas en cuadrícula. */
.ca-doctor-grid {
    grid-template-columns:repeat(3,minmax(0,1fr));
    align-items:stretch;
    gap:18px;
    padding:20px;
    background:#f7fbfc;
}
.ca-doctor {
    display:flex;
    min-width:0;
    min-height:0;
    padding:18px;
    flex-direction:column;
    align-items:stretch;
    gap:14px;
    border:1px solid #cfe1e8;
    border-top:4px solid #0f9ca6;
    border-radius:16px;
    background:linear-gradient(145deg,#ffffff 0%,#f8fcfd 100%);
    box-shadow:0 8px 22px rgba(17,65,89,.08);
}
.ca-doctor::after {
    top:-64px;
    right:-65px;
    width:150px;
    height:150px;
    border:24px solid rgba(15,156,166,.055);
}
.ca-doctor:hover {
    border-color:#83cbd1;
    border-top-color:#087e9d;
    transform:translateY(-3px);
    box-shadow:0 14px 28px rgba(17,65,89,.14);
}
.ca-doctor__top {
    display:flex;
    grid-area:auto;
    align-items:center;
    gap:13px;
}
.ca-avatar {
    flex:0 0 84px;
    width:84px;
    height:84px;
    border:3px solid #fff;
    border-radius:14px;
    box-shadow:0 6px 16px rgba(14,76,104,.18);
}
.ca-avatar:hover { transform:scale(1.035); }
.ca-avatar__fallback { font-size:19px; }
.ca-doctor__identity > span {
    margin-bottom:6px;
    padding:4px 8px;
    color:#075d68;
    background:#ddf4f3;
    font-size:9px;
    letter-spacing:.07em;
}
.ca-doctor h3 {
    display:-webkit-box;
    overflow:hidden;
    margin:0;
    color:#0c3853;
    font-size:16px;
    font-weight:850;
    line-height:1.28;
    white-space:normal;
    -webkit-box-orient:vertical;
    -webkit-line-clamp:2;
}
.ca-doctor__identity small {
    display:inline-flex;
    margin-top:7px;
    padding:4px 7px;
    color:#506c7b;
    background:#fff;
    font-size:10px;
}
.ca-specialties {
    display:flex;
    grid-area:auto;
    min-height:50px;
    margin:0;
    padding:0;
    flex-direction:column;
    justify-content:flex-start;
    gap:7px;
}
.ca-section-label {
    color:#526f7e;
    font-size:9px;
}
.ca-specialties > div {
    gap:6px;
}
.ca-specialties span {
    padding:6px 10px;
    border:1px solid #bfe3e5;
    color:#0b6774;
    background:#e8f8f8;
    font-size:10px;
}
.ca-capacity {
    display:grid;
    grid-area:auto;
    grid-template-columns:repeat(2,minmax(0,1fr));
    gap:9px;
    width:100%;
    margin:0;
}
.ca-capacity .ca-capacity__item {
    min-height:64px;
    padding:10px;
    border:1px solid #d5e8ef;
    background:#eef7fb;
}
.ca-capacity .ca-capacity__item:nth-child(2) {
    border-color:#d4e9df;
    background:#f0f9f4;
}
.ca-capacity__item > i {
    flex-basis:31px;
    width:31px;
    height:31px;
    color:#087b98;
    background:#d8eff5;
}
.ca-capacity .ca-capacity__item:nth-child(2) > i {
    color:#197957;
    background:#dcefe6;
}
.ca-capacity strong {
    color:#074f70;
    font-size:21px;
}
.ca-capacity span {
    color:#506b7a;
    font-size:9px;
    font-weight:700;
}
.ca-actions {
    display:grid;
    grid-area:auto;
    grid-template-columns:repeat(auto-fit,minmax(80px,1fr));
    gap:7px;
    width:100%;
    margin:auto 0 0;
}
.ca-actions .btn {
    width:100%;
    min-width:0;
    min-height:39px;
    padding:8px 6px;
    border-radius:9px;
    font-size:11px;
    font-weight:800;
}
.ca-actions .btn-primary {
    border-color:#087fa8;
    background:linear-gradient(135deg,#087fa8,#0c9bb0);
    box-shadow:0 5px 12px rgba(8,127,168,.2);
}
.ca-actions .btn-outline-primary {
    color:#087b98;
    border-color:#75c4d2;
    background:#f8fdfe;
}
.ca-actions .btn-outline-secondary {
    color:#4d6573;
    border-color:#aabcc5;
    background:#fff;
}
@media (max-width:1200px) {
    .ca-doctor-grid { grid-template-columns:repeat(2,minmax(0,1fr)); }
}
@media (max-width:720px) {
    .ca-doctor-grid { grid-template-columns:minmax(0,1fr); padding:12px; }
    .ca-doctor { padding:16px; }
}
@media (max-width:390px) {
    .ca-avatar { flex-basis:72px; width:72px; height:72px; }
    .ca-doctor h3 { font-size:15px; }
    .ca-capacity { grid-template-columns:1fr; }
    .ca-actions { grid-template-columns:1fr; }
}

/* Interacciones de los accesos principales de cada tarjeta. */
.ca-actions .btn {
    position:relative;
    overflow:hidden;
    transition:transform .18s ease,box-shadow .18s ease,color .18s ease,
        background-color .18s ease,border-color .18s ease;
}
.ca-actions .btn::after {
    position:absolute;
    top:-50%;
    left:-65%;
    width:42%;
    height:200%;
    background:rgba(255,255,255,.28);
    content:"";
    pointer-events:none;
    transform:rotate(18deg);
    transition:left .38s ease;
}
.ca-actions .btn:hover {
    transform:translateY(-2px);
    box-shadow:0 7px 15px rgba(10,79,112,.18);
}
.ca-actions .btn:hover::after {
    left:125%;
}
.ca-actions .btn:active {
    transform:translateY(0) scale(.98);
    box-shadow:0 3px 7px rgba(10,79,112,.14);
}
.ca-actions .btn i {
    transition:transform .18s ease;
}
.ca-actions .btn:hover i {
    transform:scale(1.15);
}
.ca-actions .btn-primary:hover {
    border-color:#066f91;
    background:linear-gradient(135deg,#066f91,#078b9e);
}
.ca-actions .btn-outline-primary:hover {
    color:#fff;
    border-color:#087b98;
    background:#087b98;
}
.ca-actions .btn-outline-secondary:hover {
    color:#fff;
    border-color:#526b78;
    background:#526b78;
}
.ca-actions .btn:focus-visible {
    outline:3px solid rgba(14,156,166,.25);
    outline-offset:2px;
}

@media (prefers-reduced-motion:reduce) {
    .ca-doctor,
    .ca-avatar,
    .ca-avatar img,
    .ca-actions .btn,
    .ca-actions .btn::after,
    .ca-actions .btn i {
        transition:none;
    }
    .ca-doctor:hover,
    .ca-avatar:hover,
    .ca-actions .btn:hover,
    .ca-actions .btn:hover i {
        transform:none;
    }
}
</style>
