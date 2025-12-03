/**
 * ADMIN PRODUCTS - JAVASCRIPT
 * Handles modal interactions and image upload previews
 */

// ===========================
// MODAL MANAGEMENT
// ===========================

function openAddModal() {
    document.getElementById('modalTitle').textContent = 'Add Product';
    document.getElementById('productForm').reset();
    document.getElementById('product_id').value = '';
    document.getElementById('submitBtn').name = 'add_product';
    document.getElementById('submitBtn').textContent = 'Add Product';
    clearPreviews();
    document.getElementById('productModal').classList.add('active');
}

function editProduct(p) {
    document.getElementById('modalTitle').textContent = 'Edit Product';
    document.getElementById('product_id').value = p.id;
    document.getElementById('name').value = p.name || '';
    document.getElementById('description').value = p.description || '';
    document.getElementById('price').value = p.price || '';
    document.getElementById('stock').value = p.stock || 0;
    document.getElementById('category').value = p.category || 'device';
    document.getElementById('status').value = p.status || 'active';
    document.getElementById('submitBtn').name = 'edit_product';
    document.getElementById('submitBtn').textContent = 'Update Product';
    
    // Show current main image
    document.getElementById('mainPreview').innerHTML = p.image ? 
        `<div class="image-preview main"><img src="${p.image}" alt="Main" onerror="this.src='Images/produkt1.avif'"><span class="label">Current</span></div>` : '';
    
    // Show existing gallery
    showExistingGallery(p.images);
    
    document.getElementById('productModal').classList.add('active');
}

function closeModal() {
    document.getElementById('productModal').classList.remove('active');
}

function deleteProduct(id, name) {
    document.getElementById('delete_id').value = id;
    document.getElementById('delete_name').textContent = name;
    document.getElementById('deleteModal').classList.add('active');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.remove('active');
}

function clearPreviews() {
    document.getElementById('mainPreview').innerHTML = '';
    document.getElementById('galleryPreview').innerHTML = '';
    document.getElementById('existingGallery').innerHTML = '';
}

function showExistingGallery(imagesJson) {
    let html = '';
    if (imagesJson) {
        try {
            const gallery = JSON.parse(imagesJson);
            if (Array.isArray(gallery) && gallery.length > 0) {
                html = '<h4>Existing Gallery (check to remove):</h4>';
                gallery.forEach((img, i) => {
                    html += `<div class="existing-image">
                        <img src="${img}" alt="Gallery ${i+1}" onerror="this.style.display='none'">
                        <label><input type="checkbox" name="remove_gallery[]" value="${img}"> Remove</label>
                    </div>`;
                });
            }
        } catch(e) { console.log('Error parsing gallery:', e); }
    }
    document.getElementById('existingGallery').innerHTML = html;
    document.getElementById('galleryPreview').innerHTML = '';
}

// ===========================
// IMAGE UPLOAD & PREVIEW
// ===========================

function setupImageUpload(areaId, inputId, previewFn) {
    const area = document.getElementById(areaId);
    const input = document.getElementById(inputId);
    
    area.addEventListener('click', () => input.click());
    area.addEventListener('dragover', (e) => { e.preventDefault(); area.classList.add('dragover'); });
    area.addEventListener('dragleave', () => area.classList.remove('dragover'));
    area.addEventListener('drop', (e) => {
        e.preventDefault();
        area.classList.remove('dragover');
        if (e.dataTransfer.files.length) {
            input.files = e.dataTransfer.files;
            previewFn();
        }
    });
    input.addEventListener('change', previewFn);
}

function previewMainImage() {
    const file = document.getElementById('main_image').files[0];
    if (!file) return;
    
    const reader = new FileReader();
    reader.onload = (e) => {
        document.getElementById('mainPreview').innerHTML = `
            <div class="image-preview main">
                <img src="${e.target.result}" alt="New main">
                <button type="button" class="remove-btn" onclick="clearMainImage()">&times;</button>
                <span class="label">New</span>
            </div>`;
    };
    reader.readAsDataURL(file);
}

function clearMainImage() {
    document.getElementById('main_image').value = '';
    document.getElementById('mainPreview').innerHTML = '';
}

function previewGalleryImages() {
    const preview = document.getElementById('galleryPreview');
    preview.innerHTML = '';
    
    Array.from(document.getElementById('gallery_images').files).forEach((file, i) => {
        const reader = new FileReader();
        reader.onload = (e) => {
            const div = document.createElement('div');
            div.className = 'image-preview';
            div.innerHTML = `<img src="${e.target.result}" alt="Gallery ${i+1}"><span class="label">New #${i+1}</span>`;
            preview.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
}

// ===========================
// INITIALIZATION
// ===========================

document.addEventListener('DOMContentLoaded', () => {
    // Setup image uploads
    setupImageUpload('mainUploadArea', 'main_image', previewMainImage);
    setupImageUpload('galleryUploadArea', 'gallery_images', previewGalleryImages);
    
    // Close modals on Escape key
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') { closeModal(); closeDeleteModal(); }
    });
    
    // Close modals on backdrop click
    document.querySelectorAll('.modal').forEach(m => {
        m.addEventListener('click', e => {
            if (e.target === m) { closeModal(); closeDeleteModal(); }
        });
    });
});
