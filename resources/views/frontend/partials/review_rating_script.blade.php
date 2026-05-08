<script>
    (() => {
        const init = () => {
            document.querySelectorAll('[data-review-rating-form]').forEach((form) => {
                if (form.dataset.ratingReady === '1') {
                    return;
                }

                form.dataset.ratingReady = '1';

                const input = form.querySelector('[data-review-rating-input]');
                const label = form.querySelector('[data-review-rating-label]');
                const stars = Array.from(form.querySelectorAll('[data-review-rating-star]'));
                let hoverRating = 0;

                const selectedRating = () => Number(input.value || 0);

                const paint = (value = selectedRating()) => {
                    stars.forEach((star) => {
                        const starValue = Number(star.dataset.reviewRatingStar || 0);
                        const isActive = value >= starValue;
                        const icon = star.querySelector('svg');

                        star.classList.toggle('text-amber-300', isActive);
                        star.classList.toggle('text-slate-500', !isActive);
                        star.setAttribute('aria-pressed', selectedRating() === starValue ? 'true' : 'false');

                        if (icon) {
                            icon.classList.toggle('fill-amber-300', isActive);
                            icon.classList.toggle('fill-transparent', !isActive);
                        }
                    });

                    if (label) {
                        label.textContent = selectedRating() ? `${selectedRating()} / 5` : 'ยังไม่ได้ให้คะแนน';
                    }
                };

                stars.forEach((star) => {
                    const starValue = Number(star.dataset.reviewRatingStar || 0);

                    star.addEventListener('click', () => {
                        input.value = String(starValue);
                        paint();
                    });

                    star.addEventListener('mouseenter', () => {
                        hoverRating = starValue;
                        paint(hoverRating);
                    });

                    star.addEventListener('mouseleave', () => {
                        hoverRating = 0;
                        paint();
                    });

                    star.addEventListener('focus', () => {
                        paint(starValue);
                    });

                    star.addEventListener('blur', () => {
                        paint();
                    });
                });

                paint();
            });
        };

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
        } else {
            init();
        }
    })();
</script>
