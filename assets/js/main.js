document.addEventListener('DOMContentLoaded', function() {
    const handlePulseClass = () => {
        const pulseElements = document.querySelectorAll('.bcc-pulse');
        if (window.innerWidth <= 768) {
            pulseElements.forEach(el => {
                // We store the class in a data attribute to be able to add it back
                if (el.classList.contains('bcc-pulse')) {
                    el.setAttribute('data-had-pulse', 'true');
                    el.classList.remove('bcc-pulse');
                }
            });
        } else {
            // Add the class back on larger screens if it was removed
            const elementsThatHadPulse = document.querySelectorAll('[data-had-pulse="true"]');
            elementsThatHadPulse.forEach(el => {
                el.classList.add('bcc-pulse');
            });
        }
    };

    // Run on page load
    handlePulseClass();

    // Run on window resize
    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(handlePulseClass, 250);
    });
});
