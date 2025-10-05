// src/components/MonthlyPlanning.jsx
import React, { useState, useMemo } from 'react';
import {
  toDateInputString,
  getMondayFromDate,
  addDays,
  isSameDay,
  parseDateStringDDMMYYYY,
} from '../Utils/dateUtils';
import '../styles/Calendar.css'; // assure-toi que tes styles existent

/*
  MonthlyPlanning:
  - colonnes = semaines (chaque semaine commence le lundi)
  - lignes = jours (Lun -> Dim)
  - surligne les jours contenant exempleData.date et affiche l'intervalle horaire.
  - exempleData peut être un tableau
*/

const TYPE_COLORS = {
  presence: '#2ecc71',
  'absence: congé maladie': '#ff6b6b',
  default: '#4a90e2'
};

export default function MonthlyPlanning(props) {
  const defaultDateString = props.now ? toDateInputString(props.now) : toDateInputString(new Date());
  const [selectedDate, setSelectedDate] = useState(defaultDateString);

  // Determine month to display based on selectedDate
  const selectedJsDate = new Date(selectedDate);
  const year = selectedJsDate.getFullYear();
  const month = selectedJsDate.getMonth();

  // 1er et dernier jour du mois
  const firstOfMonth = new Date(year, month, 1);
  const lastOfMonth = new Date(year, month + 1, 0);

  // Première colonne = lundi précédant ou égal au 1er du mois
  let startMonday = getMondayFromDate(firstOfMonth);

  // Construire colonnes semaines jusqu'à couvrir tout le mois
  const weeks = [];
  let cursor = new Date(startMonday);
  while (cursor <= lastOfMonth || weeks.length < 4) {
    const week = [];
    for (let i = 0; i < 7; i++) {
      week.push(addDays(cursor, i));
    }
    weeks.push(week);
    cursor = addDays(cursor, 7);
    if (weeks.length > 6) break;
  }

  // Normaliser exempleData en tableau avec _dateObj et _timeText
  const events = useMemo(() => {
    if (!props.exempleData) return [];
    const arr = Array.isArray(props.exempleData) ? props.exempleData : [props.exempleData];
    return arr.map(ev => {
      const copy = { ...ev };
      copy._dateObj = parseDateStringDDMMYYYY(ev.date);
      copy._timeText = `${ev.start} - ${ev.end}`;
      return copy;
    }).filter(e => e._dateObj instanceof Date && !isNaN(e._dateObj));
  }, [props.exempleData]);

  // Navigation simple: mois précédent / suivant
  const goPrevMonth = () => {
    const d = new Date(selectedJsDate);
    d.setMonth(d.getMonth() - 1);
    setSelectedDate(toDateInputString(d));
  };
  const goNextMonth = () => {
    const d = new Date(selectedJsDate);
    d.setMonth(d.getMonth() + 1);
    setSelectedDate(toDateInputString(d));
  };

  // Changer le mois via input date
  const onDateChange = (e) => {
    setSelectedDate(e.target.value);
  };

  return (
    <div className="monthly-planning">
      <div className="monthly-header" style={{ marginBottom: 10, display: 'flex', alignItems: 'center' }}>
        <button onClick={goPrevMonth} title="Mois précédent"><i className="bi bi-chevron-left" /></button>

        <input
          type="month"
          value={`${year}-${String(month + 1).padStart(2, '0')}`}
          onChange={(e) => {
            const [y, m] = e.target.value.split('-').map(Number);
            const newDate = new Date(y, m - 1, 1);
            setSelectedDate(toDateInputString(newDate));
          }}
          style={{ margin: '0 12px' }}
        />

        <input
          type="date"
          value={selectedDate}
          onChange={onDateChange}
          style={{ marginRight: 12 }}
        />

        <button onClick={goNextMonth} title="Mois suivant"><i className="bi bi-chevron-right" /></button>
      </div>

      <div style={{ overflowX: 'auto' }}>
        <table className="monthly-table" border="1" style={{ borderCollapse: 'collapse', width: '100%' }}>
          <thead>
            <tr>
              <th style={{ width: 80 }}>Jour</th>
              {weeks.map((week, wi) => {
                const monday = week[0];
                const sunday = week[6];
                const printRange = `${monday.getDate()}/${monday.getMonth()+1} → ${sunday.getDate()}/${sunday.getMonth()+1}`;
                return (
                  <th key={wi}>
                    Semaine {wi+1}<br/>
                    <small>{printRange}</small>
                  </th>
                );
              })}
            </tr>
          </thead>

          <tbody>
            {['Lun','Mar','Mer','Jeu','Ven','Sam','Dim'].map((label, rowIdx) => (
              <tr key={rowIdx}>
                <td className="day-label" style={{
                  padding: '6px',
                  backgroundColor: '#f5f5f5',
                  fontWeight: '600',
                  textAlign: 'center'
                }}>{label}</td>

                {weeks.map((week, wi) => {
                  const dayDate = week[rowIdx];
                  const inMonth = dayDate.getMonth() === month;

                  // Tous les événements pour ce jour
                  const dayEvents = events.filter(ev => isSameDay(ev._dateObj, dayDate));

                  return (
                    <td
                      key={wi}
                      className={`day-cell ${dayEvents.length ? 'has-events' : ''} ${inMonth ? 'in-month' : 'out-of-month'}`}
                      style={{
                        minWidth: 120,
                        verticalAlign: 'top',
                        height: 80,
                        padding: 6,
                        borderLeft: '1px solid #eee'
                      }}
                    >
                      <div style={{ fontSize: 12, marginBottom: 6 }}>
                        <strong>{dayDate.getDate()}</strong> / {dayDate.getMonth()+1}
                      </div>

                      <div style={{
                        display: 'flex',
                        flexDirection: 'column',
                        gap: 6,
                        overflowY: 'auto'
                      }}>
                        {dayEvents.map((ev, i) => {
                            const bg = TYPE_COLORS[ev.type] || TYPE_COLORS.default;
                            const evTitle = (ev.title && String(ev.title).trim()) ? ev.title : (ev.type && String(ev.type).trim()) ? ev.type : 'Événement';
                            const tooltip = `${evTitle} — ${ev._timeText}`;

                            return (
                                <div
                                key={i}
                                className="event-badge-monthly"
                                title={tooltip}
                                style={{
                                    backgroundColor: bg,
                                    color: 'white',
                                    borderRadius: 4,
                                    padding: '6px 8px',
                                    fontSize: 11,
                                    display: 'inline-block',
                                    boxSizing: 'border-box'
                                }}
                                >
                                {/* ligne du temps */}
                                <div style={{ fontWeight: 700, fontSize: 11, marginBottom: 2 }}>
                                    {ev.start} — {ev.end}
                                </div>

                                {/* titre : autoriser le wrap (important) */}
                                <div
                                    className="event-badge-title"
                                    style={{
                                    fontSize: 11,
                                    opacity: 0.95,
                                    whiteSpace: 'normal',        /* allow wrapping */
                                    overflow: 'visible'          /* ensure it's visible */
                                    }}
                                >
                                {evTitle}
                            </div>
                            </div>
                            );
                            })}
                      </div>
                    </td>
                  );
                })}
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}
