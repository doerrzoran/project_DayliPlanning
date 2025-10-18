import { useMemo } from 'react';

export function useFormattedPeriods(user) {
  return useMemo(() => {
    if (!user?.periodes?.presences) return [];
    return user.periodes.presences.map(p => {
      const [year, month, day] = p.date.split('-');
      const formattedDate = `${day}/${month}/${year}`;
      const formatTime = (time) => time ? time.slice(0, 5).replace(/^0/, '') : '';
      return {
        date: formattedDate,
        start: formatTime(p.arrival),
        end: formatTime(p.depature),
        type: 'presence',
      };
    });
  }, [user]);
}
