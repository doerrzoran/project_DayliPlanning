import React, { useState, useMemo } from 'react';
import { parseDateStringDDMMYYYY } from '../Utils/dateUtils';

const TYPE_COLORS = {
  presence: '#2ecc71',
  'absence: congé maladie': '#ff6b6b',
  formation: '#f1c40f',
  télétravail: '#3498db',
  default: '#4a90e2'
};

function DayRow({ dateObj, events }) {
  return (
    <div style={{
      display: 'flex',
      flexDirection: 'column',
      gap: 4,
      padding: '3px 6px',
      borderRadius: 4,
      backgroundColor: events.length ? '#fff8f2' : 'transparent',
      minHeight: 26,
    }}>
      <div style={{ fontSize: 12, fontWeight: 500 }}>{dateObj.getDate()}</div>
      <div style={{ display: 'flex', flexDirection: 'column', gap: 2 }}>
        {events.map((ev, i) => {
          const bg = TYPE_COLORS[ev.type] || TYPE_COLORS.default;
          return (
            <div key={i} style={{
              background: bg,
              color: 'white',
              padding: '2px 4px',
              borderRadius: 4,
              fontSize: 10,
              overflow: 'hidden',
              textOverflow: 'ellipsis',
              whiteSpace: 'nowrap'
            }} title={`${ev.title || ev.type || ''} (${ev.date})`}>
              {ev.title || ev.type}
            </div>
          );
        })}
      </div>
    </div>
  );
}

function MonthColumn({ year, month, eventsForYear }) {
  const daysInMonth = new Date(year, month + 1, 0).getDate();
  const monthName = new Date(year, month, 1).toLocaleString('fr-FR', { month: 'long' });

  const eventsByDay = useMemo(() => {
    const map = new Map();
    for (let d = 1; d <= daysInMonth; d++) map.set(d, []);
    for (const ev of eventsForYear) {
      if (!ev._dateObj) continue;
      if (ev._dateObj.getFullYear() === year && ev._dateObj.getMonth() === month) {
        const day = ev._dateObj.getDate();
        map.get(day)?.push(ev);
      }
    }
    return map;
  }, [eventsForYear, year, month, daysInMonth]);

  return (
    <div style={{
      width: `calc(100% / 12)`,
      border: '1px solid #e6e6e6',
      background: 'white',
      display: 'flex',
      flexDirection: 'column',
      padding: 6,
      borderRadius: 6,
      overflow: 'hidden'
    }}>
      <div style={{
        textAlign: 'center',
        textTransform: 'capitalize',
        fontWeight: 600,
        color: '#333',
        fontSize: 13,
        marginBottom: 4
      }}>{monthName}</div>

      <div style={{ display: 'flex', flexDirection: 'column', gap: 2 }}>
        {Array.from({ length: daysInMonth }).map((_, idx) => {
          const day = idx + 1;
          const dateObj = new Date(year, month, day);
          const dayEvents = eventsByDay.get(day) || [];
          return <DayRow key={day} dateObj={dateObj} events={dayEvents} />;
        })}
      </div>
    </div>
  );
}

export default function YearlyPlanning({ exempleData, now }) {
  const defaultYear = now ? now.getFullYear() : new Date().getFullYear();
  const [year, setYear] = useState(defaultYear);

  const events = useMemo(() => {
    if (!exempleData) return [];
    const arr = Array.isArray(exempleData) ? exempleData : [exempleData];
    return arr.map(ev => {
      const copy = { ...ev };
      try { copy._dateObj = parseDateStringDDMMYYYY(ev.date); } 
      catch { copy._dateObj = null; }
      return copy;
    }).filter(e => e._dateObj);
  }, [exempleData]);

  const eventsForYear = useMemo(() => events.filter(ev => ev._dateObj.getFullYear() === year), [events, year]);

  const goPrevYear = () => setYear(y => y - 1);
  const goNextYear = () => setYear(y => y + 1);

  return (
    <div style={{
      display: 'flex',
      flexDirection: 'column',
      height: '100vh',
      overflowY: 'auto',
      overflowX: 'hidden',
      background: '#fafafa'
    }}>
      <div style={{
        display: 'flex',
        alignItems: 'center',
        gap: 8,
        padding: '8px 12px',
        background: 'white',
        borderBottom: '1px solid #ddd',
        flexShrink: 0
      }}>
        <button onClick={goPrevYear} title="Année précédente"><i className="bi bi-chevron-left" /></button>
        <input
          type="number"
          value={year}
          onChange={(e) => {
            const v = parseInt(e.target.value, 10);
            if (!isNaN(v)) setYear(v);
          }}
          style={{ width: 90, textAlign: 'center', fontWeight: 600, borderRadius: 4, border: '1px solid #ccc' }}
        />
        <button onClick={goNextYear} title="Année suivante"><i className="bi bi-chevron-right" /></button>
      </div>

      <div style={{
        display: 'flex',
        flexDirection: 'row',
        gap: 8,
        padding: 8,
        width: '100%',
      }}>
        {Array.from({ length: 12 }).map((_, m) => (
          <MonthColumn key={m} year={year} month={m} eventsForYear={eventsForYear} />
        ))}
      </div>
    </div>
  );
}
