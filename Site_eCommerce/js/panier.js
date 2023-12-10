
//initial value of the total
const quantityInputs = document.querySelectorAll('input[name="quantity"]');
quantityInputs.forEach(function (input) {
  updateTotal(input);
});

// Add event listeners to quantity inputs
document.addEventListener("DOMContentLoaded", function () {
  const forms = document.querySelectorAll(".myForm");

  forms.forEach(function (form) {
    const originalFormValues = getFormValues(form);

    form.addEventListener("input", function () {
      const currentFormValues = getFormValues(form);
      const isModified = !compareFormValues(
        originalFormValues,
        currentFormValues
      );

      // Show/hide buttons based on modifications
      form.querySelector(".saveButton").style.display = isModified
        ? "inline-block"
        : "none";
      form.querySelector(".cancelButton").style.display = isModified
        ? "inline-block"
        : "none";
    });

    // Additional setup for each form
    // ...
  });
});

function getFormValues(form) {
  const formElements = form.elements;
  const formValues = {};

  for (let i = 0; i < formElements.length; i++) {
    const element = formElements[i];
    if (element.type !== "submit") {
      formValues[element.name] = element.value;
    }
  }

  return formValues;
}

function compareFormValues(obj1, obj2) {
  return JSON.stringify(obj1) === JSON.stringify(obj2);
}

function adjustQuantity(button, delta) {
  if (
    delta < 0 &&
    button.parentNode.querySelector('input[name="quantity"]').value == 1
  ) {
    return;
  }
  const quantityInput = button.parentNode.querySelector(
    'input[name="quantity"]'
  );
  const currentQuantity = parseInt(quantityInput.value);
  const newQuantity = Math.max(0, currentQuantity + delta);

  quantityInput.value = newQuantity;

  const event = new Event("input", {
    bubbles: true,
  });
  quantityInput.dispatchEvent(event);
}

function updateTotal(input) {
  const form = input.closest(".myForm");
  const quantity = parseInt(form.querySelector('input[name="quantity"]').value);
  const price = parseInt(form.querySelector('input[name="price"]').value);
  const total = quantity * price;

  form.querySelector(".totalValue").innerText = total;
}

function resetForm(button) {
  const form = button.closest(".myForm");
  form.reset();
  updateTotal(form.querySelector('input[name="quantity"]'));

  const originalFormValues = getFormValues(form);

  form.querySelector(".saveButton").style.display = "none";
  form.querySelector(".cancelButton").style.display = "none";
}
