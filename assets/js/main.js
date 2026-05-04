(function () {
  'use strict';

  const rules = {
    fullName(v) {
      if (!v) return 'Required.';
      if (/\d/.test(v)) return 'Name cannot contain numbers.';
      if (v.length < 2) return 'Name is too short.';
      return '';
    },
    age(v) {
      const n = parseInt(v, 10);
      if (!v || Number.isNaN(n)) return 'Required.';
      if (n < 18 || n > 65) return 'Must be between 18 and 65.';
      return '';
    },
    bloodGroup(v) {
      const allowed = ['A+','A-','B+','B-','AB+','AB-','O+','O-'];
      if (!v) return 'Required.';
      if (!allowed.includes(v.toUpperCase())) return 'Use A+, A-, B+, B-, AB+, AB-, O+, or O-.';
      return '';
    },
    phone(v) {
      if (!/^[6-9]\d{9}$/.test(v)) return 'Enter a 10-digit Indian mobile number.';
      return '';
    },
    email(v) {
      if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v)) return 'Enter a valid email.';
      return '';
    },
    pincode(v) {
      if (!/^\d{6}$/.test(v)) return 'Pincode must be 6 digits.';
      return '';
    },
    password(v) {
      if (v.length < 8) return 'Minimum 8 characters.';
      if (!/[A-Z]/.test(v)) return 'Add at least one uppercase letter.';
      if (!/[^A-Za-z0-9]/.test(v)) return 'Add at least one symbol.';
      return '';
    },
  };

  function setError(input, msg) {
    const errEl = input.parentElement.querySelector('.error');
    if (errEl) errEl.textContent = msg;
  }

  function attach(input, ruleName, extra) {
    if (!input) return;
    const validate = () => {
      let msg = rules[ruleName] ? rules[ruleName](input.value.trim()) : '';
      if (!msg && typeof extra === 'function') msg = extra(input.value.trim());
      setError(input, msg);
      return msg === '';
    };
    input.addEventListener('blur', validate);
    input.dataset.validator = ruleName;
  }

  document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('form[data-validate]');
    if (!form) return;

    const fields = {
      full_name:        ['fullName'],
      age:              ['age'],
      blood_group:      ['bloodGroup'],
      phone:            ['phone'],
      email:            ['email'],
      pincode:          ['pincode'],
      password:         ['password'],
      confirm_password: ['', (v) => {
        const pwd = form.querySelector('[name=password]');
        return pwd && v === pwd.value ? '' : 'Passwords do not match.';
      }],
    };

    Object.entries(fields).forEach(([name, [ruleName, extra]]) => {
      attach(form.querySelector(`[name=${name}]`), ruleName, extra);
    });

    form.addEventListener('submit', (ev) => {
      let ok = true;
      form.querySelectorAll('[data-validator]').forEach((el) => {
        el.dispatchEvent(new Event('blur'));
        const msg = el.parentElement.querySelector('.error');
        if (msg && msg.textContent) ok = false;
      });
      if (!ok) ev.preventDefault();
    });
  });
})();
