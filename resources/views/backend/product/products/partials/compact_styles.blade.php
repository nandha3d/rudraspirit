{{-- Compact styling for the tabbed product create/edit form. Scoped to the
     left-column section tabs so it never leaks into the rest of admin. --}}
<style>
    .product-section-tabs {
        border-bottom: 2px solid #eef0f3;
        flex-wrap: wrap;
    }
    .product-section-tabs .nav-item { margin-bottom: -2px; }
    .product-section-tabs .nav-link {
        border: none;
        border-bottom: 2px solid transparent;
        color: #6c757d;
        font-size: 14px;
        font-weight: 600;
        padding: 8px 16px;
        border-radius: 0;
    }
    .product-section-tabs .nav-link:hover { color: #3d4d67; }
    .product-section-tabs .nav-link.active {
        color: #2f6fed;
        background: transparent;
        border-bottom-color: #2f6fed;
    }

    /* tighten every card inside the tabbed area */
    .product-section-content .border.border-gray-300 { margin-top: 0 !important; }
    .product-section-content .py-3.py-lg-4 { padding-top: 1rem !important; padding-bottom: 1rem !important; }
    .product-section-content .mb-3.pb-1 { margin-bottom: .75rem !important; }
    .product-section-content .form-group { margin-bottom: .6rem !important; }
    .product-section-content .col-from-label { margin-bottom: .2rem; }

    /* compact media uploaders: smaller drop boxes, inline-friendly */
    #psec-media .file-upload-input.w-120px,
    #psec-media .w-120px.h-120px {
        width: 84px !important;
        height: 84px !important;
    }
    #psec-media .file-upload-input img.w-40px { width: 26px !important; height: 26px !important; }
    #psec-media .col-from-label .d-block { line-height: 1.2; }
    #psec-media .form-group { margin-bottom: .5rem !important; }

    @media (min-width: 768px) {
        .product-section-content .aiz-text-editor,
        .product-section-content textarea#meta_description { min-height: 160px; }
    }
</style>
