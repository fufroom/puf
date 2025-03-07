class PufModalManager {
    constructor(modalId) {
        this.modal = new PufModal(modalId);
    }
    open() {
        this.modal.open();
    }
    close() {
        this.modal.close();
    }
}