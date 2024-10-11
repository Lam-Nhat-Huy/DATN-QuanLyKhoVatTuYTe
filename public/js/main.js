// Lấy tất cả các nút với class 'load_animation'
const loadAnimationButtons = document.querySelectorAll('.load_animation');

// Thêm sự kiện click cho từng nút
loadAnimationButtons.forEach(button => {
    button.addEventListener('click', function (event) {
        event.preventDefault();

        document.getElementById('loading').style.display = 'block';
        document.getElementById('loading-overlay').style.display = 'block';

        const form = this.closest('form'); // Lấy form cha của nút
        const submitButton = form.querySelector('button[type="submit"]');

        submitButton.disabled = true;

        setTimeout(() => {
            form.submit();
        }, 500);
    });
});
