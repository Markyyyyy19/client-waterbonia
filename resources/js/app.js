import './bootstrap';
import * as bootstrap from 'bootstrap';

window.bootstrap = bootstrap;

document.querySelectorAll('[data-confirm]').forEach((el) => {
    el.addEventListener('submit', (event) => {
        if (!window.confirm(el.dataset.confirm)) {
            event.preventDefault();
        }
    });
});

document.querySelectorAll('.btn-print').forEach((el) => {
    el.addEventListener('click', () => window.print());
});
