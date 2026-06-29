document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
        anchor.addEventListener('click', (event) => {
            const targetId = anchor.getAttribute('href');
            if (!targetId || targetId === '#') {
                return;
            }

            const target = document.querySelector(targetId);
            if (!target) {
                return;
            }

            event.preventDefault();
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });

    document.querySelectorAll('[data-coming-soon]').forEach((element) => {
        element.addEventListener('click', (event) => {
            event.preventDefault();
            alert('Fitur ini akan diaktifkan pada tahap berikutnya.');
        });
    });
});
