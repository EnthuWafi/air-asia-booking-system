function setActivePage() {
    const currentUrl = window.location.href.split('?')[0].replace('index.php', '/');
    const navLinks = document.querySelectorAll('.nav-link');

    navLinks.forEach(link => {
        if (link.href.split('?')[0].replace('index.php', '/') === currentUrl) {
            link.classList.add('active');
        }
    });
}

setActivePage();