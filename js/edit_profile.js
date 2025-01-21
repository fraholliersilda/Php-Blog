// edit_profile.js
window.isFormDirty = false;

function handleCancel(event) {
    if (window.isFormDirty) {
        const confirmation = confirm("You have unsaved changes. Are you sure you want to leave?");
        if (!confirmation) {
            event.preventDefault();
            return;
        }
    }
    window.location.href = '/ATIS/pages/profile/profile.php';
}

window.addEventListener("DOMContentLoaded", function () {
    const forms = document.querySelectorAll("form");

    forms.forEach(form => {
        form.addEventListener("input", () => {
            window.isFormDirty = true;
        });

        form.addEventListener("submit", () => {
            window.isFormDirty = false;
        });
    });

    window.addEventListener("beforeunload", (e) => {
        if (window.isFormDirty) {
            e.preventDefault();
            e.returnValue = "You have unsaved changes. Are you sure you want to leave?";
        }
    });
});

