
const toggle=document.querySelector('.menu-toggle');const nav=document.querySelector('.nav-links');
if(toggle&&nav){toggle.addEventListener('click',()=>{const open=nav.classList.toggle('open');toggle.setAttribute('aria-expanded',String(open));});}
const observer=new IntersectionObserver(entries=>entries.forEach(e=>{if(e.isIntersecting){e.target.classList.add('visible');observer.unobserve(e.target);}}),{threshold:.08});
document.querySelectorAll('.reveal').forEach(el=>observer.observe(el));
document.querySelectorAll('[data-filter]').forEach(btn=>btn.addEventListener('click',()=>{document.querySelectorAll('[data-filter]').forEach(b=>b.classList.remove('active'));btn.classList.add('active');const key=btn.dataset.filter;document.querySelectorAll('[data-category]').forEach(card=>{card.hidden=key!=='all'&&card.dataset.category!==key;});}));
const form=document.querySelector('#enquiry-form');if(form){form.addEventListener('submit',e=>{e.preventDefault();const status=document.querySelector('.form-status');status.textContent='Your message is ready, but this demo form is not connected to an email or CRM service yet. Connect your preferred form handler before launch.';});}
