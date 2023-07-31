// Function to show the success message
function showSuccessMessage() {
    const successMessage = document.querySelector(".success-message");
    if (successMessage) {
        successMessage.style.display = "block";
    }
}
// Function to reset the form and hide success message
function resetForm() {
    const form = document.getElementById("complaintForm");
    if (form) {
        form.reset();
    }
    const successMessage = document.querySelector(".success-message");
    if (successMessage) {
        successMessage.style.display = "none";
    }
}
// Function to populate districts based on the selected state
function populateDistricts() {
    const stateSelect = document.getElementById("state");
    console.log("Selected State:", stateSelect.value);
    const districtSelect = document.getElementById("district");
    const selectedState = stateSelect.value;
    const districts = {
        Maharashtra: ["Mumbai", "Pune", "Nagpur"],
        Rajasthan: ["Jaipur", "Jodhpur", "Udaipur"],
        Delhi: ["New Delhi", "North Delhi", "South Delhi"],
        Puducherry: ["Puducherry", "Karaikal", "Yanam"],
    };
    districtSelect.innerHTML = "<option value=''>Select a District</option>";
    if (selectedState in districts) {
        const districtList = districts[selectedState];
        for (const district of districtList) {
            const option = document.createElement("option");
            option.value = district;
            option.textContent = district;
            districtSelect.appendChild(option);
        }
        districtSelect.disabled = false;
    } else {
        districtSelect.disabled = true;
    }
}
// Function to refresh the CAPTCHA image
function refreshCaptcha() {
    const captchaImg = document.getElementById("captchaImg");
    if (captchaImg) {
        captchaImg.src = "captcha.php?" + new Date().getTime();
    }
}

// Function to validate the form before submission
function validateForm() {
    const stateSelect = document.getElementById("state");
    const incidentInput = document.getElementById("incident");
    const incidentDescriptionTextarea = document.getElementById("incident_description");
    const involvedInput = document.getElementById("involved");
    const incidentDateInput = document.getElementById("incident_date");
    
    // Check if captchaInput element exists
    const captchaInput = document.getElementById("captchaInput");
    if (!captchaInput) {
        console.error("captchaInput element not found in the DOM.");
        return false;
    }

    // Validate state selection
    if (stateSelect.value === "") {
        alert("Please select a state.");
        return false;
    }
    // Validate incident input
    if (incidentInput.value.trim() === "") {
        alert("Please enter the incident.");
        return false;
    }
    // Validate incident description textarea
    if (incidentDescriptionTextarea.value.trim() === "") {
        alert("Please enter the incident description.");
        return false;
    }
    // Validate involved input
    if (involvedInput.value.trim() === "") {
        alert("Please enter the individual/organization involved.");
        return false;
    }
    // Validate incident date input
    if (incidentDateInput.value === "") {
        alert("Please select the incident date.");
        return false;
    }
    // Validate captcha input
    if (captchaInput.value.trim() === "") {
        alert("Please enter the CAPTCHA.");
        return false;
    }
    // If all validations pass, allow form submission
    return true;
}
// Submit form and handle success
document.getElementById("complaintForm").addEventListener("submit", function(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);
    if (validateForm()) {
        // If the form is valid, submit the form data using AJAX
        fetch(form.action, {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            console.log("Response Data:", data); // Output the response data to the console for debugging
            // Parse the response data as JSON
            try {
                const responseData = JSON.parse(data);
                if (responseData.success) {
                    // Show the success message
                    showSuccessMessage();
                    // Reset the form after 3 seconds
                    setTimeout(function() {
                        resetForm();
                    }, 3000);
                    // Redirect to Google.com after 3 seconds
                    setTimeout(function() {
                        window.location.href = "https://www.google.com";
                    }, 3000);
                } else {
                    // Show the error message returned by the server
                    alert(responseData.message);
                }
            } catch (error) {
                console.error('Error parsing response:', error);
                alert('An error occurred while processing the response data.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while submitting the form.');
        });
    }
});
// Event listener to reset the form and hide success message on page load
document.addEventListener("DOMContentLoaded", function() {
    resetForm();
    populateDistricts();
});
