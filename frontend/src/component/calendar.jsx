import React, { useState } from 'react';

import 'bootstrap-icons/font/bootstrap-icons.css';

import WeeklyPlanning from './WeeklyPlanning';
import MonthlyPlanning from './MonthlyPlanning';
import YearlyPlanning from './YearlyPlanning';
import { getMondayFromDate } from '../Utils/dateUtils';

export default function Calendar() {

  const exempleData = [
    {
      date: '29/09/2025', 
      start: '9:00',
      end: '12:30',
      type: 'presence'
    },
    {
      date: '29/09/2025', 
      start: '13:00',
      end: '17:30',
      type: 'presence'
    },
    {
      date: '30/09/2025', 
      start: '9:00',
      end: '12:30',
      type: 'presence'
    },
    {
      date: '30/09/2025', 
      start: '13:00',
      end: '17:30',
      type: 'presence'
    },
    {
      date: '01/10/2025', 
      start: '9:00',
      end: '12:30',
      type: 'absence: congé maladie'
    },
    {
      date: '01/10/2025', 
      start: '13:00',
      end: '17:30',
      type: 'absence: congé maladie'
    },
    {
      date: '02/10/2025', 
      start: '9:00',
      end: '12:30',
      type: 'presence'
    },
    {
      date: '02/10/2025', 
      start: '13:00',
      end: '17:30',
      type: 'absence: formation'
    },
  ];

  const now = new Date();

  // 'week' | 'month' | 'year'
  const [view, setView] = useState('week');

  const toggleTo = (target) => setView(target);

  return (
    <div style={{ padding: 12 }}>
      <h1>Mon calendrier</h1>

      {/* Toggle view */}
      <div style={{ marginBottom: 20, display: 'flex', gap: 8 }}>
        <button onClick={() => toggleTo('week')} className={view === 'week' ? 'active' : ''}>
          <i className="bi bi-calendar-week" /> Hebdo
        </button>
        <button onClick={() => toggleTo('month')} className={view === 'month' ? 'active' : ''}>
          <i className="bi bi-calendar3" /> Mensuel
        </button>
        <button onClick={() => toggleTo('year')} className={view === 'year' ? 'active' : ''}>
          <i className="bi bi-calendar4-week" /> Annuel
        </button>
      </div>

      <div>
        {view === 'week' && (
          <>
            <h2>Vue hebdomadaire</h2>
            <WeeklyPlanning
              exempleData={exempleData}
              now={now}
              getMondayFromDate={getMondayFromDate}
            />
          </>
        )}

        {view === 'month' && (
          <>
            <h2>Vue mensuelle</h2>
            <MonthlyPlanning
              exempleData={exempleData}
              now={now}
              getMondayFromDate={getMondayFromDate}
            />
          </>
        )}

        {view === 'year' && (
          <>
            <h2>Vue annuelle</h2>
            <YearlyPlanning
              exempleData={exempleData}
              now={now}
            />
          </>
        )}
      </div>
    </div>
  );
}
