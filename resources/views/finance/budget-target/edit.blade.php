@extends('layouts.app')

@section('title', __('Edit Target Anggaran'))

@section('header')
    <h2 class="hidden sm:block text-xl font-semibold text-gray-800">{{ __('Edit Target Anggaran') }}</h2>
@endsection

@section('content')
<div class="py-2">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="max-w-3xl">
                <h3 class="mb-4">{{ __('Edit Target Anggaran') }}</h3>
                <form method="POST" action="{{ route('budget-target.update', $budgetTarget->id) }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block mb-1">Tanggal</label>
                        <input type="date" name="tanggal" value="{{ old('tanggal', $budgetTarget->tanggal) }}" class="w-full px-3 py-2 border rounded" required />
                        @error('tanggal')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block mb-1">Budget Bulanan</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-500">Rp</span>
                            <input type="text" id="budget_display" class="w-full px-3 py-2 pl-10 border rounded" placeholder="0" />
                            <input type="hidden" name="budget_bulanan" id="budget_bulanan" value="{{ old('budget_bulanan', $budgetTarget->budget_bulanan) }}" required />
                        </div>
                        @error('budget_bulanan')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                    </div>
                    <div class="flex gap-2">
                        <button class="inline-block px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700" type="submit">Update</button>
                        <a class="inline-block px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300" href="{{ route('budget-target.index') }}">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const budgetDisplay = document.getElementById('budget_display');
    const budgetHidden = document.getElementById('budget_bulanan');

    // Format number with thousand separator (dots)
    function formatRupiah(angka) {
        // Convert to string and remove any non-digit characters first
        const numberString = angka.toString().replace(/[^\d]/g, '');

        // Return empty if no valid number
        if (!numberString) return '';

        // Add thousand separators
        return numberString.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    // Remove all non-numeric characters (keep only digits)
    function unformatRupiah(rupiahString) {
        // Remove dots (thousand separator), commas, and any other non-digit characters
        return rupiahString.replace(/\D/g, '');
    }

    // Initialize display on page load
    function initializeDisplay() {
        const rawValue = budgetHidden.value;

        if (rawValue) {
            // Parse as float first to handle decimal values from database
            const floatValue = parseFloat(rawValue);

            // Round to nearest integer (no decimal places)
            const cleanValue = Math.round(floatValue).toString();

            // Update both fields
            budgetHidden.value = cleanValue;
            budgetDisplay.value = formatRupiah(cleanValue);

            console.log('[EDIT] Initial raw value:', rawValue);
            console.log('[EDIT] Parsed float:', floatValue);
            console.log('[EDIT] Clean value (rounded):', cleanValue);
            console.log('[EDIT] Formatted display:', formatRupiah(cleanValue));
        }
    }

    // Handle input changes
    budgetDisplay.addEventListener('input', function(e) {
        // Get the raw input value
        const inputValue = e.target.value;

        // Remove all formatting to get clean number
        const cleanValue = unformatRupiah(inputValue);

        // Update hidden field with clean number
        budgetHidden.value = cleanValue;

        // Format and display
        if (cleanValue) {
            // Save cursor position
            const cursorPos = e.target.selectionStart;
            const oldLength = inputValue.length;

            // Apply formatting
            const formattedValue = formatRupiah(cleanValue);
            e.target.value = formattedValue;

            // Restore cursor position (accounting for added dots)
            const newLength = formattedValue.length;
            const newCursorPos = cursorPos + (newLength - oldLength);
            e.target.setSelectionRange(newCursorPos, newCursorPos);
        } else {
            e.target.value = '';
        }

        console.log('[EDIT] Input:', inputValue, '→ Clean:', cleanValue, '→ Display:', e.target.value, '→ Hidden:', budgetHidden.value);
    });

    // Validate before form submission
    document.querySelector('form').addEventListener('submit', function(e) {
        const value = budgetHidden.value;

        if (!value || value === '0' || value === '') {
            e.preventDefault();
            alert('Budget bulanan harus diisi dengan nilai yang valid');
            return false;
        }

        console.log('[EDIT] Submitting value:', value);
    });

    // Initialize when page loads
    initializeDisplay();
});
</script>
@endpush
