// dynamic domain name
const domEl = document.getElementById('domain');
domEl.textContent = window.location.hostname || 'yourdomain.tld';
document.getElementById('year').textContent = new Date().getFullYear();

// validation + mailto submit
const form = document.getElementById('offerForm');
const dlg = document.getElementById('thanks');

function setError(id, msg){ const el=document.getElementById(id); if(el) el.textContent=msg||''; }

form.addEventListener('submit', (e)=>{
  e.preventDefault();
  let ok = true;
  const name = form.name.value.trim();
  const email = form.email.value.trim();
  const amount = form.amount.value.trim();
  if(!name){ setError('nameError','Please enter your name.'); ok=false; } else setError('nameError','');
  if(!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)){ setError('emailError','Valid email required.'); ok=false; } else setError('emailError','');
  if(!amount || Number(amount)<=0){ setError('amountError','Enter a valid amount.'); ok=false; } else setError('amountError','');
  if(!ok) return;

  const payload = {
    domain: domEl.textContent,
    name, email, phone: form.phone.value.trim(),
    currency: form.currency.value, amount: Number(amount),
    message: form.message.value.trim(), ts: new Date().toISOString()
  };

  // Build mailto with prefilled subject/body
  const subject = `Offer for ${payload.domain}`;
  const bodyLines = [
    `Domain: ${payload.domain}`,
    `Name: ${payload.name}`,
    `Email: ${payload.email}`,
    `Phone: ${payload.phone || '-'}`,
    `Offer: ${payload.currency} ${payload.amount}`,
    `Message: ${payload.message || '-'}`,
    `Submitted: ${payload.ts}`
  ];
  const body = encodeURIComponent(bodyLines.join('\n'));
  const mailto = `mailto:billing@skunkworks.africa?subject=${encodeURIComponent(subject)}&body=${body}`;

  // attempt to open their email client
  window.location.href = mailto;

  if(typeof dlg.showModal === 'function'){ dlg.showModal(); }
  form.reset();
});

document.getElementById('privacyLink').addEventListener('click', (e)=>{
  e.preventDefault();
  alert('Add a hosted privacy policy URL here.');
});
