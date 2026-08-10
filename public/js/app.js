/* ============================================================
   Ekstensi BROS & BROS Ina — Utilitas global
   ============================================================ */

const App = {
    debounce(fn, wait = 300) {
        let timer;

        return function (...args) {
            clearTimeout(timer);
            timer = setTimeout(() => fn.apply(this, args), wait);
        };
    },

    escapeHtml(value) {
        const div = document.createElement('div');
        div.textContent = value ?? '';
        return div.innerHTML;
    },

    toast(message, type = 'success') {
        let stack = document.querySelector('.toast-stack');

        if (!stack) {
            stack = document.createElement('div');
            stack.className = 'toast-stack';
            document.body.appendChild(stack);
        }

        const toast = document.createElement('div');
        toast.className = `toast-card ${type === 'error' ? 'toast-error' : ''}`;
        toast.setAttribute('role', 'status');
        toast.innerHTML = `
            <i class="bi ${type === 'error' ? 'bi-exclamation-triangle-fill' : 'bi-check-circle-fill'}"></i>
            <div class="flex-grow-1">${this.escapeHtml(message)}</div>
        `;

        stack.appendChild(toast);

        setTimeout(() => {
            toast.classList.add('toast-leave');
            setTimeout(() => toast.remove(), 260);
        }, 3800);
    },

    csrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.content : '';
    },

    headers() {
        return {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': this.csrfToken(),
            'X-Requested-With': 'XMLHttpRequest',
        };
    },

    /* Muat partial HTML ke dalam region list (search/filter/pagination) */
    async loadRegion(url, region) {
        const wrapper = document.querySelector(region);
        if (!wrapper) {
            return;
        }

        wrapper.classList.add('is-loading');

        try {
            const response = await fetch(url, { headers: this.headers() });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            wrapper.innerHTML = await response.text();
        } catch (error) {
            this.toast('Gagal memuat data. Silakan coba lagi.', 'error');
        } finally {
            wrapper.classList.remove('is-loading');
        }
    },

    /* Kirim form via fetch; tampilkan error validasi di tiap field */
    async submitForm(form, { onSuccess, successMessage, method = 'POST', json = false } = {}) {
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalHtml = submitBtn ? submitBtn.innerHTML : '';
        const headers = this.headers();

        if (json) {
            headers['Content-Type'] = 'application/json';
        }

        this.clearFormErrors(form);

        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';
        }

        try {
            const response = await fetch(form.action, {
                method: method,
                headers: headers,
                body: json ? JSON.stringify(Object.fromEntries(new FormData(form))) : new FormData(form),
            });

            const data = await response.json();

            if (!response.ok) {
                if (data.errors) {
                    this.renderFormErrors(form, data.errors);
                    if (data.message && !Object.keys(data.errors).some((k) => k === 'record')) {
                        this.toast(data.message, 'error');
                    }
                } else if (data.message) {
                    this.toast(data.message, 'error');
                }
                return null;
            }

            if (successMessage) {
                this.toast(successMessage);
            } else if (data.message) {
                this.toast(data.message);
            }

            if (onSuccess) {
                onSuccess(data);
            }

            return data;
        } catch (error) {
            this.toast('Terjadi kesalahan jaringan. Silakan coba lagi.', 'error');
            return null;
        } finally {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalHtml;
            }
        }
    },

    clearFormErrors(form) {
        form.querySelectorAll('.is-invalid').forEach((el) => el.classList.remove('is-invalid'));
        form.querySelectorAll('.invalid-feedback').forEach((el) => el.remove());
    },

    renderFormErrors(form, errors) {
        Object.entries(errors).forEach(([field, messages]) => {
            const input = form.querySelector(`[name="${field}"]`);

            if (!input) {
                return;
            }

            input.classList.add('is-invalid');

            const feedback = document.createElement('div');
            feedback.className = 'invalid-feedback';
            feedback.textContent = messages[0];
            input.parentElement.appendChild(feedback);
        });
    },

    formData(form) {
        return new FormData(form);
    },

    /* Ambil nilai state pencarian/filter dari elemen toolbar */
    buildQuery(container) {
        const params = new URLSearchParams();

        container.querySelectorAll('[data-param]').forEach((el) => {
            const value = el.value.trim();
            if (value !== '' && value !== '0') {
                params.set(el.dataset.param, value);
            }
        });

        return params;
    },

    /* ============================================================
       Searchable select — dropdown filter dengan pencarian
       Membungkus <select data-param> asli yang tetap tersembunyi,
       agar buildQuery/reset yang lama tetap berfungsi.
       ============================================================ */
    searchableSelect: {
        init(scope = document) {
            scope.querySelectorAll('[data-searchable-select]').forEach((box) => {
                if (box.dataset.ssInitialized) {
                    return;
                }
                box.dataset.ssInitialized = '1';

                const trigger = box.querySelector('[data-ss-trigger]');
                const labelEl = box.querySelector('.ss-trigger-label');
                const query = box.querySelector('[data-ss-query]');
                const empty = box.querySelector('[data-ss-empty]');
                const select = box.querySelector('select[data-param]');

                const syncLabel = () => {
                    const selected = select.selectedOptions[0];
                    labelEl.textContent = selected ? selected.textContent : '';
                };

                const close = () => {
                    box.classList.remove('is-open');
                    trigger.setAttribute('aria-expanded', 'false');
                };

                const open = () => {
                    box.classList.add('is-open');
                    trigger.setAttribute('aria-expanded', 'true');
                    query.value = '';
                    renderOptions('');
                    query.focus();
                };

                const renderOptions = (term) => {
                    const q = term.trim().toLowerCase();
                    let visible = 0;

                    box.querySelectorAll('[data-ss-value]').forEach((btn) => {
                        const show = !q || btn.dataset.ssLabel.toLowerCase().includes(q);
                        btn.classList.toggle('d-none', !show);
                        if (show) {
                            visible++;
                        }
                    });

                    empty.classList.toggle('d-none', visible > 0);
                };

                const pick = (btn) => {
                    select.value = btn.dataset.ssValue;
                    syncLabel();
                    close();
                    select.dispatchEvent(new Event('change', { bubbles: true }));
                };

                trigger.addEventListener('click', () => {
                    box.classList.contains('is-open') ? close() : open();
                });

                query.addEventListener('input', () => renderOptions(query.value));

                query.addEventListener('keydown', (event) => {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                        const first = box.querySelector('.ss-option:not(.d-none)');
                        if (first) {
                            pick(first);
                        }
                    }

                    if (event.key === 'Escape') {
                        close();
                        trigger.focus();
                    }
                });

                box.querySelector('.ss-options').addEventListener('click', (event) => {
                    const option = event.target.closest('[data-ss-value]');
                    if (option) {
                        pick(option);
                    }
                });

                document.addEventListener('click', (event) => {
                    if (!box.contains(event.target)) {
                        close();
                    }
                });

                syncLabel();
                trigger.addEventListener('keydown', (event) => {
                    if (event.key === 'ArrowDown') {
                        event.preventDefault();
                        open();
                    }
                });
            });
        },

        /* Sinkronkan label tombol dengan nilai select (dipakai saat reset) */
        syncAll(scope = document) {
            scope.querySelectorAll('[data-searchable-select]').forEach((box) => {
                const labelEl = box.querySelector('.ss-trigger-label');
                const select = box.querySelector('select[data-param]');

                if (labelEl && select) {
                    labelEl.textContent = select.selectedOptions[0]?.textContent ?? '';
                }
            });
        },
    },

    /* Toggle sidebar (semua device): desktop collapse expanded ↔ rail ikon,
       mobile membuka drawer offcanvas. Status disimpan di localStorage */
    initSidebarCollapse() {
        const shell = document.querySelector('.admin-shell');
        if (!shell) {
            return;
        }

        const btn = document.querySelector('#btn-toggle-sidebar');
        const icon = btn ? btn.querySelector('i') : null;
        const links = document.querySelectorAll('.sidebar-nav .nav-link');
        const isDesktop = () => window.matchMedia('(min-width: 992px)').matches;

        const offcanvasEl = document.querySelector('#adminOffcanvas');
        const offcanvas = offcanvasEl && window.bootstrap
            ? bootstrap.Offcanvas.getOrCreateInstance(offcanvasEl)
            : null;

        const STORAGE_KEY = 'admin-sidebar-state';

        const setBtnState = (open) => {
            if (!btn) {
                return;
            }
            btn.setAttribute('aria-expanded', String(open));
            btn.setAttribute('aria-label', open ? 'Tutup menu samping' : 'Buka menu samping');
        };

        const apply = (collapsed) => {
            shell.classList.toggle('sidebar-collapsed', collapsed);

            // Tooltip saat sidebar mengecil ditangani CSS ([data-tt] + ::after)
            links.forEach((link) => {
                link.removeAttribute('title');
            });

            if (icon) {
                icon.style.transform = collapsed ? 'rotate(180deg)' : '';
            }
            setBtnState(!collapsed);
        };

        // Status lama ('hidden' / 'admin-sidebar-collapsed') diperlakukan sebagai collapsed
        const saved = localStorage.getItem(STORAGE_KEY);
        const initial = saved === 'collapsed' || saved === 'hidden'
            || localStorage.getItem('admin-sidebar-collapsed') === '1';

        if (isDesktop()) {
            apply(initial);
        }

        window.matchMedia('(min-width: 992px)').addEventListener('change', (event) => {
            if (event.matches) {
                const stored = localStorage.getItem(STORAGE_KEY);
                apply(stored === 'collapsed' || stored === 'hidden');
            } else {
                // Kembali ke mode mobile: lepas semua keadaan desktop
                shell.classList.remove('sidebar-collapsed');
                if (icon) {
                    icon.style.transform = '';
                }
                setBtnState(false);
            }
        });

        if (btn) {
            btn.addEventListener('click', () => {
                if (isDesktop()) {
                    const next = !shell.classList.contains('sidebar-collapsed');
                    apply(next);
                    localStorage.setItem(STORAGE_KEY, next ? 'collapsed' : 'expanded');
                } else if (offcanvas) {
                    offcanvas.show();
                }
            });
        }

        if (offcanvas && offcanvasEl) {
            offcanvasEl.addEventListener('show.bs.offcanvas', () => {
                if (!isDesktop() && icon) {
                    icon.style.transform = 'rotate(180deg)';
                    setBtnState(true);
                }
            });
            offcanvasEl.addEventListener('hide.bs.offcanvas', () => {
                if (!isDesktop() && icon) {
                    icon.style.transform = '';
                }
                setBtnState(false);
            });
        }
    },
};

/* Toast untuk redirect dengan flash session */
document.addEventListener('DOMContentLoaded', () => {
    const flash = document.querySelector('[data-flash-success]');
    if (flash) {
        App.toast(flash.dataset.flashSuccess);
    }

    const flashError = document.querySelector('[data-flash-error]');
    if (flashError) {
        App.toast(flashError.dataset.flashError, 'error');
    }

    document.querySelectorAll('[data-confirm]').forEach((el) => {
        el.addEventListener('click', (e) => {
            if (!window.confirm(el.dataset.confirm)) {
                e.preventDefault();
            }
        });
    });
});
