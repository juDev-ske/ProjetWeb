/* Selecteur de competences (creation / modification offre)
   Les donnees sont passees via data-all-skills et data-selected-skills
   sur l'element #skills-container */
document.addEventListener('DOMContentLoaded', function () {
  var container = document.getElementById('skills-container');
  if (!container) return;

  var skillsInput   = document.getElementById('skills-input');
  var skillsOptions = document.getElementById('skills-options');
  var skillsTags    = document.getElementById('skills-tags');
  var skillsHidden  = document.getElementById('skills-hidden');

  var allSkills      = JSON.parse(container.dataset.allSkills || '[]');
  var selectedSkills = JSON.parse(container.dataset.selectedSkills || '[]');

  function updateHidden() {
    skillsHidden.value = selectedSkills.join(',');
  }

  function addTag(name) {
    if (selectedSkills.indexOf(name) !== -1) return;
    selectedSkills.push(name);
    updateHidden();

    var tag = document.createElement('span');
    tag.className = 'skill-tag';
    tag.innerHTML = name + ' <button type="button" data-value="' + name + '">&times;</button>';
    tag.querySelector('button').addEventListener('click', function () {
      selectedSkills = selectedSkills.filter(function (s) { return s !== this.dataset.value; }.bind(this));
      updateHidden();
      tag.remove();
    });
    skillsTags.appendChild(tag);
    skillsInput.value = '';
    skillsOptions.style.display = 'none';
    skillsInput.focus();
  }

  function renderOptions(filter) {
    skillsOptions.innerHTML = '';

    var filtered = allSkills.filter(function (s) {
      return (filter === '' || s.toLowerCase().indexOf(filter.toLowerCase()) !== -1)
        && selectedSkills.indexOf(s) === -1;
    });

    filtered.forEach(function (skill) {
      var div = document.createElement('div');
      div.className = 'select-option-search';
      div.textContent = skill;
      div.addEventListener('mousedown', function (e) {
        e.preventDefault();
        addTag(skill);
      });
      skillsOptions.appendChild(div);
    });

    var exactMatch = allSkills.some(function (s) { return s.toLowerCase() === filter.toLowerCase(); });
    if (filter.length > 0 && !exactMatch) {
      var div = document.createElement('div');
      div.className = 'select-option-search select-option-new';
      div.innerHTML = '<i class="fa-solid fa-plus"></i> Ajouter "<strong>' + filter + '</strong>"';
      div.addEventListener('mousedown', function (e) {
        e.preventDefault();
        addTag(filter);
      });
      skillsOptions.appendChild(div);
    }

    skillsOptions.style.display = skillsOptions.children.length > 0 ? 'block' : 'none';
  }

  // Pre-remplir les tags existants
  selectedSkills.forEach(function (name) {
    var tag = document.createElement('span');
    tag.className = 'skill-tag';
    tag.innerHTML = name + ' <button type="button" data-value="' + name + '">&times;</button>';
    tag.querySelector('button').addEventListener('click', function () {
      selectedSkills = selectedSkills.filter(function (s) { return s !== this.dataset.value; }.bind(this));
      updateHidden();
      tag.remove();
    });
    skillsTags.appendChild(tag);
  });
  updateHidden();

  skillsInput.addEventListener('input', function () {
    renderOptions(this.value.trim());
  });

  skillsInput.addEventListener('focus', function () {
    renderOptions(this.value.trim());
  });

  document.addEventListener('click', function (e) {
    if (!e.target.closest('.skills-container')) {
      skillsOptions.style.display = 'none';
    }
  });
});
