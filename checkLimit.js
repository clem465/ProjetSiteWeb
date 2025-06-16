function checkLimit(checkbox) {
            const checked = document.querySelectorAll('input[type="checkbox"]:checked');
            if (checked.length > 8) {
                checkbox.checked = false;
                alert("Tu peux sélectionner seulement 8 cartes.");
            }
        }