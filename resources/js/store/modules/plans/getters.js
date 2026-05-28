export const items = (state) => {
  return state.plans.items;
};

export const plan = (state) => {
  return state.plan;
};

export const loader = (state) => {
  return state.plans.loader;
};
export const getSubmitLoading = (state) => {
  return state.submitLoading;
};

export const meta = (state) => {
  return state.plans.meta;
};

export const options = (state) => {
  return state.options;
};

export const indexComponent = (state) => {
  return state.indexComponent;
};

export const errorsPlan = (state) => {
  return state.errorsPlan;
};

export const id = (state) => {
  return state.plan.id;
};

export const signatures = (state) => {
  return state.plan.signatures;
};

export const isShortPlan = (state, _, rootState) => {
  const isTrue = (item) => item.status == true;
  const hasAllStatuses = state.plan.verification.length >= 5;
  const isEveryTrue = state.plan.verification.every(isTrue) && hasAllStatuses;
  const roles = [6, 7];
  const currentRoleId = rootState.auth.userData.role_id;

  return state.plan.short_plan && state.plan.need_verification && isEveryTrue && roles.includes(currentRoleId);
};

export const readOnly = (state, _, rootState) => {
  const isTrue = (item) => item.status == true;
  const isEveryTrue = state.plan.verification.every(isTrue);
  const roles = [6, 7];
  const currentRoleId = rootState.auth.userData.role_id;

  if (state.plan.need_verification && isEveryTrue && roles.includes(currentRoleId)) {
    return true;
  }

  return false;
};

export const project = (state) => {
  return state.plan.project;
}

export const subjectCalculatorError = (state) => (id) => {
  const data = state.plan.subject_errors.find((error) => error.subject_id === id);
  return data?.subject_id || false;
}
