/**
 * Campus Coin - Authentication & Validation Module
 */

document.addEventListener('DOMContentLoaded', function() {
  // Password Show / Hide Toggle
  const togglePassBtns = document.querySelectorAll('.toggle-password-btn');
  togglePassBtns.forEach(btn => {
    btn.addEventListener('click', function() {
      const targetInputId = this.getAttribute('data-target');
      const input = document.getElementById(targetInputId);
      if (!input) return;

      if (input.type === 'password') {
        input.type = 'text';
        this.innerHTML = '<i class="bi bi-eye-slash"></i>';
      } else {
        input.type = 'password';
        this.innerHTML = '<i class="bi bi-eye"></i>';
      }
    });
  });

  // Password Strength Calculator
  const passwordInput = document.getElementById('regPassword');
  const strengthBar = document.getElementById('strengthBar');
  const strengthText = document.getElementById('strengthText');

  if (passwordInput && strengthBar) {
    passwordInput.addEventListener('input', function() {
      const val = this.value;
      let score = 0;

      if (val.length >= 8) score += 25;
      if (/[A-Z]/.test(val)) score += 25;
      if (/[0-9]/.test(val)) score += 25;
      if (/[^A-Za-z0-9]/.test(val)) score += 25;

      strengthBar.style.width = score + '%';

      if (score === 0) {
        strengthBar.className = 'strength-meter-bar bg-secondary';
        if (strengthText) strengthText.innerText = 'Enter at least 8 characters';
      } else if (score <= 50) {
        strengthBar.className = 'strength-meter-bar bg-danger';
        if (strengthText) strengthText.innerText = 'Weak password';
      } else if (score <= 75) {
        strengthBar.className = 'strength-meter-bar bg-warning';
        if (strengthText) strengthText.innerText = 'Moderate strength';
      } else {
        strengthBar.className = 'strength-meter-bar bg-success';
        if (strengthText) strengthText.innerText = 'Strong password';
      }
    });
  }

  // Student Login Form Validation
  const loginForm = document.getElementById('studentLoginForm');
  if (loginForm) {
    loginForm.addEventListener('submit', function(e) {
      e.preventDefault();
      const email = document.getElementById('loginEmail').value.trim();
      const pass = document.getElementById('loginPassword').value.trim();
      const alertBox = document.getElementById('loginAlert');

      if (!email || !pass) {
        showAlert(alertBox, 'Please provide both your university email and password.', 'danger');
        return;
      }

      if (!validateEmail(email)) {
        showAlert(alertBox, 'Please enter a valid academic/university email address.', 'danger');
        return;
      }

      // Simulate Authentication Success
      showAlert(alertBox, 'Success! Logging into Campus Coin...', 'success');
      setTimeout(() => {
        window.location.href = 'dashboard.html';
      }, 800);
    });
  }

  // Student Registration Form Validation
  const regForm = document.getElementById('studentRegForm');
  if (regForm) {
    regForm.addEventListener('submit', function(e) {
      e.preventDefault();
      const name = document.getElementById('regName').value.trim();
      const email = document.getElementById('regEmail').value.trim();
      const pass = document.getElementById('regPassword').value;
      const confirmPass = document.getElementById('regConfirmPassword').value;
      const academicYear = document.getElementById('regYear').value;
      const allowance = document.getElementById('regAllowance').value;
      const goal = document.getElementById('regGoal').value;
      const terms = document.getElementById('regTerms').checked;
      const alertBox = document.getElementById('regAlert');

      if (!name || !email || !pass || !confirmPass || !academicYear) {
        showAlert(alertBox, 'Please complete all required fields.', 'danger');
        return;
      }

      if (!validateEmail(email)) {
        showAlert(alertBox, 'Please enter a valid university email address.', 'danger');
        return;
      }

      if (pass.length < 8) {
        showAlert(alertBox, 'Password must be at least 8 characters long.', 'danger');
        return;
      }

      if (pass !== confirmPass) {
        showAlert(alertBox, 'Passwords do not match.', 'danger');
        return;
      }

      if (!terms) {
        showAlert(alertBox, 'You must agree to the Campus Coin student terms of service.', 'danger');
        return;
      }

      // Save student details
      const user = CampusDB.getUser();
      user.name = name;
      user.email = email;
      user.academicYear = academicYear;
      if (allowance) user.baselineAllowance = parseFloat(allowance);
      if (goal) user.savingsGoal = parseFloat(goal);
      user.avatar = name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
      CampusDB.saveUser(user);

      showAlert(alertBox, 'Account created successfully! Redirecting to your dashboard...', 'success');
      setTimeout(() => {
        window.location.href = 'dashboard.html';
      }, 1000);
    });
  }

  // Password Recovery Form
  const forgotForm = document.getElementById('forgotPassForm');
  if (forgotForm) {
    forgotForm.addEventListener('submit', function(e) {
      e.preventDefault();
      const email = document.getElementById('forgotEmail').value.trim();
      const alertBox = document.getElementById('forgotAlert');

      if (!email || !validateEmail(email)) {
        showAlert(alertBox, 'Please enter a valid student email address.', 'danger');
        return;
      }

      showAlert(alertBox, 'Password reset link sent! Check your university inbox.', 'success');
      CampusCoin.showToast('Reset email dispatched to ' + email, 'info');
      setTimeout(() => {
        window.location.href = 'login.html';
      }, 2500);
    });
  }

  function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
  }

  function showAlert(alertEl, message, type) {
    if (!alertEl) return;
    alertEl.className = `alert alert-${type} d-block`;
    alertEl.innerHTML = `<i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i> ${message}`;
  }
});
