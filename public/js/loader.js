/**
 * Supermarket OS - Global Loader
 * Lightweight visual feedback for page transitions and async operations.
 */

const GlobalLoader = {
    element: null,

    init() {
        if (!this.element) {
            this.element = document.createElement('div');
            this.element.id = 'global-loader';
            this.element.innerHTML = '<div class="loader-bar"></div>';
            document.body.appendChild(this.element);
        }
    },

    start() {
        this.init();
        document.body.classList.add('loading-active');
        this.element.style.width = '0%';
        this.element.style.opacity = '1';

        // Simulate progress
        setTimeout(() => this.element.style.width = '30%', 10);
        setTimeout(() => this.element.style.width = '70%', 500);
    },

    complete() {
        if (!this.element) return;
        this.element.style.width = '100%';
        setTimeout(() => {
            this.element.style.opacity = '0';
            document.body.classList.remove('loading-active');
            setTimeout(() => {
                this.element.style.width = '0%';
            }, 300);
        }, 300);
    }
};

// Hook into page navigation
window.addEventListener('beforeunload', () => {
    GlobalLoader.start();
});

// Hook into Fetch API
const originalFetch = window.fetch;
window.fetch = async function (...args) {
    GlobalLoader.start();
    try {
        const response = await originalFetch(...args);
        GlobalLoader.complete();
        return response;
    } catch (error) {
        GlobalLoader.complete();
        throw error;
    }
};

// Hook into XHR (for jQuery/older libs if used)
const originalOpen = XMLHttpRequest.prototype.open;
XMLHttpRequest.prototype.open = function () {
    this.addEventListener('loadstart', () => GlobalLoader.start());
    this.addEventListener('loadend', () => GlobalLoader.complete());
    originalOpen.apply(this, arguments);
};
