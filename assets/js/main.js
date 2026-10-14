/* ClinicBook Pro — vanilla JS: menu, tabs, accordions, booking wizard, counters. */
(function () {
	'use strict';

	/* ---------- mobile menu ---------- */
	var menuBtn = document.getElementById('cbpMenuBtn');
	var nav = document.getElementById('cbpNav');
	if (menuBtn && nav) {
		menuBtn.addEventListener('click', function () { nav.classList.toggle('open'); });
	}

	/* ---------- accordions ---------- */
	document.querySelectorAll('.accordion-head').forEach(function (head) {
		head.addEventListener('click', function () {
			head.parentElement.classList.toggle('open');
		});
	});

	/* ---------- profile tabs ---------- */
	document.querySelectorAll('.tab-btn').forEach(function (btn) {
		btn.addEventListener('click', function () {
			var id = btn.getAttribute('data-tab');
			document.querySelectorAll('.tab-btn').forEach(function (b) { b.classList.remove('active'); });
			document.querySelectorAll('.tab-panel').forEach(function (p) { p.classList.remove('active'); });
			btn.classList.add('active');
			var panel = document.getElementById('tab-' + id);
			if (panel) { panel.classList.add('active'); }
		});
	});

	/* ---------- animated counters ---------- */
	var counters = document.querySelectorAll('[data-count]');
	if ('IntersectionObserver' in window && counters.length) {
		var io = new IntersectionObserver(function (entries) {
			entries.forEach(function (e) {
				if (!e.isIntersecting) { return; }
				var el = e.target, target = parseInt(el.getAttribute('data-count'), 10) || 0;
				io.unobserve(el);
				var start = null;
				function tick(ts) {
					if (!start) { start = ts; }
					var p = Math.min((ts - start) / 1400, 1);
					var val = Math.floor(target * (0.1 + 0.9 * p));
					el.textContent = val >= 1000 ? (val / 1000).toFixed(val % 1000 === 0 ? 0 : 1) + 'k+' : val + (target >= 100 ? '+' : '');
					if (p < 1) { requestAnimationFrame(tick); }
				}
				requestAnimationFrame(tick);
			});
		}, { threshold: 0.4 });
		counters.forEach(function (c) { io.observe(c); });
	}

	/* ---------- doctor picker (booking page, no doctor preselected) ---------- */
	var pickBtn = document.getElementById('cbpGoDoctor');
	if (pickBtn) {
		pickBtn.addEventListener('click', function () {
			var sel = document.getElementById('cbpPickDoctor');
			if (sel && sel.value) { window.location.href = window.location.pathname + '?doctor=' + encodeURIComponent(sel.value); }
		});
	}

	/* ---------- booking wizard ---------- */
	var wizard = document.getElementById('cbpWizard');
	if (wizard && window.CBP_DATA) {
		var doctorId = wizard.getAttribute('data-doctor');
		var state = { date: '', slot: '' };

		function gotoStep(n) {
			wizard.querySelectorAll('.wstep').forEach(function (s) {
				s.classList.toggle('active', s.getAttribute('data-step') === String(n));
			});
			wizard.querySelectorAll('.wpanel').forEach(function (p) {
				p.classList.toggle('active', p.getAttribute('data-panel') === String(n));
			});
			wizard.scrollIntoView({ behavior: 'smooth', block: 'start' });
		}

		wizard.querySelectorAll('.cbp-next').forEach(function (btn) {
			btn.addEventListener('click', function () {
				var go = parseInt(btn.getAttribute('data-goto'), 10);
				if (go === 3 && !state.slot) { alert(CBP_DATA.i18n.select_slot); return; }
				gotoStep(go);
			});
		});

		/* date chips */
		wizard.querySelectorAll('.date-chip').forEach(function (chip) {
			chip.addEventListener('click', function () {
				wizard.querySelectorAll('.date-chip').forEach(function (c) { c.classList.remove('selected'); });
				chip.classList.add('selected');
				state.date = chip.getAttribute('data-date');
				state.slot = '';
				loadSlots();
			});
		});

		function loadSlots() {
			var box = document.getElementById('cbpSlots');
			box.innerHTML = '<p style="color:var(--cbp-muted)">' + CBP_DATA.i18n.loading + '</p>';
			var fd = new FormData();
			fd.append('action', 'cbp_slots');
			fd.append('nonce', CBP_DATA.nonce);
			fd.append('doctor_id', doctorId);
			fd.append('date', state.date);
			fetch(CBP_DATA.ajax, { method: 'POST', body: fd, credentials: 'same-origin' })
				.then(function (r) { return r.json(); })
				.then(function (res) {
					if (!res.success || !res.data.length) {
						box.innerHTML = '<p style="color:var(--cbp-muted)">' + CBP_DATA.i18n.no_slots + '</p>';
						return;
					}
					box.innerHTML = '';
					res.data.forEach(function (s) {
						var b = document.createElement('button');
						b.type = 'button';
						b.className = 'slot' + (s.booked ? ' booked' : '');
						b.textContent = s.label;
						b.disabled = !!s.booked;
						if (!s.booked) {
							b.addEventListener('click', function () {
								box.querySelectorAll('.slot').forEach(function (x) { x.classList.remove('selected'); });
								b.classList.add('selected');
								state.slot = s.start;
							});
						}
						box.appendChild(b);
					});
				})
				.catch(function () {
					box.innerHTML = '<p style="color:var(--cbp-muted)">' + CBP_DATA.i18n.error + '</p>';
				});
		}

		/* confirm booking */
		var confirmBtn = document.getElementById('cbpConfirmBook');
		if (confirmBtn) {
			confirmBtn.addEventListener('click', function () {
				var err = document.getElementById('cbpBookError');
				err.style.display = 'none';
				var fd = new FormData();
				fd.append('action', 'cbp_book');
				fd.append('nonce', CBP_DATA.nonce);
				fd.append('doctor_id', doctorId);
				fd.append('date', state.date);
				fd.append('slot', state.slot);
				fd.append('name', document.getElementById('cbpName').value);
				fd.append('phone', document.getElementById('cbpPhone').value);
				fd.append('notes', document.getElementById('cbpNotes').value);
				confirmBtn.disabled = true;
				confirmBtn.textContent = CBP_DATA.i18n.loading;
				fetch(CBP_DATA.ajax, { method: 'POST', body: fd, credentials: 'same-origin' })
					.then(function (r) { return r.json(); })
					.then(function (res) {
						confirmBtn.disabled = false;
						confirmBtn.textContent = 'Confirm Appointment';
						if (!res.success) {
							err.textContent = (res.data && res.data.message) || CBP_DATA.i18n.error;
							err.style.display = 'block';
							return;
						}
						document.getElementById('cbpApptId').textContent = res.data.appt_id;
						document.getElementById('cbpApptMeta').textContent = res.data.doctor + ' · ' + res.data.date + ' · ' + res.data.slot;
						var wa = document.getElementById('cbpWaBtn');
						if (res.data.whatsapp) { wa.href = res.data.whatsapp; } else { wa.style.display = 'none'; }
						gotoStep(4);
					})
					.catch(function () {
						confirmBtn.disabled = false;
						confirmBtn.textContent = 'Confirm Appointment';
						err.textContent = CBP_DATA.i18n.error;
						err.style.display = 'block';
					});
			});
		}
	}

	/* ---------- doctor dashboard actions ---------- */
	document.querySelectorAll('.cbp-appt-act').forEach(function (btn) {
		btn.addEventListener('click', function () {
			if (!window.CBP_DATA) { return; }
			var row = btn.closest('tr');
			var fd = new FormData();
			fd.append('action', 'cbp_appt_status');
			fd.append('nonce', CBP_DATA.nonce);
			fd.append('appt_id', row.getAttribute('data-appt'));
			fd.append('status', btn.getAttribute('data-act'));
			btn.disabled = true;
			fetch(CBP_DATA.ajax, { method: 'POST', body: fd, credentials: 'same-origin' })
				.then(function (r) { return r.json(); })
				.then(function (res) {
					if (res.success) { window.location.reload(); }
					else { btn.disabled = false; alert(CBP_DATA.i18n.error); }
				})
				.catch(function () { btn.disabled = false; });
		});
	});

	/* ---------- newsletter (demo) ---------- */
	var news = document.getElementById('cbpNews');
	if (news) {
		news.addEventListener('submit', function () {
			var em = document.getElementById('cbpNewsEmail');
			if (em && em.value) { em.value = ''; em.placeholder = 'Subscribed!'; }
		});
	}
})();
