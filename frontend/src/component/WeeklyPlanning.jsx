// src/components/WeeklyPlanning.jsx
import React, { useState, useMemo } from 'react';
import '../styles/Calendar.css';
import 'bootstrap-icons/font/bootstrap-icons.css';
import { parseDateStringDDMMYYYY } from '../Utils/dateUtils'; // utilise ton utilitaire existant si présent

// convert time "HH:MM" to half-hour index, base 07:00 -> index 0
function timeToIndex(time) {
  if (!time) return null;
  const [hRaw, mRaw] = String(time).split(':');
  const hour = Number(hRaw);
  const min = Number(mRaw || 0);
  return (hour - 7) * 2 + (min === 30 ? 1 : 0);
}

function dayLabel(date) {
  return `${date.getDate()}/${date.getMonth() + 1}`;
}

const TYPE_COLORS = {
  presence: '#2ecc71',
  'absence: congé maladie': '#ff6b6b',
  default: '#4a90e2'
};

export default function WeeklyPlanning(props) {
  // props: exempleData (array), now (Date), getMondayFromDate (fn)
  const { exempleData, now, getMondayFromDate } = props;

  // selected date (yyyy-mm-dd for input)
  const defaultDateString = now ? now.toISOString().slice(0, 10) : new Date().toISOString().slice(0, 10);
  const [selectedDate, setSelectedDate] = useState(defaultDateString);
  const [mondayDisplayed, setMondayDisplayed] = useState(() => getMondayFromDate(defaultDateString));

  // normalize events: use only props.exempleData (no fallback)
  const normalizedEvents = useMemo(() => {
    if (!exempleData || exempleData.length === 0) return [];
    const arr = Array.isArray(exempleData) ? exempleData : [exempleData];
    return arr
      .map(ev => {
        // try to parse with utility if available, otherwise fallback to manual parse dd/mm/yyyy
        let dateObj = null;
        try {
          dateObj = parseDateStringDDMMYYYY ? parseDateStringDDMMYYYY(ev.date) : null;
        } catch (e) {
          dateObj = null;
        }
        if (!dateObj) {
          // manual parse fallback: dd/mm/yyyy
          const parts = String(ev.date || '').split('/').map(Number);
          if (parts.length === 3) {
            const [d, m, y] = parts;
            dateObj = new Date(y, (m || 1) - 1, d);
          } else {
            dateObj = null;
          }
        }

        const startIndex = typeof ev.start === 'string' ? timeToIndex(ev.start) : null;
        const endIndex = typeof ev.end === 'string' ? timeToIndex(ev.end) : null;

        return {
          ...ev,
          _dateObj: dateObj,
          _startIndex: startIndex,
          _endIndex: endIndex
        };
      })
      .filter(ev => ev._dateObj instanceof Date && !isNaN(ev._dateObj) &&
        typeof ev._startIndex === 'number' && typeof ev._endIndex === 'number');
  }, [exempleData]);

  // build week days (Mon..Sun) from mondayDisplayed
  const weekDays = useMemo(() => {
    const arr = [];
    for (let i = 0; i < 7; i++) {
      const d = new Date(mondayDisplayed);
      d.setDate(mondayDisplayed.getDate() + i);
      arr.push(d);
    }
    return arr;
  }, [mondayDisplayed]);

  // half-hour slots from 07:00 to 19:30
  const halfHours = useMemo(() => {
    const arr = [];
    for (let h = 7; h <= 19; h++) {
      arr.push(`${h}:00`);
      if (h < 19) arr.push(`${h}:30`);
    }
    return arr;
  }, []);

  // events covering a given cell (day + halfHourIndex)
  const getEventsForCell = (dayDate, halfHourIndex) => {
    return normalizedEvents.filter(ev => {
      const d = ev._dateObj;
      if (!d) return false;
      if (d.getDate() !== dayDate.getDate() ||
          d.getMonth() !== dayDate.getMonth() ||
          d.getFullYear() !== dayDate.getFullYear()) return false;
      return halfHourIndex >= ev._startIndex && halfHourIndex < ev._endIndex;
    });
  };

  // navigation
  const goToPreviousWeek = () => {
    setMondayDisplayed(prev => {
      const copy = new Date(prev);
      copy.setDate(copy.getDate() - 7);
      setSelectedDate(copy.toISOString().slice(0, 10));
      return copy;
    });
  };

  const goToNextWeek = () => {
    setMondayDisplayed(prev => {
      const copy = new Date(prev);
      copy.setDate(copy.getDate() + 7);
      setSelectedDate(copy.toISOString().slice(0, 10));
      return copy;
    });
  };

  const onDateChange = (e) => {
    setSelectedDate(e.target.value);
    setMondayDisplayed(getMondayFromDate(e.target.value));
  };

  // render badges (kept minimal; styles moved to Calendar.css or EventBadges.css)
  const renderCellContent = (eventsHere) => {
    if (!eventsHere || eventsHere.length === 0) return null;
    const visible = eventsHere.slice(0, 2);
    const remainder = eventsHere.length - visible.length;
    return (
      <div className="event-cell-content">
        {visible.map((ev, i) => (
          <div
            key={i}
            className="event-badge-weekly"
            style={{ background: TYPE_COLORS[ev.type] || TYPE_COLORS.default }}
            title={`${ev.title || ev.type || ''} — ${ev.start}–${ev.end}`}
          >
            {/* Hour removed from badge display */}
            <span className="event-title">{ev.title || ev.type || ''}</span>
          </div>
        ))}
        {remainder > 0 && <div className="event-remainder">+{remainder} more</div>}
      </div>
    );
  };

  return (
    <div className="calendar-container">
      <div className="calendar-header">
        <button onClick={goToPreviousWeek} title="Semaine précédente"><i className="bi bi-chevron-double-left" /></button>

        <input type="date" value={selectedDate} onChange={onDateChange} />

        <button onClick={goToNextWeek} title="Semaine suivante"><i className="bi bi-chevron-double-right" /></button>
      </div>

      <div className="calendar-table-wrapper">
        <table className="calendar-table">
          <thead>
            <tr>
              <th className="time-header" />
              {weekDays.map((date, idx) => (
                <th key={idx} className="day-header">
                  <div className="day-name">{['Lun','Mar','Mer','Jeu','Ven','Sam','Dim'][idx]}</div>
                  <div className="day-date">{dayLabel(date)}</div>
                </th>
              ))}
            </tr>
          </thead>

          <tbody>
            {halfHours.map((time, halfHourIndex) => (
              <tr key={time}>
                <td className="time-cell">{time}</td>

                {weekDays.map((dayDate, dayIndex) => {
                  const eventsHere = getEventsForCell(dayDate, halfHourIndex);
                  const bg = eventsHere.length ? (TYPE_COLORS[eventsHere[0].type] || TYPE_COLORS.default) : 'white';
                  const textColor = eventsHere.length ? 'white' : 'inherit';

                  return (
                    <td
                      key={dayIndex}
                      className="event-cell"
                      style={{ backgroundColor: eventsHere.length ? bg : 'white', color: textColor }}
                      title={eventsHere.length > 0 ? eventsHere.map(ev => `${ev.start}–${ev.end} ${ev.title || ''}`).join('\n') : ''}
                    >
                      {eventsHere.length > 0 ? renderCellContent(eventsHere) : null}
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
