<style>
    .ops-page-head{display:flex;align-items:flex-end;justify-content:space-between;gap:16px;margin-bottom:14px}
    .ops-title{margin:0;font-size:1.22rem;font-weight:950;letter-spacing:-.04em;color:var(--text)}
    .ops-subtitle{margin:4px 0 0;color:var(--muted);font-size:.82rem;font-weight:700;line-height:1.5;max-width:760px}
    .ops-tabs{display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:12px}
    .ops-tab{height:36px;padding:0 13px;border-radius:999px;border:1px solid var(--border);background:#fff;color:#475467;text-decoration:none;display:inline-flex;align-items:center;gap:8px;font-size:.76rem;font-weight:950;box-shadow:0 8px 18px rgba(16,24,40,.035);white-space:nowrap}
    .ops-tab b{font-size:.82rem;color:var(--text)}
    .ops-tab.active,.ops-tab:hover{background:var(--brand-soft);border-color:#f1d49c;color:var(--brand-dark)}
    .ops-filter-card{margin-bottom:14px;padding:14px;border:1px solid var(--border);border-radius:20px;background:rgba(255,255,255,.95);box-shadow:0 10px 24px rgba(16,24,40,.045)}
    .ops-filter-grid{display:grid;grid-template-columns:minmax(230px,1.4fr) repeat(4,minmax(140px,1fr)) auto;gap:10px;align-items:end}
    .ops-filter-grid.orders{grid-template-columns:minmax(230px,1.5fr) repeat(5,minmax(128px,1fr)) auto}
    .ops-filter-grid.shipments{grid-template-columns:minmax(230px,1.6fr) repeat(4,minmax(145px,1fr)) auto}
    .ops-field{min-width:0}.ops-label{display:block;margin:0 0 6px;color:var(--muted);font-size:.66rem;font-weight:950;letter-spacing:.06em;text-transform:uppercase}
    .ops-control,.ops-search{width:100%;height:40px;border:1px solid var(--border);border-radius:14px;background:#fbfcfd;color:var(--text);font-size:.8rem;font-weight:850;outline:none;box-shadow:none}
    .ops-control{padding:0 12px}select.ops-control{padding-right:32px}.ops-search{display:flex;align-items:center;gap:9px;padding:0 12px}.ops-search input{width:100%;min-width:0;border:0;background:transparent;outline:none;font-size:.8rem;font-weight:850;color:var(--text)}
    .ops-filter-actions{display:flex;align-items:end;gap:8px}.ops-btn-apply{height:40px;min-width:96px;padding:0 14px;border-radius:14px;border:1px solid var(--brand);background:var(--brand);color:#fff;display:inline-flex;align-items:center;justify-content:center;gap:7px;font-size:.8rem;font-weight:950;white-space:nowrap}.ops-btn-apply:hover{background:var(--brand-dark);border-color:var(--brand-dark);color:#fff}.ops-btn-reset{height:40px;min-width:86px;padding:0 13px;border-radius:14px;border:1px solid var(--border);background:#fff;color:#475467;display:inline-flex;align-items:center;justify-content:center;gap:7px;text-decoration:none;font-size:.8rem;font-weight:950;white-space:nowrap}.ops-btn-reset:hover{background:var(--brand-soft);border-color:#f1d49c;color:var(--brand-dark)}
    .ops-filter-note{margin-top:10px;display:flex;align-items:center;gap:8px;flex-wrap:wrap;color:var(--muted);font-size:.75rem;font-weight:800}
    .ops-table-card{border:1px solid var(--border);border-radius:20px;overflow:hidden;background:#fff;box-shadow:0 10px 24px rgba(16,24,40,.045)}
    .ops-table-card table{margin:0}.ops-table-card th{height:44px;padding:10px 14px!important;background:#fbfcfd;color:#667085;font-size:.68rem;font-weight:950;letter-spacing:.05em;text-transform:uppercase;vertical-align:middle!important;border-bottom:1px solid var(--border)}.ops-table-card td{height:62px;padding:12px 14px!important;vertical-align:middle!important;border-bottom:1px solid #eef1f5}.ops-table-card tr:last-child td{border-bottom:0}
    .ops-link{color:var(--text);font-weight:950;text-decoration:none}.ops-link:hover{color:var(--brand-dark)}.ops-muted{display:block;margin-top:4px;color:var(--muted);font-size:.72rem;font-weight:700}.text-brand{color:var(--brand-dark)!important}
    .ops-avatar{width:34px;height:34px;border-radius:12px;background:var(--brand-soft);color:var(--brand-dark);display:inline-flex;align-items:center;justify-content:center;font-size:.72rem;font-weight:950;flex-shrink:0}.ops-cover{width:38px;height:38px;border-radius:12px;object-fit:cover;border:1px solid var(--border);background:#fff;flex-shrink:0}.ops-cover-fallback{width:38px;height:38px;border-radius:12px;border:1px solid #f1d49c;background:var(--brand-soft);color:var(--brand-dark);display:inline-flex;align-items:center;justify-content:center;flex-shrink:0}
    .ops-pill{display:inline-flex;align-items:center;gap:6px;padding:6px 9px;border-radius:999px;border:1px solid var(--border);background:#fff;font-size:.7rem;font-weight:950;white-space:nowrap}.ops-actions{display:flex;align-items:center;justify-content:flex-end;gap:6px;flex-wrap:wrap}.ops-empty{padding:42px 18px;text-align:center;border:1px solid var(--border);border-radius:20px;background:#fff;box-shadow:0 10px 24px rgba(16,24,40,.045)}.ops-footer{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-top:12px}.address-one-line{max-width:320px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
    .btn-brand{background:var(--brand);border-color:var(--brand);color:#fff;font-weight:900;border-radius:14px}.btn-brand:hover{background:var(--brand-dark);border-color:var(--brand-dark);color:#fff}.btn-soft-brand{border:1px solid #f1d49c;background:var(--brand-soft);color:var(--brand-dark);font-weight:900;border-radius:14px}.btn-soft-brand:hover{background:#fff3d8;border-color:#e7bd70;color:var(--brand-dark)}.proof-thumb{width:54px;height:38px;border-radius:11px;object-fit:cover;border:1px solid var(--border);background:#fff}
    .detail-modal-card{border:1px solid var(--border);border-radius:18px;background:#fff;padding:16px}.detail-label{display:block;color:#667085;font-size:.68rem;font-weight:950;letter-spacing:.05em;text-transform:uppercase;margin-bottom:5px}.detail-value{font-weight:900;color:var(--text);font-size:.9rem}.detail-list{display:flex;flex-direction:column;gap:10px}.detail-product{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:12px;border:1px solid var(--border);border-radius:16px;background:#fbfcfd}.modal-proof{max-width:100%;border-radius:18px;border:1px solid var(--border);background:#fff}.modal-body-soft{background:#f8fafc}.summary-row{display:flex;align-items:center;justify-content:space-between;gap:14px;padding:9px 0;border-bottom:1px dashed #e5e7eb}.summary-row:last-child{border-bottom:0}.summary-row span{color:#667085;font-size:.78rem;font-weight:800}.summary-row strong{font-size:.86rem;font-weight:950;text-align:right}.action-modal-btn{border:0;background:transparent;padding:0}


    .flow-steps{display:flex;flex-wrap:wrap;gap:8px;margin-top:12px}
    .flow-step{min-height:34px;padding:7px 11px;border-radius:999px;border:1px solid var(--border);background:#fff;color:#667085;font-size:.72rem;font-weight:950;display:inline-flex;align-items:center;gap:7px;white-space:nowrap}
    .flow-step.done{background:#ecfdf3;border-color:#b7e4c7;color:#027a48}.flow-step.current{background:var(--brand-soft);border-color:#f1d49c;color:var(--brand-dark)}.flow-step.locked{opacity:.55;background:#f8fafc}.flow-step.action{background:var(--brand);border-color:var(--brand);color:#fff;box-shadow:0 8px 18px rgba(200,147,53,.18)}.flow-step.action:hover{background:var(--brand-dark);border-color:var(--brand-dark);color:#fff}.flow-step-form{display:inline-flex;margin:0}.flow-help{margin-top:10px;color:var(--muted);font-size:.72rem;font-weight:800;line-height:1.45}

    .flow-mini{display:flex;align-items:center;gap:8px;padding:10px 12px;border-radius:14px;background:var(--brand-soft);border:1px solid #f1d49c;color:var(--brand-dark);font-size:.74rem;font-weight:900;line-height:1.35}
    #mapPickerToko{height:360px;border-radius:18px;border:1px solid var(--border);overflow:hidden;background:#eef2f7}.modal-card{padding:16px;border:1px solid var(--border);border-radius:18px;background:#fff}.form-label-modern{font-size:.72rem;font-weight:950;color:#667085;letter-spacing:.04em;text-transform:uppercase}.form-control-modern{border-radius:14px;border:1px solid var(--border);font-weight:800;box-shadow:none}.form-control-modern:focus{border-color:#f1d49c;box-shadow:0 0 0 .2rem rgba(200,147,53,.12)}
    @media (max-width:1300px){.ops-filter-grid,.ops-filter-grid.orders,.ops-filter-grid.shipments{grid-template-columns:repeat(3,minmax(0,1fr))}.ops-filter-grid .ops-field:first-child{grid-column:1/-1}.ops-filter-actions{align-items:stretch}.ops-btn-reset,.ops-btn-apply{width:100%}.ops-table-card{overflow-x:auto}.ops-table-card table{min-width:980px}}
    @media (max-width:720px){.ops-page-head{align-items:flex-start;flex-direction:column}.ops-filter-grid,.ops-filter-grid.orders,.ops-filter-grid.shipments{grid-template-columns:1fr}.ops-tabs{overflow-x:auto;flex-wrap:nowrap;padding-bottom:2px}.ops-title{font-size:1.1rem}.ops-footer{align-items:flex-start;flex-direction:column}}
</style>
<style>
    .ops-page-head.compact{margin-bottom:10px}
    .ops-filter-card.compact{padding:12px 12px 10px;border-radius:18px}
    .ops-filter-grid.payment-only{grid-template-columns:minmax(230px,1.4fr) repeat(5,minmax(120px,1fr)) auto}
    .ops-filter-grid.order-only{grid-template-columns:minmax(240px,1.5fr) repeat(4,minmax(135px,1fr)) auto}
    .ops-filter-grid.shipping-only{grid-template-columns:minmax(240px,1.5fr) repeat(4,minmax(135px,1fr)) auto}
    .ops-table-compact th{height:40px!important;padding:9px 12px!important}
    .ops-table-compact td{height:58px!important;padding:10px 12px!important}
    .ops-modal{border:0;border-radius:22px;box-shadow:0 24px 60px rgba(16,24,40,.22);overflow:hidden}
    .ops-modal .modal-header{padding:18px 20px;background:#fff;border-bottom:1px solid var(--border)}
    .ops-modal .modal-body{padding:20px}
    .ops-modal .modal-footer{padding:14px 20px;background:#fff;border-top:1px solid var(--border)}
    .proof-button{display:inline-flex;align-items:center;gap:8px;border:0;background:transparent;padding:0;color:var(--brand-dark);font-weight:950;font-size:.74rem}
    .proof-button:hover{color:var(--brand)}
    .proof-modal-content{border:0;border-radius:22px;box-shadow:0 24px 70px rgba(16,24,40,.28);overflow:hidden;background:#fff}
    .proof-modal-content .modal-header{border-bottom:1px solid var(--border);padding:18px 20px}
    .proof-modal-body{background:#111827;padding:18px;max-height:78vh;overflow:auto}
    .proof-large{max-width:100%;max-height:72vh;border-radius:16px;object-fit:contain;background:#fff}
    @media (max-width:1300px){.ops-filter-grid.payment-only,.ops-filter-grid.order-only,.ops-filter-grid.shipping-only{grid-template-columns:repeat(3,minmax(0,1fr))}.ops-filter-grid.payment-only .ops-field:first-child,.ops-filter-grid.order-only .ops-field:first-child,.ops-filter-grid.shipping-only .ops-field:first-child{grid-column:1/-1}}
    @media (max-width:720px){.ops-filter-grid.payment-only,.ops-filter-grid.order-only,.ops-filter-grid.shipping-only{grid-template-columns:1fr}.proof-modal-body{padding:10px}.proof-large{max-height:70vh}}
</style>
