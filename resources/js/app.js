document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.querySelector('#sidebar');
    document.querySelector('[data-sidebar-toggle]')?.addEventListener('click', () => sidebar?.classList.toggle('open'));

    document.querySelectorAll('[data-dismiss]').forEach((button) => {
        button.addEventListener('click', () => button.closest('.alert')?.remove());
    });

    document.querySelectorAll('[data-password-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            const input = document.getElementById(button.dataset.passwordToggle);
            if (input) input.type = input.type === 'password' ? 'text' : 'password';
        });
    });

    document.querySelectorAll('[data-modal-open]').forEach((button) => {
        button.addEventListener('click', () => document.getElementById(button.dataset.modalOpen)?.showModal());
    });
    document.querySelectorAll('[data-modal-close]').forEach((button) => {
        button.addEventListener('click', () => button.closest('dialog')?.close());
    });

    document.querySelector('[data-photo-input]')?.addEventListener('change', (event) => {
        const preview = document.querySelector('[data-photo-preview]');
        const file = event.target.files?.[0];
        if (preview && file) {
            preview.src = URL.createObjectURL(file);
            preview.style.display = 'block';
        }
    });
});
