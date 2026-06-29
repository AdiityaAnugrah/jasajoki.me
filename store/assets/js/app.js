document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-coming-soon]').forEach((element) => {
        element.addEventListener('click', (event) => {
            event.preventDefault();
            alert('Fitur ini akan diaktifkan pada tahap berikutnya.');
        });
    });
});
