export const ROLES = {
  ID: {
    admin: 1,
    training_department: 2,
    practice_department: 3,
    educational_department_deputy: 4,
    educational_department_chief: 5,
    faculty_institute: 6,
    department: 7,
    root: 8,
    guest: 9,
    admin_department_postgraduate: 10,
  },
};
export const FORM_ORGANIZATIONS = {
  modular_cyclic: 1,
  semester: 3,
};

export const FORM_ORGANIZATIONS_TABLE = {
  [FORM_ORGANIZATIONS.modular_cyclic]: 2, //modular_cyclic
  [FORM_ORGANIZATIONS.semester]: 1, //semester
};

export const VERIFICATION_STATUS = {
  success: 'success',
  warning: 'warning',
  error: 'error',
};

export const CATALOG_SIGNATURE_TYPE = {
  head: {
    id: 1,
    label: 'Голова Ради з якості інституту (факультету)',
  },
  leader: {
    id: 2,
    label: 'Керівник групи забезпечення спеціальності',
  },
  manager: {
    id: 3,
    label: 'Завідувач кафедри',
  },
  grant: {
    id: 4,
    label: 'Гарант освітньої програми',
  },
};

export const CYCLES = {
  PRACTICAL_TRAINING: 9,
  ATTESTATION: 10,
};

export const PLAN_TYPE = {
  TEMPLATE: 1,
  PLAN: 2,
  SHORT_PLAN: 3,
  PROJECT: 4,
};

export const INDIVIDUAL_TASK_TYPE = {
  CONTROLWORK: 1,
  COURSEWORK: 2,
  WITHOUT_TASK: 3,
};

export const FORM_CONTROL = {
  EXAM: 1,
  DIFFERENTIATED_CREDIT: 2,
  CREDIT: 3,
  DEFENSE: 8,
  NO_CERTIFICATION: 10,
};

export const DEGREE = {
  BACHELOR: 2,
  MAGISTER: 4,
  POSTGRADUATE: 8,
  BACHELOR_FOREIGN: 10,
  BACHELOR_ENGLISH_PROGRAMS: 11,
};
