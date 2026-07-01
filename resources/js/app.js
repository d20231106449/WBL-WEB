import { getSupabaseClient } from './lib/supabase';

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

    const loginForm = document.querySelector('[data-login-form]');
    if (loginForm) {
        const emailInput = loginForm.querySelector('[data-login-email]');
        const passwordInput = loginForm.querySelector('[data-login-password]');
        const submitButton = loginForm.querySelector('[data-login-submit]');
        const buttonLabel = submitButton?.querySelector('[data-button-label]');
        const errorBox = document.querySelector('[data-login-error]');
        const emailError = loginForm.querySelector('[data-login-email-error]');
        const passwordError = loginForm.querySelector('[data-login-password-error]');
        const defaultButtonLabel = buttonLabel?.textContent || 'Log masuk';

        const setLoginError = (message) => {
            if (!errorBox) return;
            errorBox.hidden = false;
            errorBox.querySelector('p').textContent = message;
        };

        const clearLoginMessages = () => {
            if (errorBox) {
                errorBox.hidden = true;
                errorBox.querySelector('p').textContent = '';
            }
            [emailError, passwordError].forEach((element) => {
                if (element) element.textContent = '';
            });
            emailInput?.classList.remove('is-invalid');
            passwordInput?.classList.remove('is-invalid');
        };

        const setLoginLoading = (isLoading) => {
            if (submitButton) submitButton.disabled = isLoading;
            if (buttonLabel) buttonLabel.textContent = isLoading ? 'Memproses...' : defaultButtonLabel;
        };

        loginForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            clearLoginMessages();

            if (!emailInput?.value.trim()) {
                emailInput?.classList.add('is-invalid');
                if (emailError) emailError.textContent = 'Alamat e-mel wajib diisi.';
                return;
            }

            if (!emailInput.checkValidity()) {
                emailInput.classList.add('is-invalid');
                if (emailError) emailError.textContent = 'Format alamat e-mel tidak sah.';
                return;
            }

            if (!passwordInput?.value) {
                passwordInput?.classList.add('is-invalid');
                if (passwordError) passwordError.textContent = 'Kata laluan wajib diisi.';
                return;
            }

            setLoginLoading(true);

            try {
                const supabase = await getSupabaseClient();
                const { data, error } = await supabase.auth.signInWithPassword({
                    email: emailInput.value.trim(),
                    password: passwordInput.value,
                });

                if (error) throw error;

                const { data: profile, error: profileError } = await supabase
                    .from('profiles')
                    .select('*')
                    .eq('id', data.user.id)
                    .single();

                if (profileError) throw profileError;
                if (!profile) throw new Error('Akaun pengesahan ditemukan, tetapi profil pengguna belum tersedia.');

                const accountType = new FormData(loginForm).get('account_type');
                const response = await fetch(loginForm.dataset.sessionUrl, {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    },
                    body: JSON.stringify({
                        account_type: accountType,
                        access_token: data.session.access_token,
                        refresh_token: data.session.refresh_token,
                        user: data.user,
                        profile,
                    }),
                });
                const payload = await response.json();

                if (!response.ok) throw new Error(payload.message || 'Log masuk gagal.');

                window.location.assign(payload.redirect);
            } catch (error) {
                const message = error?.message || 'Log masuk gagal.';
                console.error(message, error);
                setLoginError(message);
            } finally {
                setLoginLoading(false);
            }
        });
    }

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
        const errorCode = parameters.get('error_code');
        const errorBox = recovery.querySelector('[data-recovery-error]');
        const recoveryForm = recovery.querySelector('[data-recovery-form]');

        const recoveryErrorMessage = () => {
            if (errorCode === 'otp_expired' || errorCode === 'access_denied') {
                return 'Pautan pemulihan tidak sah atau telah tamat tempoh. Sila minta pautan baharu.';
            }

            return error
                ? decodeURIComponent(error.replaceAll('+', ' '))
                : 'Pautan pemulihan tidak sah atau telah tamat tempoh. Sila minta pautan baharu.';
        };

        if (token) {
            recovery.querySelector('[data-recovery-token]').value = token;
            recovery.querySelector('[data-recovery-submit]').disabled = false;
            window.history.replaceState({}, document.title, window.location.pathname);
        } else {
            errorBox.hidden = false;
            errorBox.querySelector('p').textContent = recoveryErrorMessage();
            recoveryForm?.setAttribute('aria-disabled', 'true');
            recoveryForm?.querySelectorAll('input, button').forEach((element) => {
                element.disabled = true;
            });
        }
    }

    const forgotPasswordForm = document.querySelector('[data-forgot-password-form]');
    if (forgotPasswordForm) {
        const emailInput = forgotPasswordForm.querySelector('[data-forgot-password-email]');
        const submitButton = forgotPasswordForm.querySelector('[data-forgot-password-submit]');
        const buttonLabel = submitButton?.querySelector('[data-button-label]');
        const successBox = document.querySelector('[data-forgot-password-success]');
        const errorBox = document.querySelector('[data-forgot-password-error]');
        const fieldError = forgotPasswordForm.querySelector('[data-forgot-password-field-error]');
        const defaultButtonLabel = buttonLabel?.textContent || 'Hantar pautan pemulihan';

        const setMessage = (box, message) => {
            if (!box) return;
            box.hidden = false;
            box.querySelector('p').textContent = message;
        };

        const clearMessages = () => {
            [successBox, errorBox].forEach((box) => {
                if (!box) return;
                box.hidden = true;
                box.querySelector('p').textContent = '';
            });
            if (fieldError) fieldError.textContent = '';
            emailInput?.classList.remove('is-invalid');
        };

        const setLoading = (isLoading) => {
            if (submitButton) submitButton.disabled = isLoading;
            if (buttonLabel) buttonLabel.textContent = isLoading ? 'Menghantar...' : defaultButtonLabel;
        };

        forgotPasswordForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            clearMessages();

            if (!emailInput?.value.trim()) {
                emailInput?.classList.add('is-invalid');
                if (fieldError) fieldError.textContent = 'Alamat e-mel wajib diisi.';
                return;
            }

            if (!emailInput.checkValidity()) {
                emailInput.classList.add('is-invalid');
                if (fieldError) fieldError.textContent = 'Format alamat e-mel tidak sah.';
                return;
            }

            setLoading(true);

            try {
                const supabase = await getSupabaseClient();
                const { error } = await supabase.auth.resetPasswordForEmail(emailInput.value.trim(), {
                    redirectTo: `${window.location.origin}/reset-password`,
                });

                if (error) throw error;

                setMessage(successBox, 'Pautan pemulihan kata laluan telah dihantar ke e-mel anda.');
                forgotPasswordForm.reset();
            } catch (error) {
                const message = error?.message || 'Supabase environment variables are missing.';
                console.error(message, error);
                setMessage(errorBox, message);
            } finally {
                setLoading(false);
            }
        });
    }
});
