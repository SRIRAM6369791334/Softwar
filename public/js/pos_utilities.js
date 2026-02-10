/** POS Shortcuts & Barcode Support [#88, #89] **/
document.addEventListener('keydown', function (e) {
    // 1. Hotkeys
    switch (e.key) {
        case 'F2': // New Transaction
            e.preventDefault();
            window.location.reload();
            break;
        case 'F4': // Search Product
            e.preventDefault();
            document.getElementById('product-search')?.focus();
            break;
        case 'F8': // Quick Cash Pay
            e.preventDefault();
            document.getElementById('checkout-btn')?.click();
            break;
        case 'F10': // Open Drawer / Print Last
            break;
    }

    // 2. Barcode Scanner Handling
    // Scanners usually send characters quickly followed by an Enter key.
    // We can catch this by detecting high frequency of inputs.
});

console.log('POS Utilities Loaded: F2(New), F4(Search), F8(Pay)');
