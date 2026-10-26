document.addEventListener("DOMContentLoaded", () => {
    // Mobile Navbar Toggle
    const hamburger = document.querySelector(".hamburger");
    const navLinks = document.querySelector(".nav-links");

    hamburger.addEventListener("click", () => {
        navLinks.classList.toggle("active");
    });

    // Active Navigation Highlight on Scroll
    const sections = document.querySelectorAll("section");
    const navItems = document.querySelectorAll(".nav-links a");

    window.addEventListener("scroll", () => {
        let current = "";
        sections.forEach((section) => {
            const sectionTop = section.offsetTop;
            const sectionHeight = section.clientHeight;
            if (pageYOffset >= sectionTop - sectionHeight / 3) {
                current = section.getAttribute("id");
            }
        });

        navItems.forEach((item) => {
            item.classList.remove("active");
            if (item.getAttribute("href") === `#${current}`) {
                item.classList.add("active");
            }
        });
    });

    // Scroll Animations (IntersectionObserver)
    const observerOptions = {
        threshold: 0.15
    };

    const scrollObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add("visible");
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    document.querySelectorAll(".animate-on-scroll").forEach((el) => {
        scrollObserver.observe(el);
    });

    // Asynchronous Contact Form Submission with PHP
    const contactForm = document.getElementById("contact-form");
    const responseDiv = document.getElementById("form-response");

    contactForm.addEventListener("submit", function (e) {
        e.preventDefault();

        const formData = new FormData(this);
        responseDiv.style.color = "#38bdf8";
        responseDiv.innerText = "Sending message...";

        fetch("process-contact.php", {
            method: "POST",
            body: formData
        })
            .then((res) => res.json())
            .then((data) => {
                if (data.status === "success") {
                    responseDiv.style.color = "#4ade80";
                    responseDiv.innerText = data.message;
                    contactForm.reset();
                } else {
                    responseDiv.style.color = "#f87171";
                    responseDiv.innerText = data.message;
                }
            })
            .catch(() => {
                responseDiv.style.color = "#f87171";
                responseDiv.innerText = "An error occurred. Please try again later.";
            });
    });
});