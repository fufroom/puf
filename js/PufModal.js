class PufModal {
    constructor(modalId) {
        this.modalElement = document.getElementById(modalId);
        if (!this.modalElement) {
            console.error(`PufModal: Element with ID '${modalId}' not found.`);
            return;
        }

        try {
            this.modal = new bootstrap.Modal(this.modalElement);
        } catch (error) {
            console.error("Bootstrap Modal failed to initialize.", error);
            return;
        }

        this.modalLabel = this.modalElement.querySelector(".modal-title");
        this.init();
    }

    init() {
        this.modalElement.addEventListener("hide.bs.modal", (event) => {
            if (this.preventCloseIfDeletingCallback) {
                this.preventCloseIfDeletingCallback(event);
            }
        });
    }

    open() {
        if (this.modal) this.modal.show();
    }

    close() {
        if (this.modal) this.modal.hide();
    }

    setTitle(title) {
        if (this.modalLabel) this.modalLabel.textContent = title;
    }

    setPreventCloseCallback(callback) {
        this.preventCloseIfDeletingCallback = callback;
    }
}
