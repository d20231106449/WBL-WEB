document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.querySelector('#sidebar');
    const sidebarToggle = document.querySelector('[data-sidebar-toggle]');
    const setSidebar = (isOpen) => {
        sidebar?.classList.toggle('open', isOpen);
        document.body.classList.toggle('sidebar-visible', isOpen);
        sidebarToggle?.setAttribute('aria-expanded', String(isOpen));
    };

    sidebarToggle?.addEventListener('click', () => setSidebar(!sidebar?.classList.contains('open')));
    document.querySelectorAll('[data-sidebar-close]').forEach((button) => {
        button.addEventListener('click', () => setSidebar(false));
    });
    sidebar?.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', () => {
            if (window.matchMedia('(max-width: 780px)').matches) setSidebar(false);
        });
    });

    document.querySelectorAll('[data-dismiss]').forEach((button) => {
        button.addEventListener('click', () => button.closest('.alert')?.remove());
    });

    document.querySelectorAll('[data-password-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            const input = document.getElementById(button.dataset.passwordToggle);
            if (input) {
                const willShow = input.type === 'password';
                input.type = willShow ? 'text' : 'password';
                button.setAttribute('aria-label', willShow ? 'Sembunyikan kata laluan' : 'Tunjukkan kata laluan');
                button.setAttribute('aria-pressed', String(willShow));
            }
        });
    });

    document.querySelectorAll('[data-modal-open]').forEach((button) => {
        button.addEventListener('click', () => document.getElementById(button.dataset.modalOpen)?.showModal());
    });
    document.querySelectorAll('[data-modal-close]').forEach((button) => {
        button.addEventListener('click', () => button.closest('dialog')?.close());
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') setSidebar(false);
    });
    window.addEventListener('resize', () => {
        if (!window.matchMedia('(max-width: 780px)').matches) setSidebar(false);
    });

    document.querySelector('[data-photo-input]')?.addEventListener('change', (event) => {
        const preview = document.querySelector('[data-photo-preview]');
        const file = event.target.files?.[0];
        if (preview && file) {
            preview.src = URL.createObjectURL(file);
            preview.style.display = 'block';
        }
    });

    document.querySelectorAll('[data-gallery-slideshow]').forEach((gallery) => {
        const slides = [...gallery.querySelectorAll('[data-gallery-slide]')];
        const dots = [...gallery.querySelectorAll('[data-gallery-dot]')];
        const previous = gallery.querySelector('[data-gallery-prev]');
        const next = gallery.querySelector('[data-gallery-next]');
        if (slides.length < 2) return;

        let index = 0;
        let timer;
        const show = (target) => {
            index = (target + slides.length) % slides.length;
            slides.forEach((slide, slideIndex) => slide.classList.toggle('active', slideIndex === index));
            dots.forEach((dot, dotIndex) => dot.classList.toggle('active', dotIndex === index));
        };
        const start = () => {
            window.clearInterval(timer);
            timer = window.setInterval(() => show(index + 1), 4500);
        };

        previous?.addEventListener('click', () => {
            show(index - 1);
            start();
        });
        next?.addEventListener('click', () => {
            show(index + 1);
            start();
        });
        dots.forEach((dot) => {
            dot.addEventListener('click', () => {
                show(Number(dot.dataset.galleryDot));
                start();
            });
        });
        gallery.addEventListener('mouseenter', () => window.clearInterval(timer));
        gallery.addEventListener('mouseleave', start);
        gallery.addEventListener('focusin', () => window.clearInterval(timer));
        gallery.addEventListener('focusout', start);
        start();
    });

    const bookingForm = document.querySelector('[data-booking-form]');
    if (bookingForm) {
        const dateInput = bookingForm.querySelector('[data-booking-date]');
        const timeInputs = [...bookingForm.querySelectorAll('[data-booking-time]')];
        const bookedSlotsUrl = bookingForm.dataset.bookedSlotsUrl;
        let bookedSlotKeys = new Set();
        const serverStartedAt = new Date(bookingForm.dataset.serverNow);
        const browserStartedAt = Date.now();
        const malaysiaClock = new Intl.DateTimeFormat('en-CA', {
            timeZone: 'Asia/Kuala_Lumpur',
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit',
            hourCycle: 'h23',
        });

        const normalizeTime = (value) => (value || '').slice(0, 5);
        const slotKey = (start) => {
            const [hour] = start.split(':').map(Number);
            return `${start}-${String(hour + 1).padStart(2, '0')}:00`;
        };

        timeInputs.forEach((input) => {
            const label = input.closest('label')?.querySelector('[data-slot-label]');
            if (label && !label.dataset.originalLabel) label.dataset.originalLabel = label.textContent;
        });

        const fetchBookedSlots = async () => {
            if (!bookedSlotsUrl || !dateInput.value) return;
            try {
                const url = new URL(bookedSlotsUrl, window.location.origin);
                url.searchParams.set('booking_date', dateInput.value);
                const response = await fetch(url, { headers: { Accept: 'application/json' } });
                if (!response.ok) throw new Error('slot_fetch_failed');
                const payload = await response.json();
                bookedSlotKeys = new Set((payload.slots || []).map((slot) => {
                    return `${normalizeTime(slot.start_time)}-${normalizeTime(slot.end_time)}`;
                }));
            } catch (_) {
                bookedSlotKeys = new Set();
            }
        };

        const refreshPastSlots = () => {
            const current = new Date(serverStartedAt.getTime() + (Date.now() - browserStartedAt));
            const parts = Object.fromEntries(
                malaysiaClock.formatToParts(current)
                    .filter((part) => part.type !== 'literal')
                    .map((part) => [part.type, part.value]),
            );
            const today = `${parts.year}-${parts.month}-${parts.day}`;
            const currentMinutes = Number(parts.hour) * 60 + Number(parts.minute);

            timeInputs.forEach((input) => {
                const label = input.closest('label');
                const labelText = label?.querySelector('[data-slot-label]');
                const [hour, minute] = input.value.split(':').map(Number);
                const elapsed = dateInput.value === today && (hour * 60 + minute) <= currentMinutes;
                const booked = bookedSlotKeys.has(slotKey(input.value));
                input.disabled = elapsed || booked;
                label?.classList.toggle('time-slot-past', elapsed);
                label?.classList.toggle('time-slot-booked', booked);
                if (labelText) {
                    labelText.textContent = booked
                        ? 'Ditempah'
                        : labelText.dataset.originalLabel;
                }
                if ((elapsed || booked) && input.checked) input.checked = false;
            });
        };

        const refreshAvailability = async () => {
            await fetchBookedSlots();
            refreshPastSlots();
        };

        dateInput.addEventListener('change', refreshAvailability);
        refreshAvailability();
        window.setInterval(refreshAvailability, 60000);
    }

    const recovery = document.querySelector('[data-password-recovery]');
    if (recovery) {
        const parameters = new URLSearchParams(window.location.hash.slice(1));
        const token = parameters.get('access_token');
        const error = parameters.get('error_description');
        const errorBox = recovery.querySelector('[data-recovery-error]');

        if (token) {
            recovery.querySelector('[data-recovery-token]').value = token;
            recovery.querySelector('[data-recovery-submit]').disabled = false;
            window.history.replaceState({}, document.title, window.location.pathname);
        } else {
            errorBox.hidden = false;
            errorBox.querySelector('p').textContent = error
                ? decodeURIComponent(error.replaceAll('+', ' '))
                : 'Pautan pemulihan tidak sah atau telah tamat tempoh. Sila minta pautan baharu.';
        }
    }
});
