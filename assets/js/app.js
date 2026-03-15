document.documentElement.classList.add("js-enabled");

const loader = document.getElementById("page-loader");
const sections = Array.from(document.querySelectorAll("main section[id]"));
const navLinks = Array.from(
    document.querySelectorAll(
        ".site-header a[href^='#'], .nav a[href^='#']"
    )
);

function hideLoader() {
    if (!loader) {
        return;
    }

    loader.classList.add("is-hidden");
    document.body.classList.remove("is-loading");
}

function activateSection(sectionId) {
    navLinks.forEach((link) => {
        const href = link.getAttribute("href");
        const isCurrent = href === `#${sectionId}`;
        link.classList.toggle("is-active", isCurrent);
        if (isCurrent) {
            link.classList.add("text-white", "bg-white/10");
        } else {
            link.classList.remove("text-white", "bg-white/10");
        }
    });
}

function bindScrollTracking() {
    if (sections.length === 0) {
        return;
    }

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add("is-visible");
                    activateSection(entry.target.id);
                }
            });
        },
        {
            threshold: 0.15,
            rootMargin: "-10% 0px -30% 0px",
        }
    );

    sections.forEach((section) => observer.observe(section));
}

/* ═══════════ Typing Effect ═══════════ */
function initTypingEffect() {
    const el = document.getElementById("typing-role");
    if (!el) return;

    const roles = [
        "API Architect",
        "React Developer",
        "PHP Engineer",
        "UI/UX Builder",
        "DevOps Ready",
        "Database Designer",
        "Testing Expert",
    ];
    let roleIndex = 0;
    let charIndex = 0;
    let isDeleting = false;
    let typeSpeed = 80;

    function type() {
        const currentRole = roles[roleIndex];

        if (isDeleting) {
            el.textContent = currentRole.substring(0, charIndex - 1);
            charIndex--;
            typeSpeed = 40;
        } else {
            el.textContent = currentRole.substring(0, charIndex + 1);
            charIndex++;
            typeSpeed = 80;
        }

        if (!isDeleting && charIndex === currentRole.length) {
            typeSpeed = 2000;
            isDeleting = true;
        } else if (isDeleting && charIndex === 0) {
            isDeleting = false;
            roleIndex = (roleIndex + 1) % roles.length;
            typeSpeed = 400;
        }

        setTimeout(type, typeSpeed);
    }

    type();
}

/* ═══════════ Counter Animation ═══════════ */
function initCounterAnimation() {
    const counters = document.querySelectorAll(".counter[data-target]");
    if (counters.length === 0) return;

    let animated = false;

    function animateCounters() {
        if (animated) return;
        animated = true;

        counters.forEach((counter) => {
            const target = parseInt(counter.getAttribute("data-target"), 10);
            if (isNaN(target)) return;

            const duration = 2000;
            const startTime = performance.now();

            function update(currentTime) {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);
                const eased = 1 - Math.pow(1 - progress, 3);
                const current = Math.round(eased * target);

                counter.textContent = current + (target >= 100 ? "+" : "");

                if (progress < 1) {
                    requestAnimationFrame(update);
                } else {
                    counter.textContent = target + "+";
                }
            }

            requestAnimationFrame(update);
        });
    }

    const counterSection =
        document.querySelector(".counter-card") ||
        document.querySelector("#home");
    if (!counterSection) return;

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    animateCounters();
                    observer.disconnect();
                }
            });
        },
        { threshold: 0.3 }
    );

    observer.observe(counterSection);
}

/* ═══════════ Scroll Reveal ═══════════ */
function initScrollReveal() {
    const revealElements = document.querySelectorAll(
        ".timeline-item, .panel, article"
    );
    if (revealElements.length === 0) return;

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = "1";
                    entry.target.style.transform = "translateY(0)";
                    observer.unobserve(entry.target);
                }
            });
        },
        { threshold: 0.1, rootMargin: "0px 0px -50px 0px" }
    );

    revealElements.forEach((el) => {
        el.style.opacity = "0";
        el.style.transform = "translateY(20px)";
        el.style.transition = "opacity 0.6s ease, transform 0.6s ease";
        observer.observe(el);
    });
}

function bindDangerConfirmations() {
    const dangerForms = Array.from(document.querySelectorAll("form[data-confirm]"));
    dangerForms.forEach((form) => {
        form.addEventListener("submit", (event) => {
            const message = form.getAttribute("data-confirm") || "Confermi questa operazione?";
            const confirmed = window.confirm(message);
            if (!confirmed) {
                event.preventDefault();
            }
        });
    });
}

function buildShareIntentUrl(platform, url, text, title) {
    const encodedUrl = encodeURIComponent(url);
    const encodedText = encodeURIComponent(text);
    const encodedTitle = encodeURIComponent(title);

    if (platform === "facebook") {
        return `https://www.facebook.com/sharer/sharer.php?u=${encodedUrl}`;
    }

    if (platform === "x") {
        return `https://twitter.com/intent/tweet?url=${encodedUrl}&text=${encodedText}`;
    }

    if (platform === "linkedin") {
        return `https://www.linkedin.com/sharing/share-offsite/?url=${encodedUrl}`;
    }

    if (platform === "whatsapp") {
        return `https://api.whatsapp.com/send?text=${encodedTitle}%20${encodedUrl}`;
    }

    return "";
}

function resolveShareData() {
    const shareContainer = document.querySelector(".social-share");
    const canonicalElement = document.querySelector("link[rel='canonical']");
    const canonicalHref = canonicalElement ? canonicalElement.getAttribute("href") : "";
    const canonicalAbsolute = typeof canonicalHref === "string" && /^https?:\/\//i.test(canonicalHref);
    const pageUrl = canonicalAbsolute ? canonicalHref : window.location.href.split("#")[0];

    const baseTitle = shareContainer?.getAttribute("data-share-title") || document.title;
    const baseDescription = shareContainer?.getAttribute("data-share-description") || "";
    const text = `${baseTitle}${baseDescription ? ` - ${baseDescription}` : ""}`;

    return {
        url: pageUrl,
        title: baseTitle,
        text,
    };
}

function bindSocialShare() {
    const shareLinks = Array.from(document.querySelectorAll(".social-link[data-share]"));
    if (shareLinks.length === 0) {
        return;
    }

    const shareData = resolveShareData();
    shareLinks.forEach((link) => {
        const platform = link.getAttribute("data-share");
        if (!platform) {
            return;
        }

        const intentUrl = buildShareIntentUrl(platform, shareData.url, shareData.text, shareData.title);
        if (intentUrl !== "") {
            link.setAttribute("href", intentUrl);
        }
    });
}

function bindCopyActions() {
    const copyButtons = Array.from(document.querySelectorAll("[data-copy-target]"));
    copyButtons.forEach((button) => {
        button.addEventListener("click", async () => {
            const target = button.getAttribute("data-copy-target");
            if (!target || !navigator.clipboard) {
                return;
            }

            const absoluteUrl = target.startsWith("http")
                ? target
                : `${window.location.origin}${target.startsWith("/") ? "" : "/"}${target}`;

            try {
                await navigator.clipboard.writeText(absoluteUrl);
                button.classList.add("is-copied");
                const oldLabel = button.textContent || "";
                const copiedLabel = button.getAttribute("data-copy-label") || "Copiato";
                button.textContent = copiedLabel;
                window.setTimeout(() => {
                    button.classList.remove("is-copied");
                    button.textContent = oldLabel;
                }, 1200);
            } catch (_error) {
                // Clipboard API may be blocked by browser policies.
            }
        });
    });
}

window.addEventListener("load", () => {
    setTimeout(hideLoader, 260);
    bindScrollTracking();
    bindDangerConfirmations();
    bindSocialShare();
    bindCopyActions();
    initTypingEffect();
    initCounterAnimation();
    initScrollReveal();
});
