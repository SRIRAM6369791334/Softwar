/**
 * Supermarket OS - Drag & Drop Widgets
 * Allows reordering of dashboard widgets with local persistence.
 */

const WidgetManager = {
    storageKey: 'dashboard_widget_order',
    container: null,

    init(containerId) {
        this.container = document.getElementById(containerId);
        if (!this.container) return;

        this.loadOrder();
        this.attachListeners();
    },

    attachListeners() {
        const widgets = this.container.querySelectorAll('.draggable-widget');

        widgets.forEach(widget => {
            widget.addEventListener('dragstart', this.handleDragStart.bind(this));
            widget.addEventListener('dragenter', this.handleDragEnter.bind(this));
            widget.addEventListener('dragover', this.handleDragOver.bind(this));
            widget.addEventListener('dragleave', this.handleDragLeave.bind(this));
            widget.addEventListener('drop', this.handleDrop.bind(this));
            widget.addEventListener('dragend', this.handleDragEnd.bind(this));
        });
    },

    handleDragStart(e) {
        e.dataTransfer.setData('text/plain', e.target.id);
        e.dataTransfer.effectAllowed = 'move';
        e.target.classList.add('dragging');
        setTimeout(() => e.target.style.opacity = '0.5', 0);
    },

    handleDragOver(e) {
        e.preventDefault(); // Necessary to allow dropping
        e.dataTransfer.dropEffect = 'move';
        return false;
    },

    handleDragEnter(e) {
        e.preventDefault();
        const targetWidget = e.target.closest('.draggable-widget');
        if (targetWidget && targetWidget !== document.querySelector('.dragging')) {
            targetWidget.classList.add('drag-over');
        }
    },

    handleDragLeave(e) {
        const targetWidget = e.target.closest('.draggable-widget');
        if (targetWidget) {
            targetWidget.classList.remove('drag-over');
        }
    },

    handleDrop(e) {
        e.stopPropagation();
        e.preventDefault();

        const dragSourceId = e.dataTransfer.getData('text/plain');
        const dragSource = document.getElementById(dragSourceId);
        const dropTarget = e.target.closest('.draggable-widget');

        if (dragSource && dropTarget && dragSource !== dropTarget) {
            // Reorder DOM
            const widgets = [...this.container.querySelectorAll('.draggable-widget')];
            const sourceIndex = widgets.indexOf(dragSource);
            const targetIndex = widgets.indexOf(dropTarget);

            if (sourceIndex < targetIndex) {
                dropTarget.after(dragSource);
            } else {
                dropTarget.before(dragSource);
            }

            this.saveOrder();
        }

        return false;
    },

    handleDragEnd(e) {
        e.target.classList.remove('dragging');
        e.target.style.opacity = '1';

        this.container.querySelectorAll('.draggable-widget').forEach(w => {
            w.classList.remove('drag-over');
        });
    },

    saveOrder() {
        const order = [];
        this.container.querySelectorAll('.draggable-widget').forEach(w => {
            order.push(w.id);
        });
        localStorage.setItem(this.storageKey, JSON.stringify(order));
    },

    loadOrder() {
        const savedOrder = JSON.parse(localStorage.getItem(this.storageKey));
        if (!savedOrder) return;

        savedOrder.forEach(id => {
            const widget = document.getElementById(id);
            if (widget) {
                this.container.appendChild(widget);
            }
        });
    }
};

document.addEventListener('DOMContentLoaded', () => {
    WidgetManager.init('dashboard-grid');
});
