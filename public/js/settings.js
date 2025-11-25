document.getElementById('btnAvatarPick').addEventListener('click', function () {
    document.getElementById('avatarInput').click();
});

document.getElementById('avatarInput').addEventListener('change', function (e) {
    const file = e.target.files[0];
    if (file) {
        // Basic client-side file type/size check (optional)
        if (!file.type.startsWith('image/')) {
            alert('Please select an image file.');
            e.target.value = '';
            return;
        }
        // preview
        document.getElementById('avatarPreview').src = URL.createObjectURL(file);
    }
});

// Disable save button once form submit to avoid double submit
document.querySelector('form').addEventListener('submit', function () {
    const btn = document.getElementById('btnSaveProfile');
    btn.disabled = true;
    // optionally add spinner class
    btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...';
});

