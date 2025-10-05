// utilitaires de date réutilisables
export const parseDateStringDDMMYYYY = (str) => {
  // "29/09/2025" -> Date
  const [d, m, y] = str.split('/').map(Number);
  return new Date(y, m - 1, d);
};

export const toDateInputString = (date) => {
  return date.toISOString().slice(0, 10);
};

export const getMondayFromDate = (dateLike) => {
  // accepte un Date ou une string de type yyyy-mm-dd ou dd/mm/yyyy (mais on privilégie yyyy-mm-dd)
  const jsDate = (typeof dateLike === 'string' && dateLike.includes('/'))
    ? parseDateStringDDMMYYYY(dateLike)
    : new Date(dateLike);
  const dayOfWeek = jsDate.getDay();
  const diffToMonday = dayOfWeek === 0 ? -6 : 1 - dayOfWeek;
  const copy = new Date(jsDate);
  copy.setDate(copy.getDate() + diffToMonday);
  copy.setHours(0, 0, 0, 0);
  return copy;
};

export const addDays = (d, amount) => {
  const r = new Date(d);
  r.setDate(r.getDate() + amount);
  return r;
};

export const isSameDay = (a, b) =>
  a.getDate() === b.getDate() &&
  a.getMonth() === b.getMonth() &&
  a.getFullYear() === b.getFullYear();
