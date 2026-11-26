const form = document.querySelector('[data-login-form]');
const passwordToggle = form.querySelector('[data-password-toggle]');
const password = document.getElementById(passwordToggle.getAttribute('aria-controls'));
const submitButton = form.querySelector('button[type="submit"]');
const submitLabel = submitButton.querySelector('[data-submit-label]');
const progress = submitButton.querySelector('.access-progress');
const feedback = document.querySelector('[data-login-feedback]');
const idleLabel = submitLabel.textContent;

function setPasswordVisible(visible) {
    password.type = visible ? 'text' : 'password';
    passwordToggle.setAttribute('aria-pressed', String(visible));
}

function setSubmitting(submitting) {
    submitButton.disabled = submitting;
    submitLabel.textContent = submitting ? 'Verificando acceso…' : idleLabel;
    progress.hidden = !submitting;
    feedback.textContent = submitting ? submitLabel.textContent : '';
}

passwordToggle.hidden = false;
passwordToggle.addEventListener('click', () => setPasswordVisible(password.type === 'password'));

form.addEventListener('submit', (event) => {
    if (submitButton.disabled) {
        event.preventDefault();
        return;
    }

    setSubmitting(true);
});

window.addEventListener('pageshow', () => {
    setPasswordVisible(false);
    setSubmitting(false);
});
