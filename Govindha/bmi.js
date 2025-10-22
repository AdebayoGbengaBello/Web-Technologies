function calculateBMI() {
    // Get values from input fields
    let height = parseFloat(document.getElementById("height").value);
    let weight = parseFloat(document.getElementById("weight").value);

    // Check for valid input
    if (isNaN(height) || isNaN(weight) || height <= 0 || weight <= 0) {
        document.getElementById("bmiResult").textContent = "Please enter valid height and weight values.";
        document.getElementById("healthMessage").textContent = "";
        return;
    }

    // Calculate BMI
    let bmi = weight / (height * height);

    // Display BMI result
    document.getElementById("bmiResult").textContent = "Your BMI is: " + bmi.toFixed(2);

    // Determine health message based on BMI
    let message = "";
    if (bmi < 18.5) {
        message = "You are underweight.";
    } else if (bmi >= 18.5 && bmi < 24.9) {
        message = "You have a normal weight.";
    } else if (bmi >= 25 && bmi < 29.9) {
        message = "You are overweight.";
    } else {
        message = "You are obese.";
    }

    // Display health message
    document.getElementById("healthMessage").textContent = message;
}
