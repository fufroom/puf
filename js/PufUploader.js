class PufUploader {
    constructor(imageInputId, previewContainerId) {
        this.imageInput = document.getElementById(imageInputId);
        this.imagePreviewContainer = document.getElementById(previewContainerId);
        this.selectedImages = Array(10).fill(null);
        this.isDeleting = false;

        this.init();
    }

    init() {
        this.imageInput.addEventListener("change", () => this.handleImageUpload());
    }

    setImages(images) {
        console.log("📥 Received images for upload:", images);
        this.selectedImages = Array(10).fill(null);
    
        images.forEach((image, index) => {
            if (index < 10) {
                this.selectedImages[index] = image;
                console.log(`🖼️ Image ${index} set to:`, image);
            }
        });
    
        this.updateImageSlots();
    }
    

    getImageList() {
        return this.selectedImages.filter(Boolean);
    }

    reset() {
        this.selectedImages = Array(10).fill(null);
        this.updateImageSlots();
    }

    handleImageUpload() {
        const files = Array.from(this.imageInput.files);
        if (!files.length) return;

        this.reserveImageSlots(files.length);
        this.updateImageSlots();
        this.uploadFiles(files);
    }

    reserveImageSlots(fileCount) {
        for (let i = 0; i < fileCount; i++) {
            let slotIndex = this.selectedImages.findIndex(img => img === null);
            if (slotIndex !== -1) {
                this.selectedImages[slotIndex] = "/images/loading.gif";
            }
        }
    }

    uploadFiles(files) {
        const formData = new FormData();
        files.forEach(file => formData.append("images[]", file));

        fetch("/upload", { method: "POST", body: formData })
            .then(response => {
                if (!response.ok) throw new Error("Upload failed");
                return response.json();
            })
            .then(data => this.handleUploadResponse(data))
            .catch(error => console.error("❌ Upload Error:", error));
    }

    handleUploadResponse(data) {
        if (data.success) {
            this.insertUploadedImages(data.files);
        }
        this.imageInput.value = "";
    }

    insertUploadedImages(files) {
        files.forEach(file => this.replaceLoadingPlaceholder(this.normalizeImageUrl(file.url)));
        this.updateImageSlots();
    }

    replaceLoadingPlaceholder(imageUrl) {
        let placeholderIndex = this.selectedImages.findIndex(img => img === "/images/loading.gif");
        if (placeholderIndex !== -1) {
            this.selectedImages[placeholderIndex] = imageUrl;
        }
    }

    updateImageSlots() {
        document.querySelectorAll("#" + this.imagePreviewContainer.id + " .image-slot").forEach((slot, index) => {
            const img = slot.querySelector("img");
            const removeButton = slot.querySelector(".image-remove-btn");
            let imageUrl = this.selectedImages[index] || null;
    
            console.log(`🖼️ Slot ${index}:`, {
                stored: this.selectedImages[index],
                finalURL: imageUrl ? (imageUrl.includes("loading.gif") ? imageUrl : "/uploads/" + imageUrl.replace("/uploads/", "")) : "/images/empty-photo.png"
            });
    
            img.src = imageUrl ? (imageUrl.includes("loading.gif") ? imageUrl : "/uploads/" + imageUrl.replace("/uploads/", "")) : "/images/empty-photo.png";
            img.alt = `Image ${index + 1}`;
            slot.style.display = "block";
            removeButton.style.display = imageUrl ? "block" : "none";
    
            removeButton.dataset.imageIndex = index;
        });
    
        document.querySelectorAll(".image-remove-btn").forEach(button => {
            button.removeEventListener("click", this.handleImageRemove);
            button.addEventListener("click", (event) => this.handleImageRemove(event));
        });
    
        this.initializeSortable();
    }
    

    handleImageRemove(event) {
        event.stopPropagation();
        event.preventDefault();
        const index = parseInt(event.target.dataset.imageIndex);
        this.removeImageAtIndex(index);
    }

    removeImageAtIndex(index) {
        if (this.isDeleting) return;
        this.isDeleting = true;

        this.selectedImages[index] = null;
        this.selectedImages = this.selectedImages.filter(img => img !== null);

        while (this.selectedImages.length < 10) {
            this.selectedImages.push(null);
        }

        this.updateImageSlots();
        setTimeout(() => { this.isDeleting = false; }, 100);
    }

    updateExistingImageOrder() {
        this.selectedImages = this.selectedImages.filter(img => img !== null);
        while (this.selectedImages.length < 10) {
            this.selectedImages.push(null);
        }
    }

    initializeSortable() {
        new Sortable(this.imagePreviewContainer, {
            animation: 150,
            ghostClass: "sortable-ghost",
            onEnd: () => this.updateExistingImageOrder(),
            filter: ".empty",
            draggable: ".image-slot img"
        });
    }

    normalizeImageUrl(imageUrl) {
        if (imageUrl.includes("localhost:4040")) {
            imageUrl = imageUrl.replace(/^https?:\/\/[^/]+/, "");
        }
        if (!imageUrl.startsWith("/uploads/")) {
            imageUrl = "/uploads/" + imageUrl.replace("/uploads/", "");
        }
        return imageUrl;
    }
}
