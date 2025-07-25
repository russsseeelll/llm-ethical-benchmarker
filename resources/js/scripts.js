// this file has our custom js for handling scenarios, dropdowns, and choices

function saveScenario() {
    alert('Scenario saved!');
}

function toggleDropdown(event, icon) {
    event.stopPropagation();
    const dropdownContent = icon.nextElementSibling;
    dropdownContent.classList.toggle('hidden');
}

document.addEventListener('click', function(event) {
    // close all dropdowns if us click outside
    if (!event.target.closest('.dropdown')) {
        const dropdowns = document.querySelectorAll('.dropdown-content');
        dropdowns.forEach(dropdown => {
            dropdown.classList.add('hidden');
        });
    }
});

function toggleMultipleChoice(isChecked) {
    const multipleChoiceOptions = document.getElementById('multiple-choice-options');
    if (isChecked) {
        multipleChoiceOptions.classList.remove('hidden');
    } else {
        multipleChoiceOptions.classList.add('hidden');
    }
}

function addChoiceField() {
    // add a new choice field for multiple choice questions
    const choiceFieldContainer = document.createElement('div');
    choiceFieldContainer.classList.add('choice-field', 'mb-2');
    choiceFieldContainer.innerHTML = `
        <input type="text" placeholder="New Choice" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500">
        <button class="remove-choice-button" onclick="removeChoiceField(this)">
            <i class="fas fa-minus-circle text-red-500"></i>
        </button>
    `;
    const multipleChoiceOptions = document.getElementById('multiple-choice-options');
    multipleChoiceOptions.insertBefore(
        choiceFieldContainer,
        multipleChoiceOptions.lastElementChild
    );
}

function removeChoiceField(button) {
    button.parentElement.remove();
}

function generateResponse(modelId, button) {
    // fake showing a response and bias section for demo
    document.getElementById(`${modelId}-response`).classList.remove('hidden');
    button.classList.add('bg-gray-400', 'cursor-not-allowed');
    button.classList.remove('bg-green-500', 'hover:bg-green-600');
    button.disabled = true;
    document.getElementById(`${modelId}-buttons`).classList.remove('hidden');
    document.getElementById(`${modelId}-bias-section`).classList.remove('hidden');
    setTimeout(() => {
        document.getElementById(`loading-bar${modelId.slice(-1)}`).classList.add('hidden');
        document.getElementById(`${modelId}-bias-text`).classList.remove('hidden');
    }, 3000);
}

document.getElementById('currentYear').textContent = new Date().getFullYear();