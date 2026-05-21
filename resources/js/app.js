document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.getElementById('cart-floating-toggle');
    const panel = document.getElementById('cart-floating-panel');
    const chevron = document.getElementById('cart-floating-chevron');
    const footer = document.querySelector('footer');

    if (!toggle || !panel) {
        return;
    }

    const setExpanded = (expanded) => {
        panel.classList.toggle('hidden', !expanded);
        toggle.setAttribute('aria-expanded', expanded ? 'true' : 'false');
        chevron?.classList.toggle('rotate-180', expanded);
        document.body.classList.toggle('pb-36', expanded);
        document.body.classList.toggle('sm:pb-32', expanded);
    };

    setExpanded(true);

    toggle.addEventListener('click', () => {
        const isHidden = panel.classList.contains('hidden');
        setExpanded(isHidden);
    });

    if (footer) {
        footer.classList.add('mb-28', 'sm:mb-24');
    }
});
