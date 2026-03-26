document.addEventListener('DOMContentLoaded', function () {

  // ── 1. Dropdowns de recherche génériques ──
  function initSearchDropdown(config) {
    const searchInput = document.getElementById(config.inputId);
    const optionsContainer = document.getElementById(config.optionsContainerId);
    if (!searchInput || !optionsContainer) return;

    const options = optionsContainer.querySelectorAll(`.${config.optionClass}`);
    const hiddenInput = document.querySelector(`input[name="${config.hiddenInputName}"]`);

    searchInput.addEventListener('input', function () {
      const filter = this.value.toLowerCase();
      if (filter.length > 0) {
        optionsContainer.style.display = 'block';
        options.forEach(option => {
          option.style.display = option.textContent.toLowerCase().includes(filter) ? 'block' : 'none';
        });
      } else {
        optionsContainer.style.display = 'none';
      }
    });

    options.forEach(option => {
      option.addEventListener('mousedown', function (e) {
        e.preventDefault();
        searchInput.value = this.textContent;
        if (hiddenInput) hiddenInput.value = this.getAttribute('data-value');
        optionsContainer.style.display = 'none';
      });
    });
  }

  // Initialise tous les dropdowns présents sur la page
  if (document.getElementById('entreprise-input')) {
    initSearchDropdown({ inputId: 'entreprise-input', optionsContainerId: 'entreprise-options', optionClass: 'select-option-search', hiddenInputName: 'entreprise' });
  }
  if (document.getElementById('ville-input')) {
    initSearchDropdown({ inputId: 'ville-input', optionsContainerId: 'lieu-options', optionClass: 'select-option-search', hiddenInputName: 'stage_or_alternance' });
  }
  if (document.getElementById('education-input')) {
    initSearchDropdown({ inputId: 'education-input', optionsContainerId: 'education-options', optionClass: 'select-option-search', hiddenInputName: 'education' });
  }

  // ── 2. Dropdown Secteur ──
  const secteurSelect = document.querySelector('.custom-select');
  const secteurOptions = secteurSelect ? secteurSelect.querySelector('#secteur-options') : null;
  const selectSelected = secteurSelect ? secteurSelect.querySelector('.select-selected') : null;
  const secteurHidden = secteurSelect ? secteurSelect.querySelector('input[name="secteurs"]') : null;

  if (secteurSelect && selectSelected && secteurOptions) {
    selectSelected.addEventListener('click', function (e) {
      e.stopPropagation();
      const isOpen = secteurOptions.style.display === 'block';
      secteurOptions.style.display = isOpen ? 'none' : 'block';
      selectSelected.classList.toggle('open', !isOpen);
    });

    secteurOptions.querySelectorAll('.select-option').forEach(option => {
      option.addEventListener('mousedown', function (e) {
        e.preventDefault();
        selectSelected.textContent = this.textContent;
        selectSelected.classList.remove('default', 'open');
        if (secteurHidden) secteurHidden.value = this.getAttribute('data-value');
        secteurOptions.style.display = 'none';
      });
    });
  }

  // ── 3. Skills avec tags ──
  const skillsInput = document.getElementById('skills-input');
  const skillsOptions = document.getElementById('skills-options');
  const skillsTags = document.getElementById('skills-tags');
  const skillsHidden = document.getElementById('skills-hidden');

  if (skillsInput && skillsOptions && skillsTags) {
    const allSkills = ['Python', 'JavaScript', 'Java', 'CSS', 'HTML'];
    let selectedSkills = [];

    function updateHidden() {
      if (skillsHidden) skillsHidden.value = selectedSkills.join(',');
    }

    function addTag(label, value) {
      if (selectedSkills.includes(value)) return;
      selectedSkills.push(value);
      updateHidden();

      const tag = document.createElement('span');
      tag.className = 'skill-tag';
      tag.innerHTML = `${label} <button type="button" data-value="${value}">&times;</button>`;
      tag.querySelector('button').addEventListener('click', function () {
        selectedSkills = selectedSkills.filter(s => s !== this.dataset.value);
        updateHidden();
        tag.remove();
      });
      skillsTags.appendChild(tag);
      skillsInput.value = '';
      skillsOptions.style.display = 'none';
      skillsInput.focus();
    }

    function renderSkillOptions(filter) {
      skillsOptions.innerHTML = '';
      const filtered = allSkills.filter(s =>
        s.toLowerCase().includes(filter.toLowerCase()) &&
        !selectedSkills.includes(s.toLowerCase())
      );

      filtered.forEach(skill => {
        const div = document.createElement('div');
        div.className = 'select-option-search';
        div.textContent = skill;
        div.addEventListener('mousedown', function (e) {
          e.preventDefault();
          addTag(skill, skill.toLowerCase());
        });
        skillsOptions.appendChild(div);
      });

      const exactMatch = allSkills.some(s => s.toLowerCase() === filter.toLowerCase());
      if (filter.length > 0 && !exactMatch) {
        const div = document.createElement('div');
        div.className = 'select-option-search select-option-new';
        div.innerHTML = `<i class="fa-solid fa-plus"></i> Ajouter "<strong>${filter}</strong>"`;
        div.addEventListener('mousedown', function (e) {
          e.preventDefault();
          addTag(filter, filter.toLowerCase());
        });
        skillsOptions.appendChild(div);
      }

      skillsOptions.style.display = skillsOptions.children.length > 0 ? 'block' : 'none';
    }

    skillsInput.addEventListener('input', function () {
      renderSkillOptions(this.value.trim());
    });

    skillsInput.addEventListener('focus', function () {
      if (this.value.trim().length > 0) renderSkillOptions(this.value.trim());
    });
  }

  // ── 4. Listener unique pour fermer tous les dropdowns ──
  document.addEventListener('click', function (e) {
    if (!e.target.closest('.search-container')) {
      ['entreprise-options', 'lieu-options', 'education-options'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.style.display = 'none';
      });
    }
    if (secteurOptions && !e.target.closest('.custom-select')) {
      secteurOptions.style.display = 'none';
      if (selectSelected) selectSelected.classList.remove('open');
    }
    if (skillsOptions && !e.target.closest('.skills-container')) {
      skillsOptions.style.display = 'none';
    }
  });

});