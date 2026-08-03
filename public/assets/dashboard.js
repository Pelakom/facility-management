// Toggle dropdown panel visibility
function toggleDropdown() {
    const menu = document.getElementById('dropdown-menu');
    if (!menu) return;

    if (menu.classList.contains('hidden')) {
        menu.classList.remove('hidden');
    } else {
        menu.classList.add('hidden');
    }
}

function forceDropdownOpen(condition) {
    const menu = document.getElementById('dropdown-menu');
    if (!menu) return;

    if (condition) {
        menu.classList.remove('hidden');
    } else {
        menu.classList.add('hidden');
    }
}

function createRequest() {
    const boxTitle = document.getElementById('box-title');
    const boxDescription = document.getElementById('box-description');
    const listIdForm = document.getElementById('selected-list-id');
    listIdForm.value = '';


    boxTitle.textContent = 'Add A New Request';
    boxDescription.textContent = 'Reserve a request for loan.';

    document.getElementById('request-modal').showModal()

}



function editList(listId, itemId, name, duration, purpose, amount) {
    const boxTitle = document.getElementById('box-title');
    const boxDescription = document.getElementById('box-description');
    const listIdForm = document.getElementById('selected-list-id');
    const startDateInput = document.getElementById('startdate');
    const endDateInput = document.getElementById('enddate');
    const amountInput = document.getElementById('amount');
    const purposeInput = document.getElementById('purpose');
    listIdForm.value = listId;


    boxTitle.textContent = 'Editing The Request';
    boxDescription.textContent = 'Modify the request for the loan.';
    selectOption(itemId, name);
    amountInput.value = amount
    startDateInput.value = duration.split(' - ')[0];
    endDateInput.value = duration.split(' - ')[1];
    purposeInput.value = purpose;
    

    document.getElementById('request-modal').showModal()

}



// Filter option items based on input text
function filterOptions() {
    const query = document.getElementById('search-input').value.toLowerCase();
    const options = document.querySelectorAll('.option-item');
    let visibleCount = 0;

    options.forEach(option => {
        const text = option.textContent.toLowerCase();
        if (text.includes(query)) {
            option.classList.remove('hidden');
            visibleCount++;
        } else {
            option.classList.add('hidden');
        }
    });

    const noResults = document.getElementById('no-results');
    if (noResults) {
        if (visibleCount === 0) {
            noResults.classList.remove('hidden');
        } else {
            noResults.classList.add('hidden');
        }
    }
}

// Handle item selection
function selectOption(id, label) {
    const hiddenInput = document.getElementById('selected-item-id');
    const labelSpan = document.getElementById('selected-label');

    if (hiddenInput) {
        hiddenInput.value = id;
        // DISPATCH CHANGE EVENT MANUALLY so the listener picks it up!
        hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
    }

    if (labelSpan) {
        labelSpan.textContent = label;
        labelSpan.classList.remove('text-gray-500');
        labelSpan.classList.add('text-gray-900', 'font-medium');
    }

    toggleDropdown(); // Close dropdown after selection
}

// Close dropdown automatically if user clicks outside of it
document.addEventListener('click', function (e) {
    const container = document.getElementById('dropdown-menu');
    const searchInput = document.getElementById('search-input');
    if (container && !container.contains(e.target) && e.target !== searchInput) {
        container.classList.add('hidden');
    }
});




document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('request-modal');
    const mainDialogBox = document.getElementById('main-dialog-box');

    modal.addEventListener('click', function (e) {
        // If the click target is NOT inside #main-dialog-box, close the modal
        if (!mainDialogBox.contains(e.target)) {
            modal.close();
        }
    });

    const itemElementForm = document.getElementById('selected-item-id');
    const startDateInput = document.getElementById('startdate');
    const endDateInput = document.getElementById('enddate');
    const amountInput = document.getElementById('amount');
    const purposeInput = document.getElementById('purpose');
    const submitBtn = document.getElementById('submit-btn'); // 1. Select the button

    // 2. Define the validation logic
    function validateForm() {
        // Ensure elements exist and have valid values
        const isItemValid = itemElementForm && itemElementForm.value.trim() !== '';
        const isStartDateValid = startDateInput && startDateInput.value !== '';
        const isEndDateValid = endDateInput && endDateInput.value !== '';
        const isAmountValid = amountInput && amountInput.value !== '' && parseInt(amountInput.value) >= 1;
        const isPurposeValid = purposeInput && purposeInput.value.trim() !== '';

        if (isItemValid && isStartDateValid && isEndDateValid && isAmountValid && isPurposeValid) {
            // 3. Enable button and update visual styles
            submitBtn.removeAttribute('disabled');
            submitBtn.classList.remove('bg-gray-200', 'cursor-not-allowed', 'text-gray-400');
            submitBtn.classList.add('bg-blue-600', 'text-white', 'cursor-pointer'); // Example active classes
        } else {
            // Re-disable button if any field becomes empty
            submitBtn.setAttribute('disabled', 'true');
            submitBtn.classList.add('bg-gray-200', 'cursor-not-allowed', 'text-gray-400');
            submitBtn.classList.remove('bg-blue-600', 'text-white', 'cursor-pointer');
        }
    }

    if (itemElementForm) {
        itemElementForm.addEventListener('change', function () {
            //console.log('Selected item ID changed to:', this.value);
            validateForm(); // 4. Run check
        });
    }

    ['input', 'change'].forEach(eventType => {
        if (startDateInput) {
            startDateInput.addEventListener(eventType, function () {
                //if (this.value) console.log('Start date changed to:', this.value);
                validateForm(); // 4. Run check
            });
        }

        if (endDateInput) {
            endDateInput.addEventListener(eventType, function () {
                //if (this.value) console.log('End date changed to:', this.value);
                validateForm(); // 4. Run check
            });
        }
    });

    if (amountInput) {
        amountInput.addEventListener('input', function () {
            if (this.value !== '' && this.value < 1) {
                this.value = 1;
            } else if (this.value > 100) {
                this.value = 100;
            }
            //console.log('Amount changed to:', this.value);
            validateForm(); // 4. Run check
        });
    }

    if (purposeInput) {
        purposeInput.addEventListener('input', function () {
            //console.log('Purpose changed to:', this.value);
            validateForm(); // 4. Run check
        });
    }

    // Optional: Run on initial load in case fields are pre-filled by the browser
    validateForm();
});
