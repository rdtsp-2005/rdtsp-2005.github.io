// Active nav link highlight (redundant with nav.js but kept as fallback)
document.querySelectorAll(".links a[data-active]").forEach(link => {
    if (link.dataset.active === document.body.id) link.classList.add("active");
});
