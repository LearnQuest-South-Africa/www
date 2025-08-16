// year
document.getElementById('year').textContent = new Date().getFullYear();

// basic client-side validation + demo handler
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

  // In production, POST to your backend or a form service endpoint.
  // Example payload you can send:
  const payload = {
    name, email, phone: form.phone.value.trim(),
    currency: form.currency.value, amount: Number(amount),
    message: form.message.value.trim(), ts: new Date().toISOString()
  };
  console.log('Offer payload:', payload);

  if(typeof dlg.showModal === 'function'){ dlg.showModal(); }
  form.reset();
});

// Optional: hook a privacy link to a simple modal/text (replace with real page)
document.getElementById('privacyLink').addEventListener('click', (e)=>{
  e.preventDefault();
  alert('Add your privacy policy link or modal here.');
});
