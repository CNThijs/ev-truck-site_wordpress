// Progressive enhancement of the contact and trend report forms. The forms already work without JavaScript (a normal
// POST and a redirect back, see inc/forms/handler.php); this sends them with fetch, shows the server's errors next to
// the fields, announces the result and pushes events to the dataLayer. The server stays the only place that validates.
// Events carry the form, its mode and the language, never personal data.

function track(event, form, extra = {}) {
	window.dataLayer = window.dataLayer || [];
	window.dataLayer.push({
		event,
		form_id: form.dataset.cnForm,
		form_mode: form.dataset.cnMode || undefined,
		form_language: form.dataset.cnLang,
		...extra,
	});
}

function clearErrors(form) {
	form.querySelectorAll('.field.has-error').forEach((field) => field.classList.remove('has-error'));
	form.querySelectorAll('[aria-invalid]').forEach((input) => input.removeAttribute('aria-invalid'));
	form.querySelectorAll('.field__error').forEach((error) => {
		error.textContent = '';
		error.hidden = true;
	});
	const alert = form.querySelector('[data-cn-alert]');
	alert.textContent = '';
	alert.hidden = true;
}

function showErrors(form, errors, message) {
	const alert = form.querySelector('[data-cn-alert]');
	alert.textContent = message;
	alert.hidden = !message;
	Object.entries(errors).forEach(([name, text]) => {
		const input = form.querySelector(`#${CSS.escape(`${form.id}-${name}`)}`);
		const error = form.querySelector(`#${CSS.escape(`${form.id}-${name}-error`)}`);
		if (!input || !error) {
			return;
		}
		input.setAttribute('aria-invalid', 'true');
		input.closest('.field')?.classList.add('has-error');
		error.textContent = text;
		error.hidden = false;
	});
	// Focus the first invalid field in page order.
	const first = form.querySelector('[aria-invalid="true"]');
	if (first) {
		first.focus();
	} else {
		alert.setAttribute('tabindex', '-1');
		alert.focus();
	}
}

function showSuccess(form, message) {
	const wrap = form.closest('.cn-form-wrap');
	const box = document.createElement('div');
	box.className = 'cn-form__success';
	box.setAttribute('role', 'status');
	box.setAttribute('tabindex', '-1');
	const text = document.createElement('p');
	text.textContent = message;
	box.append(text);
	wrap.replaceChildren(box);
	box.focus();
}

function init(form) {
	let started = false;
	form.addEventListener('focusin', () => {
		if (!started) {
			started = true;
			track('form_start', form);
		}
	});

	form.addEventListener('submit', async (event) => {
		event.preventDefault();
		if (form.getAttribute('aria-busy') === 'true') {
			return;
		}
		clearErrors(form);
		form.setAttribute('aria-busy', 'true');
		const button = form.querySelector('button[type="submit"]');
		button.disabled = true;
		try {
			// getAttribute: form.action is the hidden input named "action" (admin-post.php needs it).
			const response = await fetch(form.getAttribute('action'), {
				method: 'POST',
				body: new FormData(form),
				headers: { Accept: 'application/json' },
				credentials: 'same-origin',
			});
			const data = await response.json();
			if (data.ok) {
				track('form_submit', form);
				showSuccess(form, data.message);
				return;
			}
			track('form_error', form, { error_fields: Object.keys(data.errors).join(',') });
			showErrors(form, data.errors, data.message);
			window.turnstile?.reset();
		} catch (error) {
			console.error('Form could not be sent', error);
			showErrors(form, {}, form.dataset.cnNetwork);
		} finally {
			form.removeAttribute('aria-busy');
			button.disabled = false;
		}
	});
}

// The page may come from the page cache: take campaign parameters from the URL (else the cookie that exists only after
// statistics consent) and a fresh nonce and time token from the server.
async function refresh(form) {
	const saved = document.cookie.match(/(?:^|; )cn_campaign=([^;]*)/);
	const sources = [
		new URLSearchParams(location.search),
		new URLSearchParams(saved ? decodeURIComponent(saved[1]) : ''),
	];
	for (const key of ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term']) {
		const value = sources.map((params) => params.get(key)).find(Boolean);
		if (value && form.elements[`cn_${key}`]) form.elements[`cn_${key}`].value = value.slice(0, 100);
	}
	try {
		const token = await (await fetch(form.dataset.cnToken, { credentials: 'omit' })).json();
		form.elements._wpnonce.value = token.nonce;
		form.elements.cn_ts.value = token.ts;
	} catch {
		// Keep the values from the page; they are valid for at least 12 hours.
	}
}

export function enhanceForms(root) {
	root.querySelectorAll('form[data-cn-form]').forEach((form) => {
		init(form);
		refresh(form);
	});
}
