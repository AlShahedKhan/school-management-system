function initPublicHomePage() {
    const homePage = document.querySelector('[data-home-page]');
    initHomeReveals(document);
    initDemoExperience(document);

    if (homePage) {
        initAttendanceBar(homePage);
        initShowcaseModal(homePage);
    }
}

function initHomeReveals(scope) {
    const revealItems = scope.querySelectorAll('.home-reveal');
    if (!revealItems.length) {
        return;
    }

    document.documentElement.classList.add('home-reveal-ready');

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) {
                return;
            }

            entry.target.classList.add('is-visible');
            observer.unobserve(entry.target);
        });
    }, {
        threshold: 0.16,
        rootMargin: '0px 0px -8% 0px',
    });

    revealItems.forEach((item, index) => {
        item.style.transitionDelay = `${Math.min(index * 35, 180)}ms`;
        observer.observe(item);
    });
}

function initAttendanceBar(homePage) {
    const attendanceBar = homePage.querySelector('.home-attendance-bar');
    if (attendanceBar) {
        const targetWidth = attendanceBar.style.width;
        attendanceBar.style.width = '0%';

        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                attendanceBar.style.width = targetWidth;
            });
        });
    }
}

function initDemoExperience(scope) {
    const modal = scope.querySelector('[data-demo-modal]');
    const openButtons = scope.querySelectorAll('[data-demo-open]');
    const closeButtons = scope.querySelectorAll('[data-demo-close]');
    const forms = scope.querySelectorAll('[data-demo-form]');

    if (!forms.length) {
        return;
    }

    let lastFocusedElement = null;
    let successCloseTimeout = null;

    const clearSuccessTimeout = () => {
        if (successCloseTimeout) {
            window.clearTimeout(successCloseTimeout);
            successCloseTimeout = null;
        }
    };

    const openModal = () => {
        if (!modal) {
            return;
        }

        clearSuccessTimeout();
        lastFocusedElement = document.activeElement;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('demo-modal-open');

        requestAnimationFrame(() => {
            modal.classList.add('is-open');
            const firstInput = modal.querySelector('input, textarea, button');

            if (firstInput) {
                firstInput.focus({ preventScroll: true });
            }
        });
    };

    const closeModal = () => {
        if (!modal) {
            return;
        }

        clearSuccessTimeout();
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('demo-modal-open');

        window.setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');

            if (lastFocusedElement && typeof lastFocusedElement.focus === 'function') {
                lastFocusedElement.focus({ preventScroll: true });
            }
        }, 220);
    };

    openButtons.forEach((button) => {
        button.addEventListener('click', openModal);
    });

    closeButtons.forEach((button) => {
        button.addEventListener('click', closeModal);
    });

    modal?.addEventListener('click', (event) => {
        if (event.target === modal || event.target.matches('[data-demo-close]')) {
            closeModal();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && modal?.classList.contains('is-open')) {
            closeModal();
        }
    });

    forms.forEach((form) => {
        const submitButton = form.querySelector('[data-demo-submit]');
        const status = form.querySelector('[data-demo-status]');

        if (!submitButton || !status) {
            return;
        }

        const messages = {
            submitting: form.dataset.submittingText || 'Submitting...',
            validationError: form.dataset.validationErrorText || 'Please check the highlighted fields.',
            genericError: form.dataset.genericErrorText || 'Something went wrong. Please try again.',
            networkError: form.dataset.networkErrorText || 'Network error. Please check your connection and try again.',
            success: form.dataset.successText || 'Demo request submitted successfully.',
        };

        const showStatus = (message, type = '') => {
            status.textContent = message;
            status.classList.remove('is-error', 'is-success');

            if (type) {
                status.classList.add(`is-${type}`);
            }
        };

        const clearErrors = () => {
            form.querySelectorAll('[data-demo-error]').forEach((errorNode) => {
                errorNode.textContent = '';
            });
            showStatus('');
        };

        const setFieldErrors = (errors) => {
            Object.entries(errors || {}).forEach(([field, fieldMessages]) => {
                const errorNode = form.querySelector(`[data-demo-error="${field}"]`);

                if (errorNode) {
                    errorNode.textContent = Array.isArray(fieldMessages) ? fieldMessages[0] : fieldMessages;
                }
            });
        };

        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            clearErrors();

            const originalSubmitText = submitButton.textContent;
            submitButton.disabled = true;
            submitButton.textContent = messages.submitting;

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const formData = new FormData(form);
            const payload = Object.fromEntries(formData.entries());

            try {
                const response = await fetch('/api/demo-requests', {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
                    },
                    body: JSON.stringify(payload),
                });

                const data = await response.json().catch(() => ({}));

                if (response.status === 422) {
                    setFieldErrors(data.errors);
                    showStatus(messages.validationError, 'error');
                    return;
                }

                if (!response.ok) {
                    showStatus(data.message || messages.genericError, 'error');
                    return;
                }

                form.reset();
                showStatus(messages.success, 'success');

                if (form.dataset.demoFormMode === 'modal') {
                    successCloseTimeout = window.setTimeout(() => {
                        closeModal();
                    }, 1500);
                }
            } catch (error) {
                showStatus(messages.networkError, 'error');
            } finally {
                submitButton.disabled = false;
                submitButton.textContent = originalSubmitText;
            }
        });
    });
}

function initShowcaseModal(homePage) {
    const modal = homePage.querySelector('[data-showcase-modal]');
    const openButtons = homePage.querySelectorAll('[data-showcase-open]');
    const closeButtons = homePage.querySelectorAll('[data-showcase-close]');
    const titleNode = homePage.querySelector('[data-showcase-modal-title]');
    const imageNode = homePage.querySelector('[data-showcase-modal-image]');

    if (!modal || !openButtons.length || !titleNode || !imageNode) {
        return;
    }

    let lastFocusedElement = null;

    const openModal = (button) => {
        lastFocusedElement = button;
        titleNode.textContent = button.dataset.showcaseTitle || '';
        imageNode.src = button.dataset.showcaseImage || '';
        imageNode.alt = button.dataset.showcaseTitle || '';

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('demo-modal-open');

        requestAnimationFrame(() => {
            modal.classList.add('is-open');
            const closeButton = modal.querySelector('[data-showcase-close]');

            if (closeButton) {
                closeButton.focus({ preventScroll: true });
            }
        });
    };

    const closeModal = () => {
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('demo-modal-open');

        window.setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            imageNode.src = '';
            imageNode.alt = '';
            titleNode.textContent = '';

            if (lastFocusedElement && typeof lastFocusedElement.focus === 'function') {
                lastFocusedElement.focus({ preventScroll: true });
            }
        }, 220);
    };

    openButtons.forEach((button) => {
        button.addEventListener('click', () => openModal(button));
    });

    closeButtons.forEach((button) => {
        button.addEventListener('click', closeModal);
    });

    modal.addEventListener('click', (event) => {
        if (event.target === modal || event.target.matches('[data-showcase-close]')) {
            closeModal();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && modal.classList.contains('is-open')) {
            closeModal();
        }
    });
}

document.addEventListener('DOMContentLoaded', initPublicHomePage);
