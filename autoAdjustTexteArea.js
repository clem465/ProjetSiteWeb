const textareas = document.querySelectorAll('textarea');

textareas.forEach(textarea => {
  const adjustSize = () => {
    textarea.style.height = 'auto';
    textarea.style.width = 'auto';
    textarea.style.height = textarea.scrollHeight + 'px';

    // Créer un élément temporaire pour mesurer la largeur du texte
    const span = document.createElement('span');
    span.style.visibility = 'hidden';
    span.style.whiteSpace = 'pre'; // espace conservé, pas de retour à la ligne
    span.style.font = getComputedStyle(textarea).font;
    span.textContent = textarea.value || textarea.placeholder || '';
    document.body.appendChild(span);
    const width = span.getBoundingClientRect().width + 20; // 20px padding
    document.body.removeChild(span);

    // Limiter la largeur selon min/max CSS
    const minWidth = parseInt(getComputedStyle(textarea).minWidth) || 150;
    const maxWidth = parseInt(getComputedStyle(textarea).maxWidth) || 600;
    textarea.style.width = Math.min(Math.max(width, minWidth), maxWidth) + 'px';
  };

  textarea.addEventListener('input', adjustSize);

  // Ajuste la taille au chargement si texte déjà présent
  adjustSize();
});
