function validateForm() {
  var password = document.getElementById("password").value;
  var confirmPassword = document.getElementById("ConfirmPassword").value;

  var mail = document.getElementById("mail").value;
  var mailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]{2,}[.][a-zA-Z]{2,4}$/;

  var tel = document.getElementById("tel").value;
  const regexTelephone =
    /^(\+|00)?33[1-9]([-. ]?[0-9]{2}){4}$|0[1-9]([-. ]?[0-9]{2}){4}$/;

  var password = document.getElementById("password").value;
  const regexMotDePasse = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;

  var codePostal = document.getElementById("codePostal").value;
  const regexCodePostal = /^[0-9]{5}$/;

  if (!regexCodePostal.test(codePostal)) {
    alert("Invalid postal code. Please try again. Format : 75000");
    document.getElementById("codePostal").value = "";
    return false;
  }
  if (!mailRegex.test(mail)) {
    alert(
      "Invalid email address. Please try again. Format : info@malyart.com "
    );
    document.getElementById("mail").value = "";
    return false;
  }

  if (!regexTelephone.test(tel)) {
    alert(
      "Invalid phone number. Please try again. Format : 0612345678 OR +33612345678"
    );
    document.getElementById("tel").value = "";
    return false;
  }
  if (!regexMotDePasse.test(password)) {
    alert(
      "Invalid password. Please try again. Format : 8 characters, 1 uppercase, 1 lowercase, 1 number"
    );
    document.getElementById("password").value = "";
    return false;
  }

  if (password !== confirmPassword) {
    alert("Passwords do not match. Please try again.");
    document.getElementById("password").value = "";
    document.getElementById("ConfirmPassword").value = "";
    return false;
  }

  return true;
}

