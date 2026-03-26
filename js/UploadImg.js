const fileInput = document.getElementById('file-upload');
  const uploadCircle = document.querySelector('.upload-circle');
  const plusSign = document.querySelector('.plus-sign');
  const uploadText = document.querySelector('.upload-text');

  // Overlay de suppression
  const deleteOverlay = document.createElement('div');
  deleteOverlay.className = 'upload-delete-overlay';
  deleteOverlay.innerHTML = '<i class="fa-solid fa-trash"></i>';
  deleteOverlay.style.display = 'none';
  uploadCircle.appendChild(deleteOverlay);

  fileInput.addEventListener('change', function () {
    const file = this.files[0];

    if (!file || !file.type.startsWith('image/')) {
      alert('Veuillez sélectionner un fichier image valide (jpg, png, gif...)');
      this.value = '';
      return;
    }

    const reader = new FileReader();
    reader.onload = function (e) {
      uploadCircle.style.backgroundImage = `url('${e.target.result}')`;
      uploadCircle.style.backgroundSize = 'cover';
      uploadCircle.style.backgroundPosition = 'center';
      plusSign.style.display = 'none';
      uploadText.textContent = 'Modifier la photo';
      deleteOverlay.style.display = 'flex';
    };
    reader.readAsDataURL(file);
  });

  // Suppression au clic sur l'overlay
  deleteOverlay.addEventListener('click', function (e) {
    e.preventDefault();
    e.stopPropagation(); // empêche d'ouvrir le file picker
    uploadCircle.style.backgroundImage = '';
    uploadCircle.style.backgroundSize = '';
    uploadCircle.style.backgroundPosition = '';
    plusSign.style.display = 'block';
    uploadText.textContent = 'Ajouter une photo';
    fileInput.value = '';
    deleteOverlay.style.display = 'none';
  });