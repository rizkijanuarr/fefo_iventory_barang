<div x-data="{
    init() {
        JsBarcode(this.$refs.barcode, '{{ $barcode }}', {
            format: 'CODE128',
            width: 1,
            height: 10,
            displayValue: true
        });
    }
}">
    <svg x-ref="barcode"></svg>
</div>

<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
