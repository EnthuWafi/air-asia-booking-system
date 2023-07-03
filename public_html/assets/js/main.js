function setActivePage() {
    const currentUrl = window.location.href.split('?')[0].replace('index.php', '/');
    const navLinks = document.querySelectorAll('.nav-link');

    navLinks.forEach(link => {
        if (link.href.split('?')[0].replace('index.php', '/') === currentUrl) {
            link.classList.add('active');
        }
    });
}

function initializePopovers() {
    const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]')
    const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl))
}

function autoScrollOffset() {
    var hash = window.location.hash;

    if (hash) {
        var target = $(hash);
        if (target.length) {
            var targetOffset = target.offset().top;
            var windowHeight = $(window).height();
            var offsetValue = 100; // Adjust this value as needed
            var scrollDuration = 1000;

            $('html, body').animate({
                scrollTop: targetOffset - windowHeight / 2 + offsetValue
            }, scrollDuration);
        }
    }
}

autoScrollOffset();
setActivePage();
initializePopovers();
