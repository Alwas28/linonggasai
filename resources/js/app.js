/**
 * Small progressive enhancements for the L-DCH pages, mirroring the
 * interactions of the reference HTML templates. Every page works without JS.
 */

// Toggle elements: <button data-toggle="#target">
document.querySelectorAll('[data-toggle]').forEach((button) => {
    button.addEventListener('click', () => {
        const target = document.querySelector(button.dataset.toggle);
        if (target) {
            target.classList.toggle('hidden');
            button.setAttribute('aria-expanded', String(!target.classList.contains('hidden')));
        }
    });
});

// Modals / bottom sheets: <button data-modal-open="id">, [data-modal-close]
document.querySelectorAll('[data-modal-open]').forEach((button) => {
    button.addEventListener('click', () => {
        const modal = document.getElementById(button.dataset.modalOpen);
        if (modal) {
            modal.classList.remove('hidden');
            modal.querySelector('textarea, input:not([type=hidden])')?.focus();
        }
    });
});

document.querySelectorAll('[data-modal-close]').forEach((element) => {
    element.addEventListener('click', () => element.closest('[data-modal]')?.classList.add('hidden'));
});

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
        document.querySelectorAll('[data-modal]').forEach((modal) => modal.classList.add('hidden'));
    }
});

// Confirm before submitting destructive forms: <form data-confirm="Yakin?">
document.querySelectorAll('form[data-confirm]').forEach((form) => {
    form.addEventListener('submit', (event) => {
        if (!window.confirm(form.dataset.confirm)) {
            event.preventDefault();
        }
    });
});

// Auto-hide flash toast
const toast = document.getElementById('toast');
if (toast) {
    setTimeout(() => toast.classList.add('opacity-0', 'pointer-events-none'), 4500);
    toast.querySelector('[data-dismiss]')?.addEventListener('click', () => toast.remove());
}

// Reveal on scroll
const reveals = document.querySelectorAll('.reveal');
if ('IntersectionObserver' in window) {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('in');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12 });
    reveals.forEach((element) => observer.observe(element));
} else {
    reveals.forEach((element) => element.classList.add('in'));
}

// Image preview for file inputs: <input type="file" data-preview="#img">
document.querySelectorAll('input[type=file][data-preview]').forEach((input) => {
    input.addEventListener('change', () => {
        const preview = document.querySelector(input.dataset.preview);
        const file = input.files?.[0];
        if (preview && file) {
            preview.src = URL.createObjectURL(file);
            preview.classList.remove('hidden');
            document.querySelector(input.dataset.previewHide ?? '')?.classList.add('hidden');
        }
    });
});

// WhatsApp order button with a quantity picker on the product page
const waForm = document.getElementById('wa-order');
if (waForm) {
    const quantity = waForm.querySelector('[name=quantity]');
    const link = waForm.querySelector('[data-wa-link]');
    const update = () => {
        const qty = Math.max(1, parseInt(quantity.value || '1', 10));
        const message = waForm.dataset.message.replace('[Jumlah]', `${qty} ${waForm.dataset.unit}`);
        link.href = `${waForm.dataset.base}?text=${encodeURIComponent(message)}`;
    };
    waForm.querySelectorAll('[data-step]').forEach((button) => {
        button.addEventListener('click', () => {
            quantity.value = Math.max(1, parseInt(quantity.value || '1', 10) + parseInt(button.dataset.step, 10));
            update();
        });
    });
    quantity.addEventListener('input', update);
    update();
}

// Repeatable order item rows on the seller "Catat Pesanan" form
const itemsContainer = document.getElementById('order-items');
if (itemsContainer) {
    const template = document.getElementById('order-item-template');
    const totalLabel = document.getElementById('order-total');
    const format = (value) => 'Rp ' + Math.round(value).toLocaleString('id-ID');

    const recalc = () => {
        let total = 0;
        itemsContainer.querySelectorAll('[data-item]').forEach((row) => {
            const qty = parseFloat(row.querySelector('[data-qty]').value || '0');
            const price = parseFloat(row.querySelector('[data-price]').value || '0');
            total += qty * price;
        });
        totalLabel.textContent = format(total);
    };

    const reindex = () => {
        itemsContainer.querySelectorAll('[data-item]').forEach((row, index) => {
            row.querySelectorAll('[data-name]').forEach((field) => {
                field.name = `items[${index}][${field.dataset.name}]`;
            });
        });
    };

    const bind = (row) => {
        const select = row.querySelector('[data-product]');
        select.addEventListener('change', () => {
            const option = select.selectedOptions[0];
            if (option?.dataset.price) {
                row.querySelector('[data-price]').value = option.dataset.price;
            }
            recalc();
        });
        row.querySelectorAll('input').forEach((input) => input.addEventListener('input', recalc));
        row.querySelector('[data-remove]')?.addEventListener('click', () => {
            if (itemsContainer.querySelectorAll('[data-item]').length > 1) {
                row.remove();
                reindex();
                recalc();
            }
        });
    };

    itemsContainer.querySelectorAll('[data-item]').forEach(bind);
    document.getElementById('add-order-item')?.addEventListener('click', () => {
        const row = template.content.firstElementChild.cloneNode(true);
        itemsContainer.appendChild(row);
        bind(row);
        reindex();
    });
    recalc();
}
