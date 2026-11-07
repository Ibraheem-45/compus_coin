/**
 * Campus Coin - Student Profile Module
 */

document.addEventListener('DOMContentLoaded', function() {
  CampusCoin.init('profile');

  const nameInput = document.getElementById('profName');
  const universityInput = document.getElementById('profUniversity');
  const yearSelect = document.getElementById('profYear');
  const allowanceInput = document.getElementById('profAllowance');
  const goalInput = document.getElementById('profGoal');
  const editToggleBtn = document.getElementById('toggleEditProfileBtn');
  const saveBtn = document.getElementById('saveProfileBtn');

  // Toggle edit state
  let isEditing = false;
  if (editToggleBtn) {
    editToggleBtn.onclick = function() {
      isEditing = !isEditing;
      const formControls = [nameInput, universityInput, yearSelect, allowanceInput, goalInput];
      formControls.forEach(ctrl => {
        if (ctrl) ctrl.disabled = !isEditing;
      });

      if (saveBtn) saveBtn.classList.toggle('d-none', !isEditing);
      editToggleBtn.innerHTML = isEditing ? '<i class="bi bi-x-circle me-1"></i> Cancel' : '<i class="bi bi-pencil me-1"></i> Edit Profile';
    };
  }

});
