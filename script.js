// Year + UX helpers
document.getElementById('year').textContent = new Date().getFullYear();

// Client-side validation + friendly feedback
const form = document.getElementById('offerForm');
const dlg = document.getElementById('thanks');
function setError(id, msg){ const el=document.getElementById(id); if(el) el.textContent=msg||''; }

form.addEventListener('submit', (e)=>{
  let ok = true;
  if(!form.name.value.trim()){ setError('nameError','Please enter your name.'); ok=false; } else setError('nameError','');
  const email = form.email.value.trim();
  if(!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)){ setError('emailError','Valid email required.'); ok=false; } else setError('emailError','');
  if(!form.amount.value || Number(form.amount.value)<=0){ setError('amountError','Enter a valid amount.'); ok=false; } else setError('amountError','');
  if(!ok){ e.preventDefault(); }
  else { setTimeout(()=>{ if(typeof dlg.showModal==='function') dlg.showModal(); }, 200); }
});

document.getElementById('privacyLink').addEventListener('click', (e)=>{
  e.preventDefault();
  alert('Add a hosted privacy policy URL here.');
});
