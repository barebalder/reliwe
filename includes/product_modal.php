<!-- Add/Edit Product Modal -->
<div class="modal" id="productModal">
    <div class="modal-box">
        <div class="modal-header">
            <h2 id="modalTitle">Add Product</h2>
            <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <form method="POST" enctype="multipart/form-data" id="productForm">
            <div class="modal-body">
                <input type="hidden" name="product_id" id="product_id">
                
                <div class="form-group">
                    <label>Product Name *</label>
                    <input type="text" name="name" id="name" required placeholder="e.g., Neupulse 435i Pro">
                </div>
                
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" id="description" rows="3" placeholder="Brief product description..."></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Price ($) *</label>
                        <input type="number" name="price" id="price" step="0.01" min="0.01" required placeholder="199.00">
                    </div>
                    <div class="form-group">
                        <label>Stock</label>
                        <input type="number" name="stock" id="stock" min="0" value="100">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Category</label>
                        <select name="category" id="category">
                            <option value="device">Device</option>
                            <option value="accessory">Accessory</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" id="status">
                            <option value="active">Active (Visible)</option>
                            <option value="inactive">Inactive (Hidden)</option>
                        </select>
                    </div>
                </div>
                
                <!-- Main Image Upload -->
                <div class="form-group">
                    <label>Main Product Image</label>
                    <div class="upload-area" id="mainUploadArea">
                        <p><strong>Click or drag</strong> to upload main image</p>
                        <p class="hint">JPG, PNG, WEBP, AVIF, GIF (max 10MB)</p>
                        <input type="file" name="main_image" id="main_image" accept="image/*">
                    </div>
                    <div class="image-previews" id="mainPreview"></div>
                </div>
                
                <!-- Gallery Images Upload -->
                <div class="form-group">
                    <label>Gallery Images (Optional)</label>
                    <div class="upload-area" id="galleryUploadArea">
                        <p><strong>Click or drag</strong> to add gallery images</p>
                        <p class="hint">Select multiple files for product gallery</p>
                        <input type="file" name="gallery_images[]" id="gallery_images" accept="image/*" multiple>
                    </div>
                    <div class="image-previews" id="galleryPreview"></div>
                    <div class="existing-images" id="existingGallery"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn btn-primary" id="submitBtn" name="add_product">Save Product</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal" id="deleteModal">
    <div class="modal-box modal-small">
        <div class="modal-header">
            <h2>Delete Product?</h2>
            <button class="modal-close" onclick="closeDeleteModal()">&times;</button>
        </div>
        <form method="POST">
            <div class="modal-body">
                <input type="hidden" name="product_id" id="delete_id">
                <p>Are you sure you want to delete <strong id="delete_name"></strong>?</p>
                <p class="text-muted">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">Cancel</button>
                <button type="submit" name="delete_product" class="btn btn-danger">Delete</button>
            </div>
        </form>
    </div>
</div>
